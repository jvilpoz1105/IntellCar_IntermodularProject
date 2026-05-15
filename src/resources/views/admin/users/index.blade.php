@extends('admin.layout')
@section('title', 'Usuarios')
@section('heading', 'Gestión de Usuarios')

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.users.index') }}"
      class="bg-slate-800 border border-slate-700 rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs text-slate-500 mb-1 uppercase tracking-wider">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nombre o email..."
               class="w-full px-3 py-2 text-sm border border-slate-700 rounded-lg
                      bg-slate-900 text-slate-100 placeholder-slate-600
                      focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500/50"/>
    </div>

    <div>
        <label class="block text-xs text-slate-500 mb-1 uppercase tracking-wider">Rol</label>
        <select name="tag"
                class="px-3 py-2 text-sm border border-slate-700 rounded-lg
                       bg-slate-900 text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-orange-500/50">
            <option value="">Todos</option>
            <option value="admin"   {{ request('tag') === 'admin'   ? 'selected' : '' }}>Admin</option>
            <option value="indv"    {{ request('tag') === 'indv'    ? 'selected' : '' }}>Particular</option>
            <option value="dealer"  {{ request('tag') === 'dealer'  ? 'selected' : '' }}>Concesionario</option>
        </select>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="pending_delete" name="pending_delete" value="1"
               {{ request('pending_delete') === '1' ? 'checked' : '' }}
               class="rounded border-slate-600 bg-slate-800">
        <label for="pending_delete" class="text-sm text-slate-400">Pend. borrado</label>
    </div>

    <button type="submit"
            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
        Filtrar
    </button>
    <a href="{{ route('admin.users.index') }}"
       class="px-4 py-2 text-sm text-slate-400 border border-slate-700 rounded-lg hover:bg-slate-700 transition">
        Limpiar
    </a>
</form>

{{-- Tabla --}}
<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-slate-500 uppercase bg-slate-900/60 tracking-wider">
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
        <tbody class="divide-y divide-slate-700">
            @forelse ($users as $user)
            <tr class="hover:bg-slate-700/30 transition">
                <td class="px-6 py-3 text-slate-500">{{ $user->user_id }}</td>
                <td class="px-6 py-3 font-medium text-slate-100">{{ $user->user_name }}</td>
                <td class="px-6 py-3 text-slate-400">{{ $user->email_address }}</td>
                <td class="px-6 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $user->user_tag === 'admin' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-700 text-slate-400' }}">
                        {{ $user->user_tag }}
                    </span>
                </td>
                <td class="px-6 py-3">
                    @if ($user->is_active)
                        <span class="inline-flex items-center gap-1 text-emerald-400 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-red-400 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Inactivo
                        </span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    @if ($user->onDeleteRequest)
                        <span class="text-xs text-orange-400 font-medium">
                            {{ $user->onDeleteRequest->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-slate-600">—</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.show', $user->user_id) }}"
                           class="text-orange-400 hover:text-orange-300 text-xs font-medium">
                            Ver
                        </a>

                        <form method="POST" action="{{ route('admin.users.toggle-active', $user->user_id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-medium {{ $user->is_active ? 'text-orange-500 hover:text-orange-400' : 'text-emerald-500 hover:text-emerald-400' }}">
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
