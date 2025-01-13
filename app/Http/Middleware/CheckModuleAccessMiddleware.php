<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Extract the module slug from the route name
        $routeName = $request->route()->getName(); // e.g., "reports.purchase-invoices"
        $slug = str_replace('reports.', '', $routeName);

        // Check if the user has access to this module
        if (!$user->modules->contains('slug', $slug)) {
            return abort(403, 'Unauthorized access to this module');
        }

        return $next($request);
    }
}
