@extends('layouts.admin')
@section('title', 'Orders')
@section('heading', 'Orders')
@section('content')
<div class="mb-4 grid gap-4 sm:grid-cols-4">
    @foreach ([['All orders', $totals['all']], ['Paid', $totals['paid']], ['Pending', $totals['pending']], ['Revenue', '৳'.number_format($totals['revenue'], 0)]] as [$l, $v])
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ $l }}</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $v }}</p>
        </div>
    @endforeach
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Order no / name / phone" class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm">
    <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Any status</option>
        @foreach (['pending', 'paid', 'failed', 'cancelled', 'refunded'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Items</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Date</th><th class="px-5 py-3 text-right"></th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($orders as $order)
                <tr>
                    <td class="px-5 py-3 font-mono font-semibold text-slate-800">{{ $order->order_no }}</td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-700">{{ optional($order->user)->name }}</p>
                        <p class="text-xs text-slate-500">{{ $order->phone }}</p>
                    </td>
                    <td class="px-5 py-3">{{ $order->items_count }}</td>
                    <td class="px-5 py-3 font-bold">৳{{ number_format($order->total) }}</td>
                    <td class="px-5 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $order->statusBadge() }}">{{ $order->status }}</span></td>
                    <td class="px-5 py-3 text-xs text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('admin.orders.show', $order->order_no) }}" class="text-sm font-semibold text-emerald-700">Open</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
