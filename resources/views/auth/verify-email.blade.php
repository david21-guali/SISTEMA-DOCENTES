<x-guest-layout>
    <!-- Header -->
    <div class="form-header text-center mb-4">
        <h2>Verifica tu correo</h2>
        <p>Casi has terminado el registro</p>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no lo recibiste, con gusto te enviaremos otro.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 border-radius-10">
            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col gap-3">

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="submit-btn text-uppercase ls-1 w-100">
                {{ __('Reenviar correo de verificación') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Cerrar sesión') }}
            </button>
        </form>
    </div>

    @push('styles')
    <style>
        .ls-1 { letter-spacing: 0.5px; }
        .border-radius-10 { border-radius: 12px; }
        .w-100 { width: 100%; }
        .flex-col { display: flex; flex-direction: column; }
        .gap-3 { gap: 1rem; }
    </style>
    @endpush

    <script>
        // "Live Verification" Polling
        // Checks every 2 seconds if the user has verified their email in another tab
        const checkStatus = setInterval(async () => {
            try {
                const response = await fetch('{{ route('verification.status') }}');
                const data = await response.json();
                
                if (data.verified) {
                    clearInterval(checkStatus);
                    // Redirect to profile with the welcome flag
                    window.location.href = '{{ route('profile.edit') }}?welcome=1';
                }
            } catch (error) {
                console.error('Error checking verification status:', error);
            }
        }, 2000);
    </script>
</x-guest-layout>
