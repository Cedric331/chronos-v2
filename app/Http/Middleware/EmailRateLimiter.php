<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EmailRateLimiter
{
    /**
     * Limite le nombre d'emails envoyés par heure
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si la limitation est activée
        if (! config('mail.rate_limiting.enabled', false)) {
            return $next($request);
        }

        // Obtenir la limite d'emails par heure
        $limit = config('mail.rate_limiting.per_hour', 150);

        // Clé de cache pour le compteur d'emails
        $cacheKey = 'email_rate_limiter_'.date('Y-m-d_H');

        // Obtenir le compteur actuel
        $count = Cache::get($cacheKey, 0);

        // Si le compteur dépasse la limite, bloquer l'envoi
        if ($count >= $limit) {
            Log::warning("Limite d'envoi d'emails atteinte: {$limit} emails par heure");

            // Rediriger ou retourner une erreur selon le contexte
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Limite d'envoi d'emails atteinte. Veuillez réessayer plus tard.",
                ], 429);
            }

            return redirect()->back()->with('error', "Limite d'envoi d'emails atteinte. Veuillez réessayer plus tard.");
        }

        // Incrémenter le compteur
        Cache::put($cacheKey, $count + 1, 3600);

        return $next($request);
    }
}
