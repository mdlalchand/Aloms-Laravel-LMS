@extends('layouts.app')
@section('content')

{{-- HERO SLIDER --}}
@php
    $slides = $sliders ?? collect();
@endphp
@if ($slides->count())
<section class="relative overflow-hidden bg-slate-900 text-white">
    <div id="heroSlides" class="relative">
        @foreach ($slides as $i => $slide)
            @php
                $theme = $slide->theme === 'light'
                    ? 'from-white via-slate-50 to-emerald-50 text-slate-900'
                    : 'from-slate-900 via-slate-900 to-emerald-950 text-white';
                $sub = $slide->theme === 'light' ? 'text-slate-600' : 'text-slate-300';
                $badge = $slide->theme === 'light'
                    ? 'border-emerald-300 bg-emerald-100 text-emerald-800'
                    : 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300';
            @endphp
            <div class="hero-slide {{ $i === 0 ? '' : 'hidden' }} bg-gradient-to-br {{ $theme }}" data-slide="{{ $i }}">
                <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
                <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 lg:grid-cols-2 lg:py-20">
                    <div>
                        @if ($slide->badge)
                            <span class="inline-flex items-center gap-2 rounded-full border {{ $badge }} px-3 py-1 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span> {{ $slide->badge }}
                            </span>
                        @endif
                        <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl">{{ $slide->title }}</h1>
                        @if ($slide->subtitle)
                            <p class="mt-4 max-w-xl text-lg {{ $sub }}">{{ $slide->subtitle }}</p>
                        @endif
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ $slide->button_url ?: route('courses.index') }}" class="rounded-xl bg-emerald-700 px-6 py-3 font-bold text-white shadow-lg shadow-emerald-600/20 hover:bg-emerald-700">{{ $slide->button_text ?: 'Explore courses' }}</a>
                            <a href="{{ auth()->check() ? auth()->user()->dashboardRoute() : route('register') }}" class="rounded-xl border px-6 py-3 font-semibold {{ $slide->theme === 'light' ? 'border-slate-300 text-slate-800 hover:bg-white' : 'border-white/20 text-white hover:bg-white/10' }}">Create free account</a>
                        </div>
                        <dl class="mt-10 grid max-w-md grid-cols-3 gap-4">
                            @foreach ([['Students', $stats['students']], ['Courses', $stats['courses']], ['Teachers', $stats['instructors']]] as [$label, $value])
                                <div>
                                    <dt class="text-xs uppercase tracking-wide {{ $slide->theme === 'light' ? 'text-slate-500' : 'text-slate-400' }}">{{ $label }}</dt>
                                    <dd class="text-2xl font-extrabold {{ $slide->theme === 'light' ? 'text-slate-900' : 'text-white' }}">{{ number_format($value) }}+</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                    <div class="relative">
                        <div class="rounded-3xl border {{ $slide->theme === 'light' ? 'border-slate-200 bg-white' : 'border-white/10 bg-white/5' }} p-3 shadow-2xl backdrop-blur">
                            <img src="{{ $slide->image ? (str_starts_with($slide->image, 'http') ? $slide->image : asset('storage/'.$slide->image)) : 'https://picsum.photos/seed/slide'.$slide->id.'/1200/800' }}"
                                 alt="{{ $slide->title }}" class="h-72 w-full rounded-2xl object-cover sm:h-96">
                        </div>
                        <div class="absolute -bottom-6 -left-4 rounded-2xl border border-white/10 bg-slate-900/90 px-4 py-3 shadow-xl backdrop-blur">
                            <p class="text-2xl font-extrabold text-emerald-400">{{ number_format($stats['lessons']) }}+</p>
                            <p class="text-xs text-slate-300">video lessons</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($slides->count() > 1)
            <button type="button" onclick="heroGo(-1)" aria-label="Previous slide"
                class="absolute left-3 top-1/2 z-10 hidden -translate-y-1/2 rounded-full bg-white/90 p-2 text-slate-900 shadow hover:bg-white sm:block">‹</button>
            <button type="button" onclick="heroGo(1)" aria-label="Next slide"
                class="absolute right-3 top-1/2 z-10 hidden -translate-y-1/2 rounded-full bg-white/90 p-2 text-slate-900 shadow hover:bg-white sm:block">›</button>
            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                @foreach ($slides as $i => $slide)
                    <button type="button" onclick="heroShow({{ $i }})" aria-label="Slide {{ $i + 1 }}"
                        class="hero-dot h-2.5 w-2.5 rounded-full transition {{ $i === 0 ? 'bg-emerald-500 w-6' : 'bg-white/60' }}"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    let heroIndex = 0, heroTimer = null;
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroDots = document.querySelectorAll('.hero-dot');
    function heroShow(n) {
        if (!heroSlides.length) return;
        heroIndex = (n + heroSlides.length) % heroSlides.length;
        heroSlides.forEach((el, i) => el.classList.toggle('hidden', i !== heroIndex));
        heroDots.forEach((el, i) => {
            el.classList.toggle('bg-emerald-500', i === heroIndex);
            el.classList.toggle('w-6', i === heroIndex);
            el.classList.toggle('bg-white/60', i !== heroIndex);
        });
        heroRestart();
    }
    function heroGo(dir) { heroShow(heroIndex + dir); }
    function heroRestart() {
        if (heroTimer) clearInterval(heroTimer);
        if (heroSlides.length > 1) heroTimer = setInterval(() => heroShow(heroIndex + 1), 6000);
    }
    heroRestart();
</script>
@endpush
@else
{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 text-white">
    <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 left-1/3 h-96 w-96 rounded-full bg-teal-400/10 blur-3xl"></div>
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 lg:grid-cols-2">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span> Online batches are ongoing — book your seat
            </span>
            <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl">
                Learn smarter with <span class="text-emerald-400">Redwan's Method</span>
            </h1>
            <p class="mt-4 max-w-xl text-lg text-slate-300">
                Class 9–10 (SSC), HSC and admission preparation with the country's best teachers.
                Video lessons, class notes, MCQ exams and lifetime access.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('courses.index') }}" class="rounded-xl bg-emerald-500 px-6 py-3 font-bold text-slate-950 shadow-lg shadow-emerald-500/20 hover:bg-emerald-400">Explore courses</a>
                <a href="{{ route('register') }}" class="rounded-xl border border-white/20 px-6 py-3 font-semibold text-white hover:bg-white/10">Create free account</a>
            </div>
            <dl class="mt-10 grid max-w-md grid-cols-3 gap-4">
                @foreach ([['Students', $stats['students']], ['Courses', $stats['courses']], ['Teachers', $stats['instructors']]] as [$label, $value])
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                        <dd class="text-2xl font-extrabold text-white">{{ number_format($value) }}+</dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <div class="relative">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-3 shadow-2xl backdrop-blur">
                <img src="https://picsum.photos/seed/lmshero/1200/800" alt="Learning" class="h-72 w-full rounded-2xl object-cover sm:h-96">
            </div>
            <div class="absolute -bottom-6 -left-4 rounded-2xl border border-white/10 bg-slate-900/90 px-4 py-3 shadow-xl backdrop-blur">
                <p class="text-2xl font-extrabold text-emerald-400">{{ number_format($stats['lessons']) }}+</p>
                <p class="text-xs text-slate-300">video lessons</p>
            </div>
        </div>
    </div>
</section>

@endif

{{-- CATEGORIES --}}
<section class="mx-auto max-w-7xl px-4 py-16">
    <div class="flex items-end justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Choose your learning path</h2>
            <p class="mt-1 text-slate-500">Diverse courses designed to help you excel in academics & admissions.</p>
        </div>
        <a href="{{ route('courses.index') }}" class="hidden text-sm font-semibold text-emerald-700 hover:underline sm:block">See all courses →</a>
    </div>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-emerald-50 text-2xl">{{ $category->icon ?: '📘' }}</span>
                <div>
                    <h3 class="font-bold text-slate-900 group-hover:text-emerald-700">{{ $category->name }}</h3>
                    <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $category->description }}</p>
                    <span class="mt-2 inline-block text-xs font-semibold text-emerald-700">{{ $category->publishedCourses()->count() }} courses →</span>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- FEATURED --}}
@if ($featured->count())
<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Featured courses</h2>
        <p class="mt-1 text-slate-500">Hand-picked by our academic team.</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $course)
                @include('partials.course-card', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- STATS BANNER --}}
<section class="bg-emerald-700">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 text-center text-white sm:grid-cols-4">
        @foreach ([['Students', $stats['students']], ['Courses', $stats['courses']], ['Teachers', $stats['instructors']], ['Lessons', $stats['lessons']]] as [$label, $value])
            <div>
                <p class="text-3xl font-extrabold">{{ number_format($value) }}+</p>
                <p class="text-sm text-emerald-100">{{ $label }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- LATEST --}}
<section class="mx-auto max-w-7xl px-4 py-16">
    <div class="flex items-end justify-between">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Latest courses</h2>
        <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">Browse all →</a>
    </div>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($latest as $course)
            @include('partials.course-card', ['course' => $course])
        @endforeach
    </div>
</section>

{{-- REVIEWS --}}
@if ($reviews->count())
<section class="bg-slate-100 py-16">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">What our students say</h2>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @foreach ($reviews as $review)
                <figure class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-amber-700">{{ str_repeat('★', $review->rating) }}<span class="text-slate-500">{{ str_repeat('★', 5 - $review->rating) }}</span></div>
                    <blockquote class="mt-3 line-clamp-4 text-sm leading-relaxed text-slate-600">“{{ $review->comment }}”</blockquote>
                    <figcaption class="mt-4 flex items-center gap-3">
                        <img src="{{ $review->user->avatarUrl() }}" class="h-9 w-9 rounded-full" alt="">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $review->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ Str::limit($review->course->title, 32) }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- EBOOKS --}}
@if ($ebooks->count())
<section class="mx-auto max-w-7xl px-4 py-16">
    <div class="flex items-end justify-between">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">E-books & notes</h2>
        <a href="{{ route('ebooks.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">All e-books →</a>
    </div>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($ebooks as $ebook)
            <a href="{{ route('ebooks.show', $ebook->slug) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md">
                <img src="{{ $ebook->coverUrl() }}" class="h-48 w-full object-cover" alt="">
                <div class="p-4">
                    <h3 class="line-clamp-2 font-bold text-slate-900 group-hover:text-emerald-700">{{ $ebook->title }}</h3>
                    <p class="mt-1 text-sm font-semibold text-emerald-700">{{ $ebook->is_free ? 'Free' : '৳'.number_format($ebook->price) }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- CTA --}}
<section class="mx-auto max-w-7xl px-4 pb-4">
    <div class="rounded-3xl bg-slate-900 px-8 py-14 text-center text-white">
        <h2 class="text-3xl font-extrabold">Ready to start learning?</h2>
        <p class="mx-auto mt-3 max-w-xl text-slate-300">Join thousands of students preparing for SSC, HSC and university admission with Redwan's Method.</p>
        <a href="{{ route('register') }}" class="mt-7 inline-block rounded-xl bg-emerald-500 px-7 py-3 font-bold text-slate-950 hover:bg-emerald-400">Get started for free</a>
    </div>
</section>
@endsection
