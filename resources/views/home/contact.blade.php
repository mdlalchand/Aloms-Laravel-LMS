@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="mx-auto grid max-w-5xl gap-10 px-4 py-16 md:grid-cols-2">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900">Talk to us</h1>
        <p class="mt-2 text-slate-600">Questions about a course, payment or your account? Send us a message — we usually reply within a few hours.</p>
        <div class="mt-8 space-y-3 text-sm">
            <p class="rounded-xl border border-slate-200 bg-white p-4"><b>Email:</b> {{ \App\Models\Setting::get('site_email', 'support@example.com') }}</p>
            <p class="rounded-xl border border-slate-200 bg-white p-4"><b>Phone:</b> {{ \App\Models\Setting::get('site_phone', '+880 1700-000000') }}</p>
            <p class="rounded-xl border border-slate-200 bg-white p-4"><b>Address:</b> {{ \App\Models\Setting::get('site_address', 'Dhaka, Bangladesh') }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('contact.send') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Name</label>
            <input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Message</label>
            <textarea name="message" rows="5" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">{{ old('message') }}</textarea>
        </div>
        <button class="w-full rounded-lg bg-emerald-700 px-4 py-3 font-semibold text-white hover:bg-emerald-800">Send message</button>
    </form>
</div>
@endsection
