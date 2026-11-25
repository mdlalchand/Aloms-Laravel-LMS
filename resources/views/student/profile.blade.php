@extends('layouts.student')
@section('title', 'Profile')
@section('heading', 'Profile settings')
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <h3 class="font-bold text-slate-900">Personal information</h3>
        <div class="mt-4 flex items-center gap-4">
            <img src="{{ $user->avatarUrl() }}" class="h-16 w-16 rounded-full object-cover" alt="">
            <input type="file" name="avatar" class="text-sm">
        </div>
        <div class="mt-4 space-y-3">
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Name</label>
                <input name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
                <input value="{{ $user->email }}" disabled class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Phone</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Bio</label>
                <textarea name="bio" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('bio', $user->bio) }}</textarea>
            </div>
        </div>
        <button class="mt-5 rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save changes</button>
    </form>

    <form method="POST" action="{{ route('student.password.update') }}" class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <h3 class="font-bold text-slate-900">Change password</h3>
        <div class="mt-4 space-y-3">
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Current password</label>
                <input type="password" name="current_password" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">New password</label>
                <input type="password" name="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Confirm new password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            </div>
        </div>
        <button class="mt-5 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white">Update password</button>
    </form>
</div>
@endsection
