<?php

namespace App\Http\Controllers;

use App\Models\CourierApiProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function rate()
    {
        $providers = CourierApiProvider::all();
        return Inertia::render('Admin/Setting/Rate', compact('providers'));
    }

    public function editRate($id)
    {
        $provider = CourierApiProvider::find($id);
        return Inertia::render('Admin/Setting/EditRate', compact('provider'));
    }

    public function updateRate($id)
    {
        $provider = CourierApiProvider::find($id);
        $provider->status = \request('status');
        $provider->profit_margin = \request('profit_margin');
        $provider->save();
        return redirect(route('setting.rate'))->with('message', 'Updated');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
