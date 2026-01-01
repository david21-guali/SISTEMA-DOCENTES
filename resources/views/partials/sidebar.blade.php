<aside class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand d-flex justify-content-between align-items-center">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-white text-decoration-none">
            <i class="fas fa-graduation-cap"></i>
            <span>Sistema Docente</span>
        </a>
        <button id="sidebarClose" class="btn btn-sm btn-outline-light d-md-none border-0 fw-bold" style="font-size: 0.75rem;">
            <i class="fas fa-chevron-left me-1"></i> Cerrar
        </button>
    </div>
    
    <!-- Core Section -->
    <div class="sidebar-section">Principal</div>
    <nav>
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('projects.index') }}" class="menu-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
            <i class="fas fa-project-diagram"></i>
            <span>Proyectos</span>
        </a>
        
        <a href="{{ route('tasks.index') }}" class="menu-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Tareas</span>
        </a>

        <a href="{{ route('resources.index') }}" class="menu-item {{ request()->routeIs('resources.*') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i>
            <span>Recursos</span>
        </a>

        <a href="{{ route('calendar.index') }}" class="menu-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Calendario</span>
        </a>
    </nav>

    <!-- Innovation Section -->
    <div class="sidebar-section">Innovación</div>
    <nav>
        <a href="{{ route('innovations.index') }}" class="menu-item {{ request()->routeIs('innovations.index') || request()->routeIs('innovations.create') || request()->routeIs('innovations.edit') ? 'active' : '' }}">
            <i class="fas fa-lightbulb"></i>
            <span>Innovaciones</span>
        </a>

        <a href="{{ route('innovations.best-practices') }}" class="menu-item {{ request()->routeIs('innovations.best-practices') ? 'active' : '' }}">
            <i class="fas fa-star"></i>
            <span>Buenas Prácticas</span>
        </a>
    </nav>

    <!-- Communication Section -->
    <div class="sidebar-section">Comunicación</div>
    <nav>
        <a href="{{ route('chat.index') }}" class="menu-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
        </a>

        <a href="{{ route('forum.index') }}" class="menu-item {{ request()->routeIs('forum.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Foro</span>
        </a>

        <a href="{{ route('meetings.index') }}" class="menu-item {{ request()->routeIs('meetings.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Reuniones</span>
        </a>
    </nav>

    <!-- Reports Section -->
    <div class="sidebar-section">Reportes</div>
    <nav>
        <a href="{{ route('reports.index') }}" class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Reportes</span>
        </a>
    </nav>

    <!-- Admin Section (visible only for admin) -->
    @if(auth()->user() && auth()->user()->hasRole('admin'))
    <div class="sidebar-section">Administración</div>
    <nav>
        <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i>
            <span>Usuarios</span>
        </a>
    </nav>
    @endif
</aside>
