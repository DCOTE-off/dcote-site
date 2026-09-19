<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReaction;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CommentsController extends Controller
{
    /**
     * Количество корневых комментариев на страницу.
     */
    private const PER_PAGE = 20;

    /**
     * Максимальная длина комментария.
     */
    private const MAX_LENGTH = 20000;

    /**
     * Роли, которые считаются «командой проекта» (для подсветки).
     */
    private const TEAM_ROLE_IDS = [2, 3, 4];

    /**
     * Загрузка комментариев к материалу (серия, глава, том, ...).
     * GET /api/comments?commentable_type=ranobe_chapter&commentable_id=42&sort=new
     */
    public function load(Request $request): JsonResponse
    {
        $data = $request->validate([
            'commentable_type' => ['required', 'string'],
            'commentable_id' => ['required', 'integer'],
            'sort' => ['nullable', 'in:new,rating'],
        ]);

        $this->ensureCommentableExists($data['commentable_type'], $data['commentable_id']);

        $sort = $data['sort'] ?? 'new';

        $rootsQuery = Comment::query()
            ->withTrashed() // удалённые показываем как «[Удалено]», но ветку храним
            ->where('commentable_type', $data['commentable_type'])
            ->where('commentable_id', $data['commentable_id'])
            ->whereNull('parent_id')
            ->with('user.role')
            ->withCount('replies');

        // Курсорная пагинация: новые записи не смещают уже загруженные страницы.
        if ($sort === 'rating') {
            $rootsQuery->orderByDesc('rating')->orderByDesc('id');
        } else {
            $rootsQuery->orderByDesc('id');
        }

        $roots = $rootsQuery->cursorPaginate(self::PER_PAGE);
        $rootCollection = $roots->getCollection();

        $this->attachDescendants($rootCollection);

        $votes = $this->currentUserVotes($this->collectTreeIds($rootCollection));

        return response()->json([
            'data' => $rootCollection
                ->map(fn (Comment $comment) => $this->formatComment($comment, $votes))
                ->values(),
            'next_cursor' => $roots->nextCursor()?->encode(),
            'has_more' => $roots->hasMorePages(),
            'total' => $this->totalFor($data['commentable_type'], $data['commentable_id']),
        ]);
    }

    /**
     * Создание комментария или ответа.
     * POST /api/comments
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'commentable_type' => ['required', 'string'],
            'commentable_id' => ['required', 'integer'],
            'parent_id' => ['nullable', 'integer'],
            'content' => ['required', 'string', 'max:'.self::MAX_LENGTH],
        ]);

        $this->ensureCommentableExists($data['commentable_type'], $data['commentable_id']);

        $content = trim($data['content']);
        if ($content === '') {
            throw ValidationException::withMessages([
                'content' => ['Введите текст комментария.'],
            ]);
        }

        $parent = null;
        if (! empty($data['parent_id'])) {
            $parent = Comment::query()
                ->whereKey($data['parent_id'])
                ->where('commentable_type', $data['commentable_type'])
                ->where('commentable_id', $data['commentable_id'])
                ->first();

            if ($parent === null) {
                throw ValidationException::withMessages([
                    'parent_id' => ['Родительский комментарий не найден.'],
                ]);
            }
        }

        $comment = Comment::create([
            'commentable_type' => $data['commentable_type'],
            'commentable_id' => $data['commentable_id'],
            'user_id' => auth()->id(),
            'parent_id' => $parent?->id,
            'root_id' => $parent ? ($parent->root_id ?? $parent->id) : null,
            'content' => $content,
        ]);

        // У корневого комментария корень — он сам. Через query builder,
        // чтобы не трогать updated_at (иначе коммент сразу «отредактирован»).
        if ($comment->root_id === null) {
            DB::table('comments')->where('id', $comment->id)->update(['root_id' => $comment->id]);
            $comment->root_id = $comment->id;
        }

        $comment->load('user.role');

        return response()->json([
            'comment' => $this->formatComment($comment, collect()),
            'total' => $this->totalFor($data['commentable_type'], $data['commentable_id']),
        ]);
    }

    /**
     * Редактирование своего комментария.
     * PATCH /api/comments/{comment}
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:'.self::MAX_LENGTH],
        ]);

        $content = trim($data['content']);
        if ($content === '') {
            throw ValidationException::withMessages([
                'content' => ['Введите текст комментария.'],
            ]);
        }

        $comment->update(['content' => $content]);
        $comment->load('user.role');

        return response()->json($this->formatComment($comment, collect()));
    }

    /**
     * Удаление комментария (автор или модерация).
     * DELETE /api/comments/{comment}
     *
     * MVP: вместе с комментарием удаляется всё поддерево ответов.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->deleteSubtree($comment);

        return response()->json([
            'id' => $comment->id,
            'is_deleted' => true,
            'total' => $this->totalFor($comment->commentable_type, (int) $comment->commentable_id),
        ]);
    }

    /**
     * Общее число комментариев к материалу (включая ответы).
     */
    private function totalFor(string $type, int $id): int
    {
        return Comment::query()
            ->withTrashed()
            ->where('commentable_type', $type)
            ->where('commentable_id', $id)
            ->count();
    }

    /**
     * Рекурсивно удаляет комментарий и всех его потомков.
     * Дети удаляются первыми: у parent_id FK с nullOnDelete,
     * поэтому родителя нельзя снести раньше них.
     */
    private function deleteSubtree(Comment $comment): void
    {
        $comment->replies()->withTrashed()->get()
            ->each(fn (Comment $reply) => $this->deleteSubtree($reply));

        $comment->forceDelete();
    }

    /**
     * Голос за комментарий (toggle).
     * POST /api/comments/{comment}/reaction  { vote: 1 | -1 }
     */
    public function reaction(Request $request, Comment $comment): JsonResponse
    {
        $data = $request->validate([
            'vote' => ['required', 'integer', 'in:-1,1'],
        ]);

        $userId = auth()->id();
        $newVote = (int) $data['vote'];

        $result = DB::transaction(function () use ($comment, $userId, $newVote) {
            $existing = CommentReaction::query()
                ->where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            $oldVote = (int) ($existing?->vote ?? 0);
            $userVote = $newVote;
            $delta = $newVote - $oldVote;

            if ($existing !== null && $oldVote === $newVote) {
                // Повторный клик по той же реакции — отменяем.
                // Через query builder: у таблицы составной PK (comment_id, user_id), id нет.
                CommentReaction::query()
                    ->where('comment_id', $comment->id)
                    ->where('user_id', $userId)
                    ->delete();

                $userVote = 0;
                $delta = -$oldVote;
            } elseif ($existing !== null) {
                // Смена реакции (up <-> down).
                CommentReaction::query()
                    ->where('comment_id', $comment->id)
                    ->where('user_id', $userId)
                    ->update(['vote' => $newVote]);
            } else {
                CommentReaction::create([
                    'comment_id' => $comment->id,
                    'user_id' => $userId,
                    'vote' => $newVote,
                ]);
            }

            if ($delta !== 0) {
                // Атомарный инкремент рейтинга, без read-modify-write.
                DB::table('comments')
                    ->where('id', $comment->id)
                    ->update(['rating' => DB::raw('rating + ('.(int) $delta.')')]);
            }

            $rating = (int) DB::table('comments')->where('id', $comment->id)->value('rating');

            return [
                'rating' => $rating,
                'user_vote' => $userVote,
            ];
        });

        return response()->json($result);
    }

    /**
     * Догружает всё поддерево ответов к переданным корневым комментариям.
     * Ветка может быть любой глубины: связь parent_id + денормализованный root_id
     * позволяют выбрать всех потомков одним запросом, без рекурсивных SQL.
     */
    private function attachDescendants(Collection $roots): void
    {
        $rootIds = $roots->pluck('id')->all();

        if ($rootIds === []) {
            return;
        }

        $descendants = Comment::query()
            ->withTrashed()
            ->whereIn('root_id', $rootIds)
            ->whereNotNull('parent_id')
            ->with('user.role')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $children = $descendants->groupBy('parent_id');

        // Собираем дерево снизу вверх в памяти: один запрос вместо N+1 по уровням.
        $attach = function (Comment $node) use (&$attach, $children): void {
            $kids = $children->get($node->id, collect())->values();

            $kids->each(function (Comment $kid) use (&$attach): void {
                $attach($kid);
            });

            $node->setRelation('replies', $kids);
        };

        $roots->each(fn (Comment $root) => $attach($root));
    }

    /**
     * id всех узлов дерева — для выборки голосов одним запросом.
     */
    private function collectTreeIds(Collection $roots): Collection
    {
        $ids = collect();

        $walk = function (Comment $node) use (&$walk, $ids): void {
            $ids->push($node->id);
            $node->replies->each(fn (Comment $reply) => $walk($reply));
        };

        $roots->each(fn (Comment $root) => $walk($root));

        return $ids;
    }

    /**
     * Голоса текущего пользователя по всем комментариям страницы — одним запросом.
     */
    private function currentUserVotes(Collection $ids): Collection
    {
        if ($ids->isEmpty() || ! auth()->check()) {
            return collect();
        }

        return CommentReaction::query()
            ->where('user_id', auth()->id())
            ->whereIn('comment_id', $ids->all())
            ->pluck('vote', 'comment_id');
    }

    /**
     * @return array<string, mixed>
     */
    private function formatComment(Comment $comment, Collection $votes): array
    {
        $isDeleted = $comment->trashed();
        $user = $comment->user;
        $isOwn = auth()->check() && (int) $comment->user_id === (int) auth()->id();

        return [
            'id' => $comment->id,
            'parent_id' => $comment->parent_id,
            'root_id' => $comment->root_id,
            'content' => $isDeleted ? null : $comment->content,
            'html' => $isDeleted ? null : $this->renderContent($comment->content),
            'rating' => (int) $comment->rating,
            'user_vote' => (int) ($votes[$comment->id] ?? 0),
            'created_at' => $comment->created_at?->toIso8601String(),
            'is_edited' => $comment->updated_at !== null
                && $comment->created_at !== null
                && $comment->updated_at->gt($comment->created_at),
            'is_deleted' => $isDeleted,
            'author' => $user ? [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'avatar' => $user->avatar_url,
                'role' => $user->role?->name,
                'is_team' => in_array($user->role_id, self::TEAM_ROLE_IDS, true),
            ] : null,
            'can' => [
                'edit' => $isOwn && ! $isDeleted,
                'delete' => ($isOwn || $this->canModerate()) && ! $isDeleted,
            ],
            'replies_count' => (int) ($comment->replies_count ?? $comment->replies->count()),
            'replies' => $comment->relationLoaded('replies')
                ? $comment->replies
                    ->map(fn (Comment $reply) => $this->formatComment($reply, $votes))
                    ->values()
                : [],
        ];
    }

    /**
     * Markdown → HTML. Для пользовательского контента сырой HTML вырезаем (XSS).
     * Синтаксис спойлера ||текст|| превращаем в разметку-обёртку.
     */
    private function renderContent(string $content): string
    {
        $html = Str::markdown($content, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Замену делаем по уже отрендеренному и очищенному HTML, поэтому
        // содержимое спойлера безопасно (без сырого HTML из пользователя).
        return preg_replace(
            '/\|\|(.+?)\|\|/u',
            '<span class="comment__spoiler" role="button" tabindex="0">'
                .'<span class="comment__spoiler-content">$1</span>'
                .'</span>',
            $html,
        ) ?? $html;
    }

    private function canModerate(): bool
    {
        return auth()->check() && in_array(auth()->user()->role_id, [2, 3, 4], true);
    }

    private function ensureCommentableExists(string $type, int $id): void
    {
        $modelClass = Relation::morphMap()[$type] ?? null;

        if ($modelClass === null) {
            throw ValidationException::withMessages([
                'commentable_type' => ['Неизвестный тип материала.'],
            ]);
        }

        if (! $modelClass::query()->whereKey($id)->exists()) {
            throw ValidationException::withMessages([
                'commentable_id' => ['Материал не существует.'],
            ]);
        }
    }
}
