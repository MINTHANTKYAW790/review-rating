<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StoreCredit extends Model
{
    protected $fillable = [
        'user_id',
        'payment_id',
        'payment_item_id',
        'plan_id',
        'duration_days',
        'consumed_at',
        'consumed_store_id',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'consumed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function consumedStore()
    {
        return $this->belongsTo(Store::class, 'consumed_store_id');
    }

    public function scopeUnused(Builder $query): Builder
    {
        return $query->whereNull('consumed_at');
    }

    public function isUnused(): bool
    {
        return $this->consumed_at === null;
    }
}
