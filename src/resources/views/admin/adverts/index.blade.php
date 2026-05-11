@extends('admin.layout')
@section('title', 'Anuncios')
@section('heading', 'Gestión de Anuncios')

@section('content')

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.adverts.index') }}"
      class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Título del anuncio..."
               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                      bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
    </div>

    <div>
        <label class="block text-xs text-gray-500 mb-1">Tipo</label>
        <select name="ad_type"
                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg
                       bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Todos</option>
            @foreach (['new' => 'Nuevo', 'km0' => 'Km 0', 'used' => 'Usado', 'renting' => 'Renting', 'leasing' => 'Leasing', 'supcription' => 'Suscripción'] as $val => $label)
                <option value="{{ $val }}" {{ request('ad_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex items-center gap-4">
        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" name="hidden" value="1"
                   {{ request('hidden') === '1' ? 'checked' : '' }}
                   class="rounded border-gray-300">
            Solo ocultos
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" name="pending_delete" value="1"
                   {{ request('pending_delete') === '1' ? 'checked' : '' }}
                   class="rounded border-gray-300">
            Pend. borrado
        </label>
    </div>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        Filtrar
    </button>
    <a href="{{ route('admin.adverts.index') }}"
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
                <th class="px-6 py-3">Título</th>
                <th class="px-6 py-3">Vendedor</th>
                <th class="px-6 py-3">Tipo</th>
                <th class="px-6 py-3">Precio</th>
                <th class="px-6 py-3">Visible</th>
                <th class="px-6 py-3">Pend. borrado</th>
                <th class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($adverts as $advert)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $advert->ad_id }}</td>
                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white max-w-[200px] truncate">
                    {{ $advert->ad_title }}
                </td>
                <td class="px-6 py-3 text-gray-600 dark:text-gray-300">
                    {{ $advert->seller?->user_name ?? '—' }}
                </td>
                <td class="px-6 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        {{ $advert->ad_type }}
                    </span>
                </td>
                <td class="px-6 py-3 font-semibold text-gray-800 dark:text-gray-200">
                    {{ number_format($advert->price, 0, ',', '.') }} €
                </td>
                <td class="px-6 py-3">
                    @if ($advert->visible)
                        <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-400 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Visible
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-gray-500 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Oculto
                        </span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    @if ($advert->onDeleteRequest)
                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                            {{ $advert->onDeleteRequest->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.adverts.show', $advert->ad_id) }}"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-medium">
                            Ver
                        </a>

                        <form method="POST" action="{{ route('admin.adverts.toggle-visible', $advert->ad_id) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-medium {{ $advert->visible ? 'text-orange-500 hover:text-orange-700' : 'text-green-600 hover:text-green-800' }}">
                                {{ $advert->visible ? 'Ocultar' : 'Mostrar' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.adverts.destroy', $advert->ad_id) }}"
                              onsubmit="return confirm('¿Eliminar este anuncio definitivamente?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">No se encontraron anuncios.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($adverts->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
        {{ $adverts->links() }}
    </div>
    @endif
</div>

@endsection
