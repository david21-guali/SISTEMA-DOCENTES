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
    
    <link rel="stylesheet" href="{{ asset('assets/front/css/welcome.css') }}">
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