<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Review extends Model
{
    use LogsActivity;

    public const STATUS_INBOX = 'inbox';

    public const STATUS_DONE = 'done';

    public const QUEUE_STATUSES = [
        self::STATUS_INBOX,
        self::STATUS_DONE,
    ];

    protected $attributes = [
        'follow_up_status' => self::STATUS_INBOX,
    ];

    protected $fillable = [
        'store_id',
        'staff_id',
        'rating_store',
        'rating_staff',
        'comment',
        'comment_staff',
        'customer_phone',
        'contact_requested',
        'image_url',
        'ip_address',
        'device_cookie',
        'read_at',
        'is_important',
        'follow_up_status',
        'admin_remark',
        'remarked_by',
        'remarked_at',
        'is_demo',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'contact_requested' => 'boolean',
            'is_important' => 'boolean',
            'remarked_at' => 'datetime',
            'is_demo' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'rating_store',
                'rating_staff',
                'comment',
                'comment_staff',
                'read_at',
                'contact_requested',
                'is_important',
                'follow_up_status',
                'admin_remark',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function remarkedBy()
    {
        return $this->belongsTo(User::class, 'remarked_by');
    }

    public function notes()
    {
        return $this->hasMany(StoreNote::class);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function isInbox(): bool
    {
        return $this->follow_up_status === self::STATUS_INBOX;
    }

    public function isDone(): bool
    {
        return $this->follow_up_status === self::STATUS_DONE;
    }
}
