@props([
    'message',
    'icon' => 'fa-inbox',
])

<div {{ $attributes->merge(['class' => 'empty-state text-center py-5 px-3']) }}>
    <div class="empty-state-icon mb-3">
        <i class="fas {{ $icon }}"></i>
    </div>
    <p class="empty-state-message mb-0">{{ $message }}</p>
    @isset($actions)
        <div class="mt-3">
            {{ $actions }}
        </div>
    @endisset
</div>
