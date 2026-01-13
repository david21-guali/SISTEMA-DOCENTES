<x-guest-layout>
    <div class="mb-4 text-sm text-black font-bold">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" class="text-black font-bold" style="color: #000 !important; font-weight: 700 !important;" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus />
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Enviar enlace para restablecer contraseña') }}
            </x-primary-button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any())
                const blobs = document.querySelector('.blobs-container');
                if (blobs) blobs.classList.add('has-error');
            @endif
        });
    </script>
    @endpush
</x-guest-layout>
