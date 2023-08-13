<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index()
    {
        $code = \request()->user()->ref_code;
        return Inertia::render('Invite/Invite', compact('code'));
    }
}
