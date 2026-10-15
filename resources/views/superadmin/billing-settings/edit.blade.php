@extends('layouts.app')

@section('title', 'Billing settings')

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header title="Billing settings" subtitle="Numbers shown on the owner checkout page" />

        <x-form-card>
            <form method="POST" action="{{ route('superadmin.billing-settings.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="account_name">Account name</label>
                    <input type="text" name="account_name" id="account_name" class="form-control" value="{{ old('account_name', $settings->account_name) }}">
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="kbz_number">KBZ Pay number</label>
                        <input type="text" name="kbz_number" id="kbz_number" class="form-control" value="{{ old('kbz_number', $settings->kbz_number) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="wave_number">Wave Money number</label>
                        <input type="text" name="wave_number" id="wave_number" class="form-control" value="{{ old('wave_number', $settings->wave_number) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="instructions">Instructions</label>
                    <textarea name="instructions" id="instructions" class="form-control" rows="4">{{ old('instructions', $settings->instructions) }}</textarea>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </form>
        </x-form-card>
    </div>
</section>
@endsection
