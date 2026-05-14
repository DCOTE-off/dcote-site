<?php

namespace App\Filament\Admin\Resources\UpdateFeedResource\Pages;

use App\Filament\Admin\Resources\UpdateFeedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUpdateFeed extends EditRecord
{
    protected static string $resource = UpdateFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
