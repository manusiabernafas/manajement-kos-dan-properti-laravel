import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    DoughnutController,
    ArcElement,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    DoughnutController,
    ArcElement,
    Tooltip,
    Legend,
    Filler
);

function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

function chartDefaults() {
    return {
        text: cssVar('--color-text'),
        muted: cssVar('--color-muted'),
        border: cssVar('--color-border'),
        primary: cssVar('--color-primary'),
        success: cssVar('--color-success'),
        warning: cssVar('--color-warning'),
        danger: cssVar('--color-danger'),
        info: cssVar('--color-info'),
    };
}

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 12;

const registry = {};

function baseGridOptions() {
    const c = chartDefaults();
    return {
        grid: { color: c.border, drawBorder: false },
        ticks: { color: c.muted },
    };
}

export function renderRevenueExpenseChart(canvasId, labels, revenue, expense) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    const c = chartDefaults();
    destroyIfExists(canvasId);

    registry[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Pendapatan', data: revenue, backgroundColor: c.success, borderRadius: 6, maxBarThickness: 22 },
                { label: 'Pengeluaran', data: expense, backgroundColor: c.danger, borderRadius: 6, maxBarThickness: 22 },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: c.text, usePointStyle: true, boxWidth: 8 } } },
            scales: { x: { ...baseGridOptions(), grid: { display: false } }, y: baseGridOptions() },
        },
    });
}

export function renderRevenue12MonthChart(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    const c = chartDefaults();
    destroyIfExists(canvasId);

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, c.primary + '55');
    gradient.addColorStop(1, c.primary + '00');

    registry[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data,
                borderColor: c.primary,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: c.primary,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { ...baseGridOptions(), grid: { display: false } }, y: baseGridOptions() },
        },
    });
}

export function renderRoomStatusChart(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    const c = chartDefaults();
    destroyIfExists(canvasId);

    registry[canvasId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: [c.success, c.info, c.warning, c.danger, c.muted],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: { legend: { position: 'bottom', labels: { color: c.text, usePointStyle: true, boxWidth: 8 } } },
        },
    });
}

export function renderOccupancyChart(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    const c = chartDefaults();
    destroyIfExists(canvasId);

    registry[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Occupancy Rate (%)', data, backgroundColor: c.primary, borderRadius: 6, maxBarThickness: 26 }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { ...baseGridOptions(), max: 100 }, y: { grid: { display: false }, ticks: { color: c.muted } } },
        },
    });
}

export function renderLineChart(canvasId, labels, datasets) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    const c = chartDefaults();
    destroyIfExists(canvasId);

    registry[canvasId] = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: c.text, usePointStyle: true, boxWidth: 8 } } },
            scales: { x: { ...baseGridOptions(), grid: { display: false } }, y: baseGridOptions() },
        },
    });
}

function destroyIfExists(canvasId) {
    if (registry[canvasId]) {
        registry[canvasId].destroy();
        delete registry[canvasId];
    }
}

let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        Object.values(registry).forEach((chart) => chart.resize());
    }, 150);
});

window.KMCharts = {
    renderRevenueExpenseChart,
    renderRevenue12MonthChart,
    renderRoomStatusChart,
    renderOccupancyChart,
    renderLineChart,
};
