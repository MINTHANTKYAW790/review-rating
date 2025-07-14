@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
@section('content')
<div class="row">
    <!-- Overall Store Rating -->
    <div class="col-md-3">
        <x-adminlte-info-box title="Overall Store Rating" text="{{ number_format($storeRating, 1) }} out of 5" icon="fas fa-star" theme="warning" />
    </div>

    <!-- Total Reviews -->
    <div class="col-md-3">
        <x-adminlte-info-box title="Total Reviews Received" text="{{ $totalReviews }}" icon="fas fa-comments" theme="primary" />
    </div>

    <!-- New Reviews This Week -->
    <div class="col-md-3">
        <x-adminlte-info-box title="New Reviews This Week" text="{{ $newReviews }}" icon="fas fa-chart-line" theme="success" />
    </div>

    <!-- Top Rated Staff -->
    <div class="col-md-3">
        <x-adminlte-info-box title="Top Rated Staff" text="{{ $topStaff->name ?? 'N/A' }} ({{ number_format($topStaff->reviews_avg_rating ?? 0, 1) }})" icon="fas fa-crown" theme="purple" />
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-4">
    <x-adminlte-button label="Edit Store Profile" theme="primary" icon="fas fa-edit" class="mr-2" />
    <x-adminlte-button label="Add New Staff" theme="success" icon="fas fa-user-plus" class="mr-2" />
    <x-adminlte-button label="Generate QR Code" theme="warning" icon="fas fa-qrcode" class="mr-2" />
    <x-adminlte-button label="View All Reviews" theme="purple" icon="fas fa-eye" />
</div>
@stop

@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop