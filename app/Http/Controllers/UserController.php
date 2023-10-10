<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\ActivationAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! Gate::check('has-role-coordinateur')) {
            return Inertia::render('Errors/401');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:255',
            'team_id' => 'required|integer',
            'role' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'birthday' => $request->input('birthday'),
            'phone' => $request->input('phone'),
            'team_id' => $request->input('team_id'),
            'password' => bcrypt(Str::random(30)),
            'company_id' => Auth::user()->company_id,
        ]);

        $activationLink = URL::temporarySignedRoute('activation', now()->addHour(48), ['email' => $user->email, 'name' => $user->name]);

        $mailData = [
            'link' => $activationLink,
            'email' => $user->email,
            'name' => $user->name,
            'title' => 'Bienvenue sur Chronos',
        ];

        Mail::to($user->email)->send(new ActivationAccount($mailData));

        activity($user->team->name)
            ->event('Enregistrement')
            ->performedOn($user)
            ->withProperties($user->getOriginal())
            ->log('L\'utilisateur ' . $user->name . ' a été créé');

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): \Illuminate\Http\JsonResponse|\Inertia\Response
    {

        if (! Gate::check('has-role-coordinateur') && Auth::id() !== $user->id) {
            return Inertia::render('Errors/401');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:255'
        ]);

        $update = $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'birthday' => $request->input('birthday'),
            'phone' => $request->input('phone'),
        ]);

        if (Auth::user()->isCoordinateur() && $request->input('team_id')) {
            $user->update([
                'team_id' => $request->input('team_id'),
            ]);
            if ($request->hasAccessAdmin) {
                $user->givePermissionTo('access-admin');
            } else {
                $user->revokePermissionTo('access-admin');
            }
        }

        if ($user->wasChanged()) {
            activity($user->team->name)
                ->event('Mise à jour')
                ->performedOn($user)
                ->withProperties($user->getOriginal())
                ->log('L\'utilisateur ' . $user->name . ' a été modifié');
        }

        if (Auth::user()->isAdmin() && $request->role) {
            if (Auth::user()->isCoordinateur() && Auth::user()->hasPermissionTo('access-admin') && $user->id === Auth::id() && $request->role !== Auth::user()->role) {
                return response()->json(['message' => 'Vous n\'avez pas les droits pour modifier votre rôle'], 401);
            }
            if ($user->id !== Auth::id()) {
                $user->syncRoles($request->role);
            }
        }

        if ($update) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'Erreur lors de l\'enregistrement'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (! Gate::check('has-role-coordinateur')) {
            return Inertia::render('Errors/401');
        }

        activity($user->team->name)
            ->event('Suppression')
            ->performedOn($user)
            ->withProperties($user->getOriginal())
            ->log('L\'utilisateur ' . $user->name . ' a été supprimé');

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé'], 200);
    }
}
