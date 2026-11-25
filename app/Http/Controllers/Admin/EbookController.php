<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    public function index()
    {
        return view('admin.ebooks.index', ['ebooks' => Ebook::withCount('purchases')->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.ebooks.form', ['ebook' => new Ebook(['is_published' => true])]);
    }

    public function store(Request $request)
    {
        Ebook::create($this->validated($request));
        return redirect()->route('admin.ebooks.index')->with('success', 'E-book created.');
    }

    public function edit(Ebook $ebook)
    {
        return view('admin.ebooks.form', compact('ebook'));
    }

    public function update(Request $request, Ebook $ebook)
    {
        $ebook->update($this->validated($request, $ebook));
        return redirect()->route('admin.ebooks.index')->with('success', 'E-book updated.');
    }

    public function destroy(Ebook $ebook)
    {
        $ebook->delete();
        return back()->with('success', 'E-book deleted.');
    }

    private function validated(Request $request, ?Ebook $ebook = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'pages' => 'nullable|integer|min:0',
            'cover' => 'nullable|image|max:4096',
            'file' => 'nullable|file|mimes:pdf,epub|max:51200',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('ebooks', 'public');
        }
        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('ebook-files', 'public');
        }

        $data['is_free'] = $request->boolean('is_free');
        $data['is_published'] = $request->boolean('is_published');
        $data['price'] = $data['is_free'] ? 0 : $data['price'];

        return $data;
    }
}
