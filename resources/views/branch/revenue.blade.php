@extends('layouts.portal')
@section('title', 'Branch revenue')
@section('heading', 'Revenue — '.$branch->name)
@section('content')
@php $cur = \App\Models\Setting::get('currency', '৳'); @endphp

<div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Total collected (paid orders)</p>
    <p class="mt-1 text-3xl font-black text-emerald-900">{{ $cur }}{{ number_format($total) }}</p>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Student</th><th class="px-5 py-3">Items</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Date</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($orders as $order)
                <tr>
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $order->order_no }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $order->user?->name }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $order->items->count() }}</td>
                    <td class="px-5 py-3 font-bold text-slate-800">{{ $cur }}{{ number_format($order->total) }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold
                            {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No orders yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
