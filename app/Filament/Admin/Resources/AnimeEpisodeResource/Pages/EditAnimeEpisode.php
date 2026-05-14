<?php

namespace App\Filament\Admin\Resources\AnimeEpisodeResource\Pages;

use App\Filament\Admin\Resources\AnimeEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnimeEpisode extends EditRecord
{
    protected static string $resource = AnimeEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
