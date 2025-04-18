<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('has-role-coordinateur', function () {
            return Auth::user()->isCoordinateur();
        });

        Gate::define('can-update-planning', function () {
            $team = Team::with('params')->find(Auth::user()->team_id);

            return Auth::user()->isCoordinateur() || $team->params->update_planning;
        });

        Gate::define('manage-tickets', function () {
            return Auth::user()->isAdministrateur();
        });
    }
}
