<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_users')->only(['index', 'show']);
        $this->middleware('permission:create_users')->only(['create', 'store']);
        $this->middleware('permission:edit_users')->only(['edit', 'update']);
        $this->middleware('permission:delete_users')->only(['destroy']);
    }

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')
            ->where('created_by', auth()->id())
            ->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'roles' => $this->assignableRoles(),
            'stores' => $this->ownedStores(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        if (auth()->user()->hasRole('admin') && collect($request->roles ?? [])->contains('superadmin')) {
            abort(403, 'You cannot assign the superadmin role.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'created_by' => auth()->id(),
        ]);

        if (! empty($request->roles)) {
            $user->syncRoles($request->roles);
        }

        $this->syncManagedStore($user, $request->integer('store_id') ?: null);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        $user->load('managedStores');

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('managedStores');
        $stores = $this->ownedStores();
        $assignedStoreId = $user->managedStores
            ->first(fn (Store $store) => $stores->contains('id', $store->id))
            ?->id;

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->assignableRoles(),
            'stores' => $stores,
            'assignedStoreId' => $assignedStoreId,
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if (auth()->user()->hasRole('admin') && collect($request->roles ?? [])->contains('superadmin')) {
            abort(403, 'You cannot assign the superadmin role.');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->password = $request->password;
            $user->save();
        }

        $user->syncRoles($request->roles ?? []);
        $this->syncManagedStore($user, $request->integer('store_id') ?: null);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    private function assignableRoles(): Collection
    {
        $names = auth()->user()?->isSuperAdmin()
            ? ['admin', 'manager']
            : ['manager'];

        return Role::query()
            ->whereIn('name', $names)
            ->orderBy('name')
            ->get();
    }

    private function ownedStores(): Collection
    {
        return auth('web')->user()->stores()->orderBy('name')->get();
    }

    private function syncManagedStore(User $user, ?int $storeId): void
    {
        $ownedStoreIds = auth('web')->user()->stores()->pluck('id');

        if ($ownedStoreIds->isEmpty()) {
            return;
        }

        if (! $user->hasRole('manager') || $storeId === null) {
            Store::query()
                ->whereIn('id', $ownedStoreIds)
                ->where('manager_id', $user->id)
                ->update(['manager_id' => null]);

            return;
        }

        Store::query()
            ->whereIn('id', $ownedStoreIds)
            ->where('manager_id', $user->id)
            ->whereKeyNot($storeId)
            ->update(['manager_id' => null]);

        Store::query()
            ->whereIn('id', $ownedStoreIds)
            ->whereKey($storeId)
            ->update(['manager_id' => $user->id]);
    }
}
