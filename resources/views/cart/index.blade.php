@extends('layouts.app')
@section('title', 'Cart')
@section('content')
<div class="mx-auto max-w-6xl px-4 py-12">
    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Your cart</h1>

    @if (! count($items))
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center">
            <p class="text-4xl">🛒</p>
            <h3 class="mt-3 text-lg font-bold text-slate-900">Your cart is empty</h3>
            <a href="{{ route('courses.index') }}" class="mt-5 inline-block rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white">Browse courses</a>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-[1.6fr_1fr]">
            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <img src="{{ $item['image'] }}" class="h-16 w-24 rounded-lg object-cover" alt="">
                        <div class="flex-1">
                            <p class="font-semibold text-slate-900">{{ $item['title'] }}</p>
                            <p class="text-xs uppercase tracking-wide text-slate-500">{{ $item['type'] }}</p>
                        </div>
                        <p class="font-bold text-slate-900">{{ $item['price'] <= 0 ? 'Free' : '৳'.number_format($item['price']) }}</p>
                        <form method="POST" action="{{ route('cart.remove', $item['key']) }}">
                            @csrf
                            <button class="rounded-lg p-2 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Remove">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-bold text-slate-900">Order summary</h2>

                <form method="POST" action="{{ route('cart.coupon') }}" class="mt-4 flex gap-2">
                    @csrf
                    <input name="code" placeholder="Coupon code" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                    <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Apply</button>
                </form>
                @if ($coupon)
                    <p class="mt-2 flex items-center justify-between text-xs text-emerald-700">
                        <span>Coupon <b>{{ $coupon->code }}</b> applied</span>
                        <form method="POST" action="{{ route('cart.coupon.remove') }}">@csrf<button class="underline">remove</button></form>
                    </p>
                @endif

                <dl class="mt-5 space-y-2 border-t border-slate-100 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="font-semibold">৳{{ number_format($subtotal, 2) }}</dd></div>
                    @if ($discount > 0)
                        <div class="flex justify-between text-emerald-700"><dt>Discount</dt><dd class="font-semibold">−৳{{ number_format($discount, 2) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-slate-100 pt-2 text-base"><dt class="font-bold">Total</dt><dd class="font-extrabold">৳{{ number_format($total, 2) }}</dd></div>
                </dl>

                <a href="{{ route('checkout.index') }}" class="mt-5 block rounded-xl bg-emerald-700 px-4 py-3 text-center font-bold text-white hover:bg-emerald-800">Proceed to checkout</a>
            </aside>
        </div>
    @endif
</div>
@endsection
