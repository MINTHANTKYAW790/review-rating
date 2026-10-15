<i class="fas {{ $cue['mode'] === 'free' ? 'fa-comment-alt' : 'fa-clock' }} header-plan-cue-icon" aria-hidden="true"></i>
<span class="header-plan-cue-copy">
    <span class="header-plan-cue-label">{{ $cue['label'] }}</span>
    <span class="header-plan-cue-value">{{ $cueValue }}</span>
    @if ($cue['mode'] === 'free')
        <span class="header-plan-cue-bar" aria-hidden="true">
            <span class="header-plan-cue-bar-fill" style="width: {{ $cue['percent'] }}%"></span>
        </span>
    @endif
</span>
