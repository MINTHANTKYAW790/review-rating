<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Review Me') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js', 'resources/css/theme.css'])
</head>
<body class="guest-body">
    <div class="guest-shell">
        <div class="guest-card">
            <div class="guest-brand mb-4">
                <img src="{{ asset('images/favicon.ico') }}" alt="" width="40" height="40" class="mb-2">
                <div class="guest-brand-title">Review Me</div>
                <div class="guest-brand-sub text-muted">Store review management</div>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
