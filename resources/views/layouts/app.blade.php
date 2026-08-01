<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preload" href="{{ asset('fonts/VAG-Rounded/VAG-Rounded-Next-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/VAG-Rounded/VAG-Rounded-Next-Bold.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/VAG-Rounded/VAG-Rounded-Next-Heavy.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/VAG-Rounded/VAG-Rounded-Next-Medium.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Nunito/Nunito-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Nunito/Nunito-Bold.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Nunito/Nunito-Medium.woff2') }}" as="font" type="font/woff2" crossorigin>
    <title>@yield('title', 'DCOTE')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    @stack('styles')
    @stack('scripts-early')
    <meta name="description" content="@yield('description', 'DCOTE - сайт, который совмещает в себе все аспекты произведения «Добро пожаловать в класс превосходства». Википедия, новости, аниме, ранобэ, манга и не только!')">
    <meta property="og:title" content="@yield('title', 'DCOTE')">
    <meta property="og:description" content="@yield('description', 'DCOTE - сайт, который совмещает в себе все аспекты произведения «Добро пожаловать в класс превосходства». Википедия, аниме, ранобэ, манга и не только!')">
    <meta property="og:image" content="https://dcote.net/images/og-main-preview.webp">
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
</head>
<body>
    @include('partials.svg_icons')
    @include('partials.header')
    <main id="app">
        @yield('content')
    </main>
    @include('partials.footer')
    <script src="{{ asset('js/main.js') }}"></script>
    @stack('scripts')
    @if(session('success'))
        <div id="toast-success" class="toast-container">
            <h1>УСПЕХ!</h1>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div id="toast-error" class="toast-container">
            <h1>ОШИБКА!</h1>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->has('cf-turnstile-response'))
        <div id="toast-error" class="toast-container">
            <h1>ОШИБКА!</h1>
            <p>{{ $errors->first('cf-turnstile-response')}}</p>
        </div>
    @endif
    @php
        $metricsBaseUrl = (string) config('services.dcote.metrics_base_url');
    @endphp
    <script>
        window.DCOTE_SITE_METRICS = {
            contractVersion: 1,
            metricsBaseUrl: @json($metricsBaseUrl),
            userId: @json(
                auth()->check()
                    ? hash_hmac('sha256', (string) auth()->id(), (string) config('app.key'))
                    : null
            ),
            page: @json(request()->route()?->getName() ?? request()->route()?->uri() ?? request()->path()),
        };
    </script>
    <script
        defer
        data-dcote-site-presence
        src="{{ $metricsBaseUrl }}/site-presence-tracker.js"
    ></script>
</body>
</html>
