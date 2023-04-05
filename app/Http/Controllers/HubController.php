<?php

namespace App\Http\Controllers;

use App\Models\Hub;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HubController extends Controller
{
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
    public function show(Hub $hub): \Inertia\Response
    {
        $hubWithUsers = $hub->load('users');
        return Inertia::render('Hub/Hub', [
            'hub' => $hubWithUsers
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hub $hub)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hub $hub)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hub $hub)
    {
        //
    }
}
