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
    {{-- Open Sans не используется на страницах, поэтому прелоадим только если выбран в читалке. --}}
    <script>
        (function () {
            try {
                var saved = JSON.parse(window.localStorage.getItem('dcote-reading-settings') || '{}');
                if (saved.fontFamily !== 'Open Sans') return;

                [
                    @json(asset('fonts/OpenSans/OpenSans-Regular.woff2')),
                    @json(asset('fonts/OpenSans/OpenSans-Bold.woff2'))
                ].forEach(function (href) {
                    var link = document.createElement('link');
                    link.rel = 'preload';
                    link.as = 'font';
                    link.type = 'font/woff2';
                    link.crossOrigin = 'anonymous';
                    link.href = href;
                    document.head.appendChild(link);
                });
            } catch (e) {}
        })();
    </script>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    @php
        $meta = $page['props']['meta'] ?? [];
        $metaTitle = $meta['title'] ?? \App\Helpers\SeoMeta::formatTitle('');
        $metaDescription = $meta['description'] ?? \App\Helpers\SeoMeta::DEFAULT_DESCRIPTION;
        $metaImage = $meta['image'] ?? \App\Helpers\SeoMeta::imageUrl(null);
        $metaType = $meta['type'] ?? 'website';
        $metaRobots = $meta['robots'] ?? 'index, follow';
        // Канонический хост берём из APP_URL, а не из запроса: иначе www/http
        // канонизируют сами себя и превращаются в дубли.
        $metaUrl = rtrim((string) config('app.url'), '/').'/'.ltrim(request()->path(), '/');
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $metaRobots }}">
    <link rel="canonical" href="{{ $metaUrl }}">
    <meta property="og:site_name" content="DCOTE">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaImage }}">
    @isset($meta['image_width'])
        <meta property="og:image:width" content="{{ $meta['image_width'] }}">
    @endisset
    @isset($meta['image_height'])
        <meta property="og:image:height" content="{{ $meta['image_height'] }}">
    @endisset
    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    @if (request()->routeIs('ranobe.chapter'))
        {{-- Анти-FOUC читалки: применяем настройки чтения до старта Vue. --}}
        <script>
            (function () {
                var root = document.documentElement;
                root.classList.add('reader-page');

                var fontMap = {
                    'Vag Rounded Next': "'Vag Rounded Next', sans-serif",
                    'Times New Roman': "'Times New Roman', Georgia, serif",
                    'Open Sans': "'Open Sans', sans-serif"
                };
                var themeMap = {
                    'Стандартная': { text: 'rgba(244, 239, 250, 1)', bg: 'rgb(14, 10, 21)' },
                    'Legacy': { text: '#e9e9e9', bg: 'rgb(7, 18, 32)' },
                    'Тёмная': { text: '#bfbfbf', bg: '#0a0a0a' },
                    'Серая': { text: '#dbdbdb', bg: '#434751' },
                    'Светлая': { text: '#212529', bg: '#f2f2f3' },
                    'Книжная': { text: '#262425', bg: '#e5cf9d' }
                };

                try {
                    var raw = window.localStorage.getItem('dcote-reading-settings');
                    var s = raw ? JSON.parse(raw) : {};
                    var mobile = window.innerWidth < 768;
                    var theme = themeMap[s.theme] || themeMap['Стандартная'];
                    var font = fontMap[s.fontFamily] || fontMap['Vag Rounded Next'];

                    root.style.setProperty('--font-size-baze', (s.fontSize || (mobile ? 16 : 18)) + 'px');
                    root.style.setProperty('--block-padding', ((s.paragraphGap || 10) / 2) + 'px');
                    root.style.setProperty('--text-line-height', String(s.lineHeight || 1.6));
                    root.style.setProperty('--text-intend', (s.indent === false) ? '0' : '0.875em');
                    root.style.setProperty('--cont-width', (s.contWidth || (mobile ? 95 : 73)) + '%');
                    root.style.setProperty('--navigation-display', (s.navigation === false) ? 'none' : 'flex');
                    root.style.setProperty('--font-family', font);
                    root.style.setProperty('--primary-text-color', theme.text);
                    root.style.setProperty('--body-bg-color', theme.bg);

                    if (window.localStorage.getItem('dcote-nav-hidden') === 'true') {
                        root.classList.add('nav-hidden');
                    }
                } catch (e) {}
            })();
        </script>
    @endif
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body>
    @include('partials.svg_icons')
    @inertia

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
