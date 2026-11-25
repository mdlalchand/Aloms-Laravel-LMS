@extends('layouts.portal')
@section('title', 'Earnings')
@section('heading', 'Earnings')
@section('content')
@php $cur = \App\Models\Setting::get('currency', '৳'); @endphp

<div class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Lifetime</p>
        <p class="mt-2 text-2xl font-black text-slate-900">{{ $cur }}{{ number_format($total) }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">This month</p>
        <p class="mt-2 text-2xl font-black text-slate-900">{{ $cur }}{{ number_format($months->last()['amount'] ?? 0) }}</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Avg. per enrolment</p>
        <p class="mt-2 text-2xl font-black text-slate-900">{{ $cur }}{{ number_format($rows->count() ? $total / max(1, $rows->total()) : 0) }}</p>
    </div>
</div>

<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="mb-4 font-bold text-slate-800">Last 6 months</h2>
    @php $max = max(1, $months->max('amount')); @endphp
    <div class="flex h-44 items-end gap-4">
        @foreach ($months as $month)
            <div class="flex flex-1 flex-col items-center gap-2">
                <span class="text-xs font-bold text-slate-600">{{ $cur }}{{ number_format($month['amount']) }}</span>
                <div class="w-full rounded-t-lg bg-emerald-600" style="height: {{ max(4, (int) ($month['amount'] / $max * 130)) }}px"></div>
                <span class="text-xs text-slate-500">{{ $month['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <h2 class="border-b border-slate-100 px-5 py-4 font-bold text-slate-800">Payment history</h2>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Student</th><th class="px-5 py-3">Course</th><th class="px-5 py-3">Amount</th><th class="px-5 py-3">Date</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($rows as $row)
                <tr>
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $row->user?->name }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($row->course?->title, 40) }}</td>
                    <td class="px-5 py-3 font-bold text-emerald-700">{{ $cur }}{{ number_format($row->price_paid) }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $row->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">No payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $rows->links() }}</div>
@endsection
