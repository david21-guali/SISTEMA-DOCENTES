@extends('layouts.admin')

@section('title', 'Innovaciones Pedagógicas')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-lightbulb me-2 text-primary"></i>Innovaciones Pedagógicas</h5>
            <p class="text-muted mb-0 small">Explora y gestiona las iniciativas de innovación educativa</p>
        </div>
        @if(Auth::user()->hasRole('docente') || Auth::user()->hasRole('admin'))
        <a href="{{ route('innovations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nueva Innovación
        </a>
        @endif
    </div>

    <!-- Innovations Grid -->
    @if($innovations->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-lightbulb fa-4x text-muted opacity-25"></i>
            </div>
            <h5 class="text-muted">No hay innovaciones registradas</h5>
            <p class="text-muted small mb-3">Sé el primero en compartir una práctica innovadora.</p>
            @can('create-innovations')
            <a href="{{ route('innovations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Crear Innovación
            </a>
            @endcan
        </div>
    @else
        <div class="row g-4">
            @foreach($innovations as $innovation)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 border-top border-4" style="border-top-color: {{ $innovation->status_color == 'success' ? '#1cc88a' : ($innovation->status_color == 'warning' ? '#f6c23e' : '#e74a3b') }} !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-light text-dark border">
                                    {{ $innovation->innovationType->name }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li><a class="dropdown-item" href="{{ route('innovations.show', $innovation) }}">Ver detalles</a></li>
                                        @can('update', $innovation)
                                        <li><a class="dropdown-item" href="{{ route('innovations.edit', $innovation) }}">Editar</a></li>
                                        @endcan
                                        @can('delete', $innovation)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('innovations.destroy', $innovation) }}" method="POST" class="form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                            </form>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>

                            <h5 class="card-title fw-bold mb-2">
                                <a href="{{ route('innovations.show', $innovation) }}" class="text-decoration-none text-dark">
                                    {{ $innovation->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small mb-3">{{ Str::limit($innovation->description, 100) }}</p>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="rounded-circle bg-gray-200 text-secondary d-flex align-items-center justify-content-center small me-2" style="width: 24px; height: 24px;">
                                        <i class="fas fa-user xs"></i>
                                    </div>
                                    <span class="small text-muted">{{ $innovation->profile->user->name }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-gray-200 text-secondary d-flex align-items-center justify-content-center small me-2" style="width: 24px; height: 24px;">
                                        <i class="fas fa-calendar xs"></i>
                                    </div>
                                    <span class="small text-muted">{{ $innovation->created_at->translatedFormat('d M, Y') }}</span>
                                </div>
                            </div>

                            @if($innovation->impact_score)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted fw-bold">Impacto</span>
                                        <span class="text-primary fw-bold">{{ $innovation->impact_score }}/10</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $innovation->impact_score >= 8 ? 'success' : ($innovation->impact_score >= 5 ? 'info' : 'warning') }}" 
                                             role="progressbar" 
                                             style="width: {{ $innovation->impact_score * 10 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-{{ $innovation->status_color }} bg-opacity-10 text-{{ $innovation->status_color }}">
                                    {{ ucfirst(str_replace('_', ' ', $innovation->status)) }}
                                </span>
                                @if($innovation->evidence_files && count($innovation->evidence_files) > 0)
                                    <small class="text-muted">
                                        <i class="fas fa-paperclip me-1"></i>{{ count($innovation->evidence_files) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $innovations->links() }}
        </div>
    @endif
</div>
@endsection
