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
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'team' => config('teams.active') && $request->user() && $request->user()->team_id ? Team::with(['rotations.details', 'params'])->find($request->user()->team_id) : null,
                'isCoordinateur' => $request->user() ? $request->user()->isCoordinateur() : false,
                'isResponsable' => $request->user() ? $request->user()->isResponsable() : false,
                'alerts' => $request->user() &&  config('teams.active') && $request->user()->isCoordinateur() && $request->user()->team_id ? Team::find($request->user()->team_id)->alerts : null
            ],
            'getMaxSizeFile' => $this->getMaxSizeFile(),
            'config' => config('teams'),
            'teams' => config('teams.active') ? Team::orderBy('name')->get() : null,
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
