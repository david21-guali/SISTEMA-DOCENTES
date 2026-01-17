document.addEventListener('DOMContentLoaded', function () {
    const projectSelect = document.querySelector('select[name="project_id"]');
    const participantItems = document.querySelectorAll('.participant-item');
    const searchInput = document.getElementById('searchParticipants');
    const form = document.getElementById('createMeetingForm') || document.getElementById('editMeetingForm');

    // Project members mapping passed from Blade
    const projectMembers = window.MeetingConfig ? window.MeetingConfig.projectMembers : {};

    // Filter participants by project
    function filterByProject() {
        if (!projectSelect) return;
        const selectedProjectId = parseInt(projectSelect.value);

        participantItems.forEach(function (item) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            const userId = parseInt(checkbox.value);

            // If no project selected, show all
            if (!selectedProjectId) {
                item.style.display = 'block';
                return;
            }

            // Show only if user is in selected project
            if (projectMembers[selectedProjectId] && projectMembers[selectedProjectId].includes(userId)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                checkbox.checked = false;
            }
        });
    }

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const search = this.value.toLowerCase();
            participantItems.forEach(function (item) {
                if (item.style.display === 'none') return;
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(search) ? 'block' : 'none';
            });
        });
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', filterByProject);
        filterByProject(); // Initial filter
    }

    // Double submission protection
    if (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            }
        });
    }
});
