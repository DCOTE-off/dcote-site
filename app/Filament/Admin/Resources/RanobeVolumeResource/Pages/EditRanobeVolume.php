<?php

namespace App\Filament\Admin\Resources\RanobeVolumeResource\Pages;

use App\Filament\Admin\Resources\RanobeVolumeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRanobeVolume extends EditRecord
{
    protected static string $resource = RanobeVolumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
