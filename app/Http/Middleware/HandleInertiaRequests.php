<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $team = null;
        $alerts = null;

        if($user){
            if(config('teams.active') && $user->team_id){
                $team = Team::with(['rotations.details', 'params'])->find($user->team_id);
                if($user->isCoordinateur()){
                    $alerts = $team->alerts;
                }
            }
        }

        $teams = null;
        if(config('teams.active') && $user){
            $teams = Team::where('company_id', $user->company_id)->orderBy('name')->get();
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'team' => $team,
                'isCoordinateur' => $user ? $user->isCoordinateur() : false,
                'isResponsable' => $user ? $user->isResponsable() : false,
                'alerts' => $alerts
            ],
            'getMaxSizeFile' => $this->getMaxSizeFile(),
            'config' => config('teams'),
            'teams' => $teams,
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }


    private function getMaxSizeFile (): string
    {
        // Get upload_max_filesize from php.ini
        $maxUploadSize = ini_get('upload_max_filesize');
        $multiplier = strtoupper(substr($maxUploadSize, -1));

        if ($multiplier == 'M') {
            $maxUploadSize = (int)$maxUploadSize;
        } elseif ($multiplier == 'K') {
            $maxUploadSize = (int)$maxUploadSize / 1024;
        } elseif ($multiplier == 'G') {
            $maxUploadSize = (int)$maxUploadSize * 1024;
        } else {
            $maxUploadSize = (int)$maxUploadSize / (1024 * 1024);
        }

        return "Taille max du fichier : " . $maxUploadSize . " MB";
    }
}
