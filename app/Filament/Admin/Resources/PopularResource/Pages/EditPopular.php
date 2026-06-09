<?php

namespace App\Filament\Admin\Resources\PopularResource\Pages;

use App\Filament\Admin\Resources\PopularResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPopular extends EditRecord
{
    protected static string $resource = PopularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
