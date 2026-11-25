@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="mx-auto max-w-6xl px-4 py-12">
    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Checkout</h1>
    <p class="mt-1 text-slate-500">Pay with bKash / Nagad and submit the transaction ID — access is granted after verification.</p>

    <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1.6fr_1fr]">
        @csrf
        <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900">Billing details</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Full name</label>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Phone</label>
                    <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Address (optional)</label>
                    <input name="address" value="{{ old('address') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Note (optional)</label>
                    <input name="note" value="{{ old('note') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
            </div>
        </div>

        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900">Your order</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @foreach ($items as $item)
                    <li class="flex justify-between gap-3">
                        <span class="line-clamp-2 text-slate-600">{{ $item['title'] }}</span>
                        <span class="font-semibold text-slate-900">{{ $item['price'] <= 0 ? 'Free' : '৳'.number_format($item['price']) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>৳{{ number_format($subtotal, 2) }}</dd></div>
                @if ($discount > 0)<div class="flex justify-between text-emerald-700"><dt>Discount</dt><dd>−৳{{ number_format($discount, 2) }}</dd></div>@endif
                <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-extrabold"><dt>Total</dt><dd>৳{{ number_format($total, 2) }}</dd></div>
            </dl>
            <button class="mt-5 w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800">
                {{ $total <= 0 ? 'Complete free order' : 'Place order' }}
            </button>
        </aside>
    </form>
</div>
@endsection
