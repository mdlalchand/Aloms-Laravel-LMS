<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveClass;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $classes = LiveClass::with('course')
            ->when(! $user->isAdmin(), fn ($q) => $q
                ->whereHas('course', fn ($c) => $c->where('instructor_id', $user->id)))
            ->when($request->get('course'), fn ($q, $id) => $q->where('course_id', $id))
            ->orderByDesc('starts_at')
            ->paginate(15)->withQueryString();

        return view('admin.live-classes.index', [
            'classes' => $classes,
            'courses' => $this->courses(),
        ]);
    }

    public function create()
    {
        return view('admin.live-classes.form', [
            'liveClass' => new LiveClass([
                'platform' => 'Zoom', 'starts_at' => now()->addDay()->startOfHour(),
                'duration_minutes' => 60, 'is_published' => true,
            ]),
            'courses' => $this->courses(),
            'platforms' => LiveClass::PLATFORMS,
        ]);
    }

    public function store(Request $request)
    {
        LiveClass::create($this->validated($request));

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class scheduled.');
    }

    public function edit(LiveClass $liveClass)
    {
        $this->authorizeOwner($liveClass);

        return view('admin.live-classes.form', [
            'liveClass' => $liveClass,
            'courses' => $this->courses(),
            'platforms' => LiveClass::PLATFORMS,
        ]);
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        $this->authorizeOwner($liveClass);
        $liveClass->update($this->validated($request));

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class updated.');
    }

    public function destroy(LiveClass $liveClass)
    {
        $this->authorizeOwner($liveClass);
        $liveClass->delete();

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class deleted.');
    }

    private function authorizeOwner(LiveClass $liveClass): void
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || $liveClass->course?->instructor_id === $user->id, 403);
    }

    private function courses()
    {
        $user = auth()->user();

        return Course::when(! $user->isAdmin(), fn ($q) => $q->where('instructor_id', $user->id))
            ->orderBy('title')->pluck('title', 'id');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:2000',
            'platform' => 'required|string|max:40',
            'meeting_url' => 'nullable|url|max:255',
            'meeting_password' => 'nullable|string|max:60',
            'recording_url' => 'nullable|url|max:255',
            'starts_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:5|max:600',
        ]);

        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
