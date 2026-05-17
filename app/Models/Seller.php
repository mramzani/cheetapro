<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Seller extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'is_active',
        'price_per_gb',
        'xui_inbound_id',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'price_per_gb' => 'integer',
            'xui_inbound_id' => 'integer',
            'last_login_at' => 'datetime',
        ];
    }

    public function effectivePricePerGb(Setting $setting): int
    {
        return $this->price_per_gb ?? $setting->price_per_gb;
    }

    public function effectiveInboundId(Setting $setting): ?int
    {
        return $this->xui_inbound_id ?? $setting->xui_inbound_id;
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
