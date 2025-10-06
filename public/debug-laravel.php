<?php
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Laravel processed request!<br>";
echo "Request URI: " . $request->getRequestUri() . "<br>";
echo "Response status: " . $response->getStatusCode() . "<br>";
