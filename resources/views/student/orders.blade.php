@extends('layouts.student')
@section('title', 'My orders')
@section('heading', 'My orders')
@section('content')
<div class="space-y-4">
    @forelse ($orders as $order)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <p class="font-bold text-slate-900">#{{ $order->order_no }}</p>
                    <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->statusBadge() }}">{{ strtoupper($order->status) }}</span>
                <span class="ml-auto font-extrabold text-slate-900">৳{{ number_format($order->total, 2) }}</span>
            </div>
            <ul class="mt-3 divide-y divide-slate-100 text-sm">
                @foreach ($order->items as $item)
                    <li class="flex justify-between py-2">
                        <span class="text-slate-600">{{ $item->title }}</span>
                        <span class="font-semibold">৳{{ number_format($item->price) }}</span>
                    </li>
                @endforeach
            </ul>
            @if ($order->status === 'pending')
                <a href="{{ route('checkout.success', $order->order_no) }}" class="mt-3 inline-block rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-white">Complete payment</a>
            @endif
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center text-slate-500">No orders yet.</div>
    @endforelse
</div>
@endsection
