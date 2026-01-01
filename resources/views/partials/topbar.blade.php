<header class="topbar">
    <!-- Left side -->
    <div class="d-flex align-items-center">
        <button class="btn btn-link text-muted d-md-none me-2" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Right side -->
    <div class="d-flex align-items-center gap-3">
        <!-- Notifications -->
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none text-muted position-relative p-0" data-bs-toggle="dropdown" id="notificationDropdown">
                <i class="fas fa-bell"></i>
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ Auth::user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow notification-dropdown" style="width: 320px;">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: var(--primary-color);">Notificaciones</h6>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none p-0 text-muted" style="font-size:0.75rem;">Marcar leídas</button>
                    </form>
                    @endif
                </div>

                @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                    <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item py-1 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 pt-1 me-2">
                                <i class="fas fa-circle text-primary" style="font-size: 0.4rem;"></i>
                            </div>
                            <div class="flex-grow-1" style="line-height: 1.2;">
                                <p class="mb-0 fw-bold text-dark" style="font-size: 0.8rem;">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                <p class="mb-0 text-muted text-truncate" style="font-size: 0.75rem; max-width: 250px;">{{ $notification->data['message'] ?? '' }}</p>
                                <span class="text-muted" style="font-size: 0.65rem;">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash text-muted mb-2"></i>
                        <p class="text-muted small mb-0">Sin notificaciones</p>
                    </div>
                @endforelse

                <div class="border-top">
                    <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small py-2" style="color: var(--primary-color);">
                        Ver todas las notificaciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="vr" style="height: 24px;"></div>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0" data-bs-toggle="dropdown">
                @if(Auth::user()->profile && Auth::user()->profile->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" 
                         class="rounded-circle border" 
                         style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                         style="width: 36px; height: 36px; background: var(--primary-gradient); font-weight: 600; font-size: 0.9rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="d-none d-md-block text-start">
                    <span class="d-block text-dark fw-medium" style="font-size: 0.85rem; line-height: 1.2;">{{ Auth::user()->name }}</span>
                    <span class="text-muted" style="font-size: 0.7rem;">
                        {{ Auth::user()->roles->first()->name ?? 'Usuario' }}
                    </span>
                </div>
                <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 0.6rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user fa-sm me-2 text-muted"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('notifications.index') }}">
                        <i class="fas fa-bell fa-sm me-2 text-muted"></i> Notificaciones
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt fa-sm me-2"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
