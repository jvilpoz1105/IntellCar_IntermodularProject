@extends('admin.layout')
@section('title', 'Anuncio #' . $advert->ad_id)
@section('heading', 'Detalle de Anuncio')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.adverts.index') }}" class="text-sm text-blue-600 hover:underline">← Volver a anuncios</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info principal --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $advert->ad_title }}</h2>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Tipo</dt>
                    <dd class="font-medium">{{ $advert->ad_type }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Precio</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">{{ number_format($advert->price, 2, ',', '.') }} €</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Color</dt>
                    <dd>{{ $advert->car_color ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Kilómetros</dt>
                    <dd>{{ $advert->kilometers ? number_format($advert->kilometers, 0, ',', '.') . ' km' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Año</dt>
                    <dd>{{ $advert->year_manufacture ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Región / Ciudad</dt>
                    <dd>{{ $advert->region }}, {{ $advert->city }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Modelo</dt>
                    <dd>{{ $advert->model?->model_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Motor</dt>
                    <dd>{{ $advert->engine?->engine_type ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Publicado</dt>
                    <dd>{{ $advert->publish_date?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs uppercase mb-0.5">Pend. borrado</dt>
                    <dd>{{ $advert->onDeleteRequest?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>

            @if ($advert->ad_details)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 uppercase mb-1">Descripción</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $advert->ad_details }}</p>
            </div>
            @endif
        </div>

        {{-- Media --}}
        @if ($advert->media && $advert->media->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300">Imágenes ({{ $advert->media->count() }})</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($advert->media as $media)
                <img src="{{ asset('storage/' . $media->file_path) }}"
                     class="w-full h-28 object-cover rounded-lg" alt="Media">
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar: vendedor + acciones --}}
    <div class="space-y-4">

        {{-- Vendedor --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Vendedor</h3>
            @if ($advert->seller)
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500">
                        {{ strtoupper(substr($advert->seller->user_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-sm text-gray-900 dark:text-white">{{ $advert->seller->user_name }}</p>
                        <p class="text-xs text-gray-500">{{ $advert->seller->email_address }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $advert->seller->user_id) }}"
                   class="text-xs text-blue-600 hover:underline">Ver perfil del vendedor →</a>
            @else
                <p class="text-sm text-gray-500">Vendedor eliminado</p>
            @endif
        </div>

        {{-- Estado + acciones --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Estado y acciones</h3>

            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Visibilidad</span>
                <span class="{{ $advert->visible ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                    {{ $advert->visible ? 'Visible' : 'Oculto' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.adverts.toggle-visible', $advert->ad_id) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg transition
                               {{ $advert->visible
                                  ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                  : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    {{ $advert->visible ? '🙈 Ocultar anuncio' : '👁 Hacer visible' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.adverts.destroy', $advert->ad_id) }}"
                  onsubmit="return confirm('¿Eliminar este anuncio definitivamente?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                    🗑 Eliminar anuncio
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
