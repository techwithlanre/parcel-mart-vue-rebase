<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index()
    {
        $code = \request()->user()->ref_code;
        return Inertia::render('Invite/Invite', compact('code'));
    }

    public function generate()
    {
        $user = User::find(\request()->user()->id);
        $user->ref_code = Str::lower(Str::random(8));
        $user->save();
        redirect(route('invite.index'))->with('message', 'Referral code generated successfully');
    }
}
