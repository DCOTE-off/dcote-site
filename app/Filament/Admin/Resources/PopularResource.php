<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PopularResource\Pages;
use App\Models\AnimeSeason;
use App\Models\Popular;
use App\Models\RanobeVolume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PopularResource extends Resource
{
    protected static ?string $model = Popular::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Popular';

    protected static ?string $modelLabel = 'запись Popular';

    protected static ?string $pluralModelLabel = 'Popular';

    protected static ?string $slug = 'popular';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Материал для блока «Популярное»')
                ->description('Данные карточки автоматически берутся из выбранного сезона или тома.')
                ->schema([
                    Forms\Components\Select::make('target_type')
                        ->label('Тип материала')
                        ->options([
                            Popular::TYPE_ANIME => 'Аниме',
                            Popular::TYPE_RANOBE => 'Ранобэ',
                        ])
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('target_id', null))
                        ->required(),
                    Forms\Components\Select::make('target_id')
                        ->label('Сезон или том')
                        ->options(fn (Get $get): array => static::getTargetOptions($get('target_type')))
                        ->disabled(fn (Get $get): bool => blank($get('target_type')))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Порядок показа')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Показывать на главной')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_label')
                    ->label('Тип')
                    ->badge(),
                Tables\Columns\TextColumn::make('target_label')
                    ->label('Материал')
                    ->searchable(false),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('На главной'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopulars::route('/'),
            'create' => Pages\CreatePopular::route('/create'),
            'edit' => Pages\EditPopular::route('/{record}/edit'),
        ];
    }

    private static function getTargetOptions(?string $targetType): array
    {
        return match ($targetType) {
            Popular::TYPE_ANIME => AnimeSeason::query()
                ->orderBy('season_number')
                ->get()
                ->mapWithKeys(fn (AnimeSeason $season): array => [
                    $season->id => "{$season->season_number} сезон",
                ])
                ->all(),
            Popular::TYPE_RANOBE => RanobeVolume::query()
                ->with('year')
                ->orderBy('ranobe_year_id')
                ->orderBy('general_number')
                ->get()
                ->mapWithKeys(fn (RanobeVolume $volume): array => [
                    $volume->id => sprintf(
                        '%s год — %s том',
                        $volume->year?->year_number ?? '?',
                        (float) $volume->volume_number
                    ),
                ])
                ->all(),
            default => [],
        };
    }
}
