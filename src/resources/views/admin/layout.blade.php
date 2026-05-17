<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — IntellCar</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0">
            {{-- Logo --}}
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <span class="text-xl font-bold tracking-tight text-white">IntellCar</span>
                <span class="ml-2 text-xs bg-orange-500/20 text-orange-400 border border-orange-500/30 px-2 py-0.5 rounded-full uppercase font-semibold tracking-wider">Admin</span>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-5 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.dashboard') ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.users.*') ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Usuarios
                </a>

                <a href="{{ route('admin.adverts.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.adverts.*') ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Anuncios
                </a>

                <a href="{{ route('admin.events.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.events.*') ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Eventos
                </a>

                <a href="{{ route('admin.posts.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('admin.posts.*') ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    Posts
                </a>
            </nav>

            {{-- User info + logout --}}
            <div class="px-4 py-4 border-t border-slate-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-orange-400 text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->user_name ?? Auth::user()->email_address, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-200 truncate">{{ Auth::user()->user_name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email_address }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left text-xs text-slate-500 hover:text-red-400 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="h-16 bg-slate-900 border-b border-slate-800 flex items-center px-6 justify-between shrink-0">
                <h1 class="text-base font-semibold text-slate-100">@yield('heading', 'Panel de Administración')</h1>
                <span class="text-xs text-slate-500">
                    {{ now()->format('d/m/Y · H:i') }}
                </span>
            </header>

            {{-- Flash messages --}}
            <div class="px-6 pt-4 space-y-2">
                @if (session('success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>