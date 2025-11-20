<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdminOrHasPermission
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasPermissionTo('access-admin')) {
            return redirect()->route('planning');
        }

        return $next($request);
    }
}
