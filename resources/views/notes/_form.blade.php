@php
    $note = $note ?? new \App\Models\StoreNote(['status' => \App\Models\StoreNote::STATUS_OPEN]);
    $storeLocked = filled(old('review_id', $note->review_id));
    $selectedStoreId = old('store_id', $note->store_id);
@endphp

<div class="form-group">
    <label for="store_id">{{ __('messages.store_name') }} <span class="text-danger">*</span></label>
    <select id="store_id" class="form-control" required @disabled($storeLocked) @if (! $storeLocked) name="store_id" @endif>
        <option value="">{{ __('messages.select_a_store') }}</option>
        @foreach ($stores as $store)
            <option value="{{ $store->id }}" @selected($selectedStoreId == $store->id)>{{ $store->name }}</option>
        @endforeach
    </select>
    @if ($storeLocked)
        <input type="hidden" name="store_id" value="{{ $selectedStoreId }}">
    @endif
    @error('store_id')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label for="title">{{ __('messages.note_title') }} <span class="text-danger">*</span></label>
    <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $note->title) }}">
    @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label for="body">{{ __('messages.note_body') }} <span class="text-danger">*</span></label>
    <textarea name="body" id="body" class="form-control" rows="6" required>{{ old('body', $note->body) }}</textarea>
    @error('body')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="category">{{ __('messages.note_category') }}</label>
        <select name="category" id="category" class="form-control">
            <option value="">{{ __('messages.note_no_category') }}</option>
            @foreach (\App\Models\StoreNote::CATEGORIES as $category)
                <option value="{{ $category }}" @selected(old('category', $note->category) === $category)>{{ __('messages.note_category_'.$category) }}</option>
            @endforeach
        </select>
        @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
        <label for="status">{{ __('messages.note_status') }} <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control" required>
            <option value="open" @selected(old('status', $note->status ?: 'open') === 'open')">{{ __('messages.note_status_open') }}</option>
            <option value="done" @selected(old('status', $note->status) === 'done')">{{ __('messages.note_status_done') }}</option>
        </select>
        @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-group">
    <label for="staff_id">{{ __('messages.linked_staff') }}</label>
    <select
        name="staff_id"
        id="staff_id"
        class="form-control js-staff-by-store"
        data-store-select="#store_id"
        data-empty-label="-"
    >
        <option value="">-</option>
        @foreach ($staffMembers as $staff)
            <option value="{{ $staff->id }}" data-store="{{ $staff->store_id }}" @selected(old('staff_id', $note->staff_id) == $staff->id)>{{ $staff->name }}</option>
        @endforeach
    </select>
    @error('staff_id')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

@if ($storeLocked)
    <input type="hidden" name="review_id" value="{{ old('review_id', $note->review_id) }}">
@endif
@error('review_id')<div class="text-danger small">{{ $message }}</div>@enderror

@include('notes.partials.staff-by-store-script')
