<?php

namespace App\Filament\Admin\Resources\ClassesTopResource\Pages;

use App\Filament\Admin\Resources\ClassesTopResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassesTop extends EditRecord
{
    protected static string $resource = ClassesTopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
