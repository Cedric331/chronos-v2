<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
    public function store(Request $request)
    {
        //
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
        if (!Gate::check('has-authorization')) {
           return Inertia::render('Errors/401');
        }

        $update = $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'birthday' => $request->input('birthday'),
            'phone' => $request->input('phone'),
            'team_id' => $request->input('team_id')
        ]);

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
        //
    }
}
