<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class AccountActivationController extends Controller
{
    public function __construct()
    {
        $this->middleware('signed');
    }

    public function activate(Request $request): \Inertia\Response
    {
        $name = $request->input('name');
        $email = $request->input('email');

        $urlSigned = URL::signedRoute('active.account');

        return Inertia::render('Auth/ActivateAccount', ['urlSigned' => $urlSigned, 'name' => $name, 'email' => $email]);
    }

    public function activeAccount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->input('email'))->firstOrFail();

        $user->update([
            'account_active' => true,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
