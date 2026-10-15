@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Subscriptions" subtitle="Current plan for every store" />

        <x-form-card>
            <form method="GET" class="form-inline mb-3">
                <input type="text" name="owner" class="form-control mr-2" placeholder="Owner name or email" value="{{ request('owner') }}">
                <div class="custom-control custom-checkbox mr-2">
                    <input type="checkbox" class="custom-control-input" name="expiring" id="expiring" value="1" @checked($expiring) onchange="this.form.submit()">
                    <label class="custom-control-label" for="expiring">Expiring in 7 days</label>
                </div>
                <button class="btn btn-outline-primary">Filter</button>
            </form>

            @if ($stores->isEmpty())
                <x-empty-state message="No stores found." icon="fa-receipt" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Owner</th>
                                <th>Plan</th>
                                <th>Ends</th>
                                <th>Store status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $store)
                                @php $subscription = $store->current_subscription; @endphp
                                <tr>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->user->name }}</td>
                                    <td>{{ $subscription?->plan?->name ?? 'None' }}</td>
                                    <td>{{ $subscription?->ends_at?->format('Y-m-d') ?? ($subscription ? 'No expiry' : '—') }}</td>
                                    <td>
                                        <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $store->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $stores->links() }}</div>
            @endif
        </x-form-card>
    </div>
</section>
@endsection
