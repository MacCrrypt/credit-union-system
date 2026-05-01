<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChangeIsComplete
{
    /**
     * Admin-issued passwords are temporary, so users must replace them before working.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->mustChangePassword()
            && ! $request->routeIs('password.change.*')
            && ! $request->routeIs('logout')) {
            return redirect()->route('password.change.edit');
        }

        return $next($request);
    }
}
