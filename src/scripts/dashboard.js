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

    canvas.classList.add('wait');

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
    } finally {
        canvas.classList.remove('wait');
    }
}

async function initStatesChart() {
    const canvas = document.getElementById('chart-states');
    if (!canvas) return;

    canvas.classList.add('wait');

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
    } finally {
        canvas.classList.remove('wait');
    }
}

async function initActivityChart() {
    const canvas = document.getElementById('chart-activity');

    canvas.classList.add('wait');

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
    } finally {
        canvas.classList.remove('wait');
    }
}

async function initConnexionChart() {
    const canvas = document.getElementById('chart-connexion');
    if (!canvas) return;

    canvas.classList.add('wait');

    try {
        const response = await fetch('../api/chart-connexion.php');
        const dataR = await response.json();

        const labels = dataR.map(item => item.Palier + " min");
        const valeurs = dataR.map(item => item.Effectif);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de connexions',
                    data: valeurs,
                    backgroundColor: 'rgba(63, 81, 181, 0.5)',
                    borderColor: '#3f51b5',
                    borderWidth: 1
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
        console.error('[chart-connexion]', e);
        chartError(canvas);
    } finally {
        canvas.classList.remove('wait');
    }
}

let stat1Chart = null;
let villeChoisie = 'Vélizy';
let memoireChoisie = 4096;
let diskChoisie = 256;

async function initSpecMinimumChart(ville=villeChoisie, memory=memoireChoisie, disk=diskChoisie) {
    const canvas = document.getElementById('chart-spec-minimum');
    if (!canvas) return;

    canvas.classList.add('wait');

    try {
        const response = await fetch(`../api/chart-pc-ok.php?ville=${ville}&ram=${memory}&disk=${disk}`);
        const dataR = await response.json();

        if (stat1Chart) {
            stat1Chart.destroy();
        }

        stat1Chart = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: dataR.labels,
                datasets: [{
                    data: dataR.valeurs,
                    backgroundColor: ['#4caf50', '#f44336'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { padding: 12, boxWidth: 14 }
                    }
                    
                }
            }
        })
    } catch (e) {
        console.error('[chart-spec-minimum]', e);
        chartError(canvas);
    } finally {
        canvas.classList.remove('wait');
    }
}

document.getElementById('select-city')?.addEventListener('change', (event) => {
    villeChoisie = event.target.value; 
    initSpecMinimumChart(villeChoisie, memoireChoisie, diskChoisie);
});

document.getElementById('select-memory')?.addEventListener('change', (event) => {
    memoireChoisie = event.target.value;
    initSpecMinimumChart(villeChoisie, memoireChoisie, diskChoisie);
});

document.getElementById('select-disk')?.addEventListener('change', (event) => {
    diskChoisie = event.target.value;
    initSpecMinimumChart(villeChoisie, memoireChoisie, diskChoisie);
});

async function initIncoherenceChart() {
    const canvas = document.getElementById('chart-incoherence');
    if (!canvas) return;

    canvas.classList.add('wait');

    try {
        // On définit des seuils par défaut (ex: moins de 8Go RAM et écran >= 27 pouces)
        const response = await fetch('../api/chart-incoherence.php?ram=8192&ecran=27');
        const dataR = await response.json();

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: dataR.map(d => d.ville),
                datasets: [{
                    label: 'Postes incohérents',
                    data: dataR.map(d => d.nb),
                    backgroundColor: '#ff9800', // Orange pour l'alerte
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y', // Transforme les barres en horizontales
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    } catch (e) {
        console.error('[chart-incoherence]', e);
        chartError(canvas);
    } finally {
        canvas.classList.remove('wait');
    }
}

async function initWarrantyChart() {
    const canvas = document.getElementById('chart-warranties');
    if (!canvas) return;

    canvas.classList.add('wait');

    try {
        const response = await fetch('../api/chart-warranties.php?debut=2026-01-01&mois=18');
        const rawData = await response.json();

        // 1. Extraire les mois uniques pour l'axe X
        const labels = [...new Set(rawData.map(d => d.Mois))].sort();

        // 2. Créer les datasets par constructeur avec tes couleurs spécifiques
        const manufacturers = {
            'Dell':   '#9C51B6',
            'HP':     '#005FFF',
            'Lenovo': '#E00000'
        };

        const datasets = Object.keys(manufacturers).map(m => ({
            label: m,
            data: labels.map(mois => {
                const found = rawData.find(d => d.Mois === mois && d.Constructeur === m);
                return found ? found.Nombre : 0;
            }),
            backgroundColor: manufacturers[m]
        }));

        new Chart(canvas, {
            type: 'bar',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true }
                },
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { padding: 12, boxWidth: 14 }
                    }
                }
            }
        });
    } catch (e) {
        console.error('[chart-warranties]', e);
    } finally {
        canvas.classList.remove('wait');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Promise.all([
        initDevicesByTypeChart(),
        initStatesChart(),
        initActivityChart(),
        initConnexionChart(),
        initSpecMinimumChart(),
        initIncoherenceChart(),
        initWarrantyChart()
    ]);
});
