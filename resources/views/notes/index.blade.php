@extends('layouts.app')

@section('title', __('messages.store_notes'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.store_notes')" :subtitle="__('messages.notes')">
            <x-slot:actions>
                <a href="{{ route('notes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __('messages.add_note') }}
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-filter-bar>
            <form method="GET" class="row align-items-end">
                <div class="col-md-6 col-xl-3">
                    <label for="store_id" class="form-label small text-uppercase text-muted fw-bold mb-2">{{ __('messages.store_name') }}</label>
                    <select id="store_id" name="store_id" class="custom-select custom-select-sm">
                        <option value="">{{ __('messages.review_all') }}</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}" @selected(request('store_id') == $store->id)>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="staff_id" class="form-label small text-uppercase text-muted fw-bold mb-2">{{ __('messages.linked_staff') }}</label>
                    <select
                        id="staff_id"
                        name="staff_id"
                        class="custom-select custom-select-sm js-staff-by-store"
                        data-store-select="#store_id"
                        data-empty-label="{{ __('messages.review_all') }}"
                        data-show-all-when-empty="1"
                        data-submit-on-store-change="1"
                        onchange="this.form.submit()"
                    >
                        <option value="">{{ __('messages.review_all') }}</option>
                        @foreach ($staffMembers as $staff)
                            <option value="{{ $staff->id }}" data-store="{{ $staff->store_id }}" @selected(request('staff_id') == $staff->id)>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="status" class="form-label small text-uppercase text-muted fw-bold mb-2">{{ __('messages.note_status') }}</label>
                    <select id="status" name="status" class="custom-select custom-select-sm" onchange="this.form.submit()">
                        <option value="">{{ __('messages.review_all') }}</option>
                        <option value="open" @selected(request('status') === 'open')">{{ __('messages.note_status_open') }}</option>
                        <option value="done" @selected(request('status') === 'done')">{{ __('messages.note_status_done') }}</option>
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="category" class="form-label small text-uppercase text-muted fw-bold mb-2">{{ __('messages.note_category') }}</label>
                    <select id="category" name="category" class="custom-select custom-select-sm" onchange="this.form.submit()">
                        <option value="">{{ __('messages.review_all') }}</option>
                        @foreach (\App\Models\StoreNote::CATEGORIES as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ __('messages.note_category_'.$category) }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </x-filter-bar>

        <x-form-card>
            @if ($notes->isEmpty())
                <x-empty-state :message="__('messages.no_notes_yet')" icon="fa-sticky-note" />
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.note_title') }}</th>
                                <th>{{ __('messages.store_name') }}</th>
                                <th>{{ __('messages.linked_staff') }}</th>
                                <th>{{ __('messages.note_category') }}</th>
                                <th>{{ __('messages.note_status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notes as $note)
                                <tr>
                                    <td>{{ $note->title }}</td>
                                    <td>{{ $note->store?->name ?? '-' }}</td>
                                    <td>{{ $note->staff?->name ?? '-' }}</td>
                                    <td>{{ $note->category ? __('messages.note_category_'.$note->category) : '-' }}</td>
                                    <td>
                                        <span class="badge {{ $note->status === 'open' ? 'badge-info' : 'badge-success' }}">
                                            {{ __('messages.note_status_'.$note->status) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('notes.show', $note) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.note_details') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $notes->links() }}</div>
            @endif
        </x-form-card>
    </div>
</section>
@endsection

@include('notes.partials.staff-by-store-script')
