<?php

namespace App\Console\Commands;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeVolume;
use Illuminate\Console\Command;

class SyncPublicationState extends Command
{
    protected $signature = 'publication:sync';

    protected $description = 'Синхронизация состояния публикаций по времени: серии, сезоны, тома';

    /**
     * Все три операции идемпотентны — запускать можно сколько угодно раз.
     */
    public function handle(): int
    {
        $released = AnimeEpisode::releaseDue();
        $closedSeasons = AnimeSeason::syncFinishedStatuses();
        $closedVolumes = RanobeVolume::syncFinishedStatuses();

        $this->info(sprintf(
            'Серий выпущено: %d, сезонов закрыто: %d, томов закрыто: %d.',
            $released,
            $closedSeasons,
            $closedVolumes,
        ));

        return self::SUCCESS;
    }
}
