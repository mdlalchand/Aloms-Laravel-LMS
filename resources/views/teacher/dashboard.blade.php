@extends('layouts.portal')
@section('title', 'Teacher dashboard')
@section('heading', 'Assalamu alaikum, '.auth()->user()->name)
@section('content')
@php $cur = \App\Models\Setting::get('currency', '৳'); @endphp

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([
        ['Courses', $stats['courses'], 'emerald'],
        ['Students', $stats['students'], 'indigo'],
        ['Active enrolments', $stats['active'], 'sky'],
        ['Revenue', $cur.number_format($stats['revenue']), 'amber'],
    ] as [$label, $value, $tone])
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ $value }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $label === 'Revenue' ? 'from your paid enrolments' : 'across your courses' }}</p>
        </div>
    @endforeach
</div>

<div class="mt-5 grid items-start gap-5 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">New enrolments (last 7 days)</h2>
            <span class="text-sm text-slate-600">Rating: <b>{{ $stats['rating'] }}</b> / 5</span>
        </div>
        <div class="flex h-40 items-end gap-3">
            @php $max = max(1, $trend->max('count')); @endphp
            @foreach ($trend as $day)
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-xs font-bold text-slate-600">{{ $day['count'] }}</span>
                    <div class="w-full rounded-t-lg bg-emerald-700" style="height: {{ max(4, (int) ($day['count'] / $max * 100)) }}px"></div>
                    <span class="text-xs text-slate-500">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold text-slate-800">Upcoming live classes</h2>
        <ul class="space-y-3 text-sm">
            @forelse ($upcoming as $class)
                <li class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <p class="font-semibold text-slate-800">{{ \Illuminate\Support\Str::limit($class->title, 40) }}</p>
                    <p class="text-xs text-slate-500">{{ $class->starts_at?->format('d M, h:i A') }} · {{ $class->platform }}</p>
                </li>
            @empty
                <li class="text-slate-500">Nothing scheduled. <a href="{{ route('admin.live-classes.create') }}" class="font-semibold text-emerald-700">Schedule one</a>.</li>
            @endforelse
        </ul>
        <a href="{{ route('admin.live-classes.index') }}" class="mt-4 inline-block text-sm font-bold text-emerald-700">Manage live classes →</a>
    </div>
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold text-slate-800">Top courses</h2>
        <ul class="divide-y divide-slate-100 text-sm">
            @forelse ($courses as $course)
                <li class="flex items-center justify-between py-2.5">
                    <div>
                        <p class="font-semibold text-slate-800">{{ \Illuminate\Support\Str::limit($course->title, 45) }}</p>
                        <p class="text-xs text-slate-500">{{ $course->lessons_count }} lessons</p>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $course->enrollments_count }} students</span>
                </li>
            @empty
                <li class="py-3 text-slate-500">No courses assigned to you yet.</li>
            @endforelse
        </ul>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold text-slate-800">Latest students</h2>
        <ul class="divide-y divide-slate-100 text-sm">
            @forelse ($recent as $enrollment)
                <li class="flex items-center gap-3 py-2.5">
                    <img src="{{ $enrollment->user?->avatarUrl() }}" class="h-8 w-8 rounded-full" alt="">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-800">{{ $enrollment->user?->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $enrollment->course?->title }}</p>
                    </div>
                    <span class="ml-auto shrink-0 text-xs text-slate-500">{{ $enrollment->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="py-3 text-slate-500">No enrolments yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
