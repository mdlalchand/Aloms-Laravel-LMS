@extends('layouts.admin')
@section('title', $user->exists ? 'Edit user' : 'New user')
@section('heading', $user->exists ? 'Edit user · '.$user->name : 'Add a user')
@section('content')
<form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}"
      class="max-w-2xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($user->exists) @method('PUT') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Name</label>
            <input name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Phone</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Role</label>
            <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Branch</label>
            <select name="branch_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">— none —</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $user->branch_id) == $branch->id)>{{ $branch->name }} ({{ $branch->code }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Designation</label>
            <input name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Physics Teacher" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">
                {{ $user->exists ? 'New password (leave blank to keep current)' : 'Password' }}
            </label>
            <input type="text" name="password" @required(! $user->exists) class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm" placeholder="Minimum 8 characters">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Bio</label>
            <textarea name="bio" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('bio', $user->bio) }}</textarea>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
        <p class="mb-2 text-sm font-bold text-slate-700">Link guardian to students</p>
        <p class="mb-2 text-xs text-slate-500">Only used when the role is “Guardian / Parent”. A guardian can watch several children.</p>
        <select name="children[]" multiple size="6" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected(in_array($student->id, old('children', $user->exists ? $user->children->pluck('id')->all() : [])))>
                    {{ $student->name }} — {{ $student->email }}
                </option>
            @endforeach
        </select>
    </div>

    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded text-emerald-700"> Account active
    </label>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save user</button>
        <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</a>
    </div>
</form>
@endsection
