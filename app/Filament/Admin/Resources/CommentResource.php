<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Комментарии';

    protected static ?string $modelLabel = 'комментарий';

    protected static ?string $pluralModelLabel = 'Комментарии';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Textarea::make('content')
                        ->label('Текст')
                        ->required()
                        ->maxLength(2000)
                        ->rows(6)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.nickname')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commentable_type')
                    ->label('Материал')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::morphLabel($state)),

                Tables\Columns\TextColumn::make('commentable_id')
                    ->label('ID материала')
                    ->sortable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Текст')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Ответ на')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('deleted_at')
                    ->label('Удалён')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commentable_type')
                    ->label('Материал')
                    ->options(self::morphOptions()),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        // Мягко удалённые тоже нужны — их показывает фильтр «Удалённые».
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function morphOptions(): array
    {
        return [
            'anime_season' => 'Аниме — сезон',
            'anime_episode' => 'Аниме — серия',
            'ranobe_volume' => 'Ранобэ — том',
            'ranobe_chapter' => 'Ранобэ — глава',
        ];
    }

    private static function morphLabel(string $type): string
    {
        return self::morphOptions()[$type] ?? $type;
    }
}
