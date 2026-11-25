@extends('layouts.student')
@section('title', 'Exams')
@section('heading', 'Exams')
@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($exams as $exam)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">{{ $exam->is_free ? 'Free' : 'Enrolled' }}</span>
                <span class="text-xs font-semibold {{ $exam->isOpen() ? 'text-emerald-700' : 'text-rose-600' }}">{{ $exam->isOpen() ? 'Open' : 'Closed' }}</span>
            </div>
            <h3 class="mt-2 line-clamp-2 font-bold text-slate-900">{{ $exam->title }}</h3>
            <p class="mt-1 text-xs text-slate-500">{{ optional($exam->course)->title }}</p>
            <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
                <div class="rounded-lg bg-slate-50 py-2"><p class="font-bold text-slate-800">{{ $exam->duration_minutes }}m</p><p class="text-slate-500">Time</p></div>
                <div class="rounded-lg bg-slate-50 py-2"><p class="font-bold text-slate-800">{{ $exam->questions()->count() }}</p><p class="text-slate-500">Questions</p></div>
                <div class="rounded-lg bg-slate-50 py-2"><p class="font-bold text-slate-800">{{ $exam->total_marks }}</p><p class="text-slate-500">Marks</p></div>
            </div>
            <a href="{{ route('student.exams.show', $exam) }}" class="mt-4 block rounded-lg bg-emerald-700 px-4 py-2 text-center text-sm font-bold text-white">View exam</a>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center text-slate-500">
            No exams available. Enroll in a course to unlock exams.
        </div>
    @endforelse
</div>
@endsection
