<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is locked
        if ($user->is_locked) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akun Anda telah dinonaktifkan atau ditutup.'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda telah dinonaktifkan atau ditutup.']);
        }

        // Superadmin is allowed to access any team context or global management
        if ($user->isSuperAdmin()) {
            $route = $request->route();
            if ($route && $route->hasParameter('slug')) {
                $route->forgetParameter('slug');
            }
            return $next($request);
        }

        // Other users must belong to a team
        if ($user->team_id === null) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akun Anda tidak terikat dengan tim futsal manapun.'], 403);
            }
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda tidak terikat dengan tim futsal manapun.']);
        }

        // Check if team free trial has expired
        if ($user->team && $user->team->isFreeExpired()) {
            $route = $request->route();
            if ($route) {
                $routeName = $route->getName();
                $allowedRoutes = [
                    'dashboard',
                    'subscription.upgrade',
                    'subscription.submit',
                    'subscription.payment',
                    'logout',
                    'settings.profile',
                    'settings.profile.update',
                    'settings.profile.close',
                ];
                if (!in_array($routeName, $allowedRoutes)) {
                    if ($request->is('api/*') || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Masa penggunaan gratis tim Anda telah habis. Silakan upgrade ke Premium.'
                        ], 403);
                    }
                    return redirect()->route('dashboard')->with('error', 'Masa penggunaan gratis tim Anda telah habis. Silakan hubungi manager Anda atau upgrade ke Premium.');
                }
            }
        }

        // Validate slug if present in the route
        $route = $request->route();
        if ($route && $route->hasParameter('slug')) {
            $slug = $route->parameter('slug');
            $correctSlug = $user->slug ?? 'user';
            if ($slug !== $correctSlug) {
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized slug.'], 403);
                }
                abort(403, 'Unauthorized slug.');
            }
            $route->forgetParameter('slug');
        }

        return $next($request);
    }
}
