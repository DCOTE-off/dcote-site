<?php

namespace App\Filament\Resources\ClsssesTopResource\Pages;

use App\Filament\Resources\ClsssesTopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClsssesTops extends ListRecords
{
    protected static string $resource = ClsssesTopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
