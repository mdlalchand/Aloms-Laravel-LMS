@extends('layouts.admin')
@section('title', $exam->exists ? 'Edit exam' : 'New exam')
@section('heading', $exam->exists ? 'Edit exam' : 'New exam')
@section('content')
<form method="POST" action="{{ $exam->exists ? route('admin.exams.update', $exam) : route('admin.exams.store') }}"
      class="max-w-3xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($exam->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Exam title *</label>
            <input name="title" value="{{ old('title', $exam->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Course</label>
            <select name="course_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">— standalone (free) exam —</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id', $exam->course_id) == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $exam->description) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Duration (minutes) *</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes ?? 30) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Pass marks</label>
            <input type="number" step="0.01" name="pass_marks" value="{{ old('pass_marks', $exam->pass_marks ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Negative marking (per wrong)</label>
            <input type="number" step="0.01" name="negative_marking" value="{{ old('negative_marking', $exam->negative_marking ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Attempt limit (0 = unlimited)</label>
            <input type="number" name="attempt_limit" value="{{ old('attempt_limit', $exam->attempt_limit ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Opens at</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($exam->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Closes at</label>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($exam->ends_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
    </div>

    <div class="flex flex-wrap gap-5 text-sm font-semibold text-slate-700">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $exam->is_published ?? true)) class="rounded text-emerald-700"> Published</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_free" value="1" @checked(old('is_free', $exam->is_free)) class="rounded text-emerald-700"> Free (no enrollment needed)</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="shuffle_questions" value="1" @checked(old('shuffle_questions', $exam->shuffle_questions)) class="rounded text-emerald-700"> Shuffle questions</label>
    </div>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">{{ $exam->exists ? 'Save exam' : 'Create exam' }}</button>
        <a href="{{ route('admin.exams.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</a>
    </div>
</form>
@endsection
