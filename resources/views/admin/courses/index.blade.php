@extends('layouts.admin')
@section('title', 'Courses')
@section('heading', 'Courses')
@section('actions')
    <a href="{{ route('admin.courses.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New course</a>
@endsection
@section('content')
<form method="GET" class="mb-4 flex gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Search courses…" class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Search</button>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">Course</th>
                <th class="px-5 py-3">Category</th>
                <th class="px-5 py-3">Instructor</th>
                <th class="px-5 py-3">Lessons</th>
                <th class="px-5 py-3">Students</th>
                <th class="px-5 py-3">Price</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($courses as $course)
                <tr>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $course->thumbnail_url }}" class="h-10 w-16 rounded object-cover" alt="">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="font-semibold text-slate-800 hover:text-emerald-700">{{ Str::limit($course->title, 40) }}</a>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-500">{{ $course->category?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $course->instructor?->name ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $course->lessons_count }}</td>
                    <td class="px-5 py-3">{{ $course->enrollments_count }}</td>
                    <td class="px-5 py-3 font-semibold">৳{{ number_format($course->final_price) }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $course->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $course->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="text-sm text-slate-600">View</a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="ml-3 text-sm font-semibold text-emerald-700">Edit</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="inline" onsubmit="return confirm('Delete course and all its lessons?')">
                            @csrf @method('DELETE')
                            <button class="ml-3 text-sm font-semibold text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $courses->links() }}</div>
@endsection
