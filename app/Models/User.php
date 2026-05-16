<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

#[Fillable(['name', 'email', 'password', 'phone', 'is_suspend', 'role', 'latitude', 'longitude', 'location_updated_at'])]
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

    public function order(): HasMany
    {
        return $this->hasMany(Orders::class, 'user_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'user_id');
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

    public function getStatusAttribute(): string
    {
        if ($this->is_suspended) {
            return 'suspended';
        }

        return $this->email_verified_at ? 'active' : 'inactive';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'  => 'Admin',
            'seller' => 'Seller',
            'user'   => 'Buyer',
            default  => ucfirst($this->role),
        };
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (filled($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }

    public function scopeFilterRole($query, ?string $role)
    {
        if (filled($role) && $role !== 'all') {
            // Mapping display value ke enum DB
            $map = [
                'buyer'  => 'user',
                'seller' => 'seller',
                'admin'  => 'admin',
            ];

            $dbRole = $map[strtolower($role)] ?? $role;
            $query->where('role', $dbRole);
        }

        return $query;
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        if (filled($status) && $status !== 'all') {
            match ($status) {
                'suspended' => $query->where('is_suspended', true),
                'active'    => $query->where('is_suspended', false)
                    ->whereNotNull('email_verified_at'),
                'inactive'  => $query->where('is_suspended', false)
                    ->whereNull('email_verified_at'),
                default     => null,
            };
        }

        return $query;
    }
}
