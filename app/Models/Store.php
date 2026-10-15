<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Store extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'manager_id',
        'name',
        'address',
        'phone',
        'email',
        'description',
        'logo_url',
        'logo_image',
        'qr_identifier',
        'slug',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'email', 'address', 'description', 'logo_url', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    protected static function booted()
    {
        static::saving(function ($store) {
            $store->slug = Str::slug($store->name);
        });
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notes()
    {
        return $this->hasMany(StoreNote::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(StoreSubscription::class);
    }

    public function currentSubscription(): ?StoreSubscription
    {
        return $this->subscriptions()
            ->with('plan')
            ->where('status', StoreSubscription::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('id')
            ->first();
    }

    public function effectivePlan(): ?Plan
    {
        return $this->currentSubscription()?->plan;
    }

    public function hasStaffCapacity(): bool
    {
        $plan = $this->effectivePlan();

        if (! $plan) {
            return true;
        }

        return $this->staff()->count() < $plan->max_staff;
    }

    public function allowsManagers(): bool
    {
        $plan = $this->effectivePlan();

        if (! $plan) {
            return true;
        }

        return $plan->max_managers > 0;
    }

    public function hasReachedReviewCap(): bool
    {
        $plan = $this->effectivePlan();

        if (! $plan || $plan->hasUnlimitedReviews()) {
            return false;
        }

        return $this->reviews()->count() >= $plan->max_reviews;
    }

    public function isOwnersFirstStore(): bool
    {
        $firstId = static::query()
            ->where('user_id', $this->user_id)
            ->orderBy('id')
            ->value('id');

        return (int) $firstId === (int) $this->id;
    }
}
