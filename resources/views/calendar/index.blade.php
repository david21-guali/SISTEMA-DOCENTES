@extends('layouts.admin')

@section('title', 'Calendario de Actividades')

@section('head')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js'></script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/calendar.css') }}">
@endpush
@endsection

@section('contenido')
<div class="container-fluid">

    <!-- Top Header (Day | Date + Actions) -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mb-4 bg-white p-4 rounded-3 shadow-sm">
        <div class="d-flex align-items-center mb-3 mb-lg-0">
            <div id="headerDay" class="cal-header-title">{{ now()->locale('es')->isoFormat('dddd') }}</div>
            <div id="headerDate" class="cal-header-date">{{ now()->locale('es')->isoFormat('D MMM, YYYY') }}</div>
        </div>
        <div class="d-flex flex-column flex-lg-row gap-2 w-100 w-lg-auto justify-content-center align-items-center">
            
            <a href="{{ route('calendar.export') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-calendar-check me-2"></i> Sincronizar
            </a>

            <button class="btn btn-info text-white shadow-sm rounded-circle" style="width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center;" data-bs-toggle="modal" data-bs-target="#helpCalendarModal" title="Ayuda">
                <i class="fas fa-question"></i>
            </button>

            <div class="vr mx-2 d-none d-lg-block"></div>

            <button class="btn btn-light border shadow-sm" onclick="calendar.refetchEvents()">
                <i class="fas fa-sync-alt me-2 text-muted"></i> Actualizar
            </button>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-2"></i> Nueva Tarea
            </a>
        </div>
    </div>

    <!-- Calendar Controls & View -->
    <div class="calendar-container">
        <!-- Custom Navigation -->
        <div class="d-flex flex-column flex-xxl-row justify-content-between align-items-center mb-4 gap-3">
            <div class="order-2 order-md-1">
                <button class="btn btn-custom-light px-4" onclick="calendar.today()">Hoy</button>
            </div>
            
            <div class="d-flex align-items-center order-1 order-md-2">
                <button class="btn btn-link text-dark text-decoration-none" onclick="calendar.prev()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h4 id="calendarTitle" class="mb-0 mx-3 fw-bold text-dark text-capitalize">Diciembre 2025</h4>
                <button class="btn btn-link text-dark text-decoration-none" onclick="calendar.next()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="btn-group order-3 order-md-3">
                <button class="btn btn-custom-light active" id="btnMonth" onclick="changeView('dayGridMonth')">Mes</button>
                <button class="btn btn-custom-light" id="btnWeek" onclick="handleWeekView()">Semana</button>
            </div>
        </div>

        <div id='calendar'></div>
    </div>

    <!-- Help Modal -->
    <div class="modal fade" id="helpCalendarModal" tabindex="-1" aria-labelledby="helpCalendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title fw-bold" id="helpCalendarModalLabel">
                        <i class="fas fa-info-circle me-2"></i> ¿Cómo sincronizar tu calendario?
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted">La opción <strong>Sincronizar</strong> descarga un archivo <code>.ics</code> que puedes importar en tu calendario personal para recibir recordatorios automáticos.</p>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark"><i class="fab fa-google text-danger me-2"></i> Google Calendar</h6>
                        <ol class="small text-muted">
                            <li>Haz clic en "Sincronizar" para descargar el archivo.</li>
                            <li>En tu PC, busca la sección <strong>"Otros calendarios"</strong> en la barra lateral izquierda.</li>
                            <li>Haz clic en el botón <strong>"+"</strong> y selecciona la opción <strong>"Importar"</strong>.</li>
                            <li>Selecciona el archivo descargado y confirma.</li>
                        </ol>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark"><i class="fab fa-microsoft text-primary me-2"></i> Outlook / Celular</h6>
                        <ol class="small text-muted">
                            <li>Descarga el archivo <code>.ics</code>.</li>
                            <li>Simplemente abre el archivo en tu computadora o envíalo a tu móvil por correo.</li>
                            <li>El sistema te preguntará si deseas añadir los eventos a tu calendario.</li>
                        </ol>
                    </div>

                    <div class="alert alert-light border-0 small mb-0">
                        <i class="fas fa-lightbulb text-warning me-2"></i> <strong>Tip:</strong> El archivo es personalizado y solo contiene los eventos en los que participas.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.AppConfig = {
        routes: {
            calendarEvents: '{{ route("calendar.events") }}'
        }
    };
</script>
@vite(['resources/js/pages/calendar-index.js'])
@endsection
