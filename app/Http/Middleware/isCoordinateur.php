<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class isCoordinateur
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return \Inertia\Response|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->user()) {
            return redirect(RouteServiceProvider::HOME);
        }

        if (!$request->user()->isCoordinateur()) {
            return Inertia::render('Errors/401');
        }

        return $next($request);
    }
}
