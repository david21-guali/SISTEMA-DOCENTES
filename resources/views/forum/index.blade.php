@extends('layouts.admin')

@section('title', 'Foro de Discusión')

@section('contenido')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-users"></i> Espacio Colaborativo</h2>
            <p class="text-muted">Comparte metodologías y buenas prácticas.</p>
        </div>
        <a href="{{ route('forum.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Tema
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($topics as $topic)
        <div class="col-md-12 mb-3">
            <div class="card shadow-sm hover-shadow transition">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">
                                <a href="{{ route('forum.show', $topic->id) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $topic->title }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-2">
                                Por {{ optional($topic->profile->user)->name ?? 'Usuario Eliminado' }} • {{ optional($topic->created_at)->diffForHumans() ?? 'Fecha desconocida' }}
                            </p>
                            <p class="card-text text-secondary">{{ Str::limit($topic->description, 150) }}</p>
                        </div>
                        <div class="text-center ms-3 d-flex flex-column gap-2">
                            <!-- Comment Badge (Now clickable part of the card) -->
                            <span class="badge bg-light text-dark border p-2">
                                <i class="fas fa-comment-alt text-primary"></i> {{ $topic->posts_count }}
                            </span>
                            
                            <!-- Delete Action (Kept separate with z-index) -->
                            @if(Auth::user()->profile->id == $topic->profile_id || Auth::user()->hasRole('admin'))
                            <form action="{{ route('forum.destroy', $topic) }}" method="POST" class="form-delete" style="position: relative; z-index: 2;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger border-0" title="Eliminar tema">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://illustrations.popsy.co/gray/question-mark.svg" alt="Empty" style="height: 150px; opacity: 0.5;">
            <p class="mt-3 text-muted">No hay temas de discusión aún.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $topics->links() }}
    </div>
</div>
@endsection
