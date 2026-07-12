<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if maintenance mode is active in settings
        if (\App\Models\Setting::get('maintenance_mode', '0') === '1') {
            
            // 2. Allow Superadmin to bypass maintenance completely
            if (Auth::check() && Auth::user()->isSuperAdmin()) {
                return $next($request);
            }

            // 3. Allow access to essential routes (auth, webhooks, assets, etc.)
            if (
                $request->is('login') || 
                $request->is('logout') || 
                $request->is('tripay/callback') ||
                $request->is('images/*') || 
                $request->is('css/*') || 
                $request->is('js/*')
            ) {
                return $next($request);
            }

            // 4. Return a stunning, premium custom 503 maintenance page
            return response()->view('errors.503', [], 503);
        }

        return $next($request);
    }
}
