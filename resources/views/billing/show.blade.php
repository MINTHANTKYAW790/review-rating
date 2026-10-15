@extends('layouts.app')

@section('title', __('messages.payment_detail'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.payment_detail')" subtitle="#{{ $payment->id }}">
            <x-slot:actions>
                <a href="{{ route('billing.payments') }}" class="btn btn-outline-secondary">{{ __('messages.back') }}</a>
            </x-slot:actions>
        </x-page-header>

        <x-form-card>
            <p class="mb-1"><strong>{{ __('messages.status') }}:</strong> {{ ucfirst($payment->status) }}</p>
            <p class="mb-1"><strong>{{ __('messages.amount') }}:</strong> {{ number_format($payment->amount_mmk) }} MMK</p>
            <p class="mb-1"><strong>{{ __('messages.transfer_method') }}:</strong> {{ strtoupper($payment->method) }}</p>
            <p class="mb-3"><strong>{{ __('messages.transaction_id') }}:</strong> {{ $payment->reference }}</p>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>{{ __('messages.item') }}</th>
                        <th>{{ __('messages.store_name') }}</th>
                        <th>{{ __('messages.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payment->items as $item)
                        <tr>
                            <td>{{ $item->type === 'new_store_slot' ? __('messages.add_new_shop') : __('messages.plan_extension') }}</td>
                            <td>{{ $item->store->name ?? '—' }}</td>
                            <td>{{ number_format($item->price_mmk) }} MMK</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($payment->admin_note)
                <p class="mb-0"><strong>{{ __('messages.admin_note') }}:</strong> {{ $payment->admin_note }}</p>
            @endif
        </x-form-card>
    </div>
</section>
@endsection
