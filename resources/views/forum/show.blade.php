@extends('layouts.admin')

@section('title', $topic->title)

@section('contenido')
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('forum.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Volver al Foro</a>
    </div>

    <!-- Topic Header -->
    <div class="card shadow mb-4 border-primary border-top-0 border-end-0 border-bottom-0 border-start-4">
        <div class="card-body">
            <h2 class="card-title">{{ $topic->title }}</h2>
            <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    {{ substr(optional($topic->profile->user)->name ?? 'A', 0, 1) }}
                </div>
                <span class="text-muted small">
                    Iniciado por <strong>{{ optional($topic->profile->user)->name ?? 'Usuario Eliminado' }}</strong> • {{ optional($topic->created_at)->translatedFormat('d M Y, H:i') ?? 'Fecha desconocida' }}
                </span>
            </div>
            <p class="card-text fs-5">{{ $topic->description }}</p>
        </div>
    </div>

    <h5 class="mb-3"><i class="fas fa-comments"></i> Respuestas ({{ $posts->count() }})</h5>

    <!-- Posts List -->
    <div class="mb-5">
        @foreach($posts as $post)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <div class="fw-bold me-2">{{ $post->profile->user->name }}</div>
                        <span class="text-muted small">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <p class="mb-0">{{ $post->content }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Reply Form -->
    <div class="card shadow">
        <div class="card-body bg-light">
            <h6 class="mb-3">Tu Respuesta</h6>
            <form action="{{ route('forum.storePost', $topic) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="3" placeholder="Escribe tu comentario aquí...">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Publicar Respuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
