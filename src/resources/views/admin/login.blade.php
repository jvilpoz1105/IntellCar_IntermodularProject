<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — IntellCar</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md px-6 py-8 bg-gray-800 rounded-2xl shadow-xl">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <span class="text-3xl font-bold text-white">🚗 IntellCar</span>
            <p class="mt-2 text-sm text-gray-400">Panel de Administración</p>
        </div>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email_address" class="block text-sm font-medium text-gray-300 mb-1">
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
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500"
                    placeholder="admin@intellcar.com"
                />
            </div>

            <div>
                <label for="user_password" class="block text-sm font-medium text-gray-300 mb-1">
                    Contraseña
                </label>
                <input
                    id="user_password"
                    type="password"
                    name="user_password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember"
                       class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-500">
                <label for="remember" class="ml-2 text-sm text-gray-300">Recordarme</label>
            </div>

            <button type="submit"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                Entrar al panel
            </button>
        </form>
    </div>

</body>
</html>
