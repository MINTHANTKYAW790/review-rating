@extends('layouts.app')

@section('title', 'Edit plan')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Edit plan" :subtitle="$plan->name" />

        <x-form-card>
            <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}">
                @csrf
                @method('PUT')
                @include('superadmin.plans._form', ['plan' => $plan])
                <div class="text-right">
                    <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update plan</button>
                </div>
            </form>
        </x-form-card>
    </div>
</section>
@endsection
