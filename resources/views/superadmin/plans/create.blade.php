@extends('layouts.app')

@section('title', 'Create plan')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Create plan" subtitle="Add a catalog plan" />

        <x-form-card>
            <form method="POST" action="{{ route('superadmin.plans.store') }}">
                @csrf
                @include('superadmin.plans._form')
                <div class="text-right">
                    <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save plan</button>
                </div>
            </form>
        </x-form-card>
    </div>
</section>
@endsection
