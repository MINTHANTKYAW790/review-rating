<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use LogsActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const METHOD_KBZ = 'kbzpay';

    public const METHOD_WAVE = 'wave';

    protected $fillable = [
        'user_id',
        'amount_mmk',
        'status',
        'method',
        'reference',
        'screenshot_path',
        'owner_note',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_mmk' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount_mmk', 'status', 'method', 'reference', 'admin_note'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(StoreSubscription::class);
    }

    public function credits()
    {
        return $this->hasMany(StoreCredit::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
