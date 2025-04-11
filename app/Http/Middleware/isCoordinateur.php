<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class isCoordinateur
{
    /**
     * @return \Inertia\Response|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->user()) {
            return redirect(RouteServiceProvider::HOME);
        }

        if (! $request->user()->isCoordinateur() && ! $request->user()->isAdmin() && ! $request->user()->hasPermissionTo('access-admin')) {
            return Inertia::render('Errors/401');
        }

        return $next($request);
    }
}
