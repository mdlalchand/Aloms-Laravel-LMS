@extends('layouts.student')
@section('title', 'Certificates')
@section('heading', 'Certificates')
@section('content')
<div class="grid gap-5 sm:grid-cols-2">
    @forelse ($certificates as $certificate)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-3xl">🏅</p>
            <h3 class="mt-2 font-bold text-slate-900">{{ $certificate->course->title }}</h3>
            <p class="mt-1 text-xs font-mono text-slate-500">{{ $certificate->code }}</p>
            <p class="mt-1 text-xs text-slate-500">Issued {{ optional($certificate->issued_at)->format('d M Y') }}</p>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('student.certificates.show', $certificate->course->slug) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">View</a>
                <a href="{{ route('certificates.verify', $certificate->code) }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Verify</a>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center text-slate-500">
            Complete a course to earn your first certificate.
        </div>
    @endforelse
</div>
@endsection
