<x-guest-layout>
    <!-- Header -->
    <div class="form-header text-center mb-4">
        <h2>Bienvenido de nuevo</h2>
        <p>Ingresa tus credenciales para acceder</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="session-status alert alert-success mb-4 rounded-3 text-center small shadow-sm border-0">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div class="form-group mb-4">
            <label for="email" class="form-label fw-bold small text-uppercase text-muted ls-1">Correo Electrónico</label>
            <div class="input-wrapper">
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    class="form-input @error('email') is-invalid @enderror"
                    placeholder="tu@correo.com"
                    autofocus 
                    autocomplete="username"
                >
            </div>
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group mb-4">
            <label for="password" class="form-label fw-bold small text-uppercase text-muted ls-1">Contraseña</label>
            <div class="input-wrapper password-wrapper">
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    class="form-input @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    autocomplete="current-password"
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

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
            <label class="remember-me">
                <input type="checkbox" name="remember">
                <span>Recuérdame</span>
            </label>

            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn text-uppercase ls-1">
            Iniciar Sesión
        </button>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="register-link">
                ¿No tienes una cuenta? 
                <a href="{{ route('register') }}">Regístrate aquí</a>
            </div>
        @endif
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
    </style>
    @endpush
</x-guest-layout>
