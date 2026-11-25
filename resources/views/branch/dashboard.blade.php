@extends('layouts.portal')
@section('title', 'Branch dashboard')
@section('heading', $branch->name)
@section('content')
@php $cur = \App\Models\Setting::get('currency', '৳'); @endphp

<p class="-mt-2 mb-4 text-sm text-slate-600">
    Code: <b>{{ $branch->code }}</b> · {{ $branch->address }} @if($branch->phone)· {{ $branch->phone }}@endif
</p>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    @foreach ([
        ['Students', $stats['students']],
        ['Teachers', $stats['teachers']],
        ['Enrolments', $stats['enrollments']],
        ['Collected', $cur.number_format($stats['revenue'])],
        ['Due', $cur.number_format($stats['pending'])],
    ] as [$label, $value])
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <h2 class="mb-4 font-bold text-slate-800">Branch collection (6 months)</h2>
        @php $max = max(1, $months->max('amount')); @endphp
        <div class="flex h-44 items-end gap-4">
            @foreach ($months as $month)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-xs font-bold text-slate-600">{{ number_format($month['amount']) }}</span>
                    <div class="w-full rounded-t-lg bg-emerald-600" style="height: {{ max(4, (int) ($month['amount'] / $max * 130)) }}px"></div>
                    <span class="text-xs text-slate-500">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold text-slate-800">Top courses</h2>
        <ul class="divide-y divide-slate-100 text-sm">
            @forelse ($topCourses as $row)
                <li class="py-2.5">
                    <p class="font-semibold text-slate-800">{{ \Illuminate\Support\Str::limit($row->title, 40) }}</p>
                    <p class="text-xs text-slate-500">{{ $row->total }} enrolments · {{ $cur }}{{ number_format($row->revenue) }}</p>
                </li>
            @empty
                <li class="py-3 text-slate-500">No enrolments in this branch yet.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-5 grid gap-5 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">New students</h2>
            <a href="{{ route('branch.students') }}" class="text-sm font-bold text-emerald-700">All →</a>
        </div>
        <ul class="divide-y divide-slate-100 text-sm">
            @forelse ($recentStudents as $student)
                <li class="flex items-center gap-3 py-2.5">
                    <img src="{{ $student->avatarUrl() }}" class="h-8 w-8 rounded-full" alt="">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-800">{{ $student->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $student->phone ?: $student->email }}</p>
                    </div>
                    <a href="{{ route('branch.students.show', $student) }}" class="ml-auto text-xs font-bold text-emerald-700">View</a>
                </li>
            @empty
                <li class="py-3 text-slate-500">No students assigned to this branch.</li>
            @endforelse
        </ul>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bold text-slate-800">Recent orders</h2>
            <a href="{{ route('branch.revenue') }}" class="text-sm font-bold text-emerald-700">Revenue →</a>
        </div>
        <ul class="divide-y divide-slate-100 text-sm">
            @forelse ($recentOrders as $order)
                <li class="flex items-center justify-between py-2.5">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $order->order_no }}</p>
                        <p class="text-xs text-slate-500">{{ $order->user?->name }} · {{ ucfirst($order->status) }}</p>
                    </div>
                    <span class="font-bold text-slate-800">{{ $cur }}{{ number_format($order->total) }}</span>
                </li>
            @empty
                <li class="py-3 text-slate-500">No orders yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
