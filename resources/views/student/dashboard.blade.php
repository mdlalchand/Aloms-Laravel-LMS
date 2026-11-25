@extends('layouts.student')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('content')

<div class="rounded-2xl bg-gradient-to-r from-slate-900 to-emerald-900 p-6 text-white">
    <h2 class="text-2xl font-extrabold">Hello, {{ auth()->user()->name }} 👋</h2>
    <p class="mt-1 text-slate-300">Keep going — you're {{ $stats['in_progress'] }} course(s) in progress.</p>
    @if ($continueCourse)
        <a href="{{ route('student.learn', $continueCourse->course->slug) }}"
           class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 font-bold text-slate-950 hover:bg-emerald-400">
            Continue: {{ Str::limit($continueCourse->course->title, 32) }} →
        </a>
    @endif
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['Enrolled courses', $stats['enrolled'], '📚'],
        ['Lessons completed', $stats['lessons_done'], '✅'],
        ['Exam attempts', $stats['exam_attempts'], '📝'],
        ['Certificates', $stats['certificates'], '🏅'],
    ] as [$label, $value, $icon])
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-2xl">{{ $icon }}</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $value }}</p>
            <p class="text-sm text-slate-600">{{ $label }}</p>
        </div>
    @endforeach
</div>

<div class="mt-8 grid items-start gap-6 lg:grid-cols-[1.6fr_1fr]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900">Continue learning</h3>
            <a href="{{ route('student.courses') }}" class="text-sm font-semibold text-emerald-700 hover:underline">All courses →</a>
        </div>
        <div class="mt-4 space-y-3">
            @forelse ($enrollments as $enrollment)
                @php($course = $enrollment->course)
                @if ($course)
                    @php($progress = $course->progressFor(auth()->user()))
                    <a href="{{ route('student.learn', $course->slug) }}" class="flex items-center gap-4 rounded-xl border border-slate-100 p-3 hover:bg-slate-50">
                        <img src="{{ $course->thumbnail_url }}" class="h-14 w-20 rounded-lg object-cover" alt="">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900">{{ $course->title }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <div class="h-1.5 w-full max-w-[160px] overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500">{{ $progress }}%</span>
                            </div>
                        </div>
                        <span class="text-emerald-700">→</span>
                    </a>
                @endif
            @empty
                <p class="rounded-xl bg-slate-50 p-6 text-center text-sm text-slate-600">
                    You are not enrolled in any course yet.
                    <a href="{{ route('courses.index') }}" class="font-semibold text-emerald-700 underline">Browse courses</a>
                </p>
            @endforelse
        </div>
    </section>

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Upcoming exams</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse ($upcomingExams as $exam)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div>
                            <p class="font-semibold text-slate-800">{{ Str::limit($exam->title, 30) }}</p>
                            <p class="text-xs text-slate-500">{{ $exam->starts_at ? $exam->starts_at->format('d M, h:i A') : 'Open now' }} · {{ $exam->duration_minutes }}m</p>
                        </div>
                        <a href="{{ route('student.exams.show', $exam) }}" class="rounded bg-white px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-slate-200">Open</a>
                    </li>
                @empty
                    <li class="text-slate-500">No exams scheduled.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Recent activity</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse ($recentLessons as $progress)
                    <li class="flex items-center gap-2 text-slate-600">
                        <span class="{{ $progress->completed_at ? 'text-emerald-500' : 'text-amber-700' }}">●</span>
                        <span class="truncate">{{ Str::limit(optional($progress->lesson)->title, 34) }}</span>
                        <span class="ml-auto text-xs text-slate-500">{{ $progress->updated_at->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">No activity yet.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
