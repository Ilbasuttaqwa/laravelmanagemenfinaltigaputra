<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Manager adalah posisi tertinggi, bisa akses semua
        if ($role === 'manager' && !$user->isManager()) {
            abort(403, 'Unauthorized access. Manager role required.');
        }

        // Admin bisa akses route admin dan manager
        if ($role === 'admin' && !$user->isAdmin() && !$user->isManager()) {
            abort(403, 'Unauthorized access. Admin role required.');
        }

        return $next($request);
    }
}
