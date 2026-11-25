<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
<div class="flex min-h-screen">
    <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col bg-slate-900 p-4 text-slate-300 lg:flex">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-2 py-2">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-500 font-black text-slate-900">R</span>
            <span class="font-extrabold text-white">Admin panel</span>
        </a>

        @php
            $isAdmin = auth()->user()->isAdmin();
            $groups = [
                'Content' => [
                    ['admin.courses.index', 'Courses', '📚'],
                    ['admin.categories.index', 'Categories', '🗂'],
                    ['admin.exams.index', 'Exams', '📝'],
                    ['admin.ebooks.index', 'E-books', '📖'],
                    ['admin.live-classes.index', 'Live classes', '🎥'],
                    ['admin.announcements.index', 'Announcements', '📣'],
                ],
                'People' => array_values(array_filter([
                    ['admin.enrollments.index', 'Enrollments', '🎓'],
                    ['admin.users.index', 'Students & staff', '👥'],
                    $isAdmin ? ['admin.branches.index', 'Branches', '🏢'] : null,
                    ['admin.reviews.index', 'Reviews', '⭐'],
                ])),
                'Commerce' => [
                    ['admin.orders.index', 'Orders', '🧾'],
                    ['admin.coupons.index', 'Coupons', '🎟'],
                ],
                'System' => array_values(array_filter([
                    $isAdmin ? ['admin.sliders.index', 'Home slider', '🖼'] : null,
                    $isAdmin ? ['admin.settings.index', 'Settings', '⚙️'] : null,
                ])),
            ];
        @endphp

        <nav class="mt-4 min-h-0 flex-1 space-y-2 overflow-y-auto pr-1 text-sm">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500 text-slate-900' : 'hover:bg-white/10' }}">
                📊 Dashboard
            </a>
            @foreach ($groups as $group => $items)
                <div>
                    <p class="px-3 pt-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $group }}</p>
                    <div class="mt-1 space-y-0.5">
                        @foreach ($items as [$route, $label, $icon])
                            <a href="{{ route($route) }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-1.5 {{ request()->routeIs(str_replace('.index', '.*', $route)) ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">
                                <span>{{ $icon }}</span> {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="mt-auto shrink-0 border-t border-white/10 pt-3 text-sm">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-1 hover:bg-white/5">🌐 View site</a>
            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-1 hover:bg-white/5">🎒 Student view</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="flex w-full items-center gap-3 rounded-lg px-3 py-1 text-rose-400 hover:bg-rose-500/10">🚪 Log out</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3">
            <h1 class="text-lg font-bold text-slate-900">@yield('heading', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-3 text-sm">
                @yield('actions')
                <img src="{{ auth()->user()->avatarUrl() }}" class="h-8 w-8 rounded-full" alt="">
                <span class="hidden font-semibold text-slate-700 sm:block">{{ auth()->user()->name }}</span>
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
