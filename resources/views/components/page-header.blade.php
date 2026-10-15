@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3']) }}>
    <div class="mb-2 mb-md-0">
        <h1 class="page-header-title mb-0">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-header-subtitle mb-0">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="page-header-actions d-flex align-items-center flex-wrap">
            {{ $actions }}
        </div>
    @endisset
</div>
