<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Exceptions\HttpResponseException;

class User extends Authenticatable implements FilamentUser,HasName
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'nickname',
        'email',
        'password',
        'avatar',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }


    public function canAccessPanel(Panel $panel): bool
    {

        $hasAccess = in_array($this->role_id, [3, 4]);

        if (!$hasAccess) {
            throw new HttpResponseException(
                redirect()
                    ->route('home')
                    ->with('error', 'Доступ ограничен. У вас нет прав для просмотра этой страницы.')
            );
        }

        return true;
    }

    public function getFilamentName(): string
    {
        return  $this->username;
    }
}