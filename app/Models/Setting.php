<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'xui_base_url',
        'subscription_base_url',
            'xui_username',
        'xui_password',
        'xui_inbound_id',
        'price_per_gb',
        'default_expiry_days',
    ];

    protected function casts(): array
    {
        return [
            'xui_password' => 'encrypted',
            'xui_inbound_id' => 'integer',
            'price_per_gb' => 'integer',
            'default_expiry_days' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'xui_base_url' => 'https://dash.cheeta.site:2053',
            'subscription_base_url' => 'https://sub.cheeta.site:2096/sub',
            'xui_inbound_id' => 23,
            'price_per_gb' => 0,
            'default_expiry_days' => 30,
        ]);
    }
}
