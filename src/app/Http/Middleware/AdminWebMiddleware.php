<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWebMiddleware
{
    /**
     * Verifica que el usuario esté autenticado por sesión web
     * y que su user_tag sea 'admin'. Si no, redirige al login de admin.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        if (Auth::user()->user_tag !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('error', 'No tienes permisos de administrador.');
        }

        return $next($request);
    }
}
