@extends('layouts.portal')
@section('title', $student->name)
@section('heading', $student->name)
@section('content')
@php $cur = \App\Models\Setting::get('currency', '৳'); @endphp

<div class="grid gap-5 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-800">Courses & progress</h2>
            @forelse ($enrollments as $enrollment)
                @php $p = $enrollment->course?->progressFor($student) ?? 0; @endphp
                <div class="mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-800">{{ $enrollment->course?->title }}</span>
                        <span class="font-bold text-emerald-700">{{ $p }}%</span>
                    </div>
                    <div class="mt-1.5 h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-emerald-600" style="width: {{ $p }}%"></div></div>
                    <p class="mt-1 text-xs text-slate-500">Enrolled {{ $enrollment->enrolled_at?->format('d M Y') ?? $enrollment->created_at->format('d M Y') }}</p>
                </div>
            @empty
                <p class="text-slate-500">No enrolments.</p>
            @endforelse
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-800">Exam attempts</h2>
            <table class="w-full text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="py-2">Exam</th><th class="py-2">Score</th><th class="py-2">Result</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attempts as $attempt)
                        <tr>
                            <td class="py-2.5 text-slate-800">{{ \Illuminate\Support\Str::limit($attempt->exam?->title, 40) }}</td>
                            <td class="py-2.5">{{ $attempt->score }} / {{ $attempt->exam?->total_marks }}</td>
                            <td class="py-2.5">
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $attempt->isPassed() ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $attempt->isPassed() ? 'Passed' : 'Failed' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-3 text-slate-500">No attempts.</td></tr>
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
            <p class="text-sm text-slate-600">{{ $student->phone }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-800">Orders</h2>
            <ul class="space-y-2 text-sm">
                @forelse ($orders as $order)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $order->order_no }}</p>
                            <p class="text-xs text-slate-500">{{ ucfirst($order->status) }} · {{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="font-bold">{{ $cur }}{{ number_format($order->total) }}</span>
                    </li>
                @empty
                    <li class="text-slate-500">No orders.</li>
                @endforelse
            </ul>
        </div>
        <a href="{{ route('branch.students') }}" class="block rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 shadow-sm">← All students</a>
    </div>
</div>
@endsection
