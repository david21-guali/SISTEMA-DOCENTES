@extends('layouts.admin')

@section('title', 'Chat Institucional')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Contactos -->
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Chats</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($users as $user)
                    <a href="{{ route('chat.show', $user->id) }}" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                        <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 45px; height: 45px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate">{{ $user->name }}</div>
                            <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                        </div>
                    </a>
                    @empty
                    <div class="p-3 text-center text-muted">No hay otros usuarios disponibles.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Placeholder Chat Area -->
        <div class="col-md-8 d-none d-md-block">
            <div class="card shadow h-100 d-flex align-items-center justify-content-center bg-light" style="min-height: 500px;">
                <div class="text-center text-muted">
                    <i class="fas fa-paper-plane fa-3x mb-3"></i>
                    <h4>Selecciona un contacto para chatear</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
