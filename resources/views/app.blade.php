<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @routes
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        <!-- Manual asset loading tanpa Vite -->
        <script type="module" src="/super-admin/build/assets/app-D_7II1BX.js"></script>
        <link rel="stylesheet" href="/super-admin/build/assets/app-BHSs9Ase.css">
    </body>
</html>
