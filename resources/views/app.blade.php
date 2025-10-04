<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    
    <!-- Manual CSS -->
    <link href="/super-admin/build/assets/<?php echo \File::glob(public_path('build/assets/app-*.css'))[0] ?? 'app.css'; ?>" rel="stylesheet">
    
    <title>SuperAdmin</title>
</head>
<body>
    @inertia
    
    <!-- Manual JS -->
    <script src="/super-admin/build/assets/<?php echo \File::glob(public_path('build/assets/app-*.js'))[0] ?? 'app.js'; ?>"></script>
</body>
</html>
