@extends('layouts.admin')
@section('title', $user->name)
@section('heading', $user->name)
@section('actions')
    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">Edit user</a>
@endsection
@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_2fr]">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
        <img src="{{ $user->avatarUrl() }}" class="mx-auto h-24 w-24 rounded-full object-cover" alt="">
        <h3 class="mt-3 text-lg font-bold text-slate-900">{{ $user->name }}</h3>
        <p class="text-sm text-slate-600">{{ $user->email }}</p>
        <p class="mt-1 text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $user->role }}</p>
        <dl class="mt-5 space-y-2 text-left text-sm">
            <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2"><dt class="text-slate-500">Phone</dt><dd class="font-semibold">{{ $user->phone ?? '—' }}</dd></div>
            <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2"><dt class="text-slate-500">Joined</dt><dd class="font-semibold">{{ $user->created_at->format('d M Y') }}</dd></div>
            <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2"><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ $user->is_active ? 'Active' : 'Disabled' }}</dd></div>
        </dl>
    </div>

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <h3 class="border-b border-slate-100 px-5 py-4 font-bold text-slate-900">Enrollments ({{ $user->enrollments->count() }})</h3>
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse ($user->enrollments as $enrollment)
                    <li class="flex justify-between px-5 py-3">
                        <span>{{ optional($enrollment->course)->title }}</span>
                        <span class="text-slate-500">৳{{ number_format($enrollment->price_paid) }} · {{ $enrollment->created_at->format('d M Y') }}</span>
                    </li>
                @empty
                    <li class="px-5 py-3 text-slate-500">No enrollments.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <h3 class="border-b border-slate-100 px-5 py-4 font-bold text-slate-900">Exam attempts</h3>
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse ($user->examAttempts as $attempt)
                    <li class="flex justify-between px-5 py-3">
                        <span>{{ optional($attempt->exam)->title }}</span>
                        <span class="font-semibold {{ $attempt->isPassed() ? 'text-emerald-700' : 'text-rose-600' }}">{{ $attempt->score }} marks</span>
                    </li>
                @empty
                    <li class="px-5 py-3 text-slate-500">No attempts.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <h3 class="border-b border-slate-100 px-5 py-4 font-bold text-slate-900">Orders</h3>
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse ($user->orders as $order)
                    <li class="flex justify-between px-5 py-3">
                        <a href="{{ route('admin.orders.show', $order->order_no) }}" class="font-semibold text-emerald-700">#{{ $order->order_no }}</a>
                        <span>৳{{ number_format($order->total) }} · <span class="capitalize">{{ $order->status }}</span></span>
                    </li>
                @empty
                    <li class="px-5 py-3 text-slate-500">No orders.</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
