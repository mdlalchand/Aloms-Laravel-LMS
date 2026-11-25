@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="mx-auto grid max-w-5xl items-center gap-10 px-4 py-14 md:grid-cols-2">
    <div class="hidden md:block">
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">Welcome back 👋</h1>
        <p class="mt-3 text-slate-600">Continue your lessons, take exams and track your progress — all in one place.</p>
        <ul class="mt-6 space-y-3 text-sm text-slate-600">
            <li class="flex gap-2"><span class="text-emerald-700">✓</span> HD video lessons & class notes</li>
            <li class="flex gap-2"><span class="text-emerald-700">✓</span> MCQ exams with instant results</li>
            <li class="flex gap-2"><span class="text-emerald-700">✓</span> Certificates on completion</li>
        </ul>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
        <h2 class="text-2xl font-bold text-slate-900">Log in to your account</h2>
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Password</label>
                <input type="password" name="password" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-700"> Remember me
            </label>
            <button type="submit" class="w-full rounded-lg bg-emerald-700 px-4 py-3 font-semibold text-white hover:bg-emerald-800">Log in</button>
        </form>
        <p class="mt-5 text-center text-sm text-slate-600">New here?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:underline">Create a free account</a>
        </p>
        <div class="mt-5 rounded-lg bg-slate-50 p-3 text-xs text-slate-500">
            <b>Demo:</b> admin@lms.test / password &nbsp;•&nbsp; teacher@lms.test / password &nbsp;•&nbsp; student@lms.test / password
        </div>
    </div>
</div>
@endsection
