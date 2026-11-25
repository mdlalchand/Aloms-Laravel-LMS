<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Exam $exam)
    {
        return view('admin.questions.index', [
            'exam' => $exam->load('questions.options'),
        ]);
    }

    public function create(Exam $exam)
    {
        return view('admin.questions.form', ['exam' => $exam, 'question' => new Question(['type' => 'mcq', 'marks' => 1])]);
    }

    public function store(Request $request, Exam $exam)
    {
        $this->save($request, $exam);
        $this->syncTotalMarks($exam);

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question added.');
    }

    public function edit(Question $question)
    {
        return view('admin.questions.form', [
            'exam' => $question->exam,
            'question' => $question->load('options'),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $this->save($request, $question->exam, $question);
        $this->syncTotalMarks($question->exam);

        return redirect()->route('admin.exams.questions.index', $question->exam_id)->with('success', 'Question updated.');
    }

    public function destroy(Question $question)
    {
        $exam = $question->exam;
        $question->delete();
        $this->syncTotalMarks($exam);

        return back()->with('success', 'Question deleted.');
    }

    private function save(Request $request, Exam $exam, ?Question $question = null): void
    {
        $data = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:mcq,cq,short',
            'marks' => 'required|numeric|min:0.25',
            'explanation' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:500',
            'correct' => 'nullable|integer',
        ]);

        $question ??= new Question(['exam_id' => $exam->id]);
        $question->fill([
            'question' => $data['question'],
            'type' => $data['type'],
            'marks' => $data['marks'],
            'explanation' => $data['explanation'] ?? null,
            'position' => $data['position'] ?? 0,
        ])->save();

        if ($data['type'] === 'mcq') {
            $question->options()->delete();
            $correct = (int) ($data['correct'] ?? 0);
            foreach (array_values(array_filter($data['options'] ?? [], fn ($o) => filled($o))) as $i => $text) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $text,
                    'is_correct' => $i === $correct,
                    'position' => $i,
                ]);
            }
        }
    }

    private function syncTotalMarks(Exam $exam): void
    {
        $exam->update(['total_marks' => $exam->questions()->sum('marks')]);
    }
}
