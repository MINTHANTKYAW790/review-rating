@php
    $plan = $plan ?? null;
@endphp

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="slug">Slug</label>
        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $plan->slug ?? '') }}" @disabled($plan?->isProtected())>
        @if ($plan?->isProtected())
            <small class="form-text text-muted">Built-in plans keep a fixed slug.</small>
        @endif
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="price_mmk">Price (MMK)</label>
        <input type="number" min="0" name="price_mmk" id="price_mmk" class="form-control" value="{{ old('price_mmk', $plan->price_mmk ?? 0) }}" required>
    </div>
    <div class="form-group col-md-4">
        <label for="duration_days">Duration (days)</label>
        <input type="number" min="0" name="duration_days" id="duration_days" class="form-control" value="{{ old('duration_days', $plan->duration_days ?? 0) }}" required>
    </div>
    <div class="form-group col-md-4">
        <label for="max_reviews">Max reviews</label>
        <input type="number" min="1" name="max_reviews" id="max_reviews" class="form-control" value="{{ old('max_reviews', $plan->max_reviews ?? '') }}" placeholder="Empty = unlimited">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="max_staff">Max staff</label>
        <input type="number" min="0" name="max_staff" id="max_staff" class="form-control" value="{{ old('max_staff', $plan->max_staff ?? 3) }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="max_managers">Max managers</label>
        <input type="number" min="0" name="max_managers" id="max_managers" class="form-control" value="{{ old('max_managers', $plan->max_managers ?? 0) }}" required>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true))>
        <label class="custom-control-label" for="is_active">Active</label>
    </div>
</div>

@if (! $plan?->isProtected())
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="is_account_free" id="is_account_free" value="1" @checked(old('is_account_free', $plan->is_account_free ?? false))>
            <label class="custom-control-label" for="is_account_free">Account free plan (first shop)</label>
        </div>
    </div>
@endif
