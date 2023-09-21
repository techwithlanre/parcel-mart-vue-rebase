<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact/Contact');
    }

    public function send(ContactRequest $request)
    {
        $send = Mail::send('mail.contact', [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message
        ]);

        if (!$send) throw ValidationException::withMessages(['email' => 'Oops!, we could not send your request.']);
    }

}
