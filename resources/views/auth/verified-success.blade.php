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


</x-guest-layout>
