@extends('layouts.app')
@section('title', 'Verify certificate')
@section('content')
<div class="mx-auto max-w-2xl px-4 py-16">
    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
        @if ($certificate)
            <p class="text-5xl">🏅</p>
            <h1 class="mt-4 text-2xl font-extrabold text-emerald-700">Certificate verified</h1>
            <p class="mt-2 text-slate-600">This certificate is valid and was issued by {{ \App\Models\Setting::get('site_name', "Redwan's Method") }}.</p>
            <dl class="mt-6 space-y-2 text-left text-sm">
                <div class="flex justify-between rounded-lg bg-slate-50 px-4 py-2"><dt class="text-slate-500">Student</dt><dd class="font-semibold">{{ $certificate->user->name }}</dd></div>
                <div class="flex justify-between rounded-lg bg-slate-50 px-4 py-2"><dt class="text-slate-500">Course</dt><dd class="font-semibold">{{ $certificate->course->title }}</dd></div>
                <div class="flex justify-between rounded-lg bg-slate-50 px-4 py-2"><dt class="text-slate-500">Certificate ID</dt><dd class="font-mono font-semibold">{{ $certificate->code }}</dd></div>
                <div class="flex justify-between rounded-lg bg-slate-50 px-4 py-2"><dt class="text-slate-500">Issued</dt><dd class="font-semibold">{{ $certificate->issued_at->format('d M Y') }}</dd></div>
            </dl>
        @else
            <p class="text-5xl">❌</p>
            <h1 class="mt-4 text-2xl font-extrabold text-rose-700">Certificate not found</h1>
            <p class="mt-2 text-slate-600">No certificate exists with the ID <span class="font-mono">{{ $code }}</span>.</p>
        @endif
    </div>
</div>
@endsection
