@extends('layouts.admin')
@section('title', $liveClass->exists ? 'Edit live class' : 'Schedule live class')
@section('heading', $liveClass->exists ? 'Edit live class' : 'Schedule a live class')
@section('content')
<form method="POST"
      action="{{ $liveClass->exists ? route('admin.live-classes.update', $liveClass) : route('admin.live-classes.store') }}"
      class="max-w-2xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($liveClass->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Course</label>
            <select name="course_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">— select course —</option>
                @foreach ($courses as $id => $title)
                    <option value="{{ $id }}" @selected(old('course_id', $liveClass->course_id) == $id)>{{ $title }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Class title</label>
            <input name="title" value="{{ old('title', $liveClass->title) }}" required placeholder="e.g. Physics — Newton's laws doubt solving" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Platform</label>
            <select name="platform" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @foreach ($platforms as $platform)
                    <option value="{{ $platform }}" @selected(old('platform', $liveClass->platform) === $platform)>{{ $platform }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Starts at ({{ config('app.timezone') }})</label>
            <input type="datetime-local" name="starts_at" required
                   value="{{ old('starts_at', $liveClass->starts_at?->format('Y-m-d\TH:i')) }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Duration (minutes)</label>
            <input type="number" name="duration_minutes" min="5" max="600" value="{{ old('duration_minutes', $liveClass->duration_minutes) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Meeting password (optional)</label>
            <input name="meeting_password" value="{{ old('meeting_password', $liveClass->meeting_password) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Meeting link</label>
            <input name="meeting_url" value="{{ old('meeting_url', $liveClass->meeting_url) }}" placeholder="https://zoom.us/j/123456789" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">Students see the link (and password) only on the course page, and the Join button unlocks 10 minutes before the class.</p>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Recording / replay link (optional)</label>
            <input name="recording_url" value="{{ old('recording_url', $liveClass->recording_url) }}" placeholder="https://youtu.be/..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $liveClass->description) }}</textarea>
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $liveClass->exists ? $liveClass->is_published : true)) class="rounded text-emerald-700"> Announce to students
    </label>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save class</button>
        <a href="{{ route('admin.live-classes.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</a>
    </div>
</form>
@endsection
