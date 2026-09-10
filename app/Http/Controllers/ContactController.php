<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.create');
    }

    public function store(StoreContactRequest $request)
    {
        ContactMessage::create($request->validated());

        return back()->with('status', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
    }
}
