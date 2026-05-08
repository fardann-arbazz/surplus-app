<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'latitude', 'longitude', 'location_updated_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'location_updated_at' => 'datetime',
        ];
    }

    public function otpTokens(): HasMany
    {
        return $this->hasMany(OtpTokens::class);
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Stores::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function getRedirectRoute(): string
    {
        return match ($this->role) {
            'admin' => route('admin.dashboard'),
            'seller' => route('seller.dashboard'),
            'user' => route('user.home'),
            default => route('user.home'),
        };
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }
}
