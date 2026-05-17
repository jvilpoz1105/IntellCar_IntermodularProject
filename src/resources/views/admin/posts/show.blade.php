@extends('admin.layout')
@section('title', 'Post #' . $post->post_id)
@section('heading', 'Detalle de Post')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.posts.index') }}" class="text-sm text-orange-400 hover:text-orange-300">← Volver a posts</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info principal --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-slate-100 mb-4">{{ $post->title }}</h2>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Modelo</dt>
                    <dd class="font-medium text-slate-200">{{ $post->model?->model_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Motor</dt>
                    <dd class="text-slate-200">{{ $post->engine?->engine_type ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Publicado</dt>
                    <dd class="text-slate-200">{{ $post->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 text-xs uppercase mb-0.5">Pend. borrado</dt>
                    <dd class="text-slate-200">{{ $post->onDeleteRequest?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>

            @if ($post->content)
            <div class="mt-4 pt-4 border-t border-slate-700">
                <p class="text-xs text-slate-500 uppercase mb-1 tracking-wider">Contenido</p>
                <p class="text-sm text-slate-300 leading-relaxed">{{ $post->content }}</p>
            </div>
            @endif
        </div>

        {{-- Media --}}
        @if ($post->media && $post->media->isNotEmpty())
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <h3 class="text-sm font-semibold mb-3 text-slate-400 uppercase tracking-wider">Imágenes ({{ $post->media->count() }})</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($post->media as $media)
                <img src="{{ asset('storage/' . $media->file_path) }}"
                     class="w-full h-28 object-cover rounded-lg" alt="Media">
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar: autor + acciones --}}
    <div class="space-y-4">

        {{-- Autor --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
            <h3 class="text-sm font-semibold text-slate-400 mb-3 uppercase tracking-wider">Autor</h3>
            @if ($post->author)
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center font-bold text-orange-400">
                        {{ strtoupper(substr($post->author->user_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-sm text-slate-200">{{ $post->author->user_name }}</p>
                        <p class="text-xs text-slate-500">{{ $post->author->email_address }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $post->author->user_id) }}"
                   class="text-xs text-orange-400 hover:text-orange-300">Ver perfil del autor →</a>
            @else
                <p class="text-sm text-slate-500">Autor eliminado</p>
            @endif
        </div>

        {{-- Estado + acciones --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-semibold text-slate-400 mb-2 uppercase tracking-wider">Estado y acciones</h3>

            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Visibilidad</span>
                <span class="{{ $post->visible ? 'text-emerald-400 font-medium' : 'text-slate-500' }}">
                    {{ $post->visible ? 'Visible' : 'Oculto' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.posts.toggle-visible', $post->post_id) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg transition
                               {{ $post->visible
                                  ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20 hover:bg-orange-500/20'
                                  : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' }}">
                    {{ $post->visible ? '🙈 Ocultar post' : '👁 Hacer visible' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.posts.destroy', $post->post_id) }}"
                  onsubmit="return confirm('¿Eliminar este post definitivamente?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full py-2 text-sm font-medium rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                    🗑 Eliminar post
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
