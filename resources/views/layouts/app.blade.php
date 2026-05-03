<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preload" href="{{ asset('fonts/VAG Rounded Next Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/VAG Rounded Next Heavy.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/VAG Rounded Next Medium.woff2') }}" as="font" type="font/woff2" crossorigin>
    <title>@yield('title', 'DCOTE')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    @stack('styles')
    <meta name="description" content="@yield('description', 'DCOTE - сайт, который совмещает в себе все аспекты произведения "Добро пожаловать в класс превосходства". Википедия, аниме, ранобэ, манга и не только!')">
    <meta property="og:title" content="@yield('title', 'DCOTE')">
    <meta property="og:description" content="@yield('description', 'DCOTE - сайт, который совмещает в себе все аспекты произведения "Добро пожаловать в класс превосходства". Википедия, аниме, ранобэ, манга и не только!')">
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
    @stack('scripts')
</body>
<script src="{{ asset('js/main.js') }}"></script>
</html>