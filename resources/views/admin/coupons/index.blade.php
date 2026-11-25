@extends('layouts.admin')
@section('title', 'Coupons')
@section('heading', 'Coupons')
@section('actions')
    <a href="{{ route('admin.coupons.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New coupon</a>
@endsection
@section('content')
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Code</th><th class="px-5 py-3">Discount</th><th class="px-5 py-3">Min order</th><th class="px-5 py-3">Used</th><th class="px-5 py-3">Expires</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($coupons as $coupon)
                <tr>
                    <td class="px-5 py-3 font-mono font-bold text-slate-800">{{ $coupon->code }}</td>
                    <td class="px-5 py-3">{{ $coupon->type === 'percent' ? $coupon->value.'%' : '৳'.$coupon->value }}</td>
                    <td class="px-5 py-3">৳{{ $coupon->min_amount }}</td>
                    <td class="px-5 py-3">{{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ optional($coupon->expires_at)->format('d M Y') ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $coupon->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-sm font-semibold text-emerald-700">Edit</a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete coupon?')">
                            @csrf @method('DELETE')<button class="ml-3 text-sm font-semibold text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection
