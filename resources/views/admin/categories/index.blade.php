@extends('layouts.admin')
@section('title', 'Categories')
@section('heading', 'Categories')
@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">+ New category</a>
@endsection
@section('content')
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">Name</th>
                <th class="px-5 py-3">Parent</th>
                <th class="px-5 py-3">Courses</th>
                <th class="px-5 py-3">Order</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($categories as $category)
                <tr>
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $category->icon }} {{ $category->name }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $category->parent?->name ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $category->courses_count }}</td>
                    <td class="px-5 py-3">{{ $category->position }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $category->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm font-semibold text-emerald-700">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="ml-3 text-sm font-semibold text-rose-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
