@extends('layouts.admin')
@section('title', 'E-books')
@section('heading', 'E-books')
@section('actions')
    <a href="{{ route('admin.ebooks.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New e-book</a>
@endsection
@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($ebooks as $ebook)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <img src="{{ $ebook->coverUrl() }}" class="h-44 w-full object-cover" alt="">
            <div class="p-4">
                <h3 class="line-clamp-2 font-bold text-slate-900">{{ $ebook->title }}</h3>
                <p class="mt-1 text-sm font-semibold text-emerald-700">{{ $ebook->is_free ? 'Free' : '৳'.number_format($ebook->price) }}</p>
                <p class="text-xs text-slate-500">{{ $ebook->purchases_count }} purchases · {{ $ebook->pages }} pages</p>
                <div class="mt-3 flex gap-3 text-xs font-semibold">
                    <a href="{{ route('admin.ebooks.edit', $ebook) }}" class="text-emerald-700">Edit</a>
                    <form method="POST" action="{{ route('admin.ebooks.destroy', $ebook) }}" onsubmit="return confirm('Delete e-book?')">
                        @csrf @method('DELETE')<button class="text-rose-600">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="mt-4">{{ $ebooks->links() }}</div>
@endsection
