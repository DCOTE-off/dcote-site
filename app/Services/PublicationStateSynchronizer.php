<?php

namespace App\Services;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeVolume;
use Illuminate\Support\Facades\Cache;
use Throwable;

class PublicationStateSynchronizer
{
    private const CACHE_KEY = 'publication-state:last-sync:v1';

    private const SYNC_INTERVAL_SECONDS = 60;

    public function sync(): void
    {
        if (!Cache::add(self::CACHE_KEY, true, now()->addSeconds(self::SYNC_INTERVAL_SECONDS))) {
            return;
        }

        try {
            AnimeEpisode::releaseDue();
            AnimeSeason::syncFinishedStatuses();
            RanobeVolume::syncFinishedStatuses();
        } catch (Throwable $error) {
            // A failed attempt must not suppress the next request's safe fallback.
            Cache::forget(self::CACHE_KEY);
            report($error);
        }
    }
}
