<section>
    <header class="mb-4">
        <h5 class="fw-bold text-primary">Actualizar Contraseña</h5>
        <p class="text-muted small">
            Asegúrate de que tu cuenta esté protegida con una contraseña larga y aleatoria.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Contraseña Actual</label>
            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                   id="update_password_current_password" name="current_password" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                   id="update_password_password" name="password" autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                   id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-1"></i> Guardar
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success py-1 px-3 mb-0 small fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> Guardado.
                </div>
            @endif
        </div>
    </form>
</section>
