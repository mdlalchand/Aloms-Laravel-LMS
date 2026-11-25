@extends('layouts.admin')
@section('title', $branch->exists ? 'Edit branch' : 'New branch')
@section('heading', $branch->exists ? 'Edit branch · '.$branch->name : 'New branch')
@section('content')
<form method="POST" action="{{ $branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store') }}"
      class="max-w-2xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($branch->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Branch name</label>
            <input name="name" value="{{ old('name', $branch->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Short code</label>
            <input name="code" value="{{ old('code', $branch->code) }}" required placeholder="e.g. DHK-01" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm uppercase">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Phone</label>
            <input name="phone" value="{{ old('phone', $branch->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $branch->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Address</label>
            <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('address', $branch->address) }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Branch manager</label>
            <select name="manager_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">— none —</option>
                @foreach ($managers as $manager)
                    <option value="{{ $manager->id }}" @selected(old('manager_id', $branch->manager_id) == $manager->id)>{{ $manager->name }} ({{ $manager->email }})</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Create the manager account first with the role “Branch manager”.</p>
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $branch->exists ? $branch->is_active : true)) class="rounded text-emerald-700"> Branch is open
    </label>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save branch</button>
        <a href="{{ route('admin.branches.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</a>
    </div>
</form>
@endsection
