<?php

namespace App\Filament\Admin\Resources\AnimeSeasonResource\Pages;

use App\Filament\Admin\Resources\AnimeSeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnimeSeasons extends ListRecords
{
    protected static string $resource = AnimeSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
