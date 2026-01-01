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

        <style>
            :root {
                --bg-dark: #1e1b4b;           /* Azul-morado oscuro */
                --card-bg: #312e81;           /* Morado medio */
                --text-light: #e0e7ff;        /* Lila muy claro */
                --primary: #8b5cf6;           /* Morado principal */
                --secondary: #f472b6;         /* Rosa para contraste */
            }

            * {
                font-family: 'Inter', sans-serif;
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                min-height: 100vh;
                background: var(--bg-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            /* Animated background circles */
            body::before,
            body::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                opacity: 0.1;
                animation: floatBg 8s ease-in-out infinite;
            }

            body::before {
                width: 500px;
                height: 500px;
                background: white;
                top: -200px;
                left: -200px;
                animation-delay: 0s;
            }

            body::after {
                width: 400px;
                height: 400px;
                background: white;
                bottom: -150px;
                right: -150px;
                animation-delay: 2s;
            }

            @keyframes floatBg {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(30px, 30px) scale(1.1); }
            }
            
            /* Main Container */
            .login-container {
                width: 100%;
                max-width: 850px;
                min-height: 580px;
                display: flex;
                background: white;
                border-radius: 32px;
                box-shadow: 0 30px 90px rgba(0, 0, 0, 0.25);
                overflow: hidden;
                margin: 20px;
                position: relative;
                z-index: 1;
            }
            
            /* Left Panel - Characters */
            .characters-panel {
                flex: 1.1;
                background: var(--card-bg);
                display: flex;
                align-items: flex-end;
                justify-content: center;
                padding: 40px 30px;
                position: relative;
                overflow: hidden;
            }

            /* Removed inner bubbles as requested */
            
            /* Blob Characters Container */
            .blobs-container {
                display: flex;
                align-items: flex-end;
                gap: 0;
                position: relative;
                height: 380px;
                z-index: 1;
            }
            
            /* Individual Blob Characters */
            .blob {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.1));
            }
            
            /* Purple RECTANGULAR Tall Blob */
            .blob-purple {
                width: 90px;
                height: 260px;
                background: linear-gradient(180deg, #a78bfa 0%, #8b5cf6 100%);
                border-radius: 20px;
                position: relative;
                z-index: 2;
                margin-right: -15px;
                box-shadow: 0 10px 40px rgba(139, 92, 246, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
                animation: floatUp 3.5s ease-in-out infinite, glowPurple 4s ease-in-out infinite;
                animation-delay: 0.6s;
            }
            
            @keyframes glowPurple {
                0%, 100% { box-shadow: 0 10px 40px rgba(139, 92, 246, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2); }
                50% { box-shadow: 0 10px 60px rgba(139, 92, 246, 0.8), inset 0 0 30px rgba(255, 255, 255, 0.4); }
            }
            
            .blob-purple .eyes {
                position: absolute;
                top: 35px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 22px;
            }
            
            .blob-purple .eye {
                width: 10px;
                height: 10px;
                background: #1a1a2e;
                border-radius: 50%;
                transition: transform 0.15s ease-out;
            }
            
            /* Orange ROUND Blob */
            .blob-orange {
                width: 150px;
                height: 130px;
                background: linear-gradient(180deg, #fb923c 0%, #f97316 100%);
                border-radius: 75px 75px 65px 65px;
                position: relative;
                z-index: 4;
                margin-right: -25px;
                box-shadow: 0 10px 40px rgba(249, 115, 22, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2);
                animation: floatUp 3s ease-in-out infinite, glowOrange 3s ease-in-out infinite;
            }
            
            @keyframes glowOrange {
                0%, 100% { box-shadow: 0 10px 40px rgba(249, 115, 22, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.2); }
                50% { box-shadow: 0 10px 60px rgba(249, 115, 22, 0.8), inset 0 0 30px rgba(255, 255, 255, 0.4); }
            }
            
            .blob-orange .face {
                position: absolute;
                top: 40px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .blob-orange .eyes {
                display: flex;
                gap: 28px;
                margin-bottom: 18px;
            }
            
            .blob-orange .eye {
                width: 8px;
                height: 8px;
                background: #1a1a2e;
                border-radius: 50%;
                transition: transform 0.15s ease-out;
            }
            
            .blob-orange .mouth {
                width: 25px;
                height: 12px;
                border: none;
                border-bottom: 3px solid #1a1a2e;
                border-radius: 0 0 25px 25px;
            }
            
            /* Black SQUARE/RECTANGULAR Blob */
            .blob-black {
                width: 85px;
                height: 180px;
                background: linear-gradient(180deg, #2d2d44 0%, #1a1a2e 100%);
                border-radius: 15px;
                position: relative;
                z-index: 3;
                margin-right: -12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(124, 58, 237, 0.2);
                animation: floatUp 3.2s ease-in-out infinite;
                animation-delay: 0.2s;
            }
            
            .blob-black .eyes {
                position: absolute;
                top: 45px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 18px;
            }
            
            .blob-black .eye {
                width: 10px;
                height: 10px;
                background: white;
                border-radius: 50%;
                transition: transform 0.15s ease-out;
            }
            
            /* Yellow ROUNDED SMALL Blob */
            .blob-yellow {
                width: 75px;
                height: 95px;
                background: linear-gradient(180deg, #fcd34d 0%, #fbbf24 100%);
                border-radius: 38px 38px 32px 32px;
                position: relative;
                z-index: 1;
                box-shadow: 0 10px 40px rgba(251, 191, 36, 0.6), inset 0 0 15px rgba(255, 255, 255, 0.2);
                animation: floatUp 2.5s ease-in-out infinite, glowYellow 2.5s ease-in-out infinite;
                animation-delay: 0.3s;
            }
            
            @keyframes glowYellow {
                0%, 100% { box-shadow: 0 10px 40px rgba(251, 191, 36, 0.6), inset 0 0 15px rgba(255, 255, 255, 0.2); }
                50% { box-shadow: 0 10px 50px rgba(251, 191, 36, 0.8), inset 0 0 25px rgba(255, 255, 255, 0.4); }
            }
            
            .blob-yellow .face {
                position: absolute;
                top: 28px;
                left: 50%;
                transform: translateX(-50%);
            }
            
            .blob-yellow .eyes {
                display: flex;
                gap: 14px;
                margin-bottom: 12px;
            }
            
            .blob-yellow .eye {
                width: 6px;
                height: 6px;
                background: #1a1a2e;
                border-radius: 50%;
                transition: transform 0.15s ease-out;
            }
            
            .blob-yellow .mouth {
                width: 18px;
                height: 2px;
                background: #1a1a2e;
                margin: 0 auto;
            }
            
            /* Looking away - eyes move to upper left */
            .looking-away .eye {
                transform: translate(-8px, -6px) !important;
            }
            
            /* ============================================
               RIGHT PANEL - IMPROVED FORM STYLES
               ============================================ */
            .form-panel {
                flex: 1;
                padding: 50px 45px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            }
            
            /* Form Header */
            .form-header {
                margin-bottom: 35px;
            }
            
            .form-header h2 {
                color: #1a1a2e;
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 8px;
                letter-spacing: -0.5px;
            }
            
            .form-header p {
                color: #6b7280;
                font-size: 15px;
                font-weight: 400;
            }
            
            .form-group {
                margin-bottom: 22px;
            }
            
            .form-group label {
                display: block;
                color: #374151;
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .input-wrapper {
                position: relative;
            }
            
            .form-input {
                width: 100%;
                padding: 16px 18px;
                border: 2px solid #e5e7eb;
                border-radius: 14px;
                font-size: 15px;
                transition: all 0.3s ease;
                outline: none;
                background: #f9fafb;
            }
            
            .form-input::placeholder {
                color: #9ca3af;
            }
            
            .form-input:focus {
                border-color: #ec4899;
                background: white;
                box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1);
            }
            
            .input-wrapper .input-icon {
                display: none;
            }
            
            /* Password Toggle */
            .password-wrapper {
                position: relative;
            }
            
            .toggle-password {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: #9ca3af;
                padding: 5px;
                transition: all 0.2s;
                border-radius: 8px;
            }
            
            .toggle-password:hover {
                color: #ec4899;
                background: rgba(236, 72, 153, 0.1);
            }
            
            .remember-forgot {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 28px;
            }
            
            .remember-me {
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
            }
            
            .remember-me input {
                width: 18px;
                height: 18px;
                accent-color: #7c3aed;
                cursor: pointer;
                border-radius: 4px;
            }
            
            .remember-me span {
                color: #6b7280;
                font-size: 14px;
            }
            
            .forgot-link {
                color: var(--secondary);
                font-size: 14px;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s;
            }
            
            .forgot-link:hover {
                color: #6d28d9;
                text-decoration: underline;
            }
            
            .submit-btn {
                width: 100%;
                padding: 16px;
                background: linear-gradient(135deg, #ec4899, #db2777);
                color: white;
                border: none;
                border-radius: 14px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-bottom: 16px;
                box-shadow: 0 8px 25px rgba(236, 72, 153, 0.35);
                position: relative;
                overflow: hidden;
            }
            
            .submit-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }
            
            .submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(236, 72, 153, 0.45);
            }
            
            .submit-btn:hover::before {
                left: 100%;
            }
            
            .submit-btn:active {
                transform: translateY(0);
            }
            
            /* Divider */
            .divider {
                display: flex;
                align-items: center;
                margin: 20px 0;
                color: #9ca3af;
                font-size: 13px;
            }
            
            .divider::before,
            .divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: #e5e7eb;
            }
            
            .divider span {
                padding: 0 15px;
            }
            
            .google-btn {
                width: 100%;
                padding: 14px;
                background: white;
                color: #374151;
                border: 2px solid #e5e7eb;
                border-radius: 14px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
            }
            
            .google-btn:hover {
                border-color: #7c3aed;
                background: #faf5ff;
            }
            
            .google-btn svg {
                width: 20px;
                height: 20px;
            }
            
            .register-link {
                text-align: center;
                margin-top: 28px;
                color: #6b7280;
                font-size: 14px;
            }
            
            .register-link a {
                color: var(--secondary);
                text-decoration: none;
                font-weight: 600;
                transition: all 0.2s;
            }
            
            .register-link a:hover {
                color: #6d28d9;
                text-decoration: underline;
            }
            
            /* Error messages */
            .error-message {
                color: #ef4444;
                font-size: 12px;
                margin-top: 8px;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .error-message::before {
                content: '⚠';
            }
            
            /* Session status */
            .session-status {
                background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                color: #065f46;
                padding: 14px 18px;
                border-radius: 12px;
                margin-bottom: 20px;
                font-size: 14px;
                font-weight: 500;
                border: 1px solid #6ee7b7;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .login-container {
                    flex-direction: column;
                    max-width: 420px;
                    border-radius: 24px;
                }
                
                .characters-panel {
                    padding: 30px 20px;
                    min-height: 260px;
                }
                
                .blobs-container {
                    transform: scale(0.65);
                }
                
                .form-panel {
                    padding: 35px 28px;
                }
                
                .form-header h2 {
                    font-size: 24px;
                }
            }
            
            /* Animation entrance */
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .login-container {
                animation: slideUp 0.6s ease-out;
            }
            
            /* Blob floating animation */
            @keyframes floatUp {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }
            
            /* Removed individual animation triggers as they are now handled per blob class */

            /* ERROR STATE - NERVOUS/CONCERNED (Matching User Reference) */
            
            /* General Eye Behavior on Error - slightly smaller, looking nervous */
            .blobs-container.has-error .blob .eye {
                background: #1a1a2e;
                transition: all 0.3s ease;
            }
            .blobs-container.has-error .blob-black .eye {
                background: white;
            }

            /* Purple Blob - Looking Up/Right nervously */
            .blobs-container.has-error .blob-purple .eye {
                transform: translate(3px, -4px) !important;
            }

            /* Black Blob - Looking Left (at the others) */
            .blobs-container.has-error .blob-black .eye {
                transform: translate(-4px, 0) !important;
            }

            /* Orange Blob - Sad/Concerned (Small Frown) */
            .blobs-container.has-error .blob-orange .eye {
                transform: translateY(2px) !important;
            }
            .blobs-container.has-error .blob-orange .mouth {
                width: 18px;
                height: 8px;
                background: transparent;
                border: 2px solid #1a1a2e;
                border-bottom: 0;
                border-radius: 10px 10px 0 0; /* Frown arch */
                transform: translateY(4px);
            }

            /* Yellow Blob - Unsure/Wobbly Mouth */
            .blobs-container.has-error .blob-yellow .eye {
                transform: translate(2px, 2px) !important;
            }
            .blobs-container.has-error .blob-yellow .mouth {
                width: 12px;
                height: 2px;
                background: #1a1a2e;
                border-radius: 2px;
                transform: rotate(-10deg) translateY(2px); /* Crooked mouth */
            }
             
            /* Fast Shake animation on error trigger */
            @keyframes shake-nervous {
                0%, 100% { transform: translate(0, 0); }
                25% { transform: translate(-3px, 1px); }
                75% { transform: translate(3px, -1px); }
            }
            
            .blobs-container.has-error {
                animation: shake-nervous 0.3s ease-in-out 2; /* Quick nervous shake */
            }
        </style>
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
