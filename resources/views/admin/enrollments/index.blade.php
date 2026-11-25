@extends('layouts.admin')
@section('title', 'Enrollments')
@section('heading', 'Enrollments')
@section('content')
<div class="mb-4 grid gap-4 lg:grid-cols-[1fr_360px]">
    <form method="GET" class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Search student or course…" class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Search</button>
    </form>

    <form method="POST" action="{{ route('admin.enrollments.store') }}" class="flex gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        @csrf
        <select name="user_id" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs">
            <option value="">Student…</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}">{{ $student->name }}</option>
            @endforeach
        </select>
        <select name="course_id" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs">
            <option value="">Course…</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}">{{ Str::limit($course->title, 24) }}</option>
            @endforeach
        </select>
        <button class="shrink-0 rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white">Enroll</button>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Student</th><th class="px-5 py-3">Course</th><th class="px-5 py-3">Paid</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Enrolled</th><th class="px-5 py-3 text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($enrollments as $enrollment)
                <tr>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.users.show', $enrollment->user_id) }}" class="font-semibold text-slate-800 hover:text-emerald-700">{{ optional($enrollment->user)->name }}</a>
                        <p class="text-xs text-slate-500">{{ optional($enrollment->user)->email }}</p>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ Str::limit(optional($enrollment->course)->title, 40) }}</td>
                    <td class="px-5 py-3 font-semibold">৳{{ number_format($enrollment->price_paid) }}</td>
                    <td class="px-5 py-3"><span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ $enrollment->status }}</span></td>
                    <td class="px-5 py-3 text-xs text-slate-500">{{ optional($enrollment->created_at)->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}" onsubmit="return confirm('Remove enrollment?')">
                            @csrf @method('DELETE')<button class="text-sm font-semibold text-rose-600">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $enrollments->links() }}</div>
@endsection
