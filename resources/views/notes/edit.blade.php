@extends('layouts.app')

@section('title', __('messages.edit_note'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.edit_note')" />

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <x-form-card>
                    <form method="POST" action="{{ route('notes.update', $note) }}">
                        @csrf
                        @method('PUT')
                        @include('notes._form')
                        <div class="form-group text-right mb-0 mt-4">
                            <a href="{{ route('notes.show', $note) }}" class="btn btn-outline-secondary mr-2">{{ __('messages.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                        </div>
                    </form>
                </x-form-card>
            </div>
        </div>
    </div>
</section>
@endsection
