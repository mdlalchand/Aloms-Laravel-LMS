@extends('layouts.admin')
@section('title', 'Exams')
@section('heading', 'Exams')
@section('actions')
    <a href="{{ route('admin.exams.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New exam</a>
@endsection
@section('content')
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">Exam</th>
                <th class="px-5 py-3">Course</th>
                <th class="px-5 py-3">Questions</th>
                <th class="px-5 py-3">Marks</th>
                <th class="px-5 py-3">Duration</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($exams as $exam)
                <tr>
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $exam->title }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ optional($exam->course)->title ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $exam->questions_count }}</td>
                    <td class="px-5 py-3">{{ $exam->total_marks }}</td>
                    <td class="px-5 py-3">{{ $exam->duration_minutes }}m</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $exam->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $exam->is_published ? 'Published' : 'Draft' }}</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-sm font-semibold text-emerald-700">Questions</a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="ml-3 text-sm font-semibold text-slate-600">Edit</a>
                        <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" class="inline" onsubmit="return confirm('Delete exam?')">
                            @csrf @method('DELETE')<button class="ml-3 text-sm font-semibold text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $exams->links() }}</div>
@endsection
