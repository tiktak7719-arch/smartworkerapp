<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        Contact::create($request->safe()->only(['name', 'email', 'subject', 'message']));

        return back()->with('success', 'Your message has been sent! We will get back to you shortly.');
    }
}
