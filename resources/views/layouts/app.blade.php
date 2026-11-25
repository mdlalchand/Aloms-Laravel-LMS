<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\Setting::get('site_name', "Redwan's Method")) — {{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('site_tagline', 'Online learning platform for SSC, HSC & Admission preparation.'))">
    @include('partials.assets')
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-3 sm:gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-700 text-lg font-black text-white">R</span>
            <span class="hidden text-lg font-extrabold tracking-tight text-slate-900 sm:block">{{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</span>
        </a>

        <nav class="ml-4 hidden items-center gap-1 text-sm font-medium text-slate-600 lg:flex">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">Home</a>
            <a href="{{ route('courses.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">Courses</a>
            <a href="{{ route('ebooks.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">E-Books</a>
            <a href="{{ route('about') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">About</a>
            <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100 hover:text-slate-900">Contact</a>
        </nav>

        <form action="{{ route('courses.index') }}" class="ml-auto hidden max-w-xs flex-1 lg:block">
            <input name="q" value="{{ request('q') }}" placeholder="Search courses…"
                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100">
        </form>

        <div class="ml-auto flex items-center gap-2 lg:ml-0">
            <a href="{{ route('cart.index') }}" class="relative rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium hover:bg-slate-100">
                🛒
                @php($cartCount = count((array) session('cart', [])))
                @if($cartCount)
                    <span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-emerald-700 text-[10px] font-bold text-white">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                <div class="relative" x-data="{ open: false }">
                    <button onclick="this.nextElementSibling.classList.toggle('hidden')"
                            class="flex items-center gap-2 rounded-lg border border-slate-200 py-1.5 pl-1.5 pr-3 text-sm font-semibold hover:bg-slate-100">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-7 w-7 rounded-md object-cover">
                        <span class="hidden sm:block">{{ Str::limit(auth()->user()->name, 14) }}</span>
                    </button>
                    <div class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                        <div class="px-4 py-2 text-xs uppercase tracking-wide text-slate-500">{{ auth()->user()->role }}</div>
                        <a href="{{ auth()->user()->dashboardRoute() }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Dashboard</a>
                        <a href="{{ route('student.courses') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">My Courses</a>
                        <a href="{{ route('student.orders') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">My Orders</a>
                        <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full px-4 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">Log out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:block">Login</a>
                <a href="{{ route('register') }}" class="hidden whitespace-nowrap rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-800 sm:block">Sign up</a>
            @endauth
        </div>

        <button type="button" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')"
                aria-label="Toggle menu" aria-controls="mobile-nav"
                class="rounded-lg border border-slate-200 px-3 py-2 text-lg leading-none hover:bg-slate-100 lg:hidden">☰</button>
    </div>

    <div id="mobile-nav" class="hidden border-t border-slate-200 bg-white px-4 pb-4 pt-3 lg:hidden">
        <nav class="grid gap-1 text-sm font-medium text-slate-700">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">Home</a>
            <a href="{{ route('courses.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">Courses</a>
            <a href="{{ route('ebooks.index') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">E-Books</a>
            <a href="{{ route('about') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">About</a>
            <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 hover:bg-slate-100">Contact</a>
        </nav>
        @guest
            <div class="mt-3 grid gap-1 border-t border-slate-200 pt-3 text-sm font-semibold">
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-100">Login</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-emerald-700 px-3 py-2 text-center text-white hover:bg-emerald-800">Create free account</a>
            </div>
        @endguest
        <form action="{{ route('courses.index') }}" class="mt-3">
            <input name="q" value="{{ request('q') }}" placeholder="Search courses…"
                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100">
        </form>
    </div>

<main class="min-h-[70vh]">
    @include('partials.flash')
    @yield('content')
</main>

<footer class="mt-16 border-t border-slate-200 bg-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-700 font-black text-white">R</span>
                <span class="font-extrabold text-slate-900">{{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</span>
            </div>
            <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ \App\Models\Setting::get('site_tagline', 'Quality education for everyone, everywhere.') }}</p>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900">Learn</h4>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li><a class="hover:text-emerald-700" href="{{ route('courses.index') }}">All Courses</a></li>
                <li><a class="hover:text-emerald-700" href="{{ route('ebooks.index') }}">E-Books</a></li>
                <li><a class="hover:text-emerald-700" href="{{ route('student.exams.index') }}">Exams</a></li>
                <li><a class="hover:text-emerald-700" href="{{ route('certificates.verify', 'CERT-XXXXXXXX') }}">Verify Certificate</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900">Company</h4>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li><a class="hover:text-emerald-700" href="{{ route('about') }}">About us</a></li>
                <li><a class="hover:text-emerald-700" href="{{ route('contact') }}">Contact</a></li>
                <li><a class="hover:text-emerald-700" href="{{ route('login') }}">Student login</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900">Get in touch</h4>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li>{{ \App\Models\Setting::get('site_email', 'support@example.com') }}</li>
                <li>{{ \App\Models\Setting::get('site_phone', '+880 1700-000000') }}</li>
                <li>{{ \App\Models\Setting::get('site_address', 'Dhaka, Bangladesh') }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-slate-100 py-4 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ \App\Models\Setting::get('site_name', "Redwan's Method") }}. All rights reserved.
    </div>
</footer>
@stack('scripts')
</body>
</html>
