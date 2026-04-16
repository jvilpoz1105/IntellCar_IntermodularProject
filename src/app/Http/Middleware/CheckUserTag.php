<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserTag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $tag
     */
    public function handle(Request $request, Closure $next, string $tag): Response
    {
        if (!$request->user() || $request->user()->user_tag !== $tag) {
            return response()->json([
                'message' => 'Acceso denegado. Se requiere el rol: ' . $tag
            ], 403);
        }

        return $next($request);
    }
}
