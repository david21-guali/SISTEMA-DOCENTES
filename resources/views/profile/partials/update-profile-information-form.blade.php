<section>
    <header class="mb-4">
        <h5 class="fw-bold text-primary">Información del Perfil</h5>
        <p class="text-muted small">
            Actualiza la información de perfil y la dirección de correo electrónico de tu cuenta.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" 
                   value="{{ old('name', $user->name) }}" autofocus autocomplete="name"
                   class="form-control @error('name', 'updateProfileInformation') is-invalid @enderror">
            @error('name', 'updateProfileInformation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" 
                   value="{{ old('email', $user->email) }}" autocomplete="username"
                   class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror">
            @error('email', 'updateProfileInformation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-danger small mb-1">
                        Tu dirección de correo no está verificada.
                    </p>
                    <button form="send-verification" class="btn btn-link btn-sm p-0">
                        Haz clic aquí para reenviar el correo de verificación.
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small mt-1">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Guardar Cambios
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success py-1 px-3 mb-0 small fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> Guardado.
                </div>
            @endif
        </div>
    </form>
</section>
