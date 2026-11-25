@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Overview')
@section('content')

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([
        ['Revenue', '৳'.number_format($stats['revenue'], 0), '💰', 'bg-emerald-50 text-emerald-700'],
        ['Students', number_format($stats['students']), '👥', 'bg-sky-50 text-sky-700'],
        ['Enrollments', number_format($stats['enrollments']), '🎓', 'bg-violet-50 text-violet-700'],
        ['Pending orders', number_format($stats['pending_orders']), '⏳', 'bg-amber-50 text-amber-700'],
    ] as [$label, $value, $icon, $class])
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl {{ $class }} text-lg">{{ $icon }}</span>
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</span>
            </div>
            <p class="mt-3 text-2xl font-extrabold text-slate-900">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="font-bold text-slate-900">Revenue (last 6 months)</h3>
        <div class="mt-6 flex h-48 items-end gap-4">
            @php($max = max(array_merge($chart['values'], [1])))
            @foreach ($chart['labels'] as $i => $label)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-[10px] font-semibold text-slate-500">৳{{ number_format($chart['values'][$i], 0) }}</span>
                    <div class="w-full rounded-t-lg bg-emerald-500/80" style="height: {{ max(4, ($chart['values'][$i] / $max) * 140) }}px"></div>
                    <span class="text-xs text-slate-500">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="font-bold text-slate-900">Top courses</h3>
        <ul class="mt-4 space-y-3 text-sm">
            @foreach ($topCourses as $course)
                <li class="flex items-center justify-between gap-2">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="truncate text-slate-700 hover:text-emerald-700">{{ $course->title }}</a>
                    <span class="shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold">{{ $course->enrollments_count }}</span>
                </li>
            @endforeach
        </ul>
    </section>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <h3 class="border-b border-slate-100 px-5 py-4 font-bold text-slate-900">Recent orders</h3>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @foreach ($recentOrders as $order)
                    <tr>
                        <td class="px-5 py-3"><a href="{{ route('admin.orders.show', $order->order_no) }}" class="font-semibold text-emerald-700">#{{ $order->order_no }}</a>
                            <p class="text-xs text-slate-500">{{ optional($order->user)->name }}</p></td>
                        <td class="px-5 py-3 text-right font-bold">৳{{ number_format($order->total) }}</td>
                        <td class="px-5 py-3 text-right"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $order->statusBadge() }}">{{ $order->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <h3 class="border-b border-slate-100 px-5 py-4 font-bold text-slate-900">New enrollments</h3>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                @foreach ($recentEnrollments as $enrollment)
                    <tr>
                        <td class="px-5 py-3">{{ optional($enrollment->user)->name }}
                            <p class="text-xs text-slate-500">{{ Str::limit(optional($enrollment->course)->title, 34) }}</p></td>
                        <td class="px-5 py-3 text-right text-xs text-slate-500">{{ $enrollment->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>

@if ($pendingReviews->count())
    <section class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-amber-900">Reviews awaiting approval ({{ $stats['pending_reviews'] }})</h3>
            <a href="{{ route('admin.reviews.index') }}" class="text-sm font-semibold text-amber-800 underline">Review all</a>
        </div>
        <ul class="mt-3 space-y-2 text-sm text-amber-900">
            @foreach ($pendingReviews as $review)
                <li class="rounded-lg bg-white/70 px-3 py-2">{{ $review->user->name }} · {{ $review->rating }}★ — {{ Str::limit($review->comment, 80) }}</li>
            @endforeach
        </ul>
    </section>
@endif
@endsection
