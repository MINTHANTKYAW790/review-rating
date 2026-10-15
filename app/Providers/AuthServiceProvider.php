<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\StoreNote;
use App\Models\StoreSubscription;
use App\Models\User;
use App\Policies\PaymentPolicy;
use App\Policies\PlanPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\RolePolicy;
use App\Policies\StaffPolicy;
use App\Policies\StoreNotePolicy;
use App\Policies\StorePolicy;
use App\Policies\StoreSubscriptionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Store::class => StorePolicy::class,
        Staff::class => StaffPolicy::class,
        Review::class => ReviewPolicy::class,
        StoreNote::class => StoreNotePolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Plan::class => PlanPolicy::class,
        Payment::class => PaymentPolicy::class,
        StoreSubscription::class => StoreSubscriptionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
