<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
{{--        @routes--}}
{{--        @viteReactRefresh--}}
{{--        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])--}}
{{--        <link rel="stylesheet" href="{{ mix('css/app.css') }}">--}}
{{--        <script src="{{ mix('js/app.js') }}" defer></script>--}}
{{--        @inertiaHead--}}
    </head>
    <body class="font-sans antialiased">
        <div id="app"></div>
    </body>
</html>
