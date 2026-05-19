<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RanobeChapterResource\Pages;
use App\Filament\Admin\Resources\RanobeChapterResource\RelationManagers;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor; // Рекомендую вместо Textarea
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Storage;


class RanobeChapterResource extends Resource
{
    protected static ?string $model = RanobeChapter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ranobe_year_id')
                    ->label('Год')
                    ->relationship('year', 'year_number')
                    ->required()
                    ->live() 
                    ->afterStateUpdated(fn (callable $set) => $set('ranobe_volume_id', null)),
                Forms\Components\Select::make('ranobe_volume_id')
                    ->label('Том')
                    ->relationship('volume', 'volume_number')
                    ->required()
                    ->options(function ($get) {
                        $yearId = $get('ranobe_year_id');
                        if (! $yearId) {
                            return [];
                        }
                        return RanobeVolume::where('ranobe_year_id', $yearId)
                            ->pluck('volume_number', 'id')
                            ->map(fn ($num) => floatval($num));
                    }),
                Forms\Components\TextInput::make('title')
                    ->label('Название главы')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('chapter_number')
                    ->label('Номер главы')
                    ->required()
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state !== null ? $state + 0 : ''),
                FileUpload::make('md_import')
                    ->label('Импорт из .md файла')
                    ->acceptedFileTypes(['text/markdown', 'text/plain', 'application/octet-stream'])
                    ->storeFiles(false)
                    ->dehydrated(false)
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (!$state) return;
                        $content = $state->get();
                        $set('chapter_content', $content);
                    }),
                MarkdownEditor::make('chapter_content')
                    ->label('Текст главы (Markdown)')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'blockquote', 'bold', 'bulletList', 'codeBlock',
                        'heading', 'italic', 'link', 'orderedList', 'redo', 'undo',
                    ]),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year.year_number')
                    ->numeric()
                    ->label('Год')
                    ->sortable(),
                Tables\Columns\TextColumn::make('volume.volume_number')
                    ->numeric()
                    ->label('Том')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chapter_number')
                    ->sortable()
                    ->label('Глава')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state + 0 : ''),
            ])
            ->filters([
                SelectFilter::make('ranobe_year_id')
                    ->label('Фильтр по году')
                    ->relationship('year', 'year_number')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('volume_number')
                    ->label('Фильтр по тому')
                    ->options(function () {
                        return RanobeVolume::query()
                            ->pluck('volume_number')
                            ->map(fn ($num) => floatval($num))
                            ->unique()
                            ->sort()
                            ->mapWithKeys(fn ($num) => [(string)$num => (string)$num]) 
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return;
                        }
                        $query->whereHas('volume', function (Builder $q) use ($data) {
                            $q->where('volume_number', $data['value']);
                        });
                    })
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Поиск по названию');
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
            'index' => Pages\ListRanobeChapters::route('/'),
            'create' => Pages\CreateRanobeChapter::route('/create'),
            'edit' => Pages\EditRanobeChapter::route('/{record}/edit'),
        ];
    }
}
