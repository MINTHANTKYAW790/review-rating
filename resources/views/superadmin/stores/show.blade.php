@extends('layouts.app')

@section('title', $store->name)

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="$store->name" :subtitle="'Owner: '.($store->user?->name ?? 'Unknown')">
            <x-slot:actions>
                <a href="{{ route('superadmin.stores.index') }}" class="btn btn-outline-secondary mr-2">All stores</a>
                <form action="{{ route('superadmin.stores.active', $store) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $store->is_active ? 'btn-outline-danger' : 'btn-primary' }}">
                        {{ $store->is_active ? 'Deactivate store' : 'Activate store' }}
                    </button>
                </form>
            </x-slot:actions>
        </x-page-header>

        <x-form-card title="Staff" class="mb-3">
            @if ($store->staff->isEmpty())
                <x-empty-state message="No staff for this store." icon="fa-user" />
            @else
                <div class="row">
                    @foreach ($store->staff as $member)
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="border rounded p-3 text-center h-100">
                                <img src="{{ $member->image_url ? asset('storage/'.$member->image_url) : asset('default-user.jpg') }}"
                                    alt="{{ $member->name }}" class="rounded-circle mb-2" width="64" height="64" style="object-fit: cover;">
                                <div class="font-weight-bold">{{ $member->name }}</div>
                                <small class="text-muted">{{ $member->role ?: 'Staff' }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-form-card>

        <x-form-card title="Recent reviews">
            @if ($reviews->isEmpty())
                <x-empty-state message="No reviews for this store." icon="fa-star" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Store rating</th>
                                <th>Staff</th>
                                <th>Staff rating</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $review)
                                <tr>
                                    <td>{{ $review->created_at?->format('d M Y H:i') }}</td>
                                    <td>{{ $review->rating_store }}/5</td>
                                    <td>{{ $review->staff?->name ?? '-' }}</td>
                                    <td>{{ $review->rating_staff ? $review->rating_staff.'/5' : '-' }}</td>
                                    <td>{{ $review->comment ?: '—' }}</td>
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