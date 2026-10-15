@extends('layouts.app')

@section('title', __('messages.note_details'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="$note->title">
            <x-slot:actions>
                <a href="{{ route('notes.index') }}" class="btn btn-outline-secondary mr-2">{{ __('messages.back') }}</a>
                @can('update', $note)
                    <a href="{{ route('notes.edit', $note) }}" class="btn btn-primary mr-2">{{ __('messages.edit') }}</a>
                @endcan
                @can('delete', $note)
                    <form method="POST" action="{{ route('notes.destroy', $note) }}" class="d-inline" onsubmit="return confirm(@json(__('messages.confirm_delete_note')))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">{{ __('messages.delete') }}</button>
                    </form>
                @endcan
            </x-slot:actions>
        </x-page-header>

        <x-form-card :title="__('messages.note_details')">
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-1">{{ __('messages.store_name') }}</label>
                    <div>{{ $note->store?->name ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-1">{{ __('messages.note_status') }}</label>
                    <span class="badge {{ $note->status === 'open' ? 'badge-info' : 'badge-success' }}">
                        {{ __('messages.note_status_'.$note->status) }}
                    </span>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-1">{{ __('messages.note_category') }}</label>
                    <div>{{ $note->category ? __('messages.note_category_'.$note->category) : __('messages.note_no_category') }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-1">{{ __('messages.linked_staff') }}</label>
                    <div>{{ $note->staff?->name ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-1">{{ __('messages.linked_review') }}</label>
                    <div>
                        @if ($note->review)
                            <a href="{{ route('review.show', $note->review) }}">#{{ $note->review->id }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-1">{{ __('messages.status') }}</label>
                    <div>{{ $note->author?->name }} · {{ $note->created_at?->format('d M Y h:i A') }}</div>
                </div>
            </div>

            <div class="card card-light">
                <div class="card-body whitespace-pre-wrap">{{ $note->body }}</div>
            </div>
        </x-form-card>
    </div>
</section>
@endsection
