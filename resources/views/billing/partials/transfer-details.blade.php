<x-form-card :title="__('messages.transfer_to')">
    <p class="mb-1"><strong>{{ $settings->account_name }}</strong></p>
    <p class="mb-1">KBZ Pay: {{ $settings->kbz_number ?: '—' }}</p>
    <p class="mb-1">Wave Money: {{ $settings->wave_number ?: '—' }}</p>
    @if ($settings->instructions)
        <p class="mb-0 text-muted">{{ $settings->instructions }}</p>
    @endif
</x-form-card>
