@extends('layouts.admin')
@section('title', 'Live classes')
@section('heading', 'Live classes')
@section('actions')
    <a href="{{ route('admin.live-classes.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ Schedule class</a>
@endsection
@section('content')
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex gap-2">
        <select name="course" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All courses</option>
            @foreach ($courses as $id => $title)
                <option value="{{ $id }}" @selected(request('course') == $id)>{{ \Illuminate\Support\Str::limit($title, 40) }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>
    <p class="text-sm text-slate-600">
        The “Join” button unlocks 10 minutes before the start time and closes 30 minutes after the class ends.
    </p>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">Class</th>
                <th class="px-5 py-3">Course</th>
                <th class="px-5 py-3">Starts</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($classes as $class)
                <tr>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800">{{ $class->title }}</p>
                        <p class="text-xs text-slate-500">{{ $class->platform }} · {{ $class->duration_minutes }} min
                            @if ($class->meeting_password) · pass: {{ $class->meeting_password }} @endif
                        </p>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($class->course?->title, 40) }}</td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-700">{{ $class->starts_at?->format('d M Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $class->starts_at?->format('h:i A') }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $class->statusBadge() }}">{{ $class->statusLabel() }}</span>
                        @unless ($class->is_published)
                            <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-500">Hidden</span>
                        @endunless
                    </td>
                    <td class="whitespace-nowrap px-5 py-3 text-right">
                        @if ($class->meeting_url)
                            <a href="{{ $class->meeting_url }}" target="_blank" rel="noopener" class="text-sm font-semibold text-slate-600">Open</a>
                        @endif
                        <a href="{{ route('admin.live-classes.edit', $class) }}" class="ml-3 text-sm font-semibold text-emerald-700">Edit</a>
                        <form method="POST" action="{{ route('admin.live-classes.destroy', $class) }}" class="inline" onsubmit="return confirm('Delete this class?')">
                            @csrf @method('DELETE')
                            <button class="ml-3 text-sm font-semibold text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No live classes scheduled yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $classes->links() }}</div>
@endsection
