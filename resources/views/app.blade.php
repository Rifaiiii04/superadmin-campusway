<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title inertia>SuperAdmin</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Manual Assets Loading -->
    @php
        $manifestPath = public_path('build/manifest.json');
        $jsFiles = glob(public_path('build/assets/app-*.js'));
        $cssFiles = glob(public_path('build/assets/app-*.css'));
    @endphp

    @if(file_exists($manifestPath))
        @php
            $manifest = json_decode(file_get_contents($manifestPath), true);
        @endphp
        @foreach($manifest as $key => $asset)
            @if(str_contains($key, '.css'))
                <link rel="stylesheet" href="/super-admin/{{ $asset['file'] }}" />
            @endif
        @endforeach
    @elseif(count($jsFiles) > 0)
        <!-- Fallback to actual JS files -->
        <script type="module" src="/super-admin/build/assets/{{ basename($jsFiles[0]) }}"></script>
    @else
        <!-- Emergency fallback -->
        <script>
            console.error('No build assets found');
        </script>
    @endif
</head>
<body>
    @inertia

    <!-- Inertia page data -->
    <script>
        window.page = @json($page);
    </script>
</body>
</html>
