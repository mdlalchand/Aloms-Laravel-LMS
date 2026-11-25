@extends('layouts.admin')
@section('title', 'Announcements')
@section('heading', 'Announcements')
@section('actions')
    <a href="{{ route('admin.announcements.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New announcement</a>
@endsection
@section('content')
<div class="space-y-3">
    @forelse ($announcements as $announcement)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="font-bold text-slate-900">{{ $announcement->title }}</h3>
                    <p class="text-xs text-slate-500">{{ optional($announcement->course)->title ?? 'All students' }} · {{ $announcement->created_at->format('d M Y') }}</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $announcement->body }}</p>
                </div>
                <div class="shrink-0 text-xs font-semibold">
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-emerald-700">Edit</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')<button class="ml-3 text-rose-600">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">No announcements yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $announcements->links() }}</div>
@endsection
