@extends('layouts.portal')
@section('title', 'My courses')
@section('heading', 'My courses')
@section('content')
<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($courses as $course)
        <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <img src="{{ $course->thumbnail_url }}" alt="" class="h-36 w-full object-cover">
            <div class="flex flex-1 flex-col p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $course->category?->name }}</p>
                <h3 class="mt-1 font-bold text-slate-900">{{ \Illuminate\Support\Str::limit($course->title, 60) }}</h3>
                <div class="mt-3 grid grid-cols-3 gap-1 text-center text-xs">
                    <div class="rounded-lg bg-slate-50 py-2"><p class="font-black text-slate-800">{{ $course->enrollments_count }}</p><p class="text-slate-500">students</p></div>
                    <div class="rounded-lg bg-slate-50 py-2"><p class="font-black text-slate-800">{{ $course->lessons_count }}</p><p class="text-slate-500">lessons</p></div>
                    <div class="rounded-lg bg-slate-50 py-2"><p class="font-black text-slate-800">{{ $course->reviews_count }}</p><p class="text-slate-500">reviews</p></div>
                </div>
                <div class="mt-4 flex gap-2 text-sm">
                    <a href="{{ route('courses.show', $course->slug) }}" class="flex-1 rounded-lg bg-emerald-700 px-3 py-2 text-center font-bold text-white">View page</a>
                    <a href="{{ route('admin.courses.show', $course) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center font-semibold text-slate-700">Curriculum</a>
                </div>
            </div>
        </div>
    @empty
        <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500 sm:col-span-2 xl:col-span-3">
            No courses are assigned to you yet. Ask an administrator to set you as the instructor.
        </p>
    @endforelse
</div>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection
