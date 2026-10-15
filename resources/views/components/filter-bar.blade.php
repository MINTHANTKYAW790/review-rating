@props([])

<div {{ $attributes->merge(['class' => 'filter-bar card shadow-sm mb-3']) }}>
    <div class="card-body py-3">
        {{ $slot }}
    </div>
</div>
