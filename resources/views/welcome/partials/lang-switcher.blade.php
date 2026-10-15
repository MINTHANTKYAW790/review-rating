@php
    $currentLocale = app()->getLocale();
@endphp
<nav class="lang-switcher" aria-label="{{ __('messages.welcome_lang_label') }}">
    <a href="{{ route('language.switch', 'en') }}"
       class="lang-switcher-btn{{ $currentLocale === 'en' ? ' is-active' : '' }}"
       @if ($currentLocale === 'en') aria-current="true" @endif
       hreflang="en"
       title="{{ __('messages.english') }}">
        <svg class="lang-flag" viewBox="0 0 16 12" width="20" height="15" aria-hidden="true" focusable="false">
            <rect width="16" height="12" fill="#B22234"/>
            <rect y="1" width="16" height="1.3" fill="#fff"/>
            <rect y="3.6" width="16" height="1.3" fill="#fff"/>
            <rect y="6.2" width="16" height="1.3" fill="#fff"/>
            <rect y="8.8" width="16" height="1.3" fill="#fff"/>
            <rect width="7" height="6.5" fill="#3C3B6E"/>
        </svg>
        <span>EN</span>
    </a>
    <a href="{{ route('language.switch', 'my') }}"
       class="lang-switcher-btn{{ $currentLocale === 'my' ? ' is-active' : '' }}"
       @if ($currentLocale === 'my') aria-current="true" @endif
       hreflang="my"
       title="{{ __('messages.burmese') }}">
        <svg class="lang-flag" viewBox="0 0 16 12" width="20" height="15" aria-hidden="true" focusable="false">
            <rect width="16" height="4" y="0" fill="#FECB00"/>
            <rect width="16" height="4" y="4" fill="#34B233"/>
            <rect width="16" height="4" y="8" fill="#EA2839"/>
            <polygon fill="#fff" points="8,2.4 8.7,4.5 10.9,4.5 9.1,5.8 9.8,7.9 8,6.6 6.2,7.9 6.9,5.8 5.1,4.5 7.3,4.5"/>
        </svg>
        <span>မြန်မာ</span>
    </a>
</nav>
