<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Plan extends Model
{
    use LogsActivity;

    public const SLUG_FREE = 'free';

    public const SLUG_PRO = 'store_pro';

    public const SLUG_PRO_3M = 'store_pro_3m';

    public const SLUG_PRO_6M = 'store_pro_6m';

    public const SLUG_PRO_9M = 'store_pro_9m';

    public const SLUG_PRO_1Y = 'store_pro_1y';

    public const PAID_SLUGS = [
        self::SLUG_PRO,
        self::SLUG_PRO_3M,
        self::SLUG_PRO_6M,
        self::SLUG_PRO_9M,
        self::SLUG_PRO_1Y,
    ];

    protected $fillable = [
        'slug',
        'name',
        'price_mmk',
        'duration_days',
        'max_staff',
        'max_managers',
        'max_reviews',
        'is_account_free',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_mmk' => 'integer',
            'duration_days' => 'integer',
            'max_staff' => 'integer',
            'max_managers' => 'integer',
            'max_reviews' => 'integer',
            'is_account_free' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['slug', 'name', 'price_mmk', 'duration_days', 'max_staff', 'max_managers', 'max_reviews', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function subscriptions()
    {
        return $this->hasMany(StoreSubscription::class);
    }

    public function isProtected(): bool
    {
        return in_array($this->slug, array_merge([self::SLUG_FREE], self::PAID_SLUGS), true);
    }

    public function isPaid(): bool
    {
        return in_array($this->slug, self::PAID_SLUGS, true);
    }

    public static function paid(): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->whereIn('slug', self::PAID_SLUGS)
            ->where('is_active', true)
            ->orderBy('price_mmk')
            ->get();
    }

    public function hasUnlimitedReviews(): bool
    {
        return $this->max_reviews === null;
    }

    public static function free(): ?self
    {
        return static::query()->where('slug', self::SLUG_FREE)->first();
    }

    public static function pro(): ?self
    {
        return static::query()->where('slug', self::SLUG_PRO)->where('is_active', true)->first();
    }
}
