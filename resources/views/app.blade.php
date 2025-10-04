<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>SuperAdmin Login</title>
    
    <!-- Manual assets loading dari manifest -->
    <script type="module" crossorigin src="/build/assets/app-C1h9YofH.js"></script>
    <link rel="stylesheet" href="/build/assets/app-DIVbgFUH.css">
</head>
<body>
    <div id="app" data-page="{{ json_encode(\$page) }}"></div>
    
    <script>
        console.log('App loaded, checking Inertia...');
        console.log('Page data:', {{ json_encode(\$page) }});
        
        // Fallback jika assets gagal load
        setTimeout(() => {
            if (!window.__INERTIA_APP__) {
                console.log('Inertia app not found, checking assets...');
                // Check if the asset file exists
                fetch('/build/assets/app-C1h9YofH.js')
                    .then(response => {
                        if (!response.ok) {
                            console.log('Asset file not found, reloading...');
                            window.location.reload();
                        } else {
                            console.log('Asset file exists, but Inertia not loaded');
                        }
                    })
                    .catch(() => {
                        console.log('Asset file check failed, reloading...');
                        window.location.reload();
                    });
            } else {
                console.log('Inertia app loaded successfully');
            }
        }, 3000);
    </script>
</body>
</html>
