<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnimeSeasonResource\Pages;
use App\Models\AnimeSeason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnimeSeasonResource extends Resource
{
    protected static ?string $model = AnimeSeason::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Сезон')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Статус')
                        ->options([
                            'Вышел' => 'Вышел',
                            'Онгоинг' => 'Онгоинг',
                            'Анонс' => 'Анонс',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('season_number')
                        ->label('Номер сезона')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('studio')
                        ->label('Студия')
                        ->required(),
                    Forms\Components\TextInput::make('season_time')
                        ->label('Время сезона')
                        ->required(),
                    Forms\Components\TextInput::make('release_time')
                        ->label('Дата релиза')
                        ->required(),
                    Forms\Components\TextInput::make('number_of_episodes')
                        ->label('Количество эпизодов')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('last_update')
                        ->label('Последнее обновление')
                        ->required(),
                    Forms\Components\TextInput::make('img_src')
                        ->label('Изображение')
                        ->required(),
                    Forms\Components\Textarea::make('season_description')
                        ->label('Описание сезона')
                        ->required(),
                    Forms\Components\TextInput::make('trailer_link')
                        ->label('Ссылка на трейлер')
                        ->url(),
                    Forms\Components\TextInput::make('adapt_volumes')
                        ->label('Адаптируемые тома')
                        ->required(),
                    Forms\Components\TextInput::make('adapt_volumes_brackets')
                        ->label('Тома в скобках'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Онгоинг' => 'ongoing',
                        'Анонс' => 'anons',
                        default => 'released',
                    }),
                Tables\Columns\TextColumn::make('season_number')
                    ->label('Сезон'),
                Tables\Columns\TextColumn::make('studio')
                    ->label('Студия'),
                Tables\Columns\TextColumn::make('season_time')
                    ->label('Время сезона'),
                Tables\Columns\TextColumn::make('release_time')
                    ->label('Дата релиза'),
                Tables\Columns\TextColumn::make('number_of_episodes')
                    ->label('Эпизодов'),
                Tables\Columns\TextColumn::make('last_update')
                    ->label('Обновлено'),
                Tables\Columns\TextColumn::make('adapt_volumes')
                    ->label('Тома'),
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
            'index' => Pages\ListAnimeSeasons::route('/'),
            'create' => Pages\CreateAnimeSeason::route('/create'),
            'edit' => Pages\EditAnimeSeason::route('/{record}/edit'),
        ];
    }
}
