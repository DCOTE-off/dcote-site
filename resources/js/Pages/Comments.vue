<script setup>
import { computed, defineOptions, onMounted, ref } from 'vue';
import axios from 'axios';
import AppLayout from '../Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import CommentInput from '../Components/CommentInput.vue';
import CommentItem from '../Components/CommentItem.vue';
import { pushToast } from '../stores/toast';
import '../../css/components/comments.css';
import '../../css/components/list-filter.css';
import '../../css/components/dropdown.css';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const props = defineProps({
    commentableType: {
        type: String,
        required: true,
    },
    commentableId: {
        type: [Number, String],
        required: true,
    },
    commentLabel: String,
});




const comments = ref([]);
const totalComments = ref(0);
const nextCursor = ref(null);
const hasMore = ref(false);
const loading = ref(false);
const submitting = ref(false);
const loadFailed = ref(false);

const topDraft = ref('');
const replyDraft = ref('');
const replyTarget = ref(null);

const sortCriterion = ref('new');
const sortOpen = ref(false);
let requestId = 0;

function startReply(comment) {
    if (!isAuthenticated.value) {
        pushToast('error', 'Войдите в аккаунт, чтобы ответить.');
        return;
    }

    replyTarget.value = comment;
    replyDraft.value = '';
}

function cancelReply() {
    replyTarget.value = null;
    replyDraft.value = '';
    topDraft.value = '';
}

function cancelComment() {
    topDraft.value = '';
}

function csrfHeaders() {
    return { 'X-CSRF-TOKEN': page.props.csrf_token };
}

function extractError(e, fallback) {
    if (e?.response?.status === 401) {
        return 'Нужно войти в аккаунт.';
    }

    const errors = e?.response?.data?.errors;
    if (errors) {
        return Object.values(errors).flat().join(' ');
    }

    return e?.response?.data?.message ?? fallback;
}

function toastError(e, fallback) {
    pushToast('error', extractError(e, fallback));
}

async function submitComment() {
    const content = topDraft.value.trim();
    if (content === '' || submitting.value) return;

    submitting.value = true;
    try {
        const { data } = await axios.post('/api/comments', {
            commentable_type: props.commentableType,
            commentable_id: props.commentableId,
            content,
        }, { headers: csrfHeaders() });

        comments.value.unshift(normalizeComment(data.comment));
        totalComments.value = Number(data.total ?? totalComments.value);
        topDraft.value = '';
    } catch (e) {
        toastError(e, 'Не удалось отправить комментарий.');
    } finally {
        submitting.value = false;
    }
}

async function submitReply() {
    const target = replyTarget.value;
    const content = replyDraft.value.trim();
    if (!target || content === '' || submitting.value) return;

    submitting.value = true;
    try {
        const { data } = await axios.post('/api/comments', {
            commentable_type: props.commentableType,
            commentable_id: props.commentableId,
            parent_id: target.id,
            content,
        }, { headers: csrfHeaders() });

        target.replies.push(normalizeComment(data.comment));
        totalComments.value = Number(data.total ?? totalComments.value);
        replyTarget.value = null;
        replyDraft.value = '';
    } catch (e) {
        toastError(e, 'Не удалось отправить ответ.');
    } finally {
        submitting.value = false;
    }
}

async function voteComment({ comment, vote }) {
    try {
        const { data } = await axios.post(
            `/api/comments/${comment.id}/reaction`,
            { vote },
            { headers: csrfHeaders() },
        );

        comment.rating = data.rating;
        comment.userVote = data.user_vote;
    } catch (e) {
        toastError(e, 'Не удалось проголосовать.');
    }
}

async function saveEdit({ comment, content }) {
    const text = content.trim();
    if (text === '') return;

    try {
        const { data } = await axios.patch(
            `/api/comments/${comment.id}`,
            { content: text },
            { headers: csrfHeaders() },
        );

        comment.text = data.is_deleted ? '[Удалено]' : (data.content ?? '');
        comment.html = data.html;
        comment.isEdited = data.is_edited;
        comment.can = data.can;
    } catch (e) {
        toastError(e, 'Не удалось сохранить изменения.');
    }
}

function removeFromTree(list, id) {
    const index = list.findIndex((item) => item.id === id);
    if (index !== -1) {
        list.splice(index, 1);
        return true;
    }

    return list.some((item) => removeFromTree(item.replies ?? [], id));
}

function containsId(list, id) {
    return list.some((item) => item.id === id || containsId(item.replies ?? [], id));
}

async function removeComment(comment) {
    if (!window.confirm('Удалить комментарий?')) return;

    try {
        const { data } = await axios.delete(
            `/api/comments/${comment.id}`,
            { headers: csrfHeaders() },
        );

        removeFromTree(comments.value, comment.id);

        if (data.total !== undefined) {
            totalComments.value = Number(data.total);
        }

        if (replyTarget.value && !containsId(comments.value, replyTarget.value.id)) {
            replyTarget.value = null;
            replyDraft.value = '';
        }
    } catch (e) {
        toastError(e, 'Не удалось удалить комментарий.');
    }
}

const commentDateFormatter = new Intl.DateTimeFormat('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

function formatCommentDate(iso) {
    if (!iso) return '';

    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return '';

    const parts = Object.fromEntries(
        commentDateFormatter.formatToParts(date).map((part) => [part.type, part.value]),
    );

    return `${parts.day}.${parts.month}.${parts.year}; ${parts.hour}:${parts.minute}`;
}

function normalizeComment(raw) {
    return {
        id: raw.id,
        parentId: raw.parent_id,
        username: raw.author?.nickname ?? 'Аноним',
        avatar: raw.author?.avatar ?? null,
        role: raw.author?.role ?? '',
        isTeam: raw.author?.is_team ?? false,
        text: raw.is_deleted ? '[Удалено]' : (raw.content ?? ''),
        html: raw.html,
        rating: raw.rating,
        userVote: raw.user_vote,
        date: formatCommentDate(raw.created_at),
        isDeleted: raw.is_deleted,
        isEdited: raw.is_edited,
        can: raw.can,
        replies: (raw.replies ?? []).map(normalizeComment),
    };
}

async function loadComms({ append = false } = {}) {
    const id = ++requestId;

    loading.value = true;
    loadFailed.value = false;

    try {
        const { data } = await axios.get('/api/comments', {
            params: {
                commentable_type: props.commentableType,
                commentable_id: props.commentableId,
                sort: sortCriterion.value,
                ...(append && nextCursor.value ? { cursor: nextCursor.value } : {}),
            },
        });

        // Пока шёл запрос, пользователь мог сменить сортировку — старый ответ не нужен.
        if (id !== requestId) return;

        const batch = (data.data ?? []).map(normalizeComment);
        comments.value = append ? [...comments.value, ...batch] : batch;
        nextCursor.value = data.next_cursor ?? null;
        hasMore.value = Boolean(data.has_more);
        totalComments.value = Number(data.total ?? 0);
    } catch (e) {
        if (id !== requestId) return;
        loadFailed.value = true;
        toastError(e, 'Не удалось загрузить комментарии.');
    } finally {
        if (id === requestId) loading.value = false;
    }
}

function selectSort(criterion) {
    sortOpen.value = false;

    if (criterion === sortCriterion.value) return;

    sortCriterion.value = criterion;
    nextCursor.value = null;
    hasMore.value = false;
    loadComms();
}

onMounted(loadComms);


</script>

<template>
    <section class="comments">
        <div class="comments__header">
            <div class="list-filter comments__sort" :class="{ 'is-open': sortOpen }">
                <button
                    type="button"
                    class="filter-toggle comments__sort-toggle link-pill-outline"
                    style="border-color: rgba(146, 21, 69, 1);"
                    :aria-expanded="sortOpen"
                    aria-haspopup="listbox"
                    aria-controls="comments-sort-menu"
                    @click="sortOpen = !sortOpen">
                    <span>СОРТИРОВКА</span>
                    <svg class="dropdown-icon" aria-hidden="true">
                        <use href="#dropdown" />
                    </svg>
                </button>
                <div class="list-filter-menu" id="comments-sort-menu" role="listbox" aria-label="Критерий сортировки комментариев">
                    <button
                        type="button"
                        class="list-filter-option"
                        :class="{ 'is-selected': sortCriterion === 'new' }"
                        role="option"
                        :aria-selected="sortCriterion === 'new'"
                        @click="selectSort('new')">
                        <svg class="list-filter-option-icon list-filter-option-icon--comments-time" aria-hidden="true">
                            <use href="#sort-descending-filled-compact" />
                        </svg>
                        <span>По времени</span>
                    </button>
                    <button
                        type="button"
                        class="list-filter-option"
                        :class="{ 'is-selected': sortCriterion === 'rating' }"
                        role="option"
                        :aria-selected="sortCriterion === 'rating'"
                        @click="selectSort('rating')">
                        <svg class="list-filter-option-icon list-filter-option-icon--comments-rating" aria-hidden="true">
                            <use href="#star" />
                        </svg>
                        <span>По оценкам</span>
                    </button>
                </div>
            </div>
            <h1 class="comments__title">КОММЕНТАРИИ {{ commentLabel }}</h1>
            <Link class="comments__rules link-pill-outline" style="border-color: rgba(146, 21, 69, 1);" :href="route('rules')">ПРАВИЛА САЙТА</Link>
            <div class="comments_comment-counter"><img :src="'/svgs/message1.svg'" class="comments_comment-counter-icon" alt="comments-icon">
                <span class="comments_comment-counter-label">{{ totalComments }}</span>
            </div>
        </div>

        <CommentInput
            v-model="topDraft"
            @submit="submitComment"
            @cancel="cancelComment" />

        <div class="comments__list">
            <p v-if="loading && comments.length === 0" class="comments__status" style="margin-inline: auto;">Загрузка…</p>
            <p v-else-if="loadFailed && comments.length === 0" class="comments__status comments__status--error" style="margin-inline: auto;">Не удалось загрузить комментарии</p>
            <h2 v-else-if="comments.length === 0" class="comments__status" style="margin-inline: auto;">Комментариев пока нет</h2>

            <CommentItem
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
                :depth="0"
                @reply="startReply"
                @vote="voteComment"
                @save-edit="saveEdit"
                @remove="removeComment">
                <template #reply="{ comment }">
                    <CommentInput
                        v-if="replyTarget?.id === comment.id"
                        v-model="replyDraft"
                        :replying-to="comment.username"
                        autofocus
                        @submit="submitReply"
                        @cancel="cancelReply" />
                </template>
            </CommentItem>

            <button
                v-if="hasMore"
                type="button"
                class="comments__more"
                :disabled="loading"
                @click="loadComms({ append: true })">
                {{ loading ? 'Загрузка…' : 'Показать ещё' }}
            </button>
        </div>
    </section>
</template>
