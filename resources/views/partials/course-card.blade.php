@php($c = $course)
<article class="group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <a href="{{ route('courses.show', $c->slug) }}" class="relative block aspect-[16/10] overflow-hidden bg-slate-100">
        <img src="{{ $c->thumbnail_url }}" alt="{{ $c->title }}" class="h-full w-full object-cover transition group-hover:scale-105">
        @if($c->has_discount)
            <span class="absolute left-3 top-3 rounded-md bg-rose-600 px-2 py-1 text-xs font-bold text-white">-{{ $c->discount_percent }}%</span>
        @endif
        <span class="absolute right-3 top-3 rounded-md bg-white/90 px-2 py-1 text-xs font-semibold capitalize text-slate-700">{{ $c->level }}</span>
    </a>
    <div class="flex flex-1 flex-col p-4">
        @if($c->category)
            <a href="{{ route('categories.show', $c->category->slug) }}" class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $c->category->name }}</a>
        @endif
        <h3 class="mt-1 line-clamp-2 font-bold leading-snug text-slate-900">
            <a href="{{ route('courses.show', $c->slug) }}" class="hover:text-emerald-700">{{ $c->title }}</a>
        </h3>
        <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $c->subtitle }}</p>

        <div class="mt-3 flex items-center gap-3 text-xs text-slate-500">
            <span>📚 {{ $c->lesson_count }} lessons</span>
            <span>👥 {{ number_format($c->students_count) }}</span>
            <span class="ml-auto font-semibold text-amber-700">★ {{ $c->rating ?: '—' }}</span>
        </div>

        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
            <div class="flex items-baseline gap-2">
                <span class="text-lg font-extrabold text-slate-900">{{ $c->final_price <= 0 ? 'Free' : '৳'.number_format($c->final_price) }}</span>
                @if($c->has_discount)
                    <span class="text-sm text-slate-600 line-through">৳{{ number_format($c->price) }}</span>
                @endif
            </div>
            <span class="text-xs font-medium text-slate-500">{{ optional($c->instructor)->name }}</span>
        </div>
    </div>
</article>
