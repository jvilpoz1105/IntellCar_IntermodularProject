@extends('admin.layout')
@section('title', 'Usuario: ' . $user->user_name)
@section('heading', 'Detalle de Usuario')

@section('content')

{{-- Breadcrumb --}}
<div class="mb-6 flex items-center gap-2 text-sm">
    <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-orange-400 transition">Usuarios</a>
    <span class="text-slate-700">/</span>
    <span class="text-slate-300">{{ $user->user_name }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna izquierda --}}
    <div class="lg:col-span-1 space-y-4">

        {{-- Card de perfil --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">

            {{-- Cabecera con avatar --}}
            <div class="bg-slate-900/60 px-6 py-6 flex flex-col items-center text-center border-b border-slate-700">
                @if ($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}"
                         class="w-20 h-20 rounded-full object-cover border-2 border-orange-500/30 mb-3"
                         alt="{{ $user->user_name }}">
                @else
                    <div class="w-20 h-20 rounded-full bg-orange-500/20 border-2 border-orange-500/30
                                flex items-center justify-center text-2xl font-bold text-orange-400 mb-3">
                        {{ strtoupper(substr($user->user_name, 0, 1)) }}
                    </div>
                @endif

                <h2 class="font-semibold text-slate-100 text-base">{{ $user->user_name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->email_address }}</p>

                <div class="flex items-center gap-2 mt-3">
                    {{-- Tag --}}
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $user->user_tag === 'admin'
                            ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30'
                            : 'bg-slate-700 text-slate-400 border border-slate-600' }}">
                        {{ $user->user_tag }}
                    </span>
                    {{-- Estado --}}
                    <span class="inline-flex items-center gap-1 text-xs font-medium
                        {{ $user->is_active ? 'text-emerald-400' : 'text-red-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            {{-- Datos del perfil --}}
            <dl class="divide-y divide-slate-700 text-sm">
                <div class="flex justify-between px-6 py-3">
                    <dt class="text-slate-500">Teléfono</dt>
                    <dd class="text-slate-300">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <dt class="text-slate-500">Dirección</dt>
                    <dd class="text-slate-300 text-right max-w-[160px]">{{ $user->address ?? '—' }}</dd>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <dt class="text-slate-500">Email contacto</dt>
                    <dd class="text-slate-300 truncate max-w-[160px]">{{ $user->contact_email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <dt class="text-slate-500">Paddock</dt>
                    <dd class="text-slate-300">{{ $user->paddock?->paddock_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <dt class="text-slate-500">Registro</dt>
                    <dd class="text-slate-400">
                        {{ $user->registration_date ? $user->registration_date->format('d/m/Y') : '—' }}
                    </dd>
                </div>
                @if ($user->onDeleteRequest)
                <div class="flex justify-between px-6 py-3">
                    <dt class="text-slate-500">Pend. borrado</dt>
                    <dd class="text-orange-400 font-medium">{{ $user->onDeleteRequest->format('d/m/Y') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Acciones --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 space-y-3">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Acciones</h3>

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
                  onsubmit="return confirm('¿Eliminar definitivamente a {{ addslashes($user->user_name) }}? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                    🗑 Eliminar usuario
                </button>
            </form>
            @endif
        </div>

    </div>

    {{-- Columna derecha --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Estadísticas rápidas --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex flex-col gap-1">
                <span class="text-2xl font-bold text-slate-100">{{ $user->adverts->count() }}</span>
                <span class="text-xs text-slate-500">Anuncios totales</span>
            </div>
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex flex-col gap-1">
                <span class="text-2xl font-bold text-emerald-400">{{ $user->adverts->where('visible', true)->count() }}</span>
                <span class="text-xs text-slate-500">Anuncios visibles</span>
            </div>
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex flex-col gap-1">
                <span class="text-2xl font-bold text-slate-100">{{ $user->garage ? $user->garage->count() : 0 }}</span>
                <span class="text-xs text-slate-500">Vehículos en garaje</span>
            </div>
        </div>

        {{-- Tabla de anuncios --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-slate-200">Anuncios</h3>
                <span class="text-xs text-slate-500 bg-slate-900/60 border border-slate-700 px-2 py-0.5 rounded-full">
                    {{ $user->adverts->count() }}
                </span>
            </div>
            @if ($user->adverts->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-slate-500">Este usuario no tiene anuncios publicados.</p>
                </div>
            @else
            <table class="w-full text-sm">
                <thead class="text-xs text-slate-500 uppercase bg-slate-900/60 tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Título</th>
                        <th class="px-5 py-3 text-left">Precio</th>
                        <th class="px-5 py-3 text-left">Tipo</th>
                        <th class="px-5 py-3 text-left">Visible</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach ($user->adverts as $ad)
                    <tr class="hover:bg-slate-700/30 transition">
                        <td class="px-5 py-3 font-medium text-slate-200 truncate max-w-[200px]">{{ $ad->ad_title }}</td>
                        <td class="px-5 py-3 text-slate-300 font-medium">{{ number_format($ad->price, 0, ',', '.') }} €</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-0.5 bg-slate-700 text-slate-400 rounded-full">{{ $ad->ad_type }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium
                                {{ $ad->visible ? 'text-emerald-400' : 'text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ad->visible ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                {{ $ad->visible ? 'Visible' : 'Oculto' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.adverts.show', $ad->ad_id) }}"
                               class="text-xs text-orange-400 hover:text-orange-300 font-medium transition">
                                Ver →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Garaje --}}
        @if ($user->garage && $user->garage->isNotEmpty())
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-sm text-slate-200">Garaje virtual</h3>
                <span class="text-xs text-slate-500 bg-slate-900/60 border border-slate-700 px-2 py-0.5 rounded-full">
                    {{ $user->garage->count() }}
                </span>
            </div>
            <div class="divide-y divide-slate-700">
                @foreach ($user->garage as $item)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-200">
                            {{ $item->make ?? '' }} {{ $item->model ?? '' }}
                        </p>
                        <p class="text-xs text-slate-500">{{ $item->year ?? '—' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
