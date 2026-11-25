@extends('layouts.admin')
@section('title', 'Home slider')
@section('heading', 'Home page slider')
@section('actions')
    <a href="{{ route('admin.sliders.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New slide</a>
@endsection
@section('content')
<p class="mb-4 text-sm text-slate-600">
    These slides rotate at the top of the home page. Drag order is controlled by the “Sort order” field (lower numbers first).
    When no slide is active, the built-in default hero is shown.
</p>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($sliders as $slider)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <img src="{{ $slider->imageUrl() }}" alt="" class="h-40 w-full object-cover">
            <div class="p-4">
                <div class="mb-1 flex items-center gap-2">
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $slider->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $slider->is_active ? 'Active' : 'Hidden' }}
                    </span>
                    <span class="text-xs text-slate-500">order {{ $slider->sort_order }}</span>
                </div>
                <h3 class="font-bold text-slate-800">{{ $slider->title ?: '(no title)' }}</h3>
                <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $slider->subtitle }}</p>
                <div class="mt-3 flex gap-3 text-sm font-semibold">
                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-emerald-700">Edit</a>
                    <form method="POST" action="{{ route('admin.sliders.toggle', $slider) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-600">{{ $slider->is_active ? 'Hide' : 'Show' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" onsubmit="return confirm('Delete this slide?')">
                        @csrf @method('DELETE')
                        <button class="text-rose-600">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500 sm:col-span-2 xl:col-span-3">
            No slides yet — the home page is showing the default hero.
        </div>
    @endforelse
</div>
<div class="mt-4">{{ $sliders->links() }}</div>
@endsection
