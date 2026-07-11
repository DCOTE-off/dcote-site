<?php

namespace App\Http\Controllers;

use App\Models\AnimeEpisode;
use App\Models\Rating;
use App\Models\RanobeVolume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RatingController extends Controller
{
    private const RATEABLE_MODELS = [
        'anime_episode' => AnimeEpisode::class,
        'ranobe_volume' => RanobeVolume::class,
    ];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rateable_type' => ['required', Rule::in(['anime_episode', 'ranobe_volume'])],
            'rateable_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $this->ensureRateableExists($data['rateable_type'], $data['rateable_id']);

        $rating = Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'rateable_type' => $data['rateable_type'],
                'rateable_id' => $data['rateable_id'],
            ],
            ['rating' => $data['rating']]
        );

        $summary = $this->ratingSummary($data['rateable_type'], $data['rateable_id']);

        return response()->json([
            'user_rating' => (int) $rating->rating,
            'avg_rating' => round((float) $summary->avg_rating, 1),
            'ratings_count' => (int) $summary->ratings_count,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rateable_type' => ['required', Rule::in(['anime_episode', 'ranobe_volume'])],
            'rateable_id' => 'required|integer',
        ]);

        Rating::where([
            'user_id' => auth()->id(),
            'rateable_type' => $data['rateable_type'],
            'rateable_id' => $data['rateable_id'],
        ])->delete();

        $summary = $this->ratingSummary($data['rateable_type'], $data['rateable_id']);

        return response()->json([
            'user_rating' => 0,
            'avg_rating' => round((float) $summary->avg_rating, 1),
            'ratings_count' => (int) $summary->ratings_count,
        ]);
    }

    private function ensureRateableExists(string $type, int $id): void
    {
        $model = self::RATEABLE_MODELS[$type];
        if ($model::query()->whereKey($id)->exists()) {
            return;
        }

        throw ValidationException::withMessages([
            'rateable_id' => ['Выбранный материал не существует.'],
        ]);
    }

    private function ratingSummary(string $type, int $id): object
    {
        return Rating::query()
            ->where('rateable_type', $type)
            ->where('rateable_id', $id)
            ->selectRaw('COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as ratings_count')
            ->first();
    }
}
