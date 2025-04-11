<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckExchangeModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté et a une équipe
        if (!Auth::check() || !Auth::user()->team) {
            return redirect()->route('planning');
        }

        // Récupérer l'utilisateur et son équipe
        $user = Auth::user();
        $team = $user->team;

        // Vérifier si les paramètres d'équipe existent
        if (!$team->params) {
            return redirect()->route('planning')
                ->with('error', 'Paramètres d\'\u00e9quipe introuvables. Veuillez contacter un administrateur.');
        }

        $isEnabled = filter_var($team->params->exchange_module, FILTER_VALIDATE_BOOLEAN);


        if (!$isEnabled) {
            return redirect()->route('planning')
                ->with('error', 'Le module d\'\u00e9change de planning n\'est pas activé pour votre équipe.');
        }

        return $next($request);
    }
}
