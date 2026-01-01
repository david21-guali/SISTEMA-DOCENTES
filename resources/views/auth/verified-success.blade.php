<x-guest-layout>
    <!-- Header -->
    <div class="form-header text-center mb-4">
        <div class="success-icon mb-3" style="font-size: 50px;">✅</div>
        <h2>¡Correo Verificado!</h2>
        <p>Tu cuenta ha sido activada con éxito.</p>
    </div>

    <div class="mb-4 text-center text-gray-600">
        {{ __('Ya puedes cerrar esta ventana. La pestaña principal donde iniciaste el registro se actualizará automáticamente en unos segundos.') }}
    </div>

    <div class="mt-4 flex flex-col gap-3">
        <a href="{{ route('profile.edit') }}" class="submit-btn text-center text-uppercase ls-1 w-100" style="text-decoration: none; display: block;">
            {{ __('Ir a mi Perfil ahora') }}
        </a>
    </div>

    @push('styles')
    <style>
        .ls-1 { letter-spacing: 0.5px; }
        .w-100 { width: 100%; }
        .success-icon {
            animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
    @endpush
</x-guest-layout>
