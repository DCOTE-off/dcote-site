<?php

namespace App\Filament\Admin\Resources\AnimeSeasonResource\Pages;

use App\Filament\Admin\Resources\AnimeSeasonResource;
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
