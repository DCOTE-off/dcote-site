<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RanobeVolumeResource\Pages;
use App\Models\RanobeVolume;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RanobeVolumeResource extends Resource
{
    protected static ?string $model = RanobeVolume::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ranobe_year_id')
                    ->label('Год')
                    ->relationship('year', 'year_number')
                    ->required(),
                Forms\Components\TextInput::make('volume_number')
                    ->label('Номер тома')
                    ->required()
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state !== null ? $state + 0 : ''),
                Forms\Components\TextInput::make('all_chapters')
                    ->label('Всего глав')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('general_number')
                    ->label('Общ номер')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->label('Статус')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('release_date_book')
                    ->label('Дата выхода(книга)')
                    ->required(),
                Forms\Components\DatePicker::make('release_date_digital')
                    ->label('Дата выхода(цифра)')
                    ->required(),
                Forms\Components\TextInput::make('pages_quantity')
                    ->label('Кол-во страниц')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('isbn')
                    ->label('ISBN')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('promo_link')
                    ->label('Ссылка на промо')
                    ->url(),
                Forms\Components\Textarea::make('volume_description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(10),
                FileUpload::make('cover_image')
                    ->label('Обложка тома')
                    ->image()
                    ->imageEditor()
                    ->directory(function ($get) {
                        $yearId = $get('ranobe_year_id');
                        $volNum = $get('volume_number');
                        if (! $yearId || ! $volNum) {
                            return 'ranobe/uploads-tmp';
                        }

                        return "ranobe/year-{$yearId}/volume-{$volNum}";
                    })
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get) {
                        $yearId = $get('ranobe_year_id');
                        $volNum = $get('volume_number');
                        $extension = $file->getClientOriginalExtension();
                        if (! $yearId || ! $volNum) {
                            return 'cover-'.Str::random(8).'.'.$extension;
                        }

                        return "y{$yearId}v{$volNum}-cover.{$extension}";
                    })
                    ->panelAspectRatio('7:10')
                    ->panelLayout('integrated'),
                FileUpload::make('cover_image_mobile')
                    ->label('Обложка тома (мобильная версия)')
                    ->image()
                    ->imageEditor()
                    ->directory(function ($get) {
                        $yearId = $get('ranobe_year_id');
                        $volNum = $get('volume_number');
                        if (! $yearId || ! $volNum) {
                            return 'ranobe/uploads-tmp';
                        }

                        return "ranobe/year-{$yearId}/volume-{$volNum}";
                    })
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get) {
                        $yearId = $get('ranobe_year_id');
                        $volNum = $get('volume_number');
                        $extension = $file->getClientOriginalExtension();
                        if (! $yearId || ! $volNum) {
                            return 'cover-'.Str::random(8).'.'.$extension;
                        }

                        return "y{$yearId}v{$volNum}-cover-mobile.{$extension}";
                    })
                    ->panelAspectRatio('7:10')
                    ->panelLayout('integrated'),
                FileUpload::make('volume_images')
                    ->label('Иллюстрации тома ')
                    ->multiple()
                    ->directory(function ($get) {
                        if (! $get) {
                            return 'ranobe/tmp';
                        }
                        $yearId = $get('ranobe_year_id');
                        $volNum = $get('volume_number');

                        return "ranobe/year-{$yearId}/volume-{$volNum}/images";
                    })
                    ->preserveFilenames()
                    ->hiddenOn('create')
                    ->reorderable()
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year.year_number')
                    ->label('Год обучения')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Обложка')
                    ->size(70)
                    ->extraImgAttributes([
                        'style' => 'aspect-ratio: 7/10; object-fit: cover; width: auto;',
                    ])
                    ->defaultImageUrl(url('/images/default-cover.webp'))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('volume_number')
                    ->label('Номер тома')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('all_chapters')
                    ->label('Всего глав')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('general_number')
                    ->label('Общ номер')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус'),
                Tables\Columns\TextColumn::make('release_date_book')
                    ->label('Дата выхода(книга)')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('release_date_digital')
                    ->label('Дата выхода(цифра)')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pages_quantity')
                    ->label('Кол-во страниц')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN'),
                Tables\Columns\TextColumn::make('promo_link')
                    ->label('Промо')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('ranobe_year_id')
                    ->label('Фильтр по году')
                    ->relationship('year', 'year_number')
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
            'index' => Pages\ListRanobeVolumes::route('/'),
            'create' => Pages\CreateRanobeVolume::route('/create'),
            'edit' => Pages\EditRanobeVolume::route('/{record}/edit'),
        ];
    }
}
