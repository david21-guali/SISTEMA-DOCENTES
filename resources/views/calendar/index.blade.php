@extends('layouts.admin')

@section('title', 'Calendario de Actividades')

@section('head')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js'></script>
<style>
    .fc-header-toolbar {
        display: none !important; /* Hide default toolbar to use our custom one */
    }
    .calendar-container {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f0f0f0;
    }
    .fc-col-header-cell {
        padding: 15px 0;
        background-color: #f8f9fc;
        color: #858796;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        border-bottom: none !important;
    }
    .fc-daygrid-day-number {
        color: #5a5c69;
        font-weight: 600;
        padding: 10px !important;
    }
    .fc-day-today {
        background-color: #f8f9fc !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 5px;
    }
    .fc-event {
        border: none;
        border-radius: 5px;
        padding: 2px 5px;
        font-size: 0.85rem;
        margin-bottom: 2px;
    }
    
    /* Custom Header Styles */
    .cal-header-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2e2e2e;
    }
    .cal-header-date {
        color: #2e2e2e;
        font-weight: 400;
        padding-left: 15px;
        border-left: 2px solid #e0e0e0;
        margin-left: 15px;
    }
    .btn-custom-light {
        background: #fff;
        border: 1px solid #e3e6f0;
        color: #5a5c69;
        font-weight: 500;
    }
    .btn-custom-light:hover {
        background: #f8f9fc;
    }
    
    /* Responsive Improvements */
    @media (max-width: 768px) {
        .cal-header-title { font-size: 1.25rem; }
        .cal-header-date { font-size: 0.9rem; padding-left: 10px; margin-left: 10px; }
        .calendar-container { 
            padding: 10px; 
            overflow-x: auto; /* Allow horizontal scrolling */
        }
        .fc-col-header-cell { font-size: 0.75rem; padding: 10px 0; }
        .d-flex.align-items-center.mb-3.mb-md-0 { justify-content: center; }
        
        /* Hide week button text on mobile if needed, or keep it */
    }
</style>
@endsection

@section('contenido')
<div class="container-fluid">

    <!-- Top Header (Day | Date + Actions) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 bg-white p-4 rounded-3 shadow-sm">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div id="headerDay" class="cal-header-title">{{ now()->locale('es')->isoFormat('dddd') }}</div>
            <div id="headerDate" class="cal-header-date">{{ now()->locale('es')->isoFormat('D MMM, YYYY') }}</div>
        </div>
        <div class="d-flex gap-2 w-100 w-md-auto justify-content-center align-items-center">
            <div class="btn-group shadow-sm">
                <a href="{{ route('calendar.export') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-check me-2"></i> Sincronizar
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#helpCalendarModal" title="¿Cómo funciona?">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
            <button class="btn btn-custom-light" onclick="calendar.refetchEvents()">
                <i class="fas fa-sync-alt me-2"></i> Actualizar
            </button>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nueva Tarea
            </a>
        </div>
    </div>

    <!-- Calendar Controls & View -->
    <div class="calendar-container">
        <!-- Custom Navigation -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
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
    var calendar;

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var titleEl = document.getElementById('calendarTitle');
        var dayEl = document.getElementById('headerDay');
        var dateEl = document.getElementById('headerDate');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false, // We use custom toolbar
            locale: 'es',
            allDayText: 'Todo el día',
            noEventsText: 'No hay eventos para mostrar',
            slotLabelFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            slotLabelInterval: '01:00', // Frequency of labels
            events: '{{ route("calendar.events") }}',
            editable: false,
            selectable: true,
            themeSystem: 'bootstrap5',
            dayMaxEvents: true, // allow "more" link when too many events
            loading: function(isLoading) {
                const btn = document.querySelector('button[onclick="calendar.refetchEvents()"]');
                if (btn) {
                    const icon = btn.querySelector('i');
                    if (isLoading) {
                        icon.classList.add('fa-spin');
                        btn.disabled = true;
                    } else {
                        icon.classList.remove('fa-spin');
                        btn.disabled = false;
                    }
                }
            },
            
            // Callbacks
            datesSet: function(info) {
                // Update Central Title (Month Year)
                var viewTitle = info.view.title;
                // Capitalize first letter because locale 'es' might return lowercase
                titleEl.innerText = viewTitle.charAt(0).toUpperCase() + viewTitle.slice(1);
            },
            
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault(); // prevents browser from following link in current tab
                }
            }
        });

        calendar.render();
        
        // Active button selection logic
        if (calendar.view.type === 'dayGridMonth') {
            document.getElementById('btnMonth').classList.add('active');
            document.getElementById('btnWeek').classList.remove('active');
        }

        // Update top header time dynamically
        setInterval(() => {
            const now = new Date();
            const optionsDay = { weekday: 'long' };
            const optionsDate = { day: 'numeric', month: 'short', year: 'numeric' };
            
            // Format example: Sunday
            let dayStr = now.toLocaleDateString('es-ES', optionsDay);
            dayStr = dayStr.charAt(0).toUpperCase() + dayStr.slice(1);
            
            // Format example: 14 Dec, 2025
            let dateStr = now.toLocaleDateString('es-ES', optionsDate);
            
            dayEl.innerText = dayStr.charAt(0).toUpperCase() + dayStr.slice(1); // Ensure capitalized
            dateEl.innerText = dateStr;
        }, 1000 * 60); // Update every minute
    });

    function changeView(viewName) {
        calendar.changeView(viewName);
        updateButtons(viewName);
    }
    
    function handleWeekView() {
        // Check if mobile
        if (window.innerWidth < 768) {
            calendar.changeView('listWeek'); // List view for mobile
            updateButtons('listWeek');
        } else {
            calendar.changeView('timeGridWeek'); // Grid view for desktop
            updateButtons('timeGridWeek');
        }
    }

    function updateButtons(viewName) {
        document.getElementById('btnMonth').classList.remove('active');
        document.getElementById('btnWeek').classList.remove('active');
        
        if(viewName === 'dayGridMonth') {
            document.getElementById('btnMonth').classList.add('active');
        } else { // Covers both timeGridWeek and listWeek
            document.getElementById('btnWeek').classList.add('active');
        }
    }
</script>
@endsection
