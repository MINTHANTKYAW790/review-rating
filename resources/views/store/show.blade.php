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
                            <th>Phone:</th>
                            <td>{{ $store->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $store->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $store->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>QR Identifier:</th>
                            <td>{{ $store->qr_identifier }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($store->description)
                <div class="mt-4">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $store->description }}</p>
                </div>
            @endif
        </div>

        <div class="card-footer bg-white text-right">
            <a href="{{ route('store.index') }}" class="btn btn-secondary">Back to Stores</a>
        </div>
    </div>
</div>
@endsection
