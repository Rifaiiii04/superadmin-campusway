<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>SuperAdmin Login</title>
    
    <!-- Manual assets loading dengan file yang benar -->
    <script type="module" crossorigin src="/build/assets/app-BWyxNqfQ.js"></script>
    <link rel="stylesheet" href="/build/assets/app-DIVbgFUH.css">
</head>
<body>
    <div id="app" data-page="{{ json_encode($page) }}"></div>
    
    <script>
        // Fallback jika assets gagal load
        setTimeout(() => {
            if (!window.__INERTIA_APP__) {
                console.log('Assets failed to load, checking...');
                // Check if the asset file exists
                fetch('/build/assets/app-BWyxNqfQ.js')
                    .then(response => {
                        if (!response.ok) {
                            console.log('Asset file not found, reloading...');
                            window.location.reload();
                        }
                    })
                    .catch(() => {
                        console.log('Asset file check failed, reloading...');
                        window.location.reload();
                    });
            }
        }, 2000);
    </script>
</body>
</html>
