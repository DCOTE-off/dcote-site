<?php

namespace App\Filament\Resources\UpdateFeedResource\Pages;

use App\Filament\Resources\UpdateFeedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUpdateFeeds extends ListRecords
{
    protected static string $resource = UpdateFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
