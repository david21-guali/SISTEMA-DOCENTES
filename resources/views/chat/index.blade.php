@extends('layouts.admin')

@section('title', 'Chat Institucional')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/chat.css') }}">
@endpush
@section('contenido')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Contactos -->
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-comments me-2"></i>Chats</h5>
                    </div>
                    <!-- Buscador -->
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="userSearch" class="form-control bg-light border-start-0" placeholder="Buscar por nombre o correo..." aria-label="Buscar contacto">
                    </div>
                </div>
                <div class="list-group list-group-flush chat-user-list" id="userList">
                    @forelse($users as $user)
                    <a href="{{ route('chat.show', $user->id) }}" class="list-group-item list-group-item-action d-flex align-items-center p-3 user-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                        <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm chat-avatar-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate text-dark">{{ $user->name }}</div>
                            <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                        </div>
                    </a>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-user-slash fa-2x mb-2 opacity-25"></i>
                        <p class="small mb-0">No hay otros usuarios disponibles.</p>
                    </div>
                    @endforelse
                    <!-- No results message -->
                    <div id="noResults" class="p-4 text-center text-muted d-none">
                        <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                        <p class="small mb-0">No se encontraron contactos que coincidan.</p>
                    </div>
                </div>
            </div>
        </div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSearch = document.getElementById('userSearch');
        const userItems = document.querySelectorAll('.user-item');
        const noResults = document.getElementById('noResults');

        userSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let hasVisible = false;

            userItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const email = item.getAttribute('data-email');

                if (name.includes(query) || email.includes(query)) {
                    item.classList.remove('d-none');
                    item.classList.add('d-flex');
                    hasVisible = true;
                } else {
                    item.classList.remove('d-flex');
                    item.classList.add('d-none');
                }
            });

            if (hasVisible) {
                noResults.classList.add('d-none');
            } else {
                noResults.classList.remove('d-none');
            }
        });
    });
</script>
@endsection

        <!-- Placeholder Chat Area -->
        <div class="col-md-8 d-none d-md-block">
            <div class="card shadow h-100 d-flex align-items-center justify-content-center bg-light chat-placeholder">
                <div class="text-center text-muted">
                    <i class="fas fa-paper-plane fa-3x mb-3"></i>
                    <h4>Selecciona un contacto para chatear</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
