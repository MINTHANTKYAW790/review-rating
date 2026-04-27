@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h4 class="mb-0">{{ $store->name }}</h4>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Store Logo -->
                @if ($store->logo_url)
                    <div class="col-md-4 mb-4">
                        <img src="{{ asset('storage/' . $store->logo_url) }}" alt="{{ $store->name }}" class="img-fluid rounded border">
                    </div>
                @endif

                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th>{{ __('messages.phone') }}</th>
                            <td>{{ $store->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('messages.email') }}</th>
                            <td>{{ $store->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('messages.address') }}</th>
                            <td>{{ $store->address ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($store->description)
                <div class="mt-4">
                    <h5>{{ __('messages.description') }}</h5>
                    <p class="text-muted">{{ $store->description }}</p>
                </div>
            @endif
        </div>

        <div class="card-footer bg-white text-right">
            <a href="{{ route('store.index') }}" class="btn btn-secondary">{{ __('messages.back_to_stores') }}</a>
        </div>
    </div>
</div>
@endsection