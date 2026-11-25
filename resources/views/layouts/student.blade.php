<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<div class="flex min-h-screen">
    {{-- SIDEBAR --}}
    <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-slate-200 bg-white p-4 lg:flex">
        <a href="{{ route('home') }}" class="flex items-center gap-2 px-2 py-2">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-700 font-black text-white">R</span>
            <span class="font-extrabold text-slate-900">{{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</span>
        </a>

        @php
            $nav = [
                ['student.dashboard', 'Dashboard', '🏠'],
                ['student.courses', 'My courses', '📚'],
                ['student.exams.index', 'Exams', '📝'],
                ['student.ebooks', 'E-books', '📖'],
                ['student.certificates', 'Certificates', '🏅'],
                ['student.orders', 'Orders', '🧾'],
                ['student.profile', 'Profile', '👤'],
            ];
        @endphp

        <nav class="mt-6 space-y-1 text-sm font-medium">
            @foreach ($nav as [$route, $label, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 {{ request()->routeIs(str_replace('.index','.*',$route)) || request()->routeIs($route) ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>{{ $icon }}</span> {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="mt-auto space-y-1 border-t border-slate-100 pt-4 text-sm">
            @if (auth()->user()->isAdmin() || auth()->user()->isInstructor())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">⚙️ Admin panel</a>
            @endif
            <a href="{{ route('courses.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-slate-600 hover:bg-slate-50">🔎 Browse courses</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-rose-600 hover:bg-rose-50">🚪 Log out</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur">
            <a href="{{ route('student.dashboard') }}" class="font-extrabold text-slate-900 lg:hidden">RM</a>
            <h1 class="text-lg font-bold text-slate-900">@yield('heading', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm">🛒</a>
                <div class="flex items-center gap-2">
                    <img src="{{ auth()->user()->avatarUrl() }}" class="h-8 w-8 rounded-full object-cover" alt="">
                    <div class="hidden text-xs sm:block">
                        <p class="font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
