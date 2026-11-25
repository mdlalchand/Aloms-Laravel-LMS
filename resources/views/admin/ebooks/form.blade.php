@extends('layouts.admin')
@section('title', $ebook->exists ? 'Edit e-book' : 'New e-book')
@section('heading', $ebook->exists ? 'Edit e-book' : 'New e-book')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $ebook->exists ? route('admin.ebooks.update', $ebook) : route('admin.ebooks.store') }}"
      class="max-w-2xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($ebook->exists) @method('PUT') @endif
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Title *</label>
        <input name="title" value="{{ old('title', $ebook->title) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Description</label>
        <textarea name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $ebook->description) }}</textarea>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Price (৳)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $ebook->price ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Pages</label>
            <input type="number" name="pages" value="{{ old('pages', $ebook->pages ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Cover image</label>
            <input type="file" name="cover" accept="image/*" class="w-full text-xs">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">PDF file</label>
            <input type="file" name="file" accept=".pdf,.epub" class="w-full text-xs">
        </div>
    </div>
    <div class="flex gap-5 text-sm font-semibold text-slate-700">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_free" value="1" @checked(old('is_free', $ebook->is_free)) class="rounded text-emerald-700"> Free</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $ebook->is_published ?? true)) class="rounded text-emerald-700"> Published</label>
    </div>
    <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save e-book</button>
</form>
@endsection
