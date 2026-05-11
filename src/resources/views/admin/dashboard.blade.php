@extends('admin.layout')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')

{{-- Stats cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

    @php
    $cards = [
        ['label' => 'Usuarios totales',       'value' => $stats['total_users'],          'color' => 'blue'],
        ['label' => 'Usuarios activos',        'value' => $stats['active_users'],          'color' => 'green'],
        ['label' => 'Pend. borrado (users)',   'value' => $stats['pending_delete_users'],  'color' => 'yellow'],
        ['label' => 'Anuncios totales',        'value' => $stats['total_adverts'],         'color' => 'indigo'],
        ['label' => 'Anuncios visibles',       'value' => $stats['visible_adverts'],       'color' => 'teal'],
        ['label' => 'Pend. borrado (ads)',     'value' => $stats['pending_delete_ads'],    'color' => 'red'],
    ];
    @endphp

    @foreach ($cards as $card)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex flex-col gap-1">
        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</span>
        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
    </div>
    @endforeach
</div>

{{-- Latest tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Últimos usuarios --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-sm">Últimos usuarios registrados</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($latest_users as $user)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->user_name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email_address }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $user->user_tag === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $user->user_tag }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-gray-500">Sin usuarios registrados aún.</p>
            @endforelse
        </div>
    </div>

    {{-- Últimos anuncios --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="font-semibold text-sm">Últimos anuncios publicados</h2>
            <a href="{{ route('admin.adverts.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($latest_adverts as $advert)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]">{{ $advert->ad_title }}</p>
                    <p class="text-xs text-gray-500">{{ $advert->seller?->user_name ?? '—' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                        {{ number_format($advert->price, 0, ',', '.') }} €
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $advert->visible ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-gray-500">Sin anuncios registrados aún.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
