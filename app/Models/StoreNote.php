<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StoreNote extends Model
{
    use LogsActivity;

    public const STATUS_OPEN = 'open';

    public const STATUS_DONE = 'done';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_DONE,
    ];

    public const CATEGORIES = [
        'staff',
        'service',
        'product',
        'ops',
    ];

    protected $fillable = [
        'store_id',
        'staff_id',
        'review_id',
        'user_id',
        'title',
        'body',
        'category',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['store_id', 'staff_id', 'review_id', 'title', 'body', 'category', 'status'])
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

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
