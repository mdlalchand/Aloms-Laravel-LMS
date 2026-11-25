@extends('layouts.admin')
@section('title', $category->exists ? 'Edit category' : 'New category')
@section('heading', $category->exists ? 'Edit category' : 'New category')
@section('content')
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      class="max-w-2xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($category->exists) @method('PUT') @endif

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Name</label>
        <input name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Parent category</label>
            <select name="parent_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="">— none —</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Icon (emoji)</label>
            <input name="icon" value="{{ old('icon', $category->icon) }}" placeholder="📘" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Description</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $category->description) }}</textarea>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Position</label>
            <input type="number" name="position" value="{{ old('position', $category->position ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <label class="flex items-center gap-2 self-end pb-3 text-sm font-semibold text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="rounded text-emerald-700"> Active
        </label>
    </div>
    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save category</button>
        <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</a>
    </div>
</form>
@endsection
