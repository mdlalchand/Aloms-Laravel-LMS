@extends('layouts.student')
@section('title', 'Exam result')
@section('heading', 'Exam result')
@section('content')
@php($passed = $attempt->isPassed())
<div class="mx-auto max-w-4xl space-y-5">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <div class="grid h-20 w-20 place-items-center rounded-full {{ $passed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                <span class="text-2xl font-extrabold">{{ $attempt->score }}</span>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ $exam->title }}</h1>
                <p class="text-sm text-slate-600">{{ optional($exam->course)->title }} · submitted {{ optional($attempt->submitted_at)->format('d M Y, h:i A') }}</p>
                <p class="mt-2 inline-block rounded-full px-3 py-1 text-xs font-bold {{ $passed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $passed ? 'PASSED' : 'FAILED' }} · pass marks {{ $exam->pass_marks }}
                </p>
            </div>
            <a href="{{ route('student.exams.show', $exam) }}" class="ml-auto rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Back to exam</a>
        </div>
        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ([['Score', $attempt->score], ['Correct', $attempt->correct_count], ['Wrong', $attempt->wrong_count], ['Total marks', $exam->total_marks]] as [$l, $v])
                <div class="rounded-xl bg-slate-50 p-3 text-center">
                    <p class="text-lg font-extrabold text-slate-900">{{ $v }}</p>
                    <p class="text-xs text-slate-500">{{ $l }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="space-y-3">
        @foreach ($exam->questions()->with('options')->get() as $i => $question)
            @php($answer = $answers[$question->id] ?? null)
            @php($correct = $question->options->firstWhere('is_correct', true))
            <div class="rounded-2xl border {{ $answer && $answer->is_correct ? 'border-emerald-200' : 'border-slate-200' }} bg-white p-5 shadow-sm">
                <p class="font-semibold text-slate-900"><span class="mr-2 rounded bg-slate-200 px-2 py-0.5 text-xs font-bold text-slate-700">Q{{ $i + 1 }}</span>{{ $question->question }}</p>
                <div class="mt-3 space-y-1 text-sm">
                    @foreach ($question->options as $option)
                        <p class="flex items-center gap-2 rounded px-2 py-1
                            {{ $correct && $correct->id === $option->id ? 'bg-emerald-50 font-semibold text-emerald-700' : '' }}
                            {{ $answer && (int) $answer->option_id === $option->id && (! $correct || $correct->id !== $option->id) ? 'bg-rose-50 text-rose-700' : '' }}">
                            <span>{{ $correct && $correct->id === $option->id ? '✓' : ($answer && (int) $answer->option_id === $option->id ? '✕' : '○') }}</span>
                            {{ $option->option_text }}
                        </p>
                    @endforeach
                    @if ($answer && $answer->answer_text)
                        <p class="rounded bg-slate-50 px-2 py-2 text-slate-700"><b>Your answer:</b> {{ $answer->answer_text }}</p>
                    @endif
                </div>
                @if ($question->explanation)
                    <p class="mt-3 rounded-lg bg-sky-50 px-3 py-2 text-xs text-sky-800"><b>Explanation:</b> {{ $question->explanation }}</p>
                @endif
                <p class="mt-2 text-xs font-semibold text-slate-500">Marks awarded: {{ $answer->marks_awarded ?? 0 }} / {{ $question->marks }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
