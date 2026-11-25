@extends('layouts.student')
@section('title', $exam->title)
@section('heading', 'Exam details')
@section('content')
<div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-extrabold text-slate-900">{{ $exam->title }}</h1>
        <p class="mt-1 text-sm text-slate-600">{{ optional($exam->course)->title }}</p>
        <p class="mt-4 whitespace-pre-line text-slate-600">{{ $exam->description }}</p>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ([
                ['Duration', $exam->duration_minutes.' min'],
                ['Questions', $exam->questions()->count()],
                ['Total marks', $exam->total_marks],
                ['Pass marks', $exam->pass_marks],
            ] as [$l, $v])
                <div class="rounded-xl bg-slate-50 p-3 text-center">
                    <p class="text-lg font-extrabold text-slate-900">{{ $v }}</p>
                    <p class="text-xs text-slate-500">{{ $l }}</p>
                </div>
            @endforeach
        </div>

        <ul class="mt-5 space-y-1 text-sm text-slate-600">
            <li>• Negative marking: <b>{{ $exam->negative_marking }}</b> per wrong answer.</li>
            <li>• Attempt limit: <b>{{ $exam->attempt_limit ?: 'Unlimited' }}</b>@if($left !== null) ({{ $left }} left)@endif</li>
            <li>• Schedule: <b>{{ $exam->starts_at ? $exam->starts_at->format('d M Y, h:i A') : 'Anytime' }}</b> {{ $exam->ends_at ? '→ '.$exam->ends_at->format('d M Y, h:i A') : '' }}</li>
        </ul>

        <form method="POST" action="{{ route('student.exams.start', $exam) }}" class="mt-6">
            @csrf
            <button class="rounded-xl bg-emerald-700 px-6 py-3 font-bold text-white hover:bg-emerald-800" @disabled(! $exam->isOpen())>
                {{ $exam->isOpen() ? 'Start exam' : 'Exam closed' }}
            </button>
        </form>
    </div>

    <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="font-bold text-slate-900">Your attempts</h3>
        <p class="mt-1 text-sm text-slate-600">Best score: <b class="text-emerald-700">{{ $best }}</b></p>
        <ul class="mt-4 space-y-2 text-sm">
            @forelse ($attempts as $attempt)
                <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $attempt->score }} marks</p>
                        <p class="text-xs text-slate-500">{{ optional($attempt->submitted_at ?? $attempt->started_at)->format('d M, h:i A') }}</p>
                    </div>
                    @if ($attempt->status === 'in_progress')
                        <a href="{{ route('student.attempts.show', $attempt) }}" class="rounded bg-amber-500 px-2 py-1 text-xs font-bold text-white">Resume</a>
                    @else
                        <a href="{{ route('student.attempts.result', $attempt) }}" class="rounded bg-white px-2 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">Result</a>
                    @endif
                </li>
            @empty
                <li class="text-slate-500">No attempts yet.</li>
            @endforelse
        </ul>
    </aside>
</div>
@endsection
