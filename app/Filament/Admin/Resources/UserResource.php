<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('username')
                        ->label('Тег (Username)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->regex('/^[a-z0-9_]{5,32}$/')
                        ->validationMessages([
                            'regex' => 'От 5 до 32 символов: только маленькая латиница, цифры и "_"',
                        ]),

                    Forms\Components\TextInput::make('nickname')
                        ->label('Никнейм')
                        ->required()
                        ->maxLength(64)
                        ->minLength(1),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\Select::make('role_id')
                        ->label('Роль пользователя')
                        ->relationship('role', 'name')
                        ->preload()
                        ->required(),

                    Forms\Components\FileUpload::make('avatar')
                        ->label('Аватар')
                        ->image()
                        ->disk('public')
                        ->directory('avatars')
                        ->visibility('public')
                        ->avatar()
                        ->maxSize(2048)
                        ->helperText('Сохраняется в storage/app/public/avatars. Пусто — покажется аватар по умолчанию.')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Аватар')
                    ->circular(),

                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('username')
                    ->label('Тег (Логин)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nickname')
                    ->label('Никнейм')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role.name')
                    ->label('Роль')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Разработчик' => 'danger',
                        'Редактор' => 'warning',
                        'Модератор' => 'info',
                        default => 'success',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата регистрации')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
