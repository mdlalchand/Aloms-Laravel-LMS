@extends('layouts.student')
@section('title', 'Certificate')
@section('heading', 'Certificate')
@section('content')
<div class="mx-auto max-w-4xl">
    <div class="rounded-2xl border-[10px] border-double border-emerald-700 bg-white p-10 text-center shadow-lg">
        <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-700">Certificate of completion</p>
        <h1 class="mt-6 text-3xl font-extrabold text-slate-900">{{ auth()->user()->name }}</h1>
        <p class="mt-4 text-slate-600">has successfully completed the course</p>
        <h2 class="mt-2 text-2xl font-bold text-emerald-800">{{ $course->title }}</h2>
        <div class="mt-8 flex items-end justify-between text-left text-xs text-slate-500">
            <div>
                <p class="border-t border-slate-300 pt-1 font-semibold text-slate-700">{{ optional($course->instructor)->name }}</p>
                <p>Instructor</p>
            </div>
            <div class="text-center">
                <p class="font-mono text-sm font-bold text-slate-800">{{ $certificate->code }}</p>
                <p>Certificate ID</p>
                <p class="mt-1">{{ optional($certificate->issued_at)->format('d F Y') }}</p>
            </div>
            <div class="text-right">
                <p class="border-t border-slate-300 pt-1 font-semibold text-slate-700">{{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</p>
                <p>Verified issuer</p>
            </div>
        </div>
    </div>
    <div class="mt-4 flex justify-center gap-3 print:hidden">
        <button onclick="window.print()" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white">🖨 Print / Save PDF</button>
        <a href="{{ route('certificates.verify', $certificate->code) }}" class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700">Public verification</a>
    </div>
</div>
@endsection
