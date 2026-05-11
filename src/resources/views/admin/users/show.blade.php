@extends('admin.layout')
@section('title', 'Usuario: ' . $user->user_name)
@section('heading', 'Detalle de Usuario')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-orange-400 hover:text-orange-300">← Volver a usuarios</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info principal --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Card de perfil --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <div class="flex items-center gap-4 mb-4">
                @if ($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                         class="w-16 h-16 rounded-full object-cover" alt="{{ $user->user_name }}">
                @else
                    <div class="w-16 h-16 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-xl font-bold text-orange-400">
                        {{ strtoupper(substr($user->user_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="font-semibold text-slate-100">{{ $user->user_name }}</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $user->user_tag === 'admin' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-700 text-slate-400' }}">
                        {{ $user->user_tag }}
                    </span>
                </div>
            </div>

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="text-slate-200 font-medium truncate max-w-[180px]">{{ $user->email_address }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Teléfono</dt>
                    <dd class="text-slate-300">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Dirección</dt>
                    <dd class="text-slate-300">{{ $user->address ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Estado</dt>
                    <dd>
                        <span class="{{ $user->is_active ? 'text-emerald-400' : 'text-red-400' }} font-medium">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Paddock</dt>
                    <dd class="text-slate-300">{{ $user->paddock?->paddock_name ?? '—' }}</dd>
                </div>
                @if ($user->onDeleteRequest)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Pend. borrado</dt>
                    <dd class="text-orange-400 font-medium">{{ $user->onDeleteRequest->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Acciones --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-semibold text-slate-400 mb-2 uppercase tracking-wider">Acciones</h3>

            <form method="POST" action="{{ route('admin.users.toggle-active', $user->user_id) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg transition
                               {{ $user->is_active
                                  ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20 hover:bg-orange-500/20'
                                  : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' }}">
                    {{ $user->is_active ? '⏸ Desactivar cuenta' : '▶ Activar cuenta' }}
                </button>
            </form>

            @if ($user->user_id !== Auth::id())
            <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}"
                  onsubmit="return confirm('\u00bfEliminar definitivamente a {{ addslashes($user->user_name) }}? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                    🗑 Eliminar usuario
                </button>
            </form>
            @endif
        </div>
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
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-slate-200">Anuncios ({{ $user->adverts->count() }})</h3>
            </div>
            @if ($user->adverts->isEmpty())
                <p class="px-6 py-4 text-sm text-slate-500">Este usuario no tiene anuncios.</p>
            @else
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500 uppercase bg-slate-900/60 tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-left">Precio</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Visible</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach ($user->adverts as $ad)
                    <tr class="hover:bg-slate-700/30 transition">
                        <td class="px-4 py-3 font-medium text-slate-200 truncate max-w-[200px]">{{ $ad->ad_title }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ number_format($ad->price, 0, ',', '.') }} €</td>
                        <td class="px-4 py-3 text-slate-500">{{ $ad->ad_type }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $ad->visible ? 'text-emerald-400' : 'text-slate-500' }}">
                                {{ $ad->visible ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.adverts.show', $ad->ad_id) }}"
                               class="text-orange-400 hover:text-orange-300 text-xs">Ver</a>
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
