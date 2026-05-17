<?php

namespace App\Filament\Admin\Resources\RanobeChapterResource\Pages;

use App\Filament\Admin\Resources\RanobeChapterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRanobeChapters extends ListRecords
{
    protected static string $resource = RanobeChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
