<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClassesTopResource\Pages;
use App\Models\ClassesTop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClassesTopResource extends Resource
{
    protected static ?string $model = ClassesTop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('letter')
                        ->label('Буква класса')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Назначается автоматически по количеству очков.'),
                    Forms\Components\TextInput::make('leader')
                        ->label('Лидер')
                        ->required(),
                    Forms\Components\TextInput::make('class_points')
                        ->label('Очки')
                        ->numeric()
                        ->required(),
                    Forms\Components\TextInput::make('leader_img')
                        ->label('Картинка лида')
                        ->required(),
                    Forms\Components\Checkbox::make('spoilers')
                        ->label('Спойлеры'),
                    Forms\Components\ColorPicker::make('color')
                        ->label('Цвет прогресс-бара')
                        ->required(),
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
                Tables\Columns\TextColumn::make('letter')
                    ->label('Буква класса'),
                Tables\Columns\TextColumn::make('leader')
                    ->label('Лидер'),
                Tables\Columns\TextColumn::make('class_points')
                    ->label('Очки'),
                Tables\Columns\TextColumn::make('leader_img')
                    ->label('Картинка лида'),
                Tables\Columns\CheckboxColumn::make('spoilers')
                    ->label('Спойлеры'),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Цвет прогресс бара'),
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
            'index' => Pages\ListClassesTops::route('/'),
            'create' => Pages\CreateClassesTop::route('/create'),
            'edit' => Pages\EditClassesTop::route('/{record}/edit'),
        ];
    }
}
