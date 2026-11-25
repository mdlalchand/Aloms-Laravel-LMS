@extends('layouts.admin')
@section('title', $section->exists ? 'Edit section' : 'New section')
@section('heading', $section->exists ? 'Edit section' : 'New section · '.$course->title)
@section('content')
<form method="POST" action="{{ $section->exists ? route('admin.sections.update', $section) : route('admin.courses.sections.store', $course) }}"
      class="max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($section->exists) @method('PUT') @endif
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Section title</label>
        <input name="title" value="{{ old('title', $section->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Position</label>
        <input type="number" name="position" value="{{ old('position', $section->position ?? 1) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save</button>
        <a href="{{ route('admin.courses.edit', $course) }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Back to course</a>
    </div>
</form>
@endsection
