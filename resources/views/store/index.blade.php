@extends('layouts.app')

@section('title', __('messages.all_stores'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.all_stores')">
            <x-slot:actions>
                @can('create', \App\Models\Store::class)
                <a href="{{ route('store.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __('messages.add_new_store') }}
                </a>
                @endcan
            </x-slot:actions>
        </x-page-header>

        <div class="row">
            @forelse ($stores as $store)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm store-card">
                    <img src="{{ $store->logo_url ? asset('storage/' . $store->logo_url) : asset('default-store.jpg') }}" class="card-img-top store-card-img" alt="{{ $store->name }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="card-title mb-0">{{ $store->name }}</h5>
                            <div class="d-flex align-items-center">
                                @php
                                    $avgRating = round($store->reviews_avg_rating_store ?? 0);
                                    $rounded = floor($avgRating);
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $rounded)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif ($i - 0.5 <= $avgRating)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                <span class="ml-2 text-muted">{{ number_format($avgRating, 1) }}/5</span>
                            </div>
                        </div>
                        <p class="card-text text-muted">{{ Str::limit($store->description, 100) }}</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('store.show', $store->id) }}" class="btn btn-outline-primary btn-sm">{{ __('messages.view_details') }}</a>
                            @can('update', $store)
                            <a href="{{ route('store.edit', $store->id) }}" class="btn btn-primary btn-sm">{{ __('messages.edit') }}</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <x-form-card>
                    <x-empty-state :message="__('messages.no_stores_found')" icon="fa-store">
                        <x-slot:actions>
                            @can('create', \App\Models\Store::class)
                            <a href="{{ route('store.create') }}" class="btn btn-primary">{{ __('messages.add_new_store') }}</a>
                            @endcan
                        </x-slot:actions>
                    </x-empty-state>
                </x-form-card>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
