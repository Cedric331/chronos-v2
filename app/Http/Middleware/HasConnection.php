<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (! $this->hasInternetConnection()) {
            return response()->json([
                'message' => 'No internet connection',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $next($request);
    }

    private function hasInternetConnection()
    {
        // Vous pouvez, par exemple, utiliser un ping ou une autre méthode pour vérifier la connexion
        $connected = @fsockopen('www.google.com', 80);
        if ($connected) {
            fclose($connected);

            return true;
        }

        return false;
    }
}
