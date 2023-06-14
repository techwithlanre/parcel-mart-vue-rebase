<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact/Contact');
    }

    public function send(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $message = $request->message;
        Mail::send('mail.contact', [
            'name' => $name,
            'email' => $email,
            'message' => $message ]
        );
    }
}
