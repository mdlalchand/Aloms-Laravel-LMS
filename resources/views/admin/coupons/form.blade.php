@extends('layouts.admin')
@section('title', $coupon->exists ? 'Edit coupon' : 'New coupon')
@section('heading', $coupon->exists ? 'Edit coupon' : 'New coupon')
@section('content')
<form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
      class="max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($coupon->exists) @method('PUT') @endif
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Coupon code *</label>
        <input name="code" value="{{ old('code', $coupon->code) }}" required placeholder="EID2025" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm uppercase">
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Type</label>
            <select name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Percentage</option>
                <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed amount</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Value</label>
            <input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Min order</label>
            <input type="number" step="0.01" name="min_amount" value="{{ old('min_amount', $coupon->min_amount ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Usage limit (0 = ∞)</label>
            <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Starts at</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Expires at</label>
            <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
    </div>
    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded text-emerald-700"> Active</label>
    <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save coupon</button>
</form>
@endsection
