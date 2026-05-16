<?php

namespace App\Filament\Admin\Resources\RanobeYearResource\Pages;

use App\Filament\Admin\Resources\RanobeYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRanobeYears extends ListRecords
{
    protected static string $resource = RanobeYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
