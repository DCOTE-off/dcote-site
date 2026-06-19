<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnimeEpisodeResource\Pages;
use App\Filament\Resources\AnimeEpisodeResource\RelationManagers;
use App\Models\AnimeEpisode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnimeEpisodeResource extends Resource
{
    protected static ?string $model = AnimeEpisode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('episode_number')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('episode_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('trailer_link')
                    ->url()
                    ->default(null),
                Forms\Components\Toggle::make('has_dub'),
                Forms\Components\Toggle::make('has_sub'),
                Forms\Components\Toggle::make('has_anilibria'),
                Forms\Components\TextInput::make('opening_start')
                    ->numeric()
                    ->default(-1),
                Forms\Components\DateTimePicker::make('appear_in'),
                Forms\Components\TextInput::make('season_id')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('episode_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('episode_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trailer_link'),
                Tables\Columns\IconColumn::make('has_dub')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_sub')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_anilibria')
                    ->boolean(),
                Tables\Columns\TextColumn::make('opening_start')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appear_in')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('season_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
