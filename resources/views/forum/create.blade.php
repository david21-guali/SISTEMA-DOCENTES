@extends('layouts.admin')

@section('title', 'Nuevo Tema')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Crear Nuevo Tema de Discusión</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('forum.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Título del Tema</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Ej: Estrategias para evaluación formativa..." value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Describe el tema o pregunta a discutir...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Sé claro y conciso para fomentar la participación.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('forum.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Publicar Tema</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
