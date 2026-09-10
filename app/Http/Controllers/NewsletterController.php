<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterRequest;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function store(StoreNewsletterRequest $request)
    {
        NewsletterSubscriber::updateOrCreate(
            ['email' => $request->validated('email')],
            ['is_active' => true, 'subscribed_at' => now()]
        );

        return back()->with('status', 'Merci pour votre inscription à la newsletter !');
    }
}
