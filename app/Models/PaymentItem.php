<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    public const TYPE_STORE_RENEWAL = 'store_renewal';

    public const TYPE_NEW_STORE_SLOT = 'new_store_slot';

    protected $fillable = [
        'payment_id',
        'type',
        'store_id',
        'plan_id',
        'price_mmk',
        'duration_days',
    ];

    protected function casts(): array
    {
        return [
            'price_mmk' => 'integer',
            'duration_days' => 'integer',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
