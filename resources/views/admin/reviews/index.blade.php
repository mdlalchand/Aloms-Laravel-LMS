@extends('layouts.admin')
@section('title', 'Reviews')
@section('heading', 'Course reviews')
@section('content')
<div class="space-y-3">
    @forelse ($reviews as $review)
        <div class="flex flex-wrap items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <img src="{{ $review->user->avatarUrl() }}" class="h-10 w-10 rounded-full" alt="">
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-slate-900">{{ $review->user->name }} <span class="text-xs font-normal text-slate-500">on {{ optional($review->course)->title }}</span></p>
                <p class="text-amber-700 text-sm">{{ str_repeat('★', $review->rating) }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $review->comment }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $review->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $review->is_approved ? 'Approved' : 'Pending' }}</span>
                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                    @csrf<button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white">{{ $review->is_approved ? 'Unpublish' : 'Approve' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete review?')">
                    @csrf @method('DELETE')<button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">No reviews yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
