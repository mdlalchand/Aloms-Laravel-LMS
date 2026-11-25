@extends('layouts.app')
@section('title', 'All courses')
@section('content')
<div class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ isset($activeCategory) ? $activeCategory->name : 'All courses' }}</h1>
        <p class="mt-1 text-slate-500">{{ $courses->total() }} courses available</p>

        <form method="GET" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search…" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            <select name="category" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }} ({{ $category->published_courses_count ?? $category->publishedCourses()->count() }})</option>
                @endforeach
            </select>
            <select name="level" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                <option value="">Any level</option>
                @foreach (['beginner', 'intermediate', 'advanced'] as $level)
                    <option value="{{ $level }}" @selected(($filters['level'] ?? '') === $level)>{{ ucfirst($level) }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <select name="sort" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    <option value="">Newest</option>
                    <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>Most popular</option>
                    <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Top rated</option>
                    <option value="price_low" @selected(($filters['sort'] ?? '') === 'price_low')>Price ↑</option>
                    <option value="price_high" @selected(($filters['sort'] ?? '') === 'price_high')>Price ↓</option>
                </select>
                <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Go</button>
            </div>
        </form>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-10">
    @if ($courses->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($courses as $course)
                @include('partials.course-card', ['course' => $course])
            @endforeach
        </div>
        <div class="mt-10">{{ $courses->links() }}</div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center">
            <p class="text-4xl">🔍</p>
            <h3 class="mt-3 text-lg font-bold text-slate-900">No courses found</h3>
            <p class="mt-1 text-sm text-slate-600">Try a different keyword or clear the filters.</p>
            <a href="{{ route('courses.index') }}" class="mt-5 inline-block rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white">Reset filters</a>
        </div>
    @endif
</div>
@endsection
