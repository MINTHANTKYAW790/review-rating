@php
    $selectedStoreId = old('store_id', $assignedStoreId ?? null);
    $managerChecked = in_array('manager', old('roles', isset($user) ? $user->roles->pluck('name')->all() : []), true);
@endphp

<div class="form-group js-manager-store-group" style="{{ $managerChecked ? '' : 'display: none;' }}">
    <label for="store_id">{{ __('messages.assign_store') }}</label>
    <select name="store_id" id="store_id" class="form-control">
        <option value="">{{ __('messages.select_a_store') }}</option>
        @foreach ($stores as $store)
            <option value="{{ $store->id }}" @selected((string) $selectedStoreId === (string) $store->id)>{{ $store->name }}</option>
        @endforeach
    </select>
    <small class="form-text text-muted">{{ __('messages.manager_store_hint') }}</small>
    @error('store_id')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var storeGroup = document.querySelector('.js-manager-store-group');
        var managerInputs = document.querySelectorAll('input[name="roles[]"][value="manager"]');

        if (!storeGroup || !managerInputs.length) {
            return;
        }

        var toggleStoreField = function () {
            var checked = Array.prototype.some.call(managerInputs, function (input) {
                return input.checked;
            });
            storeGroup.style.display = checked ? '' : 'none';
        };

        managerInputs.forEach(function (input) {
            input.addEventListener('change', toggleStoreField);
        });

        toggleStoreField();
    });
</script>
@endpush
@endonce
