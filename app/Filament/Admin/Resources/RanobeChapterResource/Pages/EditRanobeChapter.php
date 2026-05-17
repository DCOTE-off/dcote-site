<?php

namespace App\Filament\Admin\Resources\RanobeChapterResource\Pages;

use App\Filament\Admin\Resources\RanobeChapterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRanobeChapter extends EditRecord
{
    protected static string $resource = RanobeChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
