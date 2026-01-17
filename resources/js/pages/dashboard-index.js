document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'No se pudo cargar la librería de gráficos.', 'error');
        }
        return;
    }

    if (!window.DashboardConfig) return;

    // 1. Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx && window.DashboardConfig.categoryData.length > 0) {
        new Chart(categoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: window.DashboardConfig.categoryLabels,
                datasets: [{
                    data: window.DashboardConfig.categoryData,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true }
                    }
                }
            }
        });
    }

    // 2. Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        new Chart(monthlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Proyectos',
                    data: window.DashboardConfig.monthlyData,
                    backgroundColor: '#4e73df',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { drawBorder: false }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    // 3. Tasks Donut Chart
    const tasksCtx = document.getElementById('tasksChart');
    if (tasksCtx) {
        new Chart(tasksCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Completadas', 'Atrasadas'],
                datasets: [{
                    data: window.DashboardConfig.taskStats,
                    backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
