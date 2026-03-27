<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel Realtime') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>

        <!-- Scripts -->
        <script>
            (function () {
                try {
                    var isAuthPublicRoute = window.location.pathname === '/login' || window.location.pathname === '/registro' || window.location.pathname === '/registro/verificar';
                    var savedMode = localStorage.getItem('theme-mode');
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    var useDark = isAuthPublicRoute ? false : (savedMode ? savedMode === 'dark' : prefersDark);
                    document.documentElement.classList.toggle('dark', useDark);
                } catch (e) {
                    // noop
                }
            })();
        </script>
        <meta name="realtime-enabled" content="{{ config('broadcasting.default') === 'pusher' && !empty(config('broadcasting.connections.pusher.key')) ? '1' : '0' }}">
        <meta name="realtime-key" content="{{ config('broadcasting.connections.pusher.key') }}">
        <meta name="realtime-cluster" content="{{ env('PUSHER_APP_CLUSTER', 'mt1') }}">
        <meta name="realtime-host" content="{{ env('PUSHER_HOST') }}">
        <meta name="realtime-port" content="{{ (int) env('PUSHER_PORT', 443) }}">
        <meta name="realtime-scheme" content="{{ env('PUSHER_SCHEME', 'https') }}">
        <script>
            var realtimeMeta = function (name, fallback) {
                var el = document.querySelector('meta[name="' + name + '"]');
                return el && el.content !== '' ? el.content : fallback;
            };

            window.__realtime = {
                enabled: realtimeMeta('realtime-enabled', '0') === '1',
                key: realtimeMeta('realtime-key', ''),
                cluster: realtimeMeta('realtime-cluster', 'mt1'),
                host: realtimeMeta('realtime-host', ''),
                port: Number(realtimeMeta('realtime-port', '443')),
                scheme: realtimeMeta('realtime-scheme', 'https'),
            };
        </script>
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="bg-surface text-on-surface antialiased {{ Auth::check() && Auth::user()->nutritionistProfile?->theme_color && Auth::user()->nutritionistProfile->theme_color !== 'emerald' ? 'theme-' . Auth::user()->nutritionistProfile->theme_color : '' }}">
        @inertia
    </body>
</html>
