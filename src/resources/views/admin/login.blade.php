<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — IntellCar</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-slate-950 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md px-6 py-8 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <span class="text-2xl font-bold text-white tracking-tight">IntellCar</span>
            <span class="ml-2 text-xs bg-orange-500/20 text-orange-400 border border-orange-500/30 px-2 py-0.5 rounded-full uppercase font-semibold tracking-wider">Admin</span>
            <p class="mt-3 text-sm text-slate-500">Panel de Administración</p>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email_address" class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">
                    Correo electrónico
                </label>
                <input
                    id="email_address"
                    type="email"
                    name="email_address"
                    value="{{ old('email_address') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 text-slate-100 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500/50
                           placeholder-slate-600 text-sm"
                    placeholder="admin@intellcar.com"
                />
            </div>

            <div>
                <label for="user_password" class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">
                    Contraseña
                </label>
                <input
                    id="user_password"
                    type="password"
                    name="user_password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 text-slate-100 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500/50 text-sm"
                />
            </div>

            <button type="submit"
                    class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition text-sm">
                Entrar al panel
            </button>
        </form>
    </div>

</body>
</html>
