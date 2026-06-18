<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnimeEpisodeResource\Pages;
use App\Filament\Admin\Resources\AnimeEpisodeResource\RelationManagers;
use App\Models\AnimeEpisode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnimeEpisodeResource extends Resource
{
    protected static ?string $model = AnimeEpisode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Эпизод')
                    ->schema([
                        Forms\Components\Select::make('season_id')
                            ->label('Сезон')
                            ->relationship('season', 'season_number')
                            ->required(),
                        Forms\Components\TextInput::make('episode_number')
                            ->label('Номер эпизода')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('episode_name')
                            ->label('Название эпизода')
                            ->required(),
                        Forms\Components\TextInput::make('trailer_link')
                            ->label('Ссылка на трейлер')
                            ->url(),
                        Forms\Components\Checkbox::make('completed')
                            ->label('Вышел')
                            ->inline(false),
                        Forms\Components\TextInput::make('opening_start')
                            ->label('Начало опенинга (def=-1)')
                            ->required(),
                        Forms\Components\DateTimePicker::make('appear_in')
                            ->label('Появится в'),
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
                Tables\Columns\TextColumn::make('season.season_number')
                    ->label('Сезон')
                    ->sortable(),
                Tables\Columns\TextColumn::make('episode_number')
                    ->label('Эпизод')
                    ->sortable(),
                Tables\Columns\TextColumn::make('episode_name')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('trailer_link')
                    ->label('Трейлер'),
                Tables\Columns\CheckboxColumn::make('completed')
                    ->label('Вышел'),
                Tables\Columns\TextColumn::make('opening_start')
                    ->label('Начало опенинга'),
                Tables\Columns\TextColumn::make('appear_in')
                    ->label('Появится в')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                SelectFilter::make('season_id')
                ->label('Фильтр по сезону')
                ->relationship('season', 'season_number')
                ->searchable()
                ->preload(),
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
            'index' => Pages\ListAnimeEpisodes::route('/'),
            'create' => Pages\CreateAnimeEpisode::route('/create'),
            'edit' => Pages\EditAnimeEpisode::route('/{record}/edit'),
        ];
    }
}
