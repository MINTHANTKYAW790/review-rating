@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2 mx-2">
        <h4 class="mb-4 font-weight-bold mx-2">All Stores</h4>
        <a href="{{ route('store.create') }}" class="btn btn-primary mb-3">
            + Add New Store
        </a>
    </div>

    <div class="row">
        @forelse ($stores as $store)
        <div class="col-md-4 md-4">
            <div class="card h-100 shadow-sm">
                @if ($store->logo_url)
                <img src="{{ asset('storage/' . $store->logo_url) }}" class="card-img-top" alt="{{ $store->name }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $store->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($store->description, 100) }}</p>
                    <a href="{{ route('store.show', $store->id) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                    <a href="{{ route('store.edit', $store->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>

                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No stores found.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection