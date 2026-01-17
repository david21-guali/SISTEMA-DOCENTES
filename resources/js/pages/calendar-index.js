document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const titleEl = document.getElementById('calendarTitle');
    const dayEl = document.getElementById('headerDay');
    const dateEl = document.getElementById('headerDate');

    if (!calendarEl || !window.FullCalendar) return;

    window.calendar = new FullCalendar.Calendar(calendarEl, {
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
        slotLabelInterval: '01:00',
        events: window.AppConfig.routes.calendarEvents,
        editable: false,
        selectable: true,
        themeSystem: 'bootstrap5',
        dayMaxEvents: true,
        loading: function (isLoading) {
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
        datesSet: function (info) {
            const viewTitle = info.view.title;
            titleEl.innerText = viewTitle.charAt(0).toUpperCase() + viewTitle.slice(1);
        },
        eventClick: function (info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault();
            }
        }
    });

    window.calendar.render();

    // Active button selection logic
    if (window.calendar.view.type === 'dayGridMonth') {
        const btnMonth = document.getElementById('btnMonth');
        if (btnMonth) btnMonth.classList.add('active');
    }

    // Update top header time dynamically
    setInterval(() => {
        const now = new Date();
        const optionsDay = { weekday: 'long' };
        const optionsDate = { day: 'numeric', month: 'short', year: 'numeric' };

        let dayStr = now.toLocaleDateString('es-ES', optionsDay);
        dayStr = dayStr.charAt(0).toUpperCase() + dayStr.slice(1);

        let dateStr = now.toLocaleDateString('es-ES', optionsDate);

        if (dayEl) dayEl.innerText = dayStr;
        if (dateEl) dateEl.innerText = dateStr;
    }, 1000 * 60);
});

window.changeView = function (viewName) {
    if (window.calendar) {
        window.calendar.changeView(viewName);
        updateButtons(viewName);
    }
}

window.handleWeekView = function () {
    if (!window.calendar) return;

    if (window.innerWidth < 768) {
        window.calendar.changeView('listWeek');
        updateButtons('listWeek');
    } else {
        window.calendar.changeView('timeGridWeek');
        updateButtons('timeGridWeek');
    }
}

function updateButtons(viewName) {
    const btnMonth = document.getElementById('btnMonth');
    const btnWeek = document.getElementById('btnWeek');

    if (btnMonth) btnMonth.classList.remove('active');
    if (btnWeek) btnWeek.classList.remove('active');

    if (viewName === 'dayGridMonth') {
        if (btnMonth) btnMonth.classList.add('active');
    } else {
        if (btnWeek) btnWeek.classList.add('active');
    }
}
