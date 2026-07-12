<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class PrefixSlugMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user) {
            $correctSlug = $user->isSuperAdmin() ? 'superadmin' : ($user->slug ?? 'user');
            
            // Set default parameter for route() helper so route('dashboard') auto-fills
            URL::defaults(['slug' => $correctSlug]);
            
            $route = $request->route();
            if ($route && $route->hasParameter('slug')) {
                $slug = $route->parameter('slug');
                
                if ($slug !== $correctSlug) {
                    if ($request->isMethod('GET')) {
                        $parameters = $route->parameters();
                        $parameters['slug'] = $correctSlug;
                        return redirect()->route($route->getName(), array_merge($parameters, $request->query()));
                    } else {
                        abort(403, 'Unauthorized slug.');
                    }
                }
                
                // Forget parameter so it doesn't get passed to controller arguments
                $route->forgetParameter('slug');
            }
        }
        
        return $next($request);
    }
}
