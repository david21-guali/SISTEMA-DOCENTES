@props(['user', 'showAvatar' => false])

<div class="dropdown d-inline-block">
    <a href="#" class="text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown" aria-expanded="false">
        @if($showAvatar)
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white me-1" style="width: 24px; height: 24px; font-size: 0.8rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <span class="fw-bold">{{ $user->name }}</span>
    </a>
    <ul class="dropdown-menu shadow">
        <li>
            <a class="dropdown-item" href="{{ route('users.show', $user) }}">
                <i class="fas fa-user-circle me-2 text-primary"></i> Ver Perfil
            </a>
        </li>
        @if(auth()->id() !== $user->id)
        <li>
            <a class="dropdown-item" href="{{ route('chat.show', $user) }}">
                <i class="fas fa-envelope me-2 text-info"></i> Enviar Mensaje
            </a>
        </li>
        @endif
    </ul>
</div>
