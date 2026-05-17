<?php

namespace App\Filament\Admin\Resources\RanobeVolumeResource\Pages;

use App\Filament\Admin\Resources\RanobeVolumeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRanobeVolumes extends ListRecords
{
    protected static string $resource = RanobeVolumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
