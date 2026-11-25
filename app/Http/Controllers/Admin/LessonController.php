<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function create(Request $request)
    {
        $course = Course::with('sections')->findOrFail($request->get('course_id'));

        return view('admin.lessons.form', [
            'lesson' => new Lesson(['course_id' => $course->id, 'section_id' => $request->get('section_id'), 'type' => 'video']),
            'course' => $course,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Lesson::create($data);

        return redirect()->route('admin.courses.edit', $data['course_id'])->with('success', 'Lesson added.');
    }

    public function edit(Lesson $lesson)
    {
        return view('admin.lessons.form', [
            'lesson' => $lesson,
            'course' => $lesson->course->load('sections'),
        ]);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $lesson->update($this->validated($request));
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Lesson updated.');
    }

    public function destroy(Lesson $lesson)
    {
        $courseId = $lesson->course_id;
        $lesson->delete();
        return redirect()->route('admin.courses.edit', $courseId)->with('success', 'Lesson deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'section_id' => 'nullable|exists:sections,id',
            'title' => 'required|string|max:180',
            'type' => 'required|in:video,text,pdf,live,exam',
            'video_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'position' => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|max:20480',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('lessons', 'public');
        }

        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $data['is_published'] = $request->boolean('is_published');
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }
}
