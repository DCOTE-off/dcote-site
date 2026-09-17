<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Чтение комментариев публично.
Route::get('/comments', [CommentsController::class, 'load']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/ratings', [RatingController::class, 'store']);
    Route::delete('/ratings', [RatingController::class, 'destroy']);

    Route::post('/comments', [CommentsController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentsController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentsController::class, 'destroy']);
    Route::post('/comments/{comment}/reaction', [CommentsController::class, 'reaction']);
});
