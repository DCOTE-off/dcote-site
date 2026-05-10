<?php

namespace App\Filament\Resources\AnimeSeasonResource\Pages;

use App\Filament\Resources\AnimeSeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnimeSeason extends EditRecord
{
    protected static string $resource = AnimeSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
