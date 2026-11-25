<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — {{ \App\Models\Setting::get('site_name', "Redwan's Method") }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
@php
    $user  = auth()->user();
    $role  = $user->role;
    $menus = [
        'instructor' => [
            ['teacher.dashboard',    'Dashboard',    '📊'],
            ['teacher.courses',      'My courses',   '📚'],
            ['teacher.students',     'My students',  '👩‍🎓'],
            ['teacher.live-classes', 'Live classes', '🎥'],
            ['teacher.earnings',     'Earnings',     '💳'],
        ],
        'guardian' => [
            ['guardian.dashboard', 'My children', '👨‍👩‍👦'],
        ],
        'branch_manager' => [
            ['branch.dashboard', 'Dashboard',      '📊'],
            ['branch.students',  'Branch students','👩‍🎓'],
            ['branch.staff',     'Teachers & staff','👥'],
            ['branch.revenue',   'Revenue',        '💰'],
        ],
        'admin' => [
            ['admin.dashboard', 'Admin panel', '⚙️'],
        ],
    ];
    $menu  = $menus[$role] ?? $menus['instructor'];
    $label = \App\Models\User::ROLES[$role] ?? ucfirst($role);
@endphp

<div class="flex min-h-screen">
    <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col bg-slate-900 p-4 text-slate-300 lg:flex">
        <a href="{{ $user->dashboardRoute() }}" class="flex items-center gap-2 px-2 py-2">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-500 font-black text-slate-900">R</span>
            <span class="font-extrabold text-white">{{ $label }} portal</span>
        </a>

        <nav class="mt-5 space-y-1 text-sm">
            @foreach ($menu as [$routeName, $text, $icon])
                <a href="{{ route($routeName) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold {{ request()->routeIs(str_replace(['.dashboard', '.index'], ['.*', '.*'], $routeName)) ? 'bg-emerald-500 text-slate-900' : 'hover:bg-white/10' }}">
                    <span>{{ $icon }}</span> {{ $text }}
                </a>
            @endforeach
        </nav>

        <div class="mt-4 rounded-xl bg-white/5 p-3 text-xs leading-relaxed text-slate-400">
            <p class="font-bold text-white">{{ $user->name }}</p>
            <p>{{ $label }}</p>
            @if ($user->branch)
                <p class="mt-1">🏢 {{ $user->branch->name }}</p>
            @endif
        </div>

        <div class="mt-auto space-y-0.5 border-t border-white/10 pt-3 text-sm">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/5">🌐 View site</a>
            <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/5">🎒 My learning</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-rose-400 hover:bg-rose-500/10">🚪 Log out</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3">
            <h1 class="text-lg font-bold text-slate-900">@yield('heading', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-3 text-sm">
                @yield('actions')
                <img src="{{ $user->avatarUrl() }}" class="h-8 w-8 rounded-full" alt="">
                <span class="hidden font-semibold text-slate-700 sm:block">{{ $user->name }}</span>
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
