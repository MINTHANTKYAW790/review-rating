@extends('layouts.app')

@section('title', 'Stores')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="All stores" subtitle="Every shop, owner, and status" />

        <x-form-card>
            @if ($stores->isEmpty())
                <x-empty-state message="No stores found." icon="fa-store" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Owner</th>
                                <th>Staff</th>
                                <th>Reviews</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $store)
                                <tr>
                                    <td>{{ $store->name }}</td>
                                    <td>
                                        @if ($store->user)
                                            <a href="{{ route('superadmin.users.show', $store->user) }}">{{ $store->user->name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
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
