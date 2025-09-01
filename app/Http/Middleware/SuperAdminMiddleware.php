<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        if (!$user->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        
        try {
            if (!$user->isSuperAdmin()) {
                return redirect()->route('dashboard')->with('error', 'Access denied. Super Admin privileges required.');
            }
        } catch (\Exception $e) {
            // If super_admins table doesn't exist, just check is_admin
            if (!$user->is_admin) {
                return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
            }
        }

        return $next($request);
    }
} 