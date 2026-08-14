<?php

namespace App\Http\Middleware;

use App\Models\Spoke;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSpoke
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Missing bearer token.'], 401);
        }

        $spoke = Spoke::query()
            ->where('api_token', $token)
            ->where('is_active', true)
            ->first();

        if (! $spoke) {
            return response()->json(['message' => 'Invalid spoke token.'], 401);
        }

        $request->attributes->set('spoke', $spoke);

        return $next($request);
    }
}
