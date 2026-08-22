<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpokeRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.role') !== 'spoke') {
            abort(404);
        }

        return $next($request);
    }
}
