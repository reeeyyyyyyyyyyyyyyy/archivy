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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // If user doesn't have required role, redirect to appropriate dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak untuk halaman tersebut.');
        } elseif ($user->isStaff()) {
            return redirect()->route('staff.dashboard')->with('error', 'Akses ditolak untuk halaman tersebut.');
        } elseif ($user->isIntern()) {
            return redirect()->route('intern.dashboard')->with('error', 'Akses ditolak untuk halaman tersebut.');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
    }
}
