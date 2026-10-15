@extends('layouts.app')

@section('title', __('messages.add_note'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.add_note')" />

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <x-form-card>
                    <form method="POST" action="{{ route('notes.store') }}">
                        @csrf
                        @include('notes._form')
                        <div class="form-group text-right mb-0 mt-4">
                            <a href="{{ route('notes.index') }}" class="btn btn-outline-secondary mr-2">{{ __('messages.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </x-form-card>
            </div>
        </div>
    </div>
</section>
@endsection
