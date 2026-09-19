<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UpdateFeedResource\Pages;
use App\Models\UpdateFeed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UpdateFeedResource extends Resource
{
    protected static ?string $model = UpdateFeed::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->label('Описание новости')
                        ->required(),
                    Forms\Components\TextInput::make('link')
                        ->label('Ссылка')
                        ->required(),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Дата создания')
                        ->seconds(false)
                        ->helperText('Если не указать дату при создании, будет использована текущая.'),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание новости')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->label('Ссылка')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Нет данных'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUpdateFeeds::route('/'),
            'create' => Pages\CreateUpdateFeed::route('/create'),
            'edit' => Pages\EditUpdateFeed::route('/{record}/edit'),
        ];
    }
}
