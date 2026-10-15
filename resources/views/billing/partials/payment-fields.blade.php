@php
    $prefix = $prefix ?? 'pay';
    $disabled = $disabled ?? false;
@endphp

<div class="form-group">
    <label for="{{ $prefix }}-method">{{ __('messages.transfer_method') }}</label>
    <select name="method" id="{{ $prefix }}-method" class="form-control" required @disabled($disabled)>
        <option value="kbzpay" @selected(old('method') === 'kbzpay')>KBZ Pay</option>
        <option value="wave" @selected(old('method') === 'wave')>Wave Money</option>
    </select>
</div>
<div class="form-group">
    <label for="{{ $prefix }}-reference">{{ __('messages.transaction_id') }}</label>
    <input type="text" name="reference" id="{{ $prefix }}-reference" class="form-control" value="{{ old('reference') }}" required @disabled($disabled)>
</div>
<div class="form-group">
    <label for="{{ $prefix }}-screenshot">{{ __('messages.transfer_screenshot') }}</label>
    <input type="file" name="screenshot" id="{{ $prefix }}-screenshot" class="form-control-file" accept="image/*" required @disabled($disabled)>
</div>
<div class="form-group">
    <label for="{{ $prefix }}-note">{{ __('messages.optional_note') }}</label>
    <textarea name="owner_note" id="{{ $prefix }}-note" class="form-control" rows="2" @disabled($disabled)>{{ old('owner_note') }}</textarea>
</div>
