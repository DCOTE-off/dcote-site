<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rateable_type' => ['required', Rule::in(['anime_episode', 'ranobe_volume'])],
            'rateable_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $rating = Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'rateable_type' => $data['rateable_type'],
                'rateable_id' => $data['rateable_id'],
            ],
            ['rating' => $data['rating']]
        );

        $avg = Rating::where('rateable_type', $data['rateable_type'])
            ->where('rateable_id', $data['rateable_id'])
            ->avg('rating');

        $count = Rating::where('rateable_type', $data['rateable_type'])
            ->where('rateable_id', $data['rateable_id'])
            ->count();

        return response()->json([
            'user_rating' => (int) $rating->rating,
            'avg_rating' => round((float) $avg, 1),
            'ratings_count' => $count,
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

        $avg = Rating::where('rateable_type', $data['rateable_type'])
            ->where('rateable_id', $data['rateable_id'])
            ->avg('rating');

        $count = Rating::where('rateable_type', $data['rateable_type'])
            ->where('rateable_id', $data['rateable_id'])
            ->count();

        return response()->json([
            'user_rating' => 0,
            'avg_rating' => $avg ? round((float) $avg, 1) : 0,
            'ratings_count' => $count,
        ]);
    }
}
