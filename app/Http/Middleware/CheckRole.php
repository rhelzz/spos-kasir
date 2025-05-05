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
     */
    public function handle(Request $request, Closure $next, ...$roleParams): Response
    {
        // Convert any comma-separated roles into an array
        $roles = [];
        foreach ($roleParams as $roleParam) {
            if (strpos($roleParam, ',') !== false) {
                // Split comma-separated strings into individual roles
                $split = explode(',', $roleParam);
                $roles = array_merge($roles, $split);
            } else {
                $roles[] = $roleParam;
            }
        }

        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            // Method using direct role property comparison (more reliable)
            if ($request->user()->role === $role) {
                return $next($request);
            }
            
            // Alternatively, you can use the helper method approach:
            // $checkMethod = 'is' . ucfirst($role);
            // if (method_exists($request->user(), $checkMethod) && $request->user()->{$checkMethod}()) {
            //     return $next($request);
            // }
        }

        // If user doesn't have any of the required roles
        return response()->view('errors.unauthorized', [], 403);
        // Or use: abort(403, 'Unauthorized action.');
    }
}
