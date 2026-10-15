@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Payments" subtitle="Approve or reject owner transfers" />

        <x-form-card>
            <form method="GET" class="form-inline mb-3">
                <label class="mr-2">Status</label>
                <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="approved" @selected($status === 'approved')>Approved</option>
                    <option value="rejected" @selected($status === 'rejected')>Rejected</option>
                </select>
            </form>

            @if ($payments->isEmpty())
                <x-empty-state message="No payments found." icon="fa-file-invoice-dollar" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Owner</th>
                                <th>Amount</th>
                                <th>Items</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        {{ $payment->user->name }}
                                        <div class="text-muted small">{{ $payment->user->email }}</div>
                                    </td>
                                    <td>{{ number_format($payment->amount_mmk) }} MMK</td>
                                    <td>{{ $payment->items->count() }}</td>
                                    <td>{{ strtoupper($payment->method) }}</td>
                                    <td>
                                        <span class="badge {{ $payment->status === 'pending' ? 'badge-warning' : ($payment->status === 'approved' ? 'badge-success' : 'badge-danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('superadmin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $payments->links() }}</div>
            @endif
        </x-form-card>
    </div>
</section>
@endsection
