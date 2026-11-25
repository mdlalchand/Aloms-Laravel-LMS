@extends('layouts.admin')
@section('title', $lesson->exists ? 'Edit lesson' : 'New lesson')
@section('heading', $lesson->exists ? 'Edit lesson' : 'New lesson · '.$course->title)
@section('content')
<form method="POST" enctype="multipart/form-data"
      action="{{ $lesson->exists ? route('admin.lessons.update', $lesson) : route('admin.lessons.store') }}"
      class="max-w-3xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($lesson->exists) @method('PUT') @endif
    <input type="hidden" name="course_id" value="{{ $course->id }}">

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Lesson title</label>
            <input name="title" value="{{ old('title', $lesson->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Section</label>
            <select name="section_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">— none —</option>
                @foreach ($course->sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id', $lesson->section_id) == $section->id)>{{ $section->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Type</label>
            <select name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @foreach (['video' => 'Video', 'text' => 'Text / notes', 'pdf' => 'PDF', 'live' => 'Live class', 'exam' => 'Exam'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $lesson->type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Video URL (YouTube / Vimeo / mp4)</label>
            <input name="video_url" value="{{ old('video_url', $lesson->video_url) }}" placeholder="https://www.youtube.com/watch?v=…" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Content / notes</label>
            <textarea name="content" rows="6" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('content', $lesson->content) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Duration (minutes)</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Position</label>
            <input type="number" name="position" value="{{ old('position', $lesson->position ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Attachment (PDF / zip)</label>
            <input type="file" name="attachment" class="w-full text-sm">
            @if ($lesson->attachment)
                <a href="{{ asset('storage/'.$lesson->attachment) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-emerald-700">Current file</a>
            @endif
        </div>
    </div>

    <div class="flex gap-5 text-sm font-semibold text-slate-700">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_free_preview" value="1" @checked(old('is_free_preview', $lesson->is_free_preview)) class="rounded text-emerald-700"> Free preview</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $lesson->is_published ?? true)) class="rounded text-emerald-700"> Published</label>
    </div>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save lesson</button>
        <a href="{{ route('admin.courses.edit', $course) }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Back to course</a>
    </div>
</form>
@endsection
