<?php
// Direct Super Admin Access - Bypass potential routing conflicts
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SUPER ADMIN DIRECT ACCESS</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #800000 0%, #a00000 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
        }
        .logo {
            color: #800000;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .badge {
            background: #800000;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 1rem;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: left;
        }
        .url-list {
            text-align: left;
            margin: 1rem 0;
        }
        .url-list a {
            display: block;
            padding: 0.5rem;
            margin: 0.25rem 0;
            background: #e9ecef;
            border-radius: 3px;
            text-decoration: none;
            color: #800000;
        }
        .url-list a:hover {
            background: #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🎓 SUPER ADMIN</div>
        <div class="badge">DIRECT ACCESS PAGE</div>
        
        <div class="info-box">
            <strong>Application:</strong> Super Admin Campusway<br>
            <strong>Type:</strong> Laravel + Inertia + React<br>
            <strong>Issue:</strong> Conflict with Landing application<br>
            <strong>Solution:</strong> Use specific Super Admin URLs below
        </div>
        
        <h2>Super Admin Access Points</h2>
        <p>Try these specific URLs to access Super Admin:</p>
        
        <div class="url-list">
            <a href="/super-admin-system/login" target="_blank">
                🔑 <strong>/super-admin-system/login</strong> - Main Login
            </a>
            <a href="/admin-system/login" target="_blank">
                🔑 /admin-system/login - Alternative Login
            </a>
            <a href="/super-admin-test" target="_blank">
                🧪 /super-admin-test - Test Route (JSON)
            </a>
            <a href="/health-check" target="_blank">
                ❤️ /health-check - Health Check (JSON)
            </a>
            <a href="/super-admin-app-check" target="_blank">
                🔍 /super-admin-app-check - App Identification (JSON)
            </a>
        </div>
        
        <div class="info-box" style="background: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Note:</strong> If the main URLs don't work, the server might be configured 
            to route all requests to the Landing application. Contact server administrator 
            to configure proper routing for Super Admin.
        </div>
    </div>
</body>
</html>
