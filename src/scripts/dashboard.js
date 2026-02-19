Chart.defaults.color = 'rgb(173,173,173)';
Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';
Chart.defaults.font.family = 'Arial, Helvetica, sans-serif';

const STATE_COLORS = {
    'table-item-DEPLOYED':    '#52ff58',
    'table-item-END_OF_LIFE': '#a10000',
    'table-item-IN_INVENTORY':'#808080',
};
const STATE_COLORS_DEFAULT = '#3f51b5';

function chartError(canvas, message = 'Erreur de chargement') {
    const card = canvas.closest('.chart-card');
    canvas.style.display = 'none';
    const p = document.createElement('p');
    p.className = 'chart-error';
    p.textContent = message;
    card.appendChild(p);
}

async function fetchChartData(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

async function initDevicesByTypeChart() {
    const canvas = document.getElementById('chart-devices-by-type');
    if (!canvas) return;

    try {
        const { labels, data } = await fetchChartData('../api/chart-devices-by-type.php');

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Appareils',
                    data,
                    backgroundColor: 'rgba(63, 81, 181, 0.7)',
                    borderColor:     '#3f51b5',
                    borderWidth: 1.5,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(255,255,255,0.07)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (e) {
        console.error('[chart-devices-by-type]', e);
        chartError(canvas);
    }
}

async function initStatesChart() {
    const canvas = document.getElementById('chart-states');
    if (!canvas) return;

    try {
        const { labels, data, classes } = await fetchChartData('../api/chart-states.php');

        const bgColors     = classes.map(c => STATE_COLORS[c] ?? STATE_COLORS_DEFAULT);
        const borderColors = bgColors.map(c => c + 'cc');

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: bgColors,
                    borderColor:     borderColors,
                    borderWidth: 2,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, boxWidth: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed}`
                        }
                    }
                },
                cutout: '62%'
            }
        });
    } catch (e) {
        console.error('[chart-states]', e);
        chartError(canvas);
    }
}

async function initActivityChart() {
    const canvas = document.getElementById('chart-activity');
    if (!canvas) return;

    try {
        const { labels, data } = await fetchChartData('../api/chart-activity.php');

        const shortLabels = labels.map(d => {
            const [, m, day] = d.split('-');
            return `${day}/${m}`;
        });

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: shortLabels,
                datasets: [{
                    label: 'Actions',
                    data,
                    fill: true,
                    backgroundColor: 'rgba(63, 81, 181, 0.15)',
                    borderColor:     '#3f51b5',
                    borderWidth: 2,
                    pointBackgroundColor: '#3f51b5',
                    pointRadius: 3,
                    tension: 0.35,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(255,255,255,0.07)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (e) {
        console.error('[chart-activity]', e);
        chartError(canvas);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Promise.all([
        initDevicesByTypeChart(),
        initStatesChart(),
        initActivityChart(),
    ]);
});
