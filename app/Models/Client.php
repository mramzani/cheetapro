<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    protected $fillable = [
        'seller_id',
        'uuid',
        'email',
        'sub_id',
        'inbound_id',
        'total_gb',
        'total_bytes',
        'used_traffic_bytes',
        'remaining_traffic_bytes',
        'expiry_time',
        'xui_expiry_time',
        'remaining_seconds',
        'tg_id',
        'comment',
        'cost',
        'status',
        'config_link',
        'subscription_link',
        'xui_response',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'total_gb' => 'integer',
            'total_bytes' => 'integer',
            'used_traffic_bytes' => 'integer',
            'remaining_traffic_bytes' => 'integer',
            'expiry_time' => 'integer',
            'xui_expiry_time' => 'integer',
            'remaining_seconds' => 'integer',
            'cost' => 'integer',
            'xui_response' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function remainingTrafficLabel(): string
    {
        if ($this->remaining_traffic_bytes === null) {
            return 'نامشخص';
        }

        if ($this->remaining_traffic_bytes <= 0) {
            return '0 GB';
        }

        return number_format($this->remaining_traffic_bytes / 1024 / 1024 / 1024, 2).' GB';
    }

    public function remainingTimeLabel(): string
    {
        if ($this->remaining_seconds === null) {
            return 'نامشخص';
        }

        if ($this->remaining_seconds <= 0) {
            return 'منقضی';
        }

        $days = intdiv($this->remaining_seconds, 86400);
        $hours = intdiv($this->remaining_seconds % 86400, 3600);

        return $days > 0 ? $days.' روز و '.$hours.' ساعت' : $hours.' ساعت';
    }
}
