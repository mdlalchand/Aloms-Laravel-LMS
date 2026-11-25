<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('home.about');
    }

    public function contact()
    {
        return view('home.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'message' => 'required|max:2000',
        ]);

        // In production: Mail::to(Setting::get('contact_email'))->send(new ContactMail(...));
        logger()->info('Contact message', $request->only('name', 'email', 'message'));

        return back()->with('success', 'Your message has been sent. We will reply soon!');
    }
}
