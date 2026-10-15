<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Staff extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'staff';

    protected $fillable = [
        'store_id',
        'name',
        'image_url',
        'role',
        'rating',
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['store_id', 'name', 'role', 'image_url'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notes()
    {
        return $this->hasMany(StoreNote::class);
    }
}
