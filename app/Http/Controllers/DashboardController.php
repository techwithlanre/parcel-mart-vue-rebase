<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $balance = number_format(auth()->user()->balance, 2);
        return Inertia::render('Dashboard', compact('balance'));
    }
}
