<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return asset('images/user-avatar.webp');
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://') || str_starts_with($this->avatar, '/')) {
            return $this->avatar;
        }

        return asset('storage/' . ltrim($this->avatar, '/'));
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
