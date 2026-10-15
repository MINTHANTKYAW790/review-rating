@extends('layouts.app')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Details</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Roles:</strong></p>
                @forelse ($user->getRoleNames() as $roleName)
                    <span class="badge badge-primary mr-1">{{ $roleName }}</span>
                @empty
                    <span class="text-muted">No roles assigned.</span>
                @endforelse

                <p class="mt-3"><strong>{{ __('messages.managed_store') }}:</strong></p>
                @forelse ($user->managedStores as $store)
                    <span class="badge badge-secondary mr-1">{{ $store->name }}</span>
                @empty
                    <span class="text-muted">{{ __('messages.no_managed_store') }}</span>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
