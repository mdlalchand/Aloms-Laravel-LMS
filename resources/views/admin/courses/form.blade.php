@extends('layouts.admin')
@section('title', $course->exists ? 'Edit course' : 'New course')
@section('heading', $course->exists ? 'Edit: '.$course->title : 'New course')
@section('actions')
    @if ($course->exists)
        <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600">Preview</a>
    @endif
@endsection
@section('content')

<form method="POST" enctype="multipart/form-data"
      action="{{ $course->exists ? route('admin.courses.update', $course) : route('admin.courses.store') }}"
      class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
    @csrf
    @if ($course->exists) @method('PUT') @endif

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Course information</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Title *</label>
                    <input name="title" value="{{ old('title', $course->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Subtitle</label>
                    <input name="subtitle" value="{{ old('subtitle', $course->subtitle) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Description</label>
                    <textarea name="description" rows="6" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $course->description) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">What students will learn (one per line)</label>
                        <textarea name="outcomes" rows="5" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('outcomes', implode("\n", $course->outcomes ?? [])) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Requirements</label>
                        <textarea name="requirements" rows="5" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('requirements', $course->requirements) }}</textarea>
                    </div>
                </div>
            </div>
        </section>

        @if ($course->exists)
            {{-- CURRICULUM BUILDER --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="font-bold text-slate-900">Curriculum</h3>
                    <a href="{{ route('admin.courses.sections.create', $course) }}"
                       class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white">+ Add section</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($course->sections as $section)
                        <div class="p-4">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-slate-800">{{ $section->position }}. {{ $section->title }}</p>
                                <div class="ml-auto flex items-center gap-3 text-xs">
                                    <a href="{{ route('admin.lessons.create', ['course_id' => $course->id, 'section_id' => $section->id]) }}" class="font-bold text-emerald-700">+ Lesson</a>
                                    <a href="{{ route('admin.sections.edit', $section) }}" class="text-slate-500">Edit</a>
                                    <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Delete section and its lessons?')">
                                        @csrf @method('DELETE')<button class="text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach ($section->lessons as $lesson)
                                    <li class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2">
                                        <span class="text-slate-500">{{ $lesson->type === 'video' ? '▶' : ($lesson->type === 'pdf' ? '📄' : '📖') }}</span>
                                        <span class="flex-1 text-slate-700">{{ $lesson->title }}</span>
                                        @if ($lesson->is_free_preview)<span class="rounded bg-emerald-100 px-1.5 text-[10px] font-bold text-emerald-700">PREVIEW</span>@endif
                                        <span class="text-xs text-slate-500">{{ $lesson->duration_minutes }}m</span>
                                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="text-xs font-semibold text-emerald-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete lesson?')">
                                            @csrf @method('DELETE')<button class="text-xs font-semibold text-rose-600">Del</button>
                                        </form>
                                    </li>
                                @endforeach
                                @if (! $section->lessons->count())
                                    <li class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">No lessons in this section yet.</li>
                                @endif
                            </ul>
                        </div>
                    @empty
                        <p class="p-6 text-sm text-slate-600">No sections yet — add your first section to start building the curriculum.</p>
                    @endforelse
                </div>
            </section>

            {{-- EXAMS --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Exams</h3>
                    <a href="{{ route('admin.exams.create') }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white">+ Add exam</a>
                </div>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse ($course->exams as $exam)
                        <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                            <span>{{ $exam->title }} <span class="text-xs text-slate-500">({{ $exam->questions()->count() }} questions)</span></span>
                            <span class="flex gap-3 text-xs">
                                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="font-bold text-emerald-700">Questions</a>
                                <a href="{{ route('admin.exams.edit', $exam) }}" class="text-slate-500">Edit</a>
                            </span>
                        </li>
                    @empty
                        <li class="text-slate-500">No exams yet.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </div>

    {{-- SIDEBAR --}}
    <aside class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Publish</h3>
            <div class="mt-4 space-y-3 text-sm">
                <label class="flex items-center gap-2 font-semibold text-slate-700"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $course->is_published)) class="rounded text-emerald-700"> Published</label>
                <label class="flex items-center gap-2 font-semibold text-slate-700"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $course->is_featured)) class="rounded text-emerald-700"> Featured on homepage</label>
                <label class="flex items-center gap-2 font-semibold text-slate-700"><input type="checkbox" name="has_certificate" value="1" @checked(old('has_certificate', $course->has_certificate ?? true)) class="rounded text-emerald-700"> Certificate on completion</label>
            </div>
            <button class="mt-5 w-full rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white">{{ $course->exists ? 'Save changes' : 'Create course' }}</button>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Organisation</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Category</label>
                    <select name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                        <option value="">— none —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $course->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if (auth()->user()->isAdmin())
                    <div>
                        <label class="mb-1 block font-semibold text-slate-700">Instructor</label>
                        <select name="instructor_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                            <option value="">— none —</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected(old('instructor_id', $course->instructor_id) == $instructor->id)>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block font-semibold text-slate-700">Level</label>
                        <select name="level" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                            @foreach (['beginner', 'intermediate', 'advanced'] as $level)
                                <option value="{{ $level }}" @selected(old('level', $course->level) === $level)>{{ ucfirst($level) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block font-semibold text-slate-700">Language</label>
                        <input name="language" value="{{ old('language', $course->language ?? 'Bangla') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Pricing & schedule</h3>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Price (৳)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $course->price ?? 0) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Discount price</label>
                    <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Duration (min)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $course->duration_minutes ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Seat limit</label>
                    <input type="number" name="seat_limit" value="{{ old('seat_limit', $course->seat_limit) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
                <div class="col-span-2">
                    <label class="mb-1 block font-semibold text-slate-700">Batch starts at</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($course->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Media & SEO</h3>
            <div class="mt-4 space-y-3 text-sm">
                @if ($course->thumbnail)
                    <img src="{{ $course->thumbnail_url }}" class="h-24 w-full rounded-lg object-cover" alt="">
                @endif
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Thumbnail</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs">
                </div>
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Promo video URL</label>
                    <input name="promo_video" value="{{ old('promo_video', $course->promo_video) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Meta title</label>
                    <input name="meta_title" value="{{ old('meta_title', $course->meta_title) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">
                </div>
                <div>
                    <label class="mb-1 block font-semibold text-slate-700">Meta description</label>
                    <textarea name="meta_description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5">{{ old('meta_description', $course->meta_description) }}</textarea>
                </div>
            </div>
        </section>
    </aside>
</form>
@endsection
