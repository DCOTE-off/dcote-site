<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RanobeYearResource\Pages;
use App\Filament\Admin\Resources\RanobeYearResource\RelationManagers;
use App\Models\RanobeYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RanobeYearResource extends Resource
{
    protected static ?string $model = RanobeYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year_number')
                    ->required()
                    ->label('Год')
                    ->numeric(),
                Forms\Components\TextInput::make('year_readable')
                    ->required()
                    ->label('Читаемый год')
                    ->maxLength(255),
                Forms\Components\TextInput::make('words_quantity')
                    ->required()
                    ->label('Кол-во слов')
                    ->numeric(),
                Forms\Components\TextInput::make('hours_of_reading')
                    ->required()
                    ->label('Время чтения')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->label('Статус')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year_number')
                    ->numeric()
                    ->label('Год')
                    ->sortable(),
                Tables\Columns\TextColumn::make('year_readable')
                    ->searchable()
                    ->label('Читаемый год'),
                Tables\Columns\TextColumn::make('words_quantity')
                    ->numeric()
                    ->sortable()
                    ->label('Кол-во слов'),
                Tables\Columns\TextColumn::make('hours_of_reading')
                    ->searchable()
                    ->label('Время чтения'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->label('Статус'),
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
            'index' => Pages\ListRanobeYears::route('/'),
            'create' => Pages\CreateRanobeYear::route('/create'),
            'edit' => Pages\EditRanobeYear::route('/{record}/edit'),
        ];
    }
}
