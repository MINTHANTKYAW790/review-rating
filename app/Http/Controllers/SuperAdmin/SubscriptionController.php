<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreSubscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StoreSubscription::class);

        $expiring = $request->boolean('expiring');

        $stores = Store::query()
            ->with(['user', 'subscriptions' => fn ($query) => $query->latest('id')])
            ->when($request->filled('owner'), function ($query) use ($request) {
                $query->whereHas('user', function ($owner) use ($request) {
                    $owner->where('name', 'like', '%'.$request->string('owner').'%')
                        ->orWhere('email', 'like', '%'.$request->string('owner').'%');
                });
            })
            ->when($expiring, function ($query) {
                $query->whereHas('subscriptions', function ($subscription) {
                    $subscription->where('status', StoreSubscription::STATUS_ACTIVE)
                        ->whereNotNull('ends_at')
                        ->whereBetween('ends_at', [now(), now()->addDays(7)]);
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stores->getCollection()->each(function (Store $store) {
            $store->setRelation('current_subscription', $store->currentSubscription());
        });

        return view('superadmin.subscriptions.index', compact('stores', 'expiring'));
    }
}
