<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" href="{{asset('assets/front/css/guest.css')}}">
        @stack('styles')
    </head>
    <body>
        <div class="login-container">
            <!-- Left Panel - Blob Characters -->
            <div class="characters-panel">
                <div class="blobs-container" id="blobsContainer">
                    <!-- Purple RECTANGULAR Tall Blob -->
                    <div class="blob blob-purple">
                        <div class="eyes">
                            <div class="eye"></div>
                            <div class="eye"></div>
                        </div>
                    </div>
                    
                    <!-- Orange ROUND Blob (Main/Front) -->
                    <div class="blob blob-orange">
                        <div class="face">
                            <div class="eyes">
                                <div class="eye"></div>
                                <div class="eye"></div>
                            </div>
                            <div class="mouth"></div>
                        </div>
                    </div>
                    
                    <!-- Black RECTANGULAR Blob -->
                    <div class="blob blob-black">
                        <div class="eyes">
                            <div class="eye"></div>
                            <div class="eye"></div>
                        </div>
                    </div>
                    
                    <!-- Yellow ROUNDED Small Blob -->
                    <div class="blob blob-yellow">
                        <div class="face">
                            <div class="eyes">
                                <div class="eye"></div>
                                <div class="eye"></div>
                            </div>
                            <div class="mouth"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Panel - Form -->
            <div class="form-panel">
                {{ $slot }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const allEyes = document.querySelectorAll('.blob .eye');
                const blobsContainer = document.getElementById('blobsContainer');
                const passwordInput = document.getElementById('password');
                const emailInput = document.getElementById('email');
                
                let isPasswordVisible = false;
                let isLookingAway = false;
                
                // Clear error state on interaction
                function clearError() {
                    blobsContainer.classList.remove('has-error');
                }
                
                if (emailInput) {
                    emailInput.addEventListener('input', clearError);
                }
                
                if (passwordInput) {
                    passwordInput.addEventListener('input', clearError);
                }
                
                // Function to make eyes look away (to the upper left)
                function lookAway() {
                    isLookingAway = true;
                    blobsContainer.classList.add('looking-away');
                }
                
                // Function to resume normal eye tracking
                function resumeTracking() {
                    isLookingAway = false;
                    blobsContainer.classList.remove('looking-away');
                }
                
                // Toggle password visibility (Generic)
                document.querySelectorAll('.toggle-password').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const wrapper = this.closest('.password-wrapper');
                        const input = wrapper.querySelector('input');
                        const isVisible = input.type === 'text';
                        
                        if (!isVisible) {
                            input.type = 'text';
                            this.innerHTML = `
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>`;
                            lookAway();
                        } else {
                            input.type = 'password';
                            this.innerHTML = `
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>`;
                            // Only resume tracking if NO password field is visible
                            const anyVisible = Array.from(document.querySelectorAll('.password-wrapper input'))
                                .some(i => i.type === 'text');
                            if (!anyVisible) resumeTracking();
                        }
                    });
                });
                
                // Mouse tracking - eyes follow the cursor
                document.addEventListener('mousemove', function(e) {
                    if (!isLookingAway) {
                        const containerRect = blobsContainer.getBoundingClientRect();
                        const centerX = containerRect.left + containerRect.width / 2;
                        const centerY = containerRect.top + containerRect.height / 2;
                        
                        const deltaX = e.clientX - centerX;
                        const deltaY = e.clientY - centerY;
                        
                        const maxMove = 6;
                        const moveX = Math.max(-maxMove, Math.min(maxMove, deltaX / 60));
                        const moveY = Math.max(-maxMove, Math.min(maxMove, deltaY / 60));
                        
                        allEyes.forEach(eye => {
                            eye.style.transform = `translate(${moveX}px, ${moveY}px)`;
                        });
                    }
                });
                
                // Email input - eyes follow text position
                if (emailInput) {
                    emailInput.addEventListener('input', function(e) {
                        if (!isLookingAway) {
                            const inputLength = e.target.value.length;
                            const maxLength = 30;
                            const moveX = Math.min(inputLength, maxLength) / maxLength * 8 - 2;
                            
                            allEyes.forEach(eye => {
                                 eye.style.transform = `translate(${moveX}px, 2px)`;
                            });
                        }
                    });
                }
            });
        </script>
        @stack('scripts')
    </body>
</html>
