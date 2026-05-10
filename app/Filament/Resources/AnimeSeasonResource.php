<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnimeSeasonResource\Pages;
use App\Filament\Resources\AnimeSeasonResource\RelationManagers;
use App\Models\AnimeSeason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnimeSeasonResource extends Resource
{
    protected static ?string $model = AnimeSeason::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->maxLength(255)
                    ->default('Вышел'),
                Forms\Components\TextInput::make('season_time')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('release_time')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('studio')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('number_of_episodes')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('last_update')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('img_src')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('season_number')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('season_description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('trailer_link')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('adapt_volumes')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('adapt_volumes_brackets')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('season_time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('release_time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('studio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_episodes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_update')
                    ->searchable(),
                Tables\Columns\TextColumn::make('img_src')
                    ->searchable(),
                Tables\Columns\TextColumn::make('season_number')
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
            'index' => Pages\ListAnimeSeasons::route('/'),
            'create' => Pages\CreateAnimeSeason::route('/create'),
            'edit' => Pages\EditAnimeSeason::route('/{record}/edit'),
        ];
    }
}
