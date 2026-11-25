@extends('layouts.portal')
@section('title', 'Live classes')
@section('heading', 'My live classes')
@section('actions')
    <a href="{{ route('admin.live-classes.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ Schedule class</a>
@endsection
@section('content')
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Class</th><th class="px-5 py-3">Course</th><th class="px-5 py-3">Starts</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($classes as $class)
                <tr>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800">{{ $class->title }}</p>
                        <p class="text-xs text-slate-500">{{ $class->platform }} · {{ $class->duration_minutes }} min</p>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($class->course?->title, 40) }}</td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-700">{{ $class->starts_at?->format('d M Y, h:i A') }}</p>
                        <p class="text-xs text-slate-500">{{ $class->starts_at?->diffForHumans() }}</p>
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if ($class->meeting_url)
                            <a href="{{ $class->meeting_url }}" target="_blank" rel="noopener" class="text-sm font-bold text-emerald-700">Open room →</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">No classes scheduled for your courses.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $classes->links() }}</div>
@endsection
