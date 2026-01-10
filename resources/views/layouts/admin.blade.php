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
    
    <link rel="stylesheet" href="{{asset('assets/back/css/admin.css')}}">



    
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
