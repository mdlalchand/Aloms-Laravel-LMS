<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $query = Exam::with('course')->withCount('questions')->latest();

        if (! auth()->user()->isAdmin()) {
            $query->whereHas('course', fn ($c) => $c->where('instructor_id', auth()->id()));
        }

        return view('admin.exams.index', ['exams' => $query->paginate(15)]);
    }

    public function create()
    {
        return view('admin.exams.form', [
            'exam' => new Exam(['duration_minutes' => 30, 'is_published' => true, 'pass_marks' => 0]),
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $exam = Exam::create($this->validated($request));
        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Exam created. Add questions below.');
    }

    public function edit(Exam $exam)
    {
        return view('admin.exams.form', [
            'exam' => $exam,
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Exam $exam)
    {
        $exam->update($this->validated($request));
        return redirect()->route('admin.exams.index')->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:180',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'pass_marks' => 'nullable|numeric|min:0',
            'negative_marking' => 'nullable|numeric|min:0',
            'attempt_limit' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['is_free'] = $request->boolean('is_free');
        $data['shuffle_questions'] = $request->boolean('shuffle_questions');
        $data['total_marks'] = (float) $request->input('total_marks', 0);
        $data['pass_marks'] = $data['pass_marks'] ?? 0;
        $data['negative_marking'] = $data['negative_marking'] ?? 0;
        $data['attempt_limit'] = $data['attempt_limit'] ?? 0;

        return $data;
    }
}
