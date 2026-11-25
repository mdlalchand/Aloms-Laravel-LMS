@extends('layouts.portal')
@section('title', $child->name.' — report card')
@section('heading', $child->name)
@section('content')
@php $cur = \App\Models\Setting::get('currency', '৳'); @endphp

<div class="grid gap-5 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-bold text-slate-800">Enrolled courses</h2>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{{ (int) $progress }}% overall</span>
            </div>
            <div class="space-y-4">
                @forelse ($enrollments as $enrollment)
                    @php $p = $enrollment->course?->progressFor($child) ?? 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <a href="{{ route('courses.show', $enrollment->course?->slug) }}" class="font-semibold text-slate-800 hover:text-emerald-700">{{ $enrollment->course?->title }}</a>
                            <span class="font-bold text-emerald-700">{{ $p }}%</span>
                        </div>
                        <div class="mt-1.5 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-emerald-600" style="width: {{ $p }}%"></div></div>
                    </div>
                @empty
                    <p class="text-slate-500">Not enrolled in any course yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-800">Exam results</h2>
            <table class="w-full text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="py-2">Exam</th><th class="py-2">Course</th><th class="py-2">Score</th><th class="py-2">Result</th><th class="py-2">Date</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attempts as $attempt)
                        <tr>
                            <td class="py-2.5 font-semibold text-slate-800">{{ \Illuminate\Support\Str::limit($attempt->exam?->title, 30) }}</td>
                            <td class="py-2.5 text-slate-600">{{ \Illuminate\Support\Str::limit($attempt->exam?->course?->title, 25) }}</td>
                            <td class="py-2.5">{{ $attempt->score }} / {{ $attempt->exam?->total_marks }}</td>
                            <td class="py-2.5">
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $attempt->isPassed() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $attempt->isPassed() ? 'Passed' : 'Failed' }}</span>
                            </td>
                            <td class="py-2.5 text-slate-500">{{ $attempt->created_at->format('d M') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-3 text-slate-500">No exams taken yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
            <img src="{{ $child->avatarUrl() }}" class="mx-auto h-20 w-20 rounded-full" alt="">
            <p class="mt-3 font-bold text-slate-900">{{ $child->name }}</p>
            <p class="text-sm text-slate-600">{{ $child->email }}</p>
            @if ($child->phone)<p class="text-sm text-slate-600">{{ $child->phone }}</p>@endif
            @if ($child->branch)
                <p class="mt-2 inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">🏢 {{ $child->branch->name }}</p>
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
                <p class="text-sm text-slate-600">No certificates yet.</p>
            @endforelse
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-800">Payments</h2>
            <ul class="space-y-2 text-sm">
                @forelse ($orders as $order)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $order->order_no }}</p>
                            <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="font-bold text-slate-800">{{ $cur }}{{ number_format($order->total) }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">No payments yet.</li>
                @endforelse
            </ul>
        </div>

        <a href="{{ route('guardian.dashboard') }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 shadow-sm">← All children</a>
    </div>
</div>
@endsection
