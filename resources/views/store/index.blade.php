@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-2 mx-2">
        <h4 class="mb-4 font-weight-bold mx-2">{{ __('messages.all_stores') }}</h4>
        <a href="{{ route('store.create') }}" class="btn btn-primary mb-3">
            + {{ __('messages.add_new_store') }}
        </a>
    </div>

    <div class="row">
        @forelse ($stores as $store)
        <div class="col-md-4 md-4 my-2">
            <div class="card h-100 shadow-sm ">
                <img src="{{ $store->logo_url ? asset('storage/' . $store->logo_url) : asset('default-store.jpg') }}" class="card-img-top" alt="{{ $store->name }}">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">    
                        <h5 class="card-title">{{ $store->name }}</h5>
                        <div class="d-flex align-items-center  mb-2">
                            <div>
                                @php
                                $avgRating = round($store->reviews_avg_rating_store ?? 0);$rounded = floor($avgRating);
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($i <=$rounded)
                                    <i class="fas fa-star text-warning"></i>
                                    @elseif($i - 0.5 <= $avgRating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                        <i class="far fa-star text-warning"></i>
                                        @endif
                                        @endfor
                            </div>
                            <span class="ms-2 text-muted  pl-2">{{ number_format($avgRating, 1) }}/5</span>
                        </div>
                    </div>
                    <p class="card-text text-muted">{{ Str::limit($store->description, 100) }}</p>
                    <div class="d-flex align-items-center justify-content-around mb-2">
                        <a href="{{ route('store.show', $store->id) }}" class="btn btn-outline-primary btn-sm">{{ __('messages.view_details') }}</a>
                        <a href="{{ route('store.edit', $store->id) }}" class="btn btn-outline-primary btn-sm">{{ __('messages.edit') }}</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">{{ __('messages.no_stores_found') }}</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
