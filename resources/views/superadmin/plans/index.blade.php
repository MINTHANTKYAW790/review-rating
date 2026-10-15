@extends('layouts.app')

@section('title', 'Plans')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Plans" subtitle="Pricing and store limits">
            <x-slot:actions>
                <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Add plan
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-form-card>
            @if ($plans->isEmpty())
                <x-empty-state message="No plans yet." icon="fa-tags" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Staff</th>
                                <th>Managers</th>
                                <th>Reviews</th>
                                <th>Status</th>
                                <th class="text-right" width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plans as $plan)
                                <tr>
                                    <td>
                                        {{ $plan->name }}
                                        <div class="text-muted small">{{ $plan->slug }}</div>
                                    </td>
                                    <td>{{ number_format($plan->price_mmk) }} MMK</td>
                                    <td>{{ $plan->duration_days ? $plan->duration_days.' days' : 'No expiry' }}</td>
                                    <td>{{ $plan->max_staff }}</td>
                                    <td>{{ $plan->max_managers }}</td>
                                    <td>{{ $plan->hasUnlimitedReviews() ? 'Unlimited' : $plan->max_reviews }}</td>
                                    <td>
                                        <span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('superadmin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @if (! $plan->isProtected())
                                            <form action="{{ route('superadmin.plans.destroy', $plan) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this plan?')">Delete</button>
                                            </form>
                                        @endif
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
