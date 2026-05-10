<?php

namespace App\Filament\Resources\AnimeEpisodeResource\Pages;

use App\Filament\Resources\AnimeEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnimeEpisodes extends ListRecords
{
    protected static string $resource = AnimeEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
