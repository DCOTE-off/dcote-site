<?php

namespace App\Filament\Admin\Resources\UpdateFeedResource\Pages;

use App\Filament\Admin\Resources\UpdateFeedResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUpdateFeed extends CreateRecord
{
    protected static string $resource = UpdateFeedResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['created_at'])) {
            $data['created_at'] = now();
        }

        return $data;
    }
}
