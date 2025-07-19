<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $user = Auth::user();

        if (! $user->hasRole($role)) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
