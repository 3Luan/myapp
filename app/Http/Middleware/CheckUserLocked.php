<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserLocked
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_locked) {
            return response()->json(['message' => 'Your account is locked.'], 403);
        }
        return $next($request);
    }
}
