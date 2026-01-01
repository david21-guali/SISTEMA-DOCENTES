<section class="space-y-6">
    <header class="mb-4">
        <h5 class="fw-bold text-danger">Eliminar Cuenta</h5>
        <p class="text-muted small">
            Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar su cuenta, descargue cualquier dato o información que desee conservar.
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i class="fas fa-trash-alt me-1"></i> Eliminar Cuenta
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmUserDeletionModalLabel">¿Estás seguro de que quieres eliminar tu cuenta?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente. Por favor, introduzca su contraseña para confirmar que desea eliminar permanentemente su cuenta.
                    </p>

                    <div class="mb-3">
                        <label for="password" class="form-label visually-hidden">Contraseña</label>
                        <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" id="password" name="password" placeholder="Contraseña">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('password', 'userDeletion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</section>
