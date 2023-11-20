<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateInsuranceOptionRequest;
use App\Models\InsuranceOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsuranceOptionController extends Controller
{
    public function index()
    {
        $insurance_options = InsuranceOption::all();
        return Inertia::render('Admin/InsuranceOptions/InsuranceOptionsList', compact('insurance_options'));
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
    public function store(CreateInsuranceOptionRequest $request)
    {
        InsuranceOption::create([
            'name' => $request->name,
            'description' => $request->description,
            'percentage' => $request->percentage,
        ]);

        return redirect(route('insurance.options.index'))->with('message', 'Insurance option created');
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
