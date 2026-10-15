<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'users' => User::count(),
            'active_stores' => Store::where('is_active', true)->count(),
            'inactive_stores' => Store::where('is_active', false)->count(),
            'reviews_today' => Review::whereDate('created_at', today())->count(),
        ];

        $recentReviews = Review::query()
            ->with(['store.user', 'staff'])
            ->latest()
            ->limit(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentReviews'));
    }

    public function users(): View
    {
        $users = User::query()
            ->with('roles')
            ->withCount('stores')
            ->latest()
            ->get();

        return view('superadmin.users.index', compact('users'));
    }

    public function showUser(User $user): View
    {
        $user->load([
            'roles',
            'stores' => fn ($query) => $query->withCount(['staff', 'reviews'])->latest(),
        ]);

        return view('superadmin.users.show', compact('user'));
    }

    public function toggleUser(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $label = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$user->name} has been {$label}.");
    }

    public function stores(): View
    {
        $stores = Store::query()
            ->with('user')
            ->withCount(['staff', 'reviews'])
            ->latest()
            ->get();

        return view('superadmin.stores.index', compact('stores'));
    }

    public function showStore(Store $store): View
    {
        $store->load(['user', 'staff']);
        $reviews = $store->reviews()->with('staff')->latest()->limit(20)->get();

        return view('superadmin.stores.show', compact('store', 'reviews'));
    }

    public function toggleStore(Store $store): RedirectResponse
    {
        $store->update(['is_active' => ! $store->is_active]);

        $label = $store->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$store->name} has been {$label}.");
    }
}
