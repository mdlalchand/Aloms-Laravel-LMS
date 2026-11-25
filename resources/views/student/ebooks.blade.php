@extends('layouts.student')
@section('title', 'My e-books')
@section('heading', 'My e-books')
@section('content')
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    @forelse ($purchases as $purchase)
        @if ($purchase->ebook)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <img src="{{ $purchase->ebook->coverUrl() }}" class="h-48 w-full object-cover" alt="">
                <div class="p-4">
                    <h3 class="line-clamp-2 font-bold text-slate-900">{{ $purchase->ebook->title }}</h3>
                    <a href="{{ route('student.ebooks.read', $purchase->ebook->slug) }}" class="mt-3 block rounded-lg bg-emerald-700 px-3 py-2 text-center text-sm font-bold text-white">Read</a>
                </div>
            </div>
        @endif
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center text-slate-500">
            You haven't purchased any e-book yet. <a href="{{ route('ebooks.index') }}" class="font-semibold text-emerald-700 underline">Browse e-books</a>
        </div>
    @endforelse
</div>
@endsection
