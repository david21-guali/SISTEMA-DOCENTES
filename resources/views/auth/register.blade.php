<x-guest-layout>
    <!-- Header -->
    <div class="form-header text-center mb-4">
        <h2>Crear cuenta</h2>
        <p>Regístrate para comenzar</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        {{-- Honeypot field for bot protection --}}
        <div style="display:none !important; visibility:hidden !important;">
            <input type="text" name="website_url_check" tabindex="-1" autocomplete="off">
        </div>

        <!-- Name -->
        <div class="form-group mb-3">
            <label for="name" class="form-label fw-bold small text-uppercase text-muted ls-1">Nombre completo</label>
            <div class="input-wrapper">
                <input 
                    id="name" 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    class="form-input @error('name') is-invalid @enderror"
                    placeholder="Tu nombre"
                    autofocus 
                    autocomplete="name"
                >
            </div>
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group mb-3">
            <label for="email" class="form-label fw-bold small text-uppercase text-muted ls-1">Correo Electrónico</label>
            <div class="input-wrapper">
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="form-input @error('email') is-invalid @enderror"
                    placeholder="usuario@ejemplo.com"
                    autocomplete="username"
                >
            </div>
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group mb-3">
            <label for="password" class="form-label fw-bold small text-uppercase text-muted ls-1">Contraseña</label>
            <div class="input-wrapper password-wrapper">
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    class="form-input @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    autocomplete="new-password"
                    style="padding-right: 50px;"
                >
                <button type="button" id="togglePassword" class="toggle-password" title="Mostrar/Ocultar contraseña">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group mb-3">
            <label for="password_confirmation" class="form-label fw-bold small text-uppercase text-muted ls-1">Confirmar Contraseña</label>
            <div class="input-wrapper password-wrapper">
                <input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    class="form-input @error('password_confirmation') is-invalid @enderror"
                    placeholder="••••••••"
                    autocomplete="new-password"
                    style="padding-right: 50px;"
                >
                <button type="button" id="togglePasswordConfirmation" class="toggle-password" title="Mostrar/Ocultar contraseña">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn text-uppercase ls-1">
            Registrarse
        </button>

        <!-- Login Link -->
        <div class="register-link">
            ¿Ya tienes una cuenta? 
            <a href="{{ route('login') }}">Inicia sesión</a>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Si hay errores, añadimos clase al contenedor principal para que los personajes reaccionen
            @if($errors->any())
                document.querySelector('.blobs-container').classList.add('has-error');
            @endif
        });
    </script>
    <style>
        .ls-1 { letter-spacing: 0.5px; }
        /* Compact adjustments for Register page */
        .form-panel { padding: 25px 25px !important; } /* Reduce panel padding */
        .form-input { padding: 10px 15px !important; } /* Smaller inputs */
        .form-header { margin-bottom: 15px !important; } /* Closer header */
        .form-header h2 { font-size: 24px !important; } /* Smaller title */
        .form-group { margin-bottom: 12px !important; } /* Tighter spacing */
        .login-container { min-height: auto !important; } /* Constrain height */
        .register-link { margin-top: 15px !important; } /* Reduce bottom spacing */
        
        @if($errors->any())
            .form-group { margin-bottom: 8px !important; }
            .error-message { margin-top: 4px !important; }
            .form-header { margin-bottom: 8px !important; }
        @endif
    </style>
    @endpush
</x-guest-layout>
