@extends('layouts.student')
@section('title', 'My courses')
@section('heading', 'My courses')
@section('content')
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($enrollments as $enrollment)
        @php($course = $enrollment->course)
        @if (! $course) @continue @endif
        @php($progress = $course->progressFor(auth()->user()))
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <img src="{{ $course->thumbnail_url }}" class="h-40 w-full object-cover" alt="">
            <div class="p-4">
                <h3 class="line-clamp-2 font-bold text-slate-900">{{ $course->title }}</h3>
                <div class="mt-3 flex items-center gap-2">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-emerald-400' }}" style="width: {{ $progress }}%"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $progress }}%</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('student.learn', $course->slug) }}" class="flex-1 rounded-lg bg-emerald-700 px-3 py-2 text-center text-sm font-bold text-white hover:bg-emerald-800">
                        {{ $progress ? 'Continue' : 'Start' }}
                    </a>
                    @if ($progress >= 100 && $course->has_certificate)
                        <a href="{{ route('student.certificates.show', $course->slug) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">🏅</a>
                    @endif
                </div>
                <p class="mt-2 text-xs text-slate-500">Enrolled {{ optional($enrollment->enrolled_at)->format('d M Y') }}</p>
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center">
            <p class="text-4xl">📚</p>
            <h3 class="mt-3 font-bold text-slate-900">No courses yet</h3>
            <a href="{{ route('courses.index') }}" class="mt-4 inline-block rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white">Find a course</a>
        </div>
    @endforelse
</div>
@endsection
