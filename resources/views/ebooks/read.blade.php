@extends('layouts.app')
@section('title', $ebook->title)
@section('content')
<div class="mx-auto max-w-5xl px-4 py-8">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900">{{ $ebook->title }}</h1>
        @if ($ebook->file)
            <a href="{{ asset('storage/'.$ebook->file) }}" download class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Download PDF</a>
        @endif
    </div>
    @if ($ebook->file)
        <iframe src="{{ asset('storage/'.$ebook->file) }}" class="h-[80vh] w-full rounded-xl border border-slate-200 bg-white"></iframe>
    @else
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-16 text-center text-slate-500">
            This e-book file has not been uploaded yet.
        </div>
    @endif
</div>
@endsection
