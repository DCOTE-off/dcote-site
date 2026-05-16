<?php

namespace App\Filament\Admin\Resources\RanobeYearResource\Pages;

use App\Filament\Admin\Resources\RanobeYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRanobeYear extends EditRecord
{
    protected static string $resource = RanobeYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
