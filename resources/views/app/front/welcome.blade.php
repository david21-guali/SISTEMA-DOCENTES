<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema Docentes') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Nueva paleta moderna y vibrante */
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3a0ca3;
            --secondary: #f72585;
            --accent: #4cc9f0;
            --success: #06d6a0;
            --warning: #ffd166;
            --light: #f8f9fa;
            --dark: #1a1a2e;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 5px 20px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 15px 40px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            background-color: var(--light);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            line-height: 1.2;
        }
        
        /* NAVBAR MODERNA */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }
        
        .navbar.scrolled {
            padding: 0.8rem 0;
            box-shadow: var(--shadow-md);
        }
        
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* NAV LINKS - Solo para menú principal */
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark) !important;
            margin: 0 0.3rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: var(--transition);
            transform: translateX(-50%);
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary) !important;
        }
        
        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary) !important;
        }
        
        .navbar-nav .nav-link.active::after {
            width: 80%;
        }
        
        /* BOTONES DE AUTH - Siempre visibles */
        .auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-left: auto;
        }
        
        .btn-login {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: var(--transition);
            border: 2px solid transparent;
        }
        
        .btn-login:hover {
            color: var(--primary-dark);
            background: rgba(67, 97, 238, 0.05);
            border-color: var(--primary-light);
        }
        
        .btn-register {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
        }
        
        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.7s ease;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        .btn-register:hover::before {
            left: 100%;
        }
        
        /* Para usuarios autenticados */
        .btn-dashboard {
            background: linear-gradient(90deg, var(--success), var(--accent));
            color: white;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(6, 214, 160, 0.3);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 214, 160, 0.4);
            color: white;
        }
        
        /* Botón hamburguesa personalizado */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            font-size: 1.2rem;
            color: var(--primary);
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }
        
        /* HERO SECTION */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            padding-top: 100px;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(67, 97, 238, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(247, 37, 133, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(76, 201, 240, 0.1) 0%, transparent 50%);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            color: var(--dark);
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }
        
        .hero-title span {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            display: inline-block;
        }
        
        .hero-title span::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(76, 201, 240, 0.3);
            z-index: -1;
            border-radius: 4px;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--gray);
            margin-bottom: 2.5rem;
            max-width: 600px;
            font-weight: 400;
        }
        
        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }
        
        .stat-item {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--light-gray);
            min-width: 150px;
            text-align: center;
            transition: var(--transition);
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            display: block;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 500;
            margin-top: 0.5rem;
        }
        
        .hero-image {
            position: relative;
        }
        
        .hero-img {
            width: 100%;
            max-width: 600px;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: 10px solid white;
        }
        
        .hero-img:hover {
            transform: translateY(-10px) rotate(2deg);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }
        
        /* FEATURES SECTION */
        .features-section {
            padding: 100px 0;
            background: white;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-subtitle {
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: rgba(247, 37, 133, 0.1);
            border-radius: 50px;
        }
        
        .section-title {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .section-description {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.1rem;
        }
        
        .feature-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2.5rem 2rem;
            height: 100%;
            border: 2px solid transparent;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: var(--transition);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.5rem;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(10deg);
        }
        
        .feature-title {
            font-size: 1.3rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .feature-description {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.7;
        }
        
        /* TESTIMONIALS SECTION */
        .testimonials-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .testimonial-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2.5rem;
            box-shadow: var(--shadow-md);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 6rem;
            color: rgba(67, 97, 238, 0.1);
            font-family: 'Poppins', sans-serif;
            line-height: 1;
            font-weight: 800;
        }
        
        .testimonial-text {
            color: var(--dark);
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            font-style: italic;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
            border-top: 1px solid var(--light-gray);
            padding-top: 1.5rem;
        }
        
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            padding: 3px;
            background: white;
        }
        
        .author-info h5 {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
        }
        
        .author-info p {
            margin: 0;
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .author-rating {
            color: var(--warning);
            margin-top: 0.3rem;
        }
        
        /* CTA SECTION */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -200px;
            right: -200px;
        }
        
        .cta-section::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
        }
        
        .cta-content {
            position: relative;
            z-index: 2;
        }
        
        .cta-title {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1.5rem;
        }
        
        .cta-description {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 2.5rem;
        }
        
        .btn-light-custom {
            background: white;
            color: var(--primary);
            border: none;
            padding: 1rem 3rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-light-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            color: var(--primary);
        }
        
        .btn-light-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(67, 97, 238, 0.1), transparent);
            transition: left 0.7s ease;
        }
        
        .btn-light-custom:hover::before {
            left: 100%;
        }
        
        /* FOOTER */
        footer {
            background: var(--dark);
            color: white;
            padding: 80px 0 40px;
        }
        
        .footer-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-brand span {
            background: linear-gradient(90deg, var(--accent), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .footer-description {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        
        .footer-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: white;
            position: relative;
            display: inline-block;
        }
        
        .footer-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--primary-light));
            border-radius: 2px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.8rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-links a:hover {
            color: var(--accent);
            transform: translateX(5px);
        }
        
        .footer-links a i {
            font-size: 0.8rem;
            opacity: 0;
            transition: var(--transition);
        }
        
        .footer-links a:hover i {
            opacity: 1;
        }
        
        .contact-info {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .contact-info li {
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }
        
        .contact-info i {
            color: var(--accent);
            margin-top: 5px;
            min-width: 20px;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .social-links a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            font-size: 1.1rem;
        }
        
        .social-links a:hover {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }
        
        .copyright {
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
            font-size: 0.9rem;
        }
        
        /* ANIMACIONES */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* RESPONSIVE */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .section-title, .cta-title {
                font-size: 2rem;
            }
            
            .hero-stats {
                gap: 1rem;
            }
            
            .stat-item {
                min-width: 120px;
                padding: 1rem;
            }
            
            /* Ocultar auth buttons en móvil dentro del menú */
            .auth-buttons {
                display: none;
            }
            
            /* Mostrar auth buttons en el menú colapsado */
            .navbar-collapse .auth-buttons-mobile {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                padding: 1.5rem 0;
                border-top: 1px solid var(--light-gray);
                margin-top: 1rem;
            }
            
            .navbar-collapse .btn-login,
            .navbar-collapse .btn-register {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.3rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .feature-card, .testimonial-card {
                margin-bottom: 1.5rem;
            }
            
            .hero-img {
                margin-top: 2rem;
            }

            .cta-section {
                padding: 60px 0;
            }
        }
        
        @media (max-width: 576px) {
            .hero-section {
                padding-top: 130px;
                padding-bottom: 60px;
            }

            .hero-title {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 1rem;
                align-items: center;
                margin-top: 2rem;
            }
            
            .stat-item {
                width: 100%;
                max-width: 280px;
                padding: 1.2rem;
            }

            .stat-number {
                font-size: 1.8rem;
            }

            .stat-label {
                font-size: 0.9rem;
            }

            .hero-content .d-flex {
                flex-direction: column;
                gap: 0.75rem;
                width: 100%;
            }

            .navbar .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .btn-light-custom {
                padding: 0.8rem 1.5rem;
                font-size: 0.95rem;
                width: 100%;
                max-width: 280px;
            }

            .cta-title {
                font-size: 1.8rem;
            }

            .cta-description {
                font-size: 0.95rem;
            }

            .hero-content .btn-register,
            .hero-content .btn-login {
                width: 100%;
                text-align: center;
                padding: 0.8rem 1.5rem !important;
            }
            
            .navbar-brand {
                font-size: 1.4rem;
                gap: 8px;
            }

            .navbar {
                padding: 0.8rem 0;
            }
        }
        
        @media (min-width: 993px) {
            .auth-buttons-mobile {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-chalkboard-teacher"></i>
                EduFlow
            </a>
            
            <!-- Botones de autenticación VISIBLES en desktop -->
            <div class="auth-buttons d-none d-lg-flex">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-register">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </a>
                    @endif
                @endauth
            </div>
            
            <!-- Botón hamburguesa para móvil -->
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Menú colapsable -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonios</a>
                    </li>
                </ul>
                
                <!-- Botones de autenticación en móvil (dentro del menú) -->
                <div class="auth-buttons-mobile d-lg-none">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-register">
                            <i class="fas fa-user-plus me-2"></i>Registrarse
                        </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content fade-in">
                    <h1 class="hero-title">
                        Transforma la <span>Gestión Académica</span>
                    </h1>
                    <p class="hero-subtitle">
                        Una plataforma moderna diseñada para simplificar la administración 
                        educativa. Centraliza proyectos, tareas y métricas en un solo lugar.
                    </p>
                    
                    <div class="d-flex gap-3 mb-4 flex-wrap">
                        <a href="{{ route('login') }}" class="btn-register" style="padding: 0.9rem 2.5rem;">
                            <i class="fas fa-rocket me-2"></i>Comenzar Ahora
                        </a>
                        <a href="#features" class="btn-login" style="border-color: var(--primary); padding: 0.9rem 2.5rem;">
                            <i class="fas fa-play-circle me-2"></i>Ver Demo
                        </a>
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item fade-in" style="animation-delay: 0.2s">
                            <span class="stat-number">850+</span>
                            <span class="stat-label">Docentes</span>
                        </div>
                        <div class="stat-item fade-in" style="animation-delay: 0.3s">
                            <span class="stat-number">3.5K+</span>
                            <span class="stat-label">Proyectos</span>
                        </div>
                        <div class="stat-item fade-in" style="animation-delay: 0.4s">
                            <span class="stat-number">99.8%</span>
                            <span class="stat-label">Satisfacción</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 hero-image fade-in" style="animation-delay: 0.3s">
                    <img src="{{ asset('images/docentes.png') }}" 
                         onerror="this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'"
                         alt="Plataforma de Gestión Académica"
                         class="hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-subtitle">Características Principales</span>
                <h2 class="section-title">Todo lo que necesitas en una plataforma</h2>
                <p class="section-description">
                    Herramientas diseñadas específicamente para el entorno educativo moderno
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 class="feature-title">Gestión de Tareas</h3>
                        <p class="feature-description">
                            Organiza y prioriza tareas académicas con recordatorios 
                            automáticos y seguimiento de progreso.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card fade-in" style="animation-delay: 0.1s">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="feature-title">Análisis Avanzado</h3>
                        <p class="feature-description">
                            Dashboard interactivo con visualización de datos y 
                            reportes personalizables para toma de decisiones.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card fade-in" style="animation-delay: 0.2s">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Colaboración</h3>
                        <p class="feature-description">
                            Espacios de trabajo compartidos para equipos académicos 
                            con chat integrado y gestión de documentos.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card fade-in" style="animation-delay: 0.3s">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Seguridad Total</h3>
                        <p class="feature-description">
                            Protección de datos con encriptación de nivel empresarial 
                            y controles de acceso granulares.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-subtitle">Testimonios</span>
                <h2 class="section-title">Lo que dicen nuestros usuarios</h2>
                <p class="section-description">
                    Docentes e instituciones que confían en nuestra plataforma
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="testimonial-card fade-in">
                        <p class="testimonial-text">
                            "Esta plataforma ha transformado completamente cómo 
                            gestionamos nuestros proyectos de investigación. 
                            La eficiencia ha aumentado en un 40% desde que la implementamos."
                        </p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="Dr. Carlos Méndez" 
                                 class="author-avatar">
                            <div class="author-info">
                                <h5>Dr. Carlos Méndez</h5>
                                <p>Director de Investigación</p>
                                <div class="author-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="testimonial-card fade-in" style="animation-delay: 0.1s">
                        <p class="testimonial-text">
                            "La interfaz es intuitiva y las herramientas de 
                            colaboración han mejorado significativamente la 
                            comunicación entre nuestro equipo docente."
                        </p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="Dra. Ana García" 
                                 class="author-avatar">
                            <div class="author-info">
                                <h5>Dra. Ana García</h5>
                                <p>Coordinadora Académica</p>
                                <div class="author-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="testimonial-card fade-in" style="animation-delay: 0.2s">
                        <p class="testimonial-text">
                            "El soporte técnico es excelente y las actualizaciones 
                            constantes mantienen la plataforma a la vanguardia 
                            tecnológica de la gestión educativa."
                        </p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="Prof. Javier López" 
                                 class="author-avatar">
                            <div class="author-info">
                                <h5>Prof. Javier López</h5>
                                <p>Decano de Facultad</p>
                                <div class="author-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content fade-in">
                <h2 class="cta-title">¿Listo para transformar tu gestión académica?</h2>
                <p class="cta-description">
                    Únete a cientos de instituciones educativas que ya están utilizando 
                    nuestra plataforma para optimizar sus procesos y mejorar la calidad educativa.
                </p>
                <a href="{{ route('register') }}" class="btn-light-custom">
                    <i class="fas fa-user-plus me-2"></i>Crear Cuenta Gratuita
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5">
                    <div class="footer-brand">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>EduFlow</span>
                    </div>
                    <p class="footer-description">
                        Plataforma líder en gestión académica, diseñada para 
                        facilitar la administración educativa y potenciar la 
                        excelencia docente con tecnología de vanguardia.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-5">
                    <h5 class="footer-title">Enlaces</h5>
                    <ul class="footer-links">
                        <li><a href="#home">Inicio <i class="fas fa-chevron-right"></i></a></li>
                        <li><a href="#features">Características <i class="fas fa-chevron-right"></i></a></li>
                        <li><a href="#testimonials">Testimonios <i class="fas fa-chevron-right"></i></a></li>
                        <li><a href="#">Precios <i class="fas fa-chevron-right"></i></a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4 mb-5">
                    <h5 class="footer-title">Recursos</h5>
                    <ul class="footer-links">
                        <li><a href="#">Documentación <i class="fas fa-book"></i></a></li>
                        <li><a href="#">Centro de Ayuda <i class="fas fa-question-circle"></i></a></li>
                        <li><a href="#">Blog <i class="fas fa-blog"></i></a></li>
                        <li><a href="#">Webinars <i class="fas fa-video"></i></a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4 mb-5">
                    <h5 class="footer-title">Contacto</h5>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Av. Universidad 1234, Ciudad Educativa</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>contacto@eduflow.com</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Lun - Vie: 8:00 AM - 6:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <small>
                    &copy; {{ date('Y') }} EduFlow. Todos los derechos reservados. 
                    <a href="#" style="color: var(--accent); text-decoration: none;">Política de Privacidad</a> | 
                    <a href="#" style="color: var(--accent); text-decoration: none;">Términos de Servicio</a>
                </small>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Scroll animation
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        // Observe elements with fade-in class
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
        
        // Initialize all elements as visible on load
        window.addEventListener('load', function() {
            document.body.style.opacity = '1';
            
            // Animate stats counting
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach(stat => {
                const originalText = stat.textContent;
                const target = parseFloat(originalText.replace(/[^0-9.]/g, ''));
                const suffix = originalText.replace(/[0-9.]/g, '');
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target + suffix;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current) + suffix;
                    }
                }, 16);
            });
        });
        
        // Highlight active nav link on scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        window.addEventListener('scroll', () => {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
        
        // Add hover effect to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-12px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-10px)';
            });
        });
    </script>
</body>
</html>