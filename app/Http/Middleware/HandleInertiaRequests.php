<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        // BYPASS - langsung return next
        return $next($request);
    }
}
