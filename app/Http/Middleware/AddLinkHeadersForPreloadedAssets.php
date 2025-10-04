<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddLinkHeadersForPreloadedAssets
{
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass middleware - langsung return next
        return $next($request);
    }
}
