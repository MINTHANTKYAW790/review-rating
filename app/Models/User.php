<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('name', 'superadmin')->exists();
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function createdUsers()
    {
        return $this->hasMany(self::class, 'created_by');
    }

    public function createdThe(User $user): bool
    {
        return (int) $user->created_by === (int) $this->id;
    }

    public function managedStores()
    {
        return $this->hasMany(Store::class, 'manager_id');
    }

    public function accessibleStores()
    {
        return Store::query()->where(function ($query) {
            $query->where('user_id', $this->id)
                ->orWhere('manager_id', $this->id);
        });
    }

    public function canAccessStore(Store $store): bool
    {
        return (int) $store->user_id === (int) $this->id
            || (int) $store->manager_id === (int) $this->id;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function storeCredits()
    {
        return $this->hasMany(StoreCredit::class);
    }

    public function unusedStoreCredits()
    {
        return $this->storeCredits()->unused();
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments()->where('status', Payment::STATUS_PENDING)->exists();
    }

    public function canOpenAnotherStore(): bool
    {
        if (! $this->stores()->exists()) {
            return true;
        }

        return $this->unusedStoreCredits()->exists();
    }
}
