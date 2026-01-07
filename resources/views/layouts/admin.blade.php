<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema Docente')</title>
    
    <!-- Vite Assets (Tailwind - Load BEFORE Bootstrap to avoid conflicts) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- jQuery (Load FIRST and in HEAD to avoid race conditions) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables 2.0.3 + Buttons CSS -->
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/3.0.1/css/responsive.bootstrap5.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-gradient: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%);
            --sidebar-bg: #f8f9fc;
            --sidebar-border: #e3e6f0;
            --topbar-bg: #ffffff;
            --body-bg: #f8f9fc;
            --text-dark: #5a5c69;
            --text-muted: #858796;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body { 
            background: var(--body-bg); 
            color: var(--text-dark);
        }

        /* Sidebar - Estilo SB Admin Pro */
        .sidebar { 
            position: fixed; 
            top: 0; 
            left: 0; 
            height: 100vh; 
            width: 250px; 
            background: var(--sidebar-bg); 
            border-right: 1px solid var(--sidebar-border);
            padding-top: 0;
            z-index: 1050; /* Mayor que Bootstrap dropdowns y overlays */
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
        }

        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            background: var(--primary-gradient);
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .sidebar-brand i {
            font-size: 1.3rem;
            margin-right: 0.75rem;
        }

        .sidebar-section {
            padding: 1rem 1.25rem 0.5rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            color: var(--text-muted);
        }

        .sidebar .menu-item { 
            display: flex; 
            align-items: center; 
            padding: 0.75rem 1.25rem; 
            color: var(--text-dark); 
            text-decoration: none; 
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }

        .sidebar .menu-item:hover { 
            background: #eaecf4; 
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .sidebar .menu-item.active { 
            background: #eaecf4; 
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar .menu-item i { 
            width: 24px; 
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-right: 0.75rem;
        }

        .sidebar .menu-item:hover i,
        .sidebar .menu-item.active i {
            color: var(--primary-color);
        }

        /* Topbar - Estilo SB Admin Pro */
        .topbar { 
            position: fixed; 
            top: 0; 
            left: 250px; 
            right: 0; 
            height: 60px; 
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 1.5rem; 
            z-index: 1030; /* Menor que el sidebar */
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }

        /* Main content */
        .main-wrapper { 
            margin-left: 250px; 
            margin-top: 60px; 
            padding: 1.5rem; 
            min-height: calc(100vh - 60px); 
        }

        @media (min-width: 768px) {
            .main-wrapper {
                margin-left: 250px !important;
            }
        }

        /* Footer */
        .footer { 
            background: white; 
            padding: 1.25rem; 
            text-align: center; 
            color: var(--text-muted); 
            border-top: 1px solid var(--sidebar-border);
            margin-top: 2rem;
            font-size: 0.85rem;
            word-break: break-word; /* Asegurar que el texto largo no desborde */
        }

        /* Cards - Estilo SB Admin Pro */
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: var(--card-shadow);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Botones primarios */
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4262c5 0%, #5d35a8 100%);
        }

        /* Tables */
        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--text-dark);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05rem;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            letter-spacing: 0.03rem;
        }

        /* Dropdown menus */
        .dropdown-menu {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 0.35rem;
        }

        .dropdown-item:hover {
            background-color: #f8f9fc;
        }

        /* Page header */
        .page-header-gradient {
            background: var(--primary-gradient);
            border-radius: 0.35rem;
            padding: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        /* Notification dropdown */
        .notification-dropdown {
            max-height: 400px;
            overflow-y: auto;
        }
        
        /* RESPONSIVE STYLES */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden; /* Prevenir scroll horizontal global */
            }
            .sidebar {
                width: 250px;
                left: -250px; /* Escondido por defecto */
                transition: left 0.3s ease-in-out;
                background-color: white !important; /* Asegurar fondo sólido */
            }
            .sidebar.show {
                left: 0;
            }
            .topbar {
                left: 0;
                z-index: 1040; /* Superar contenido pero no sidebar */
            }
            .main-wrapper {
                margin-left: 0;
                padding: 1rem; /* Reducir padding en móviles para ganar espacio */
                overflow-x: hidden;
            }
        }

        /* Clase para el overlay (creada en JS) */
        .sidebar-overlay {
            z-index: 1045 !important; /* Justo debajo del sidebar (1050) */
        }

        /* --- DATATABLES FIXES (Force styles over Tailwind) --- */
        div.dataTables_wrapper div.dataTables_length select {
            width: auto !important;
            display: inline-block !important;
            padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            color: #6e707e !important;
            vertical-align: middle !important;
            background-color: #fff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 16px 12px !important;
            border: 1px solid #d1d3e2 !important;
            border-radius: 0.35rem !important;
            appearance: none !important;
        }
        
        div.dataTables_wrapper div.dataTables_filter input {
            display: inline-block !important;
            width: auto !important;
            margin-left: 0.5rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            color: #6e707e !important;
            background-color: #fff !important;
            border: 1px solid #d1d3e2 !important;
            border-radius: 0.35rem !important;
        }

        .dataTables_paginate .pagination .page-item .page-link {
            position: relative;
            display: block;
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            line-height: 1.25;
            color: #4e73df;
            background-color: #fff;
            border: 1px solid #dddfeb;
            text-decoration: none;
        }

        .dataTables_paginate .pagination .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #4e73df;
            border-color: #4e73df;
        }

        /* --- DATATABLES FIXES (Force styles over Tailwind) --- */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: none !important;
            border: none !important;
        }

        .dataTables_paginate .pagination .page-item .page-link {
            cursor: pointer !important;
            pointer-events: auto !important;
            z-index: 10 !important;
        }

        div.dataTables_wrapper div.dataTables_length select {
            cursor: pointer !important;
            pointer-events: auto !important;
            display: inline-block !important;
            width: auto !important;
        }
    </style>
    
    @yield('head')
    @stack('styles')
</head>
<body>
    <x-loader />
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Topbar -->
    @include('partials.topbar')

    <!-- Main Content -->
    <main class="main-wrapper">
        @yield('contenido')

        <!-- Footer -->
        <footer class="footer">
            <span>© {{ date('Y') }} Sistema de Gestión de Proyectos e Innovación Docente</span>
        </footer>
    </main>

    <!-- Core Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DataTables 2.0.3 & Extensions (User provided snippet) -->
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.1/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.1/js/responsive.bootstrap5.js"></script>

    <!-- DataTables Script Error Handling & Force Click -->
    <script>
        $(document).ready(function() {
            // Re-bind click events just in case
            $(document).on('click', '.page-link', function(e) {
                if ($(this).attr('href') === '#') {
                    // Let DataTables handle it, but ensure it's not blocked
                }
            });
        });
    </script>

    <!-- SweetAlert para mensajes flash -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    </script>
    @endif

    @if(session('swal_error'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Acción no permitida',
            text: '{{ session('swal_error') }}',
            confirmButtonColor: '#3085d6'
        });
    </script>
    @endif

    <script>
        // Global delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;
            
            // Crear overlay para cerrar menú al hacer clic fuera en móvil
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            body.appendChild(overlay);

            // Estilos del overlay vía JS para no ensuciar CSS
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100vw';
            overlay.style.height = '100vh';
            overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
            overlay.style.zIndex = '99'; // Justo debajo del sidebar (z-100)
            overlay.style.display = 'none';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.3s';

            function toggleSidebar() {
                if (window.innerWidth < 768) {
                    sidebar.classList.toggle('show');
                    
                    // Manejar overlay
                    if(sidebar.classList.contains('show')) {
                        overlay.style.display = 'block';
                        setTimeout(() => overlay.style.opacity = '1', 10);
                    } else {
                        overlay.style.opacity = '0';
                        setTimeout(() => overlay.style.display = 'none', 300);
                    }
                } else {
                    body.classList.toggle('sidebar-toggled');
                    sidebar.classList.toggle('toggled');
                }
            }

            if(sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebar();
                });
            }

            // Listener para el nuevo botón "Cerrar" del sidebar
            const sidebarClose = document.getElementById('sidebarClose');
            if(sidebarClose) {
                sidebarClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebar();
                });
            }

            // Cerrar al hacer clic en el overlay
            overlay.addEventListener('click', function() {
                toggleSidebar();
            });

            const deleteForms = document.querySelectorAll('.form-delete');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74a3b', // bootstrap danger
                        cancelButtonColor: '#858796', // bootstrap secondary
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Initialize Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
              return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
