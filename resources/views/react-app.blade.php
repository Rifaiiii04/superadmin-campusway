<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin - React App</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @if(file_exists(public_path('build/manifest.json')))
        <!-- Vite assets -->
        <link rel="stylesheet" href="/build/assets/app-*.css">
    @else
        <style>
            body { font-family: 'Figtree', sans-serif; background: #f8fafc; margin: 0; }
            #root { padding: 20px; }
            .login-container { max-width: 400px; margin: 50px auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        </style>
    @endif
</head>
<body>
    <div id="root">
        <!-- React akan me-render di sini -->
        <div class="login-container">
            <h2>SuperAdmin React App</h2>
            <p>Loading React application...</p>
        </div>
    </div>

    @if(file_exists(public_path('build/manifest.json')))
        <script type="module" src="/build/assets/app-*.js"></script>
    @else
        <script>
            console.log('React app - build pending');
        </script>
    @endif
</body>
</html>
