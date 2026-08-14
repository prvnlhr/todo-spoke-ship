<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHubRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.role') !== 'hub') {
            abort(404);
        }

        return $next($request);
    }
}
