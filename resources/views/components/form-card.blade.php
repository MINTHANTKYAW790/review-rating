@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'card form-card shadow-sm']) }}>
    @if ($title)
        <div class="card-header">
            <h3 class="card-title mb-0">{{ $title }}</h3>
            @isset($headerActions)
                <div class="card-tools">
                    {{ $headerActions }}
                </div>
            @endisset
        </div>
    @elseif (isset($headerActions))
        <div class="card-header d-flex justify-content-end">
            {{ $headerActions }}
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
