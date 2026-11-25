@extends('layouts.app')
@section('title', $course->title)
@section('meta_description', $course->meta_description ?: $course->subtitle)
@section('content')

<section class="bg-slate-900 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-[1.6fr_1fr]">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                @if ($course->category)
                    <a href="{{ route('categories.show', $course->category->slug) }}" class="rounded-full bg-emerald-500/20 px-3 py-1 text-emerald-300">{{ $course->category->name }}</a>
                @endif
                <span class="rounded-full bg-white/10 px-3 py-1 capitalize">{{ $course->level }}</span>
                <span class="rounded-full bg-white/10 px-3 py-1">{{ $course->language }}</span>
            </div>
            <h1 class="mt-4 text-3xl font-extrabold leading-tight sm:text-4xl">{{ $course->title }}</h1>
            <p class="mt-3 max-w-2xl text-slate-300">{{ $course->subtitle }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-300">
                <span class="font-semibold text-amber-400">★ {{ $course->rating ?: 'New' }} <span class="font-normal text-slate-400">({{ $reviews->count() }} reviews)</span></span>
                <span>👥 {{ number_format($course->students_count) }} students</span>
                <span>📚 {{ $course->totalLessons() }} lessons</span>
                <span>⏱ {{ intdiv($course->duration_minutes, 60) }}h {{ $course->duration_minutes % 60 }}m</span>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <img src="{{ $course->instructor->avatarUrl() ?? '' }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-white/20" alt="">
                <div>
                    <p class="text-sm font-bold">{{ $course->instructor->name ?? 'Redwan’s Method' }}</p>
                    <p class="text-xs text-slate-400">{{ $course->instructor->designation ?? 'Instructor' }}</p>
                </div>
            </div>
        </div>

        {{-- BUY BOX --}}
        <div class="lg:-mb-24">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                <img src="{{ $course->thumbnail_url }}" alt="" class="aspect-video w-full object-cover">
                <div class="p-5">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-slate-900">{{ $course->final_price <= 0 ? 'Free' : '৳'.number_format($course->final_price) }}</span>
                        @if ($course->has_discount)
                            <span class="text-slate-400 line-through">৳{{ number_format($course->price) }}</span>
                            <span class="rounded bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-700">-{{ $course->discount_percent }}%</span>
                        @endif
                    </div>

                    @if ($enrolled)
                        <a href="{{ route('student.learn', $course->slug) }}" class="mt-4 block rounded-xl bg-emerald-700 px-4 py-3 text-center font-bold text-white hover:bg-emerald-800">Continue learning →</a>
                        <div class="mt-3">
                            <div class="flex justify-between text-xs font-semibold text-slate-500"><span>Your progress</span><span>{{ $course->progressFor(auth()->user()) }}%</span></div>
                            <div class="mt-1 h-2 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $course->progressFor(auth()->user()) }}%"></div>
                            </div>
                        </div>
                    @elseif ($course->final_price <= 0)
                        <form method="POST" action="{{ route('courses.enroll-free', $course->slug) }}" class="mt-4">
                            @csrf
                            <button class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800">Enroll free</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('cart.add', ['course', $course->id]) }}" class="mt-4 space-y-2">
                            @csrf
                            <button class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white hover:bg-emerald-800">Add to cart</button>
                        </form>
                    @endif

                    <ul class="mt-5 space-y-2 text-sm text-slate-600">
                        <li class="flex gap-2"><span class="text-emerald-700">✓</span> Full lifetime access</li>
                        <li class="flex gap-2"><span class="text-emerald-700">✓</span> Downloadable notes & resources</li>
                        <li class="flex gap-2"><span class="text-emerald-700">✓</span> MCQ exams with instant results</li>
                        <li class="flex gap-2"><span class="text-emerald-700">✓</span> {{ $course->has_certificate ? 'Certificate of completion' : 'Progress tracking' }}</li>
                    </ul>

                    @guest
                        <p class="mt-4 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                            <a href="{{ route('login') }}" class="font-semibold underline">Log in</a> to enroll in this course.
                        </p>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-[1.6fr_1fr]">
    <div class="space-y-10">
        {{-- OUTCOMES --}}
        @if (!empty($course->outcomes))
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">What you'll learn</h2>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($course->outcomes as $outcome)
                        <li class="flex gap-2 text-sm text-slate-600"><span class="mt-0.5 text-emerald-700">✓</span>{{ $outcome }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- DESCRIPTION --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Course description</h2>
            <div class="prose prose-sm mt-3 max-w-none whitespace-pre-line text-slate-600">{{ $course->description }}</div>
            @if ($course->requirements)
                <h3 class="mt-6 font-bold text-slate-900">Requirements</h3>
                <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $course->requirements }}</p>
            @endif
        </section>

        {{-- CURRICULUM --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="text-xl font-bold text-slate-900">Course content</h2>
                <span class="text-sm text-slate-600">{{ $course->sections->count() }} sections · {{ $course->totalLessons() }} lessons</span>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($course->sections as $section)
                    <details class="group" {{ $loop->first ? 'open' : '' }}>
                        <summary class="flex cursor-pointer items-center justify-between gap-3 px-6 py-4 font-semibold text-slate-800 hover:bg-slate-50">
                            <span>{{ $section->title }}</span>
                            <span class="text-xs font-normal text-slate-500">{{ $section->lessons->count() }} lessons</span>
                        </summary>
                        <ul class="divide-y divide-slate-50 border-t border-slate-100">
                            @foreach ($section->lessons as $lesson)
                                <li class="flex items-center gap-3 px-6 py-3 text-sm">
                                    <span class="text-slate-400">
                                        @switch($lesson->type)
                                            @case('video') ▶ @break
                                            @case('pdf') 📄 @break
                                            @case('exam') 📝 @break
                                            @default 📖
                                        @endswitch
                                    </span>
                                    <span class="flex-1 text-slate-700">{{ $lesson->title }}</span>
                                    @if ($lesson->is_free_preview)
                                        <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-700">Preview</span>
                                    @endif
                                    <span class="text-xs text-slate-500">{{ $lesson->duration_minutes }}m</span>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- EXAMS --}}
        @if ($exams->count())
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Exams in this course</h2>
                <ul class="mt-4 space-y-3">
                    @foreach ($exams as $exam)
                        <li class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $exam->title }}</p>
                                <p class="text-xs text-slate-500">{{ $exam->duration_minutes }} min · {{ $exam->questions()->count() }} questions · {{ $exam->total_marks }} marks</p>
                            </div>
                            <a href="{{ route('student.exams.show', $exam) }}" class="rounded-lg bg-white px-3 py-2 text-xs font-bold text-emerald-700 shadow-sm ring-1 ring-slate-200 hover:bg-emerald-50">Details</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- REVIEWS --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">Student reviews</h2>
                <span class="text-sm font-semibold text-amber-700">★ {{ $course->rating ?: '—' }}/5</span>
            </div>

            @auth
                @if ($enrolled)
                    <form method="POST" action="{{ route('reviews.store', $course->slug) }}" class="mt-4 rounded-xl bg-slate-50 p-4">
                        @csrf
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-semibold text-slate-700">Your rating</label>
                            <select name="rating" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(optional($myReview)->rating === $i)>{{ $i }} ★</option>
                                @endfor
                            </select>
                        </div>
                        <textarea name="comment" rows="3" placeholder="Share your experience…" class="mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ optional($myReview)->comment }}</textarea>
                        <button class="mt-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Submit review</button>
                    </form>
                @endif
            @endauth

            <ul class="mt-6 space-y-4">
                @forelse ($reviews as $review)
                    <li class="flex gap-3">
                        <img src="{{ $review->user->avatarUrl() }}" class="h-10 w-10 rounded-full" alt="">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $review->user->name }}</p>
                            <p class="text-xs text-amber-700">{{ str_repeat('★', $review->rating) }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $review->comment }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-600">No reviews yet — be the first to review this course.</li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- SIDEBAR --}}
    <aside class="space-y-6">
        @if ($course->liveClasses->count())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-slate-900">Live classes</h3>
                <ul class="mt-3 space-y-3 text-sm">
                    @foreach ($course->liveClasses as $live)
                        <li class="rounded-xl bg-slate-50 p-3">
                            <p class="font-semibold text-slate-800">{{ $live->title }}</p>
                            <p class="text-xs text-slate-500">{{ optional($live->starts_at)->format('d M Y, h:i A') }} · {{ ucfirst($live->platform) }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($related->count())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-slate-900">Related courses</h3>
                <ul class="mt-4 space-y-4">
                    @foreach ($related as $rel)
                        <li>
                            <a href="{{ route('courses.show', $rel->slug) }}" class="flex gap-3 group">
                                <img src="{{ $rel->thumbnail_url }}" class="h-16 w-24 rounded-lg object-cover" alt="">
                                <div>
                                    <p class="line-clamp-2 text-sm font-semibold text-slate-800 group-hover:text-emerald-700">{{ $rel->title }}</p>
                                    <p class="mt-1 text-xs font-bold text-emerald-700">{{ $rel->final_price <= 0 ? 'Free' : '৳'.number_format($rel->final_price) }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </aside>
</div>
@endsection
