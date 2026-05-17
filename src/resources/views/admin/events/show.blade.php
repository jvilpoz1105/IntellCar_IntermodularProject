@extends('admin.layout')
@section('title', 'Evento #' . $event->event_id)
@section('heading', 'Detalle de Evento')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.events.index') }}" class="text-sm text-orange-400 hover:text-orange-300">← Volver a eventos</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info principal --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-100 mb-4">{{ $event->title }}</h2>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Ciudad</dt>
                    <dd class="font-medium text-slate-200">{{ $event->city ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Ubicación</dt>
                    <dd class="text-slate-200">{{ $event->location_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Dirección</dt>
                    <dd class="text-slate-200">{{ $event->address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Fecha del evento</dt>
                    <dd class="font-semibold text-slate-100">{{ $event->event_date?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Máx. participantes</dt>
                    <dd class="text-slate-200">{{ $event->max_participants ?? 'Sin límite' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Asistentes confirmados</dt>
                    <dd class="font-semibold text-slate-100">{{ $event->attendees->count() }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Creado</dt>
                    <dd class="text-slate-200">{{ $event->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Pend. borrado</dt>
                    <dd class="text-slate-200">{{ $event->onDeleteRequest?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>

            @if ($event->event_description)
            <div class="mt-4 pt-4 border-t border-slate-700">
                <p class="text-xs text-slate-500 uppercase mb-1 tracking-wider">Descripción</p>
                <p class="text-sm text-slate-300">{{ $event->event_description }}</p>
            </div>
            @endif
        </div>

        {{-- Lista de asistentes --}}
        @if ($event->attendees->isNotEmpty())
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <h3 class="text-sm font-semibold mb-3 text-slate-400 uppercase tracking-wider">Asistentes ({{ $event->attendees->count() }})</h3>
            <div class="divide-y divide-slate-700">
                @foreach ($event->attendees as $attendee)
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-orange-400 text-xs font-bold">
                            {{ strtoupper(substr($attendee->user_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm text-slate-200">{{ $attendee->user_name }}</p>
                            <p class="text-xs text-slate-500">{{ $attendee->email_address }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $attendee->user_id) }}"
                       class="text-xs text-orange-400 hover:text-orange-300">Ver →</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar: creador + acciones --}}
    <div class="space-y-4">

        {{-- Creador --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-slate-400 mb-3 uppercase tracking-wider">Creador</h3>
            @if ($event->creator)
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center font-bold text-orange-400">
                        {{ strtoupper(substr($event->creator->user_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-sm text-slate-200">{{ $event->creator->user_name }}</p>
                        <p class="text-xs text-slate-500">{{ $event->creator->email_address }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $event->creator->user_id) }}"
                   class="text-xs text-orange-400 hover:text-orange-300">Ver perfil del creador →</a>
            @else
                <p class="text-sm text-slate-500">Creador eliminado</p>
            @endif
        </div>

        {{-- Estado + acciones --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-semibold text-slate-400 mb-2 uppercase tracking-wider">Estado y acciones</h3>

            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Visibilidad</span>
                <span class="{{ $event->visible ? 'text-emerald-400 font-medium' : 'text-slate-500' }}">
                    {{ $event->visible ? 'Visible' : 'Oculto' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.events.toggle-visible', $event->event_id) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg transition
                               {{ $event->visible
                                  ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20 hover:bg-orange-500/20'
                                  : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' }}">
                    {{ $event->visible ? '🙈 Ocultar evento' : '👁 Hacer visible' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.events.destroy', $event->event_id) }}"
                  onsubmit="return confirm('¿Eliminar este evento definitivamente?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                    🗑 Eliminar evento
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
