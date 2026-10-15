@extends('layouts.app')

@section('title', 'Superadmin')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Superadmin" subtitle="Accounts, stores, and recent reviews" />

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="text-primary stat-icon mb-2"><i class="fas fa-users"></i></div>
                        <h6 class="mb-1">Users</h6>
                        <h3 class="mb-0">{{ $stats['users'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="text-success stat-icon mb-2"><i class="fas fa-store"></i></div>
                        <h6 class="mb-1">Active stores</h6>
                        <h3 class="mb-0">{{ $stats['active_stores'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="text-muted stat-icon mb-2"><i class="fas fa-store-slash"></i></div>
                        <h6 class="mb-1">Inactive stores</h6>
                        <h3 class="mb-0">{{ $stats['inactive_stores'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm stat-card">
                    <div class="card-body text-center">
                        <div class="text-warning stat-icon mb-2"><i class="fas fa-star"></i></div>
                        <h6 class="mb-1">Reviews today</h6>
                        <h3 class="mb-0">{{ $stats['reviews_today'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <x-form-card title="Latest reviews">
            @if ($recentReviews->isEmpty())
                <x-empty-state message="No reviews yet." icon="fa-star" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Store</th>
                                <th>Owner</th>
                                <th>Staff</th>
                                <th>Rating</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentReviews as $review)
                                <tr>
                                    <td>{{ $review->created_at?->format('d M Y H:i') }}</td>
                                    <td>
                                        @if ($review->store)
                                            <a href="{{ route('superadmin.stores.show', $review->store) }}">{{ $review->store->name }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $review->store?->user?->name ?? '-' }}</td>
                                    <td>{{ $review->staff?->name ?? '-' }}</td>
                                    <td>{{ $review->rating_store }}/5</td>
                                    <td>{{ \Illuminate\Support\Str::limit($review->comment ?? '—', 80) }}</td>
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
