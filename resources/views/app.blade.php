<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>SuperAdmin Login</title>
    
    <!-- Manual assets loading -->
    <script type="module" crossorigin src="/super-admin/build/assets/app-DbXShL1P.js"></script>
</head>
<body>
    <div id="app" data-page="{{ json_encode($page) }}"></div>
    
    <script>
        // Fallback jika assets gagal load
        setTimeout(() => {
            if (!window.__INERTIA_APP__) {
                console.log('Reloading page...');
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
