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
        ['label' => 'Eventos totales',         'value' => $stats['total_events'],          'color' => 'purple'],
        ['label' => 'Eventos visibles',        'value' => $stats['visible_events'],        'color' => 'teal'],
        ['label' => 'Posts totales',           'value' => $stats['total_posts'],           'color' => 'orange'],
        ['label' => 'Posts visibles',          'value' => $stats['visible_posts'],         'color' => 'teal'],
    ];
    @endphp

    @foreach ($cards as $card)
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 flex flex-col gap-1">
        <span class="text-2xl font-bold text-slate-100">{{ $card['value'] }}</span>
        <span class="text-xs text-slate-500">{{ $card['label'] }}</span>
    </div>
    @endforeach
</div>

{{-- Latest tables --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Últimos usuarios --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-sm text-slate-200">Últimos usuarios registrados</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-orange-400 hover:text-orange-300">Ver todos →</a>
        </div>
        <div class="divide-y divide-slate-700">
            @forelse ($latest_users as $user)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-200">{{ $user->user_name }}</p>
                    <p class="text-xs text-slate-500">{{ $user->email_address }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $user->user_tag === 'admin' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-700 text-slate-400' }}">
                        {{ $user->user_tag }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-slate-500">Sin usuarios registrados aún.</p>
            @endforelse
        </div>
    </div>

    {{-- Últimos anuncios --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-sm text-slate-200">Últimos anuncios publicados</h2>
            <a href="{{ route('admin.adverts.index') }}" class="text-xs text-orange-400 hover:text-orange-300">Ver todos →</a>
        </div>
        <div class="divide-y divide-slate-700">
            @forelse ($latest_adverts as $advert)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-200 truncate max-w-[200px]">{{ $advert->ad_title }}</p>
                    <p class="text-xs text-slate-500">{{ $advert->seller?->user_name ?? '—' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-300">
                        {{ number_format($advert->price, 0, ',', '.') }} €
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $advert->visible ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-slate-500">Sin anuncios registrados aún.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Latest events + posts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Últimos eventos --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-sm text-slate-200">Últimos eventos creados</h2>
            <a href="{{ route('admin.events.index') }}" class="text-xs text-orange-400 hover:text-orange-300">Ver todos →</a>
        </div>
        <div class="divide-y divide-slate-700">
            @forelse ($latest_events as $event)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-200 truncate max-w-[200px]">{{ $event->title }}</p>
                    <p class="text-xs text-slate-500">{{ $event->creator?->user_name ?? '—' }} · {{ $event->city }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400">{{ $event->event_date?->format('d/m/Y') ?? '—' }}</span>
                    <span class="w-2 h-2 rounded-full {{ $event->visible ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-slate-500">Sin eventos creados aún.</p>
            @endforelse
        </div>
    </div>

    {{-- Últimos posts --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-sm text-slate-200">Últimos posts publicados</h2>
            <a href="{{ route('admin.posts.index') }}" class="text-xs text-orange-400 hover:text-orange-300">Ver todos →</a>
        </div>
        <div class="divide-y divide-slate-700">
            @forelse ($latest_posts as $post)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-200 truncate max-w-[200px]">{{ $post->title }}</p>
                    <p class="text-xs text-slate-500">{{ $post->author?->user_name ?? '—' }}</p>
                </div>
                <span class="w-2 h-2 rounded-full {{ $post->visible ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-slate-500">Sin posts publicados aún.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
