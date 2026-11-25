<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function create(Course $course)
    {
        return view('admin.sections.form', [
            'section' => new Section(['course_id' => $course->id]),
            'course' => $course,
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'position' => 'nullable|integer|min:0',
        ]);

        $course->sections()->create($data + ['position' => $data['position'] ?? $course->sections()->count() + 1]);

        return redirect()->route('admin.courses.edit', $course)->with('success', 'Section added.');
    }

    public function edit(Section $section)
    {
        return view('admin.sections.form', [
            'section' => $section,
            'course' => $section->course,
        ]);
    }

    public function update(Request $request, Section $section)
    {
        $section->update($request->validate([
            'title' => 'required|string|max:180',
            'position' => 'nullable|integer|min:0',
        ]));

        return redirect()->route('admin.courses.edit', $section->course_id)->with('success', 'Section updated.');
    }

    public function destroy(Section $section)
    {
        $courseId = $section->course_id;
        $section->delete();
        return redirect()->route('admin.courses.edit', $courseId)->with('success', 'Section deleted.');
    }
}
