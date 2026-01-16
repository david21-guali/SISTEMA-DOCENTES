@extends('layouts.admin')

@section('title', 'Votar Innovación')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-vote-yea me-2"></i>Validación Comunitaria Anónima</h5>
                    <span class="badge bg-light text-info">3 Días para Votar</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <div class="d-flex">
                            <i class="fas fa-user-secret fa-2x me-3 opacity-50"></i>
                            <div>
                                <h6 class="alert-heading fw-bold">Compromiso de Anonimato</h6>
                                <p class="mb-0 small">Tu identidad <strong>nunca</strong> será revelada al creador ni a otros usuarios. Los administradores solo verán estadísticas agregadas y comentarios sin autor. Los votos son consultivos para ayudar al administrador en la decisión final.</p>
                            </div>
                        </div>
                    </div>

                    <div class="innovation-context mb-4 p-3 bg-light rounded border">
                        <h6 class="fw-bold text-dark mb-1">{{ $innovation->title }}</h6>
                        <p class="text-muted small mb-0">{{ Str::limit($innovation->description, 150) }}</p>
                    </div>

                    <form action="{{ route('innovations.review.store', $innovation) }}" method="POST" id="reviewForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block mb-3 text-center">¿Consideras que esta innovación debería ser aprobada?</label>
                            <div class="d-flex justify-content-center gap-4">
                                <div class="vote-option text-center">
                                    <input type="radio" class="btn-check" name="vote" id="vote_approved" value="approved" required>
                                    <label class="btn btn-outline-success border-2 px-4 py-3 shadow-sm d-flex flex-column align-items-center" for="vote_approved">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <span class="fw-bold">SÍ, APROBAR</span>
                                    </label>
                                </div>
                                <div class="vote-option text-center">
                                    <input type="radio" class="btn-check" name="vote" id="vote_rejected" value="rejected">
                                    <label class="btn btn-outline-danger border-2 px-4 py-3 shadow-sm d-flex flex-column align-items-center" for="vote_rejected">
                                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                                        <span class="fw-bold">NO, RECHAZAR</span>
                                    </label>
                                </div>
                            </div>
                            @error('vote')
                                <div class="text-danger text-center small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">Comentario Constructivo *</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="3" 
                                      maxlength="70"
                                      placeholder="Breve opinión técnica (Máx 70 caracteres)..." 
                                      required>{{ old('comment') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Sea honesto y respetuoso.</small>
                                <small id="charCount" class="text-muted">0/70</small>
                            </div>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-info text-white py-2 shadow-sm" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i> Enviar mi Voto Anónimo
                            </button>
                            <a href="{{ route('innovations.show', $innovation) }}" class="btn btn-link text-muted">Volver sin votar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const comment = document.getElementById('comment');
        const charCount = document.getElementById('charCount');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('reviewForm');

        comment.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length + '/70';
            
            if (length < 20 || length > 70) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
        });
    });
</script>
@endpush

<style>
    .btn-check:checked + .btn-outline-success {
        background-color: #198754;
        color: white;
    }
    .btn-check:checked + .btn-outline-danger {
        background-color: #dc3545;
        color: white;
    }
    .vote-option label {
        min-width: 140px;
        transition: all 0.2s ease;
    }
    .vote-option label:hover {
        transform: translateY(-2px);
    }
</style>
