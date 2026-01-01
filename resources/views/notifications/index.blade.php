@extends('layouts.admin')

@section('title', 'Notificaciones')

@section('contenido')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="text-dark fw-bold mb-0">Notificaciones</h2>
        
        <div class="d-flex gap-2">
            @if(Auth::user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-check-double me-1"></i> Marcar todas leídas
                </button>
            </form>
            @endif
            @if(Auth::user()->readNotifications->count() > 0)
            <form action="{{ route('notifications.destroyAllRead') }}" method="POST" class="form-delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="fas fa-trash-alt me-1"></i> Borrar leídas
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($notifications->count() > 0)
        @php
            // Group notifications by date (Y-m-d)
            $groupedNotifications = $notifications->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });
        @endphp

        <div class="notifications-container">
            @foreach($groupedNotifications as $date => $group)
                @php
                    $isToday = $date === now()->format('Y-m-d');
                    $isYesterday = $date === now()->subDay()->format('Y-m-d');
                    $dateLabel = $isToday ? 'Hoy' : ($isYesterday ? 'Ayer' : \Carbon\Carbon::parse($date)->isoFormat('LL'));
                @endphp

                <h5 class="fw-bold text-dark mb-3 mt-4">{{ $dateLabel }}</h5>

                <div class="bg-white rounded-3 shadow-sm mb-4">
                    @foreach($group as $notification)
                        @php
                            $isRead = $notification->read_at !== null;
                            $type = $notification->data['type'] ?? '';
                            $title = $notification->data['title'] ?? 'Notificación';
                            $message = $notification->data['message'] ?? '';
                            
                            // Determine icon/color
                            $iconClass = 'fas fa-info';
                            $iconBg = 'bg-light';
                            $iconColor = 'text-muted';

                            if (Str::contains(strtolower($title), 'reunión') || Str::contains($type, 'meeting')) {
                                $iconClass = 'fas fa-calendar-check';
                                $iconBg = 'bg-info bg-opacity-10';
                                $iconColor = 'text-info';
                            } elseif (Str::contains(strtolower($title), 'tarea')) {
                                $iconClass = 'fas fa-tasks';
                                $iconBg = 'bg-warning bg-opacity-10';
                                $iconColor = 'text-warning';
                            } elseif (Str::contains(strtolower($title), 'proyecto')) {
                                $iconClass = 'fas fa-project-diagram';
                                $iconBg = 'bg-primary bg-opacity-10';
                                $iconColor = 'text-primary';
                            } elseif (Str::contains(strtolower($title), 'comentario')) {
                                $iconClass = 'fas fa-comment-alt'; // Updated icon
                                $iconBg = 'bg-secondary bg-opacity-10';
                                $iconColor = 'text-secondary';
                            } elseif (!$isRead) {
                                $iconBg = 'bg-primary bg-opacity-10';
                                $iconColor = 'text-primary';
                            }
                        @endphp

                        <div class="p-3 border-bottom position-relative notification-item {{ !$isRead ? 'bg-primary bg-opacity-10' : '' }}" 
                             onclick="window.location='{{ route('notifications.read', $notification->id) }}';"
                             style="cursor: pointer;">
                            <div class="d-flex cross-start">
                                <!-- Icon/Avatar -->
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $iconBg }}" style="width: 48px; height: 48px;">
                                        <i class="{{ $iconClass }} {{ $iconColor }} fs-5"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-dark fw-bold">
                                                {{ $title }}
                                                @if(!$isRead)
                                                    <span class="badge bg-danger rounded-pill ms-2" style="font-size: 0.6rem;">NUEVA</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-muted text-sm">{{ $message }}</p>
                                        </div>
                                        <div class="d-flex flex-column align-items-end ms-3">
                                            <span class="text-muted small mb-1">{{ $notification->created_at->diffForHumans(null, true, true) }}</span>
                                            
                                            <div class="dropdown" onclick="event.stopPropagation();">
                                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    <li>
                                                        <a href="{{ route('notifications.read', $notification->id) }}" class="dropdown-item small">
                                                            <i class="fas fa-external-link-alt me-2 text-muted"></i> Ver detalles
                                                        </a>
                                                    </li>
                                                    @if(!$isRead)
                                                    <li>
                                                        <a href="{{ route('notifications.read', $notification->id) }}" onclick="event.preventDefault(); fetch('{{ route('notifications.read', $notification->id) }}').then(() => location.reload());" class="dropdown-item small">
                                                            <i class="fas fa-check me-2 text-muted"></i> Marcar como leída
                                                        </a>
                                                    </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="form-delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item small text-danger">
                                                                <i class="fas fa-trash me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Date Footer Line -->
                                    <div class="mt-2 text-muted small d-flex align-items-center">
                                        <i class="far fa-clock me-2"></i>
                                        {{ $notification->created_at->isoFormat('h:mm A, D MMMM YYYY') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center py-4">
            {{ $notifications->links() }}
        </div>

    @else
        <div class="text-center py-5 mt-5">
            <div class="mb-4">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                    <i class="fas fa-bell-slash fa-3x text-muted opacity-50"></i>
                </div>
            </div>
            <h4 class="text-muted fw-bold">No tienes notificaciones</h4>
            <p class="text-muted">Cuando recibas avisos importantes, aparecerán aquí.</p>
        </div>
    @endif
</div>

@push('styles')
<style>
    .notification-item {
        transition: background-color 0.2s ease;
    }
    .notification-item:last-child {
        border-bottom: none !important;
    }
    .notification-item:hover {
        background-color: #f8f9fa !important;
    }
</style>
@endpush
@endsection
