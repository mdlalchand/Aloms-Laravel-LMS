@extends('layouts.admin')
@section('title', 'Questions')
@section('heading', 'Questions · '.$exam->title)
@section('actions')
    <a href="{{ route('admin.exams.questions.create', $exam) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ Add question</a>
@endsection
@section('content')
<div class="mb-4 flex flex-wrap gap-3 text-sm">
    <span class="rounded-lg bg-white px-4 py-2 font-semibold shadow-sm">Questions: {{ $exam->questions->count() }}</span>
    <span class="rounded-lg bg-white px-4 py-2 font-semibold shadow-sm">Total marks: {{ $exam->total_marks }}</span>
    <span class="rounded-lg bg-white px-4 py-2 font-semibold shadow-sm">Duration: {{ $exam->duration_minutes }} min</span>
    <a href="{{ route('admin.exams.edit', $exam) }}" class="ml-auto rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-600">Exam settings</a>
</div>

<div class="space-y-3">
    @forelse ($exam->questions as $i => $question)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <p class="font-semibold text-slate-800"><span class="mr-2 rounded bg-slate-200 px-2 py-0.5 text-xs font-bold text-slate-700">Q{{ $i + 1 }}</span>{{ $question->question }}</p>
                <div class="shrink-0 text-xs font-bold">
                    <span class="rounded bg-emerald-50 px-2 py-1 text-emerald-700">{{ $question->marks }} marks</span>
                    <span class="ml-1 rounded bg-slate-100 px-2 py-1 uppercase text-slate-500">{{ $question->type }}</span>
                </div>
            </div>
            @if ($question->type === 'mcq')
                <ul class="mt-3 grid gap-1 text-sm sm:grid-cols-2">
                    @foreach ($question->options as $option)
                        <li class="rounded px-2 py-1 {{ $option->is_correct ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-slate-600' }}">
                            {{ $option->is_correct ? '✓' : '○' }} {{ $option->option_text }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-3 flex gap-3 text-xs font-semibold">
                <a href="{{ route('admin.questions.edit', $question) }}" class="text-emerald-700">Edit</a>
                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Delete question?')">
                    @csrf @method('DELETE')<button class="text-rose-600">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
            No questions yet. <a href="{{ route('admin.exams.questions.create', $exam) }}" class="font-semibold text-emerald-700 underline">Add the first question</a>
        </div>
    @endforelse
</div>
@endsection
