@extends('layouts.portal')
@section('title', $student->name)
@section('heading', $student->name)
@section('content')
<div class="grid gap-5 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-bold text-slate-800">Course progress</h2>
            <div class="space-y-4">
                @forelse ($enrollments as $enrollment)
                    @php $progress = $enrollment->course?->progressFor($student) ?? 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <p class="font-semibold text-slate-800">{{ $enrollment->course?->title }}</p>
                            <span class="font-bold text-emerald-700">{{ $progress }}%</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-emerald-600" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Enrolled {{ $enrollment->created_at->format('d M Y') }} · status: {{ ucfirst($enrollment->status) }}</p>
                    </div>
                @empty
                    <p class="text-slate-500">No enrolments in your courses.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-bold text-slate-800">Recent exam attempts</h2>
            <table class="w-full text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="py-2">Exam</th><th class="py-2">Score</th><th class="py-2">Result</th><th class="py-2">When</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attempts as $attempt)
                        <tr>
                            <td class="py-2.5 font-semibold text-slate-800">{{ \Illuminate\Support\Str::limit($attempt->exam?->title, 35) }}</td>
                            <td class="py-2.5">{{ $attempt->score }} / {{ $attempt->exam?->total_marks }}</td>
                            <td class="py-2.5">
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $attempt->isPassed() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $attempt->isPassed() ? 'Passed' : 'Failed' }}
                                </span>
                            </td>
                            <td class="py-2.5 text-slate-500">{{ $attempt->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-3 text-slate-500">No attempts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
            <img src="{{ $student->avatarUrl() }}" class="mx-auto h-20 w-20 rounded-full" alt="">
            <p class="mt-3 font-bold text-slate-900">{{ $student->name }}</p>
            <p class="text-sm text-slate-600">{{ $student->email }}</p>
            @if ($student->phone)<p class="text-sm text-slate-600">{{ $student->phone }}</p>@endif
            @if ($student->branch)
                <p class="mt-2 inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">🏢 {{ $student->branch->name }}</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-800">Certificates</h2>
            @forelse ($certificates as $certificate)
                <a href="{{ route('certificates.verify', $certificate->code) }}" class="mb-2 block rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm">
                    <p class="font-semibold text-emerald-900">{{ $certificate->course?->title }}</p>
                    <p class="text-xs text-emerald-700">{{ $certificate->code }}</p>
                </a>
            @empty
                <p class="text-sm text-slate-600">No certificates issued yet.</p>
            @endforelse
        </div>

        <a href="{{ route('teacher.students') }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 shadow-sm">← All students</a>
    </div>
</div>
@endsection
