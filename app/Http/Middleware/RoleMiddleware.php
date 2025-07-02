<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        $user = $request->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah role user termasuk dalam roles yg diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak: role tidak sesuai.');
        }

        return $next($request);
    }

}
