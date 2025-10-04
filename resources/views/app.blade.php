<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @inertiaHead
        
        <!-- Manual asset loading tanpa Vite untuk production -->
        @if(app()->environment('production'))
            <script type="module" src="/super-admin/build/assets/app-D_7II1BX.js"></script>
            <link rel="stylesheet" href="/super-admin/build/assets/app-BHSs9Ase.css">
        @else
            @viteReactRefresh
            @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
