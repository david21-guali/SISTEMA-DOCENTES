@extends('layouts.admin')

@section('title', 'Repositorio de Buenas Prácticas')

@section('contenido')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-star text-warning"></i> Repositorio de Buenas Prácticas</h1>
            <p class="text-muted mb-0">Experiencias exitosas e innovación pedagógica destacada</p>
        </div>
        <div class="col-md-4 text-end">
             <a href="{{ route('innovations.index') }}" class="btn btn-secondary">
                 <i class="fas fa-arrow-left"></i> Volver a Innovaciones
             </a>
        </div>
    </div>

    @if($bestPractices->count() > 0)
        <div class="row">
            @foreach($bestPractices as $practice)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center mb-3">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ $practice->innovationType->name }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $practice->title }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $practice->impact_score }}/10</div>
                                <div class="small text-muted">Impacto</div>
                            </div>
                        </div>
                        
                        <p class="card-text text-truncate" style="max-height: 3rem;">
                            {{ $practice->description }}
                        </p>
                        
                        <!-- Archivos destacados -->
                        @if($practice->attachments->count() > 0)
                        <div class="mb-3">
                            <h6 class="small font-weight-bold">Recursos Disponibles:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($practice->attachments->take(3) as $file)
                                    <a href="{{ $file->url }}" target="_blank" class="badge bg-light text-dark border text-decoration-none" title="{{ $file->original_name }}">
                                        <i class="{{ $file->icon }}"></i> {{ Str::limit($file->original_name, 15) }}
                                    </a>
                                @endforeach
                                @if($practice->attachments->count() > 3)
                                    <span class="badge bg-light text-muted border">+{{ $practice->attachments->count() - 3 }} más</span>
                                @endif
                            </div>
                        </div>
                        @else
                           <div class="mb-3">
                                <small class="text-muted">Sin archivos adjuntos</small>
                           </div>
                        @endif

                        <div class="d-flex justify-content-between mt-3 align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> {{ $practice->profile->user->name }}
                            </small>
                            <a href="{{ route('innovations.show', $practice) }}" class="btn btn-sm btn-success">
                                Ver Detalle <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info shadow-sm">
            <h4 class="alert-heading"><i class="fas fa-info-circle"></i> No hay registros aún</h4>
            <p>Aún no se han documentado experiencias exitosas con alto impacto. ¡Sé el primero en registrar una innovación exitosa!</p>
            <hr>
            @if(Auth::user()->hasRole('docente') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('coordinador'))
            <a href="{{ route('innovations.create') }}" class="btn btn-primary btn-sm">Nueva Innovación</a>
            @endif
        </div>
    @endif
</div>
@endsection
