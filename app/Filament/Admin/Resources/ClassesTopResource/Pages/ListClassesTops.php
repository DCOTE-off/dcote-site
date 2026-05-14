<?php

namespace App\Filament\Admin\Resources\ClassesTopResource\Pages;

use App\Filament\Admin\Resources\ClassesTopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListClassesTops extends ListRecords
{
    protected static string $resource = ClassesTopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Топ классов на главной. Не более 8 записей!';
    }
}
