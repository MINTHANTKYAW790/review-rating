<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSubscription extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'store_id',
        'plan_id',
        'payment_id',
        'status',
        'starts_at',
        'ends_at',
        'price_mmk',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'price_mmk' => 'integer',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function isActiveNow(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->ends_at === null) {
            return true;
        }

        return $this->ends_at->isFuture();
    }
}
