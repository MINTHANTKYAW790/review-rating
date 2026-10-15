@extends('layouts.app')

@section('title', 'Payment #'.$payment->id)

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Payment #{{ $payment->id }}" :subtitle="$payment->user->name">
            <x-slot:actions>
                <a href="{{ route('superadmin.payments.index') }}" class="btn btn-outline-secondary">Back</a>
            </x-slot:actions>
        </x-page-header>

        <div class="row">
            <div class="col-lg-7">
                <x-form-card title="Invoice">
                    <p class="mb-1"><strong>Amount:</strong> {{ number_format($payment->amount_mmk) }} MMK</p>
                    <p class="mb-1"><strong>Method:</strong> {{ strtoupper($payment->method) }}</p>
                    <p class="mb-1"><strong>Transaction ID:</strong> {{ $payment->reference }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
                    @if ($payment->owner_note)
                        <p class="mb-1"><strong>Owner note:</strong> {{ $payment->owner_note }}</p>
                    @endif
                    @if ($payment->admin_note)
                        <p class="mb-1"><strong>Admin note:</strong> {{ $payment->admin_note }}</p>
                    @endif
                    @if ($payment->reviewer)
                        <p class="mb-0 text-muted">Reviewed by {{ $payment->reviewer->name }} at {{ $payment->reviewed_at?->format('Y-m-d H:i') }}</p>
                    @endif

                    <table class="table table-sm mt-3 mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Store</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payment->items as $item)
                                <tr>
                                    <td>{{ $item->type === 'new_store_slot' ? 'New shop' : 'Plan extension' }}</td>
                                    <td>{{ $item->store->name ?? '—' }}</td>
                                    <td>{{ number_format($item->price_mmk) }} MMK</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-form-card>
            </div>
            <div class="col-lg-5">
                <x-form-card title="Screenshot">
                    <a href="{{ asset('storage/'.$payment->screenshot_path) }}" target="_blank">
                        <img src="{{ asset('storage/'.$payment->screenshot_path) }}" alt="Transfer screenshot" class="img-fluid">
                    </a>
                </x-form-card>

                @if ($payment->isPending())
                    <x-form-card title="Review">
                        <form method="POST" action="{{ route('superadmin.payments.approve', $payment) }}" class="mb-2">
                            @csrf
                            <div class="form-group">
                                <label for="admin_note">Note (optional)</label>
                                <textarea name="admin_note" id="admin_note" class="form-control" rows="2">{{ old('admin_note') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('superadmin.payments.reject', $payment) }}">
                            @csrf
                            <input type="hidden" name="admin_note" value="{{ old('admin_note') }}">
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Reject this payment?')">Reject</button>
                        </form>
                    </x-form-card>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
