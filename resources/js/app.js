import './bootstrap';
import Chart from 'chart.js/auto';

const chartInstances = new WeakMap();

function getJsonData(element, key) {
    const raw = element.dataset[key];

    if (!raw) {
        return null;
    }

    try {
        return JSON.parse(raw);
    } catch (error) {
        return null;
    }
}

function destroyChart(canvas) {
    const existing = chartInstances.get(canvas);

    if (existing) {
        existing.destroy();
        chartInstances.delete(canvas);
    }
}

function initYearlyCharts() {
    document.querySelectorAll('[data-yearly-chart]').forEach((canvas) => {
        destroyChart(canvas);

        const chartType = canvas.dataset.chartType;
        const labels = getJsonData(canvas, 'chartLabels') ?? [];
        const values = getJsonData(canvas, 'chartValues') ?? [];
        const started = getJsonData(canvas, 'chartStarted') ?? [];
        const completed = getJsonData(canvas, 'chartCompleted') ?? [];
        const overdue = getJsonData(canvas, 'chartOverdue') ?? [];
        const links = getJsonData(canvas, 'chartLinks') ?? [];

        const config = chartType === 'line'
            ? {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'เริ่มใหม่',
                            data: started,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.12)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                        },
                        {
                            label: 'เสร็จ',
                            data: completed,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                        },
                        {
                            label: 'ค้าง',
                            data: overdue,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.10)',
                            tension: 0.35,
                            fill: false,
                            pointRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 10,
                                boxHeight: 10,
                            },
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.18)',
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                },
            }
            : chartType === 'doughnut'
                ? {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [
                            {
                                data: values,
                                backgroundColor: [
                                    '#2563eb',
                                    '#f59e0b',
                                    '#10b981',
                                    '#f43f5e',
                                    '#8b5cf6',
                                ],
                                borderWidth: 0,
                                hoverOffset: 6,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 10,
                                    boxHeight: 10,
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => ` ${context.label}: ${context.parsed}`,
                                },
                            },
                        },
                    },
                }
            : {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'โครงการ',
                            data: values,
                            borderRadius: 12,
                            backgroundColor: 'rgba(37, 99, 235, 0.92)',
                            hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (_event, elements) => {
                        if (!elements.length || !links.length) {
                            return;
                        }

                        const index = elements[0].index;
                        const target = links[index];

                        if (target) {
                            window.location.href = target;
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => ` ${context.parsed.y} โครงการ`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.18)',
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                },
            };

        if (chartType === 'bar' && links.length) {
            canvas.style.cursor = 'pointer';
        }

        const chart = new Chart(canvas, config);
        chartInstances.set(canvas, chart);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initYearlyCharts();
});
