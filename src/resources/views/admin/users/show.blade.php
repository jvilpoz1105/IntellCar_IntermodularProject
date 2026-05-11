@extends('admin.layout')
@section('title', 'Usuario: ' . $user->user_name)
@section('heading', 'Detalle de Usuario')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:underline">← Volver a usuarios</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info principal --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Card de perfil --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-4 mb-4">
                @if ($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                         class="w-16 h-16 rounded-full object-cover" alt="{{ $user->user_name }}">
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xl font-bold text-gray-500">
                        {{ strtoupper(substr($user->user_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ $user->user_name }}</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $user->user_tag === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $user->user_tag }}
                    </span>
                </div>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-900 dark:text-white font-medium truncate max-w-[180px]">{{ $user->email_address }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Teléfono</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Dirección</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $user->address ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Estado</dt>
                    <dd>
                        <span class="{{ $user->is_active ? 'text-green-600' : 'text-red-500' }} font-medium">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Paddock</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $user->paddock?->paddock_name ?? '—' }}</dd>
                </div>
                @if ($user->onDeleteRequest)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Pend. borrado</dt>
                    <dd class="text-orange-600 font-medium">{{ $user->onDeleteRequest->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Acciones --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Acciones</h3>

            <form method="POST" action="{{ route('admin.users.toggle-active', $user->user_id) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg transition
                               {{ $user->is_active
                                  ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                  : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    {{ $user->is_active ? '⏸ Desactivar cuenta' : '▶ Activar cuenta' }}
                </button>
            </form>

            @if ($user->user_id !== Auth::id())
            <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}"
                  onsubmit="return confirm('¿Eliminar definitivamente a {{ addslashes($user->user_name) }}? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                    🗑 Eliminar usuario
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Anuncios del usuario --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm">Anuncios ({{ $user->adverts->count() }})</h3>
            </div>
            @if ($user->adverts->isEmpty())
                <p class="px-6 py-4 text-sm text-gray-500">Este usuario no tiene anuncios.</p>
            @else
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-left">Precio</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Visible</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($user->adverts as $ad)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium truncate max-w-[200px]">{{ $ad->ad_title }}</td>
                        <td class="px-4 py-3">{{ number_format($ad->price, 0, ',', '.') }} €</td>
                        <td class="px-4 py-3 text-gray-500">{{ $ad->ad_type }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $ad->visible ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $ad->visible ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.adverts.show', $ad->ad_id) }}"
                               class="text-blue-600 hover:underline text-xs">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

@endsection
