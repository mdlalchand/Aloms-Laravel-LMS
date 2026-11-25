@extends('layouts.admin')
@section('title', $slider->exists ? 'Edit slide' : 'New slide')
@section('heading', $slider->exists ? 'Edit slide' : 'New slide')
@section('content')
<form method="POST" enctype="multipart/form-data"
      action="{{ $slider->exists ? route('admin.sliders.update', $slider) : route('admin.sliders.store') }}"
      class="max-w-3xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($slider->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Badge (small label)</label>
            <input name="badge" value="{{ old('badge', $slider->badge) }}" placeholder="Admission 2025" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Colour theme</label>
            <select name="theme" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @foreach ($themes as $value => $label)
                    <option value="{{ $value }}" @selected(old('theme', $slider->theme) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Headline</label>
            <input name="title" value="{{ old('title', $slider->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Sub headline</label>
            <textarea name="subtitle" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('subtitle', $slider->subtitle) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Button text</label>
            <input name="button_text" value="{{ old('button_text', $slider->button_text) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Button link</label>
            <input name="button_url" value="{{ old('button_url', $slider->button_url) }}" placeholder="/courses" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Sort order</label>
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $slider->sort_order) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Background image (optional)</label>
            <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            @if ($slider->imageUrl())
                <img src="{{ $slider->imageUrl() }}" class="mt-2 h-24 rounded-lg object-cover" alt="">
            @endif
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider->exists ? $slider->is_active : true)) class="rounded text-emerald-700"> Publish this slide
    </label>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save slide</button>
        <a href="{{ route('admin.sliders.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</a>
    </div>
</form>
@endsection
