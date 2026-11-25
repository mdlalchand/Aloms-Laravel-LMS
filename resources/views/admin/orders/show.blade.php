@extends('layouts.admin')
@section('title', 'Order '.$order->order_no)
@section('heading', 'Order #'.$order->order_no)
@section('content')
<div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Items</h3>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->statusBadge() }}">{{ strtoupper($order->status) }}</span>
            </div>
            <table class="mt-4 w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="py-3"><span class="mr-2 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] uppercase text-slate-500">{{ $item->item_type }}</span>{{ $item->title }}</td>
                            <td class="py-3 text-right font-semibold">৳{{ number_format($item->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <dl class="mt-4 space-y-1 border-t border-slate-100 pt-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>৳{{ number_format($order->subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd>−৳{{ number_format($order->discount, 2) }}</dd></div>
                <div class="flex justify-between text-base font-extrabold"><dt>Total</dt><dd>৳{{ number_format($order->total, 2) }}</dd></div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Payments</h3>
            <div class="mt-3 space-y-2 text-sm">
                @forelse ($order->payments as $payment)
                    <div class="flex flex-wrap items-center gap-3 rounded-xl bg-slate-50 px-4 py-3">
                        <div>
                            <p class="font-semibold uppercase text-slate-700">{{ $payment->gateway }} · {{ $payment->transaction_id }}</p>
                            <p class="text-xs text-slate-500">from {{ $payment->sender_number }} · ৳{{ number_format($payment->amount) }}</p>
                        </div>
                        <span class="ml-auto rounded-full px-2 py-0.5 text-xs font-bold
                            {{ $payment->status === 'verified' ? 'bg-emerald-100 text-emerald-700' : ($payment->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $payment->status }}
                        </span>
                        @if ($payment->status === 'pending')
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="flex gap-2">
                                @csrf
                                <button name="action" value="verify" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white">Verify & grant access</button>
                                <button name="action" value="reject" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white">Reject</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-500">No payment submitted yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Customer</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Name</dt><dd class="font-semibold">{{ $order->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Phone</dt><dd class="font-semibold">{{ $order->phone }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd class="font-semibold">{{ $order->email }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Account</dt><dd class="font-semibold"><a href="{{ route('admin.users.show', $order->user_id) }}" class="text-emerald-700">{{ optional($order->user)->name }}</a></dd></div>
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-bold text-slate-900">Change status</h3>
            <form method="POST" action="{{ route('admin.orders.status', $order->order_no) }}" class="mt-3 space-y-3">
                @csrf
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @foreach (['pending', 'paid', 'failed', 'cancelled', 'refunded'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white">Update order</button>
                <p class="text-xs text-slate-500">Marking an order as <b>paid</b> automatically grants course & e-book access.</p>
            </form>
        </section>
    </div>
</div>
@endsection
