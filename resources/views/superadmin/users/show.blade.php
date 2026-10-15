@extends('layouts.app')

@section('title', $user->name)

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="$user->name" :subtitle="$user->email">
            <x-slot:actions>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary mr-2">Back</a>
                @if (! $user->is(auth()->user()))
                    <form action="{{ route('superadmin.users.active', $user) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $user->is_active ? 'btn-outline-danger' : 'btn-primary' }}">
                            {{ $user->is_active ? 'Deactivate user' : 'Activate user' }}
                        </button>
                    </form>
                @endif
            </x-slot:actions>
        </x-page-header>

        <x-form-card title="Account" class="mb-3">
            <p class="mb-1"><strong>Status:</strong>
                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-secondary' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
            <p class="mb-0"><strong>Roles:</strong>
                @forelse ($user->roles as $role)
                    <span class="badge badge-primary mr-1">{{ $role->name }}</span>
                @empty
                    <span class="text-muted">None</span>
                @endforelse
            </p>
        </x-form-card>

        <x-form-card title="Stores">
            @if ($user->stores->isEmpty())
                <x-empty-state message="This user has no stores." icon="fa-store" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Staff</th>
                                <th>Reviews</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user->stores as $store)
                                <tr>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->staff_count }}</td>
                                    <td>{{ $store->reviews_count }}</td>
                                    <td>
                                        <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $store->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('superadmin.stores.show', $store) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-form-card>
    </div>
</section>
@endsection
