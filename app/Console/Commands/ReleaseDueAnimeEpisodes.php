<?php

namespace App\Console\Commands;

use App\Models\AnimeEpisode;
use Illuminate\Console\Command;

class ReleaseDueAnimeEpisodes extends Command
{
    protected $signature = 'anime:release-due-episodes';

    protected $description = 'Mark anime episodes as released when their appear_in date is reached';

    public function handle(): int
    {
        $releasedCount = AnimeEpisode::releaseDue();

        $this->info("Released anime episodes: {$releasedCount}");

        return self::SUCCESS;
    }
}
