@extends('admin.layout')
@section('title', 'Usuarios')
@section('heading', 'Gestión de Usuarios')

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.users.index') }}"
      class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nombre o email..."
               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                      bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>

    <div>
        <label class="block text-xs text-gray-500 mb-1">Rol</label>
        <select name="tag"
                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                       bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todos</option>
            <option value="admin"   {{ request('tag') === 'admin'   ? 'selected' : '' }}>Admin</option>
            <option value="indv"    {{ request('tag') === 'indv'    ? 'selected' : '' }}>Particular</option>
            <option value="dealer"  {{ request('tag') === 'dealer'  ? 'selected' : '' }}>Concesionario</option>
        </select>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="pending_delete" name="pending_delete" value="1"
               {{ request('pending_delete') === '1' ? 'checked' : '' }}
               class="rounded border-gray-300">
        <label for="pending_delete" class="text-sm text-gray-600 dark:text-gray-300">Pend. borrado</label>
    </div>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        Filtrar
    </button>
    <a href="{{ route('admin.users.index') }}"
       class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        Limpiar
    </a>
</form>

{{-- Tabla --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">Nombre</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Rol</th>
                <th class="px-6 py-3">Estado</th>
                <th class="px-6 py-3">Pend. borrado</th>
                <th class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $user->user_id }}</td>
                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $user->user_name }}</td>
                <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $user->email_address }}</td>
                <td class="px-6 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $user->user_tag === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $user->user_tag }}
                    </span>
                </td>
                <td class="px-6 py-3">
                    @if ($user->is_active)
                        <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-400 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactivo
                        </span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    @if ($user->onDeleteRequest)
                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                            {{ $user->onDeleteRequest->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        {{-- Ver detalle --}}
                        <a href="{{ route('admin.users.show', $user->user_id) }}"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-medium">
                            Ver
                        </a>

                        {{-- Toggle activo --}}
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user->user_id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-medium {{ $user->is_active ? 'text-orange-500 hover:text-orange-700' : 'text-green-600 hover:text-green-800' }}">
                                {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>

                        {{-- Eliminar --}}
                        @if ($user->user_id !== Auth::id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($user->user_name) }} definitivamente?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No se encontraron usuarios.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginación --}}
    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
