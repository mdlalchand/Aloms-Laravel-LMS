<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        return view('admin.sliders.index', [
            'sliders' => Slider::orderBy('sort_order')->orderBy('id')->paginate(12),
        ]);
    }

    public function create()
    {
        return view('admin.sliders.form', [
            'slider' => new Slider([
                'is_active' => true, 'theme' => 'emerald', 'sort_order' => (int) Slider::max('sort_order') + 1,
                'button_text' => 'Explore courses', 'button_url' => '/courses',
            ]),
            'themes' => Slider::THEMES,
        ]);
    }

    public function store(Request $request)
    {
        Slider::create($this->validated($request));

        return redirect()->route('admin.sliders.index')->with('success', 'Slider added.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.form', ['slider' => $slider, 'themes' => Slider::THEMES]);
    }

    public function update(Request $request, Slider $slider)
    {
        $slider->update($this->validated($request, $slider));

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted.');
    }

    /** Quick AJAX-free toggle used by the eye button in the list. */
    public function toggle(Slider $slider)
    {
        $slider->update(['is_active' => ! $slider->is_active]);

        return back()->with('success', 'Slider '.($slider->is_active ? 'published' : 'hidden').'.');
    }

    private function validated(Request $request, ?Slider $slider = null): array
    {
        $data = $request->validate([
            'badge' => 'nullable|string|max:80',
            'title' => 'required|string|max:160',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'button_text' => 'nullable|string|max:60',
            'button_url' => 'nullable|string|max:255',
            'theme' => 'required|in:'.implode(',', array_keys(Slider::THEMES)),
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sliders', 'public');
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
