@extends('layouts.app')

@section('title', __('messages.payment_history'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.payment_history')" :subtitle="__('messages.payment_history_subtitle')" />

        @include('billing.partials.pending-alert')

        <x-form-card>
            @if ($payments->isEmpty())
                <x-empty-state :message="__('messages.no_payments_empty')" icon="fa-list-alt">
                    <x-slot:actions>
                        <a href="{{ route('billing.add-store') }}" class="btn btn-primary btn-sm mr-1">{{ __('messages.add_one_store_card') }}</a>
                        <a href="{{ route('billing.extend') }}" class="btn btn-outline-primary btn-sm">{{ __('messages.extend_stores_card') }}</a>
                    </x-slot:actions>
                </x-empty-state>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.item') }}</th>
                                <th>{{ __('messages.submitted_at') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ number_format($payment->amount_mmk) }} MMK</td>
                                    <td>
                                        <span class="badge {{ $payment->status === 'pending' ? 'badge-warning' : ($payment->status === 'approved' ? 'badge-success' : 'badge-danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        @if ($payment->status === 'pending')
                                            <div class="text-muted small">{{ __('messages.payment_waiting_review') }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $payment->items->count() }}</td>
                                    <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('billing.show', $payment) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view_payment') }}</a>
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
