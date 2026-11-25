<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    public function index()
    {
        return view('ebooks.index', [
            'ebooks' => Ebook::where('is_published', true)->latest()->paginate(12),
        ]);
    }

    public function show(Ebook $ebook)
    {
        abort_unless($ebook->is_published, 404);
        return view('ebooks.show', [
            'ebook' => $ebook,
            'owned' => $ebook->ownedBy(auth()->user()),
            'related' => Ebook::where('is_published', true)->where('id', '!=', $ebook->id)->take(3)->get(),
        ]);
    }

    public function read(Request $request, Ebook $ebook)
    {
        abort_unless($ebook->ownedBy($request->user()), 403, 'Please purchase this e-book first.');
        return view('ebooks.read', compact('ebook'));
    }
}
