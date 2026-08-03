import Chart from 'chart.js/auto';

const BRANCH_PALETTE = ['#d97706', '#059669', '#0284c7', '#7c3aed', '#dc2626', '#0891b2', '#ca8a04', '#4f46e5'];

function fmt(v) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v));
}

function tooltipBase() {
    return {
        backgroundColor: '#292524',
        titleFont: { size: 12, weight: '600' },
        bodyFont: { size: 12 },
        footerFont: { size: 11, weight: '600' },
        padding: { x: 14, y: 10 },
        cornerRadius: 10,
        displayColors: true,
        boxPadding: { x: 6, y: 4 },
    };
}

function branchGradient(ctx, area, color, opacity) {
    const g = ctx.createLinearGradient(0, area.top, 0, area.bottom);
    g.addColorStop(0, color + opacity);
    g.addColorStop(1, color + '00');
    return g;
}

function pctChange(current, previous) {
    if (!previous || previous === 0) return '';
    const pct = ((current - previous) / previous) * 100;
    const sign = pct >= 0 ? '↑' : '↓';
    return `${sign}${Math.abs(pct).toFixed(1)}%`;
}

export function profitBranchChart(config) {
    return {
        _chart: null,
        init() {
            const allProfitValues = config.profitBranches.flatMap(b => b.data);
            const dataMax = Math.max(...allProfitValues, 1);
            const positiveValues = allProfitValues.filter(v => v > 0);
            const dataMin = positiveValues.length > 0 ? Math.min(...positiveValues) : 0;
            const yMax = Math.ceil(dataMax * 1.2);
            const yMin = dataMax > 0 ? Math.floor(dataMin * 0.85) : 0;

            const datasets = config.profitBranches.map((b, i) => {
                const color = BRANCH_PALETTE[i % BRANCH_PALETTE.length];
                return {
                    label: b.name,
                    data: b.data,
                    borderColor: color,
                    backgroundColor: c => {
                        if (!c.chart.chartArea) return color + '1A';
                        return branchGradient(c.chart.ctx, c.chart.chartArea, color, '33');
                    },
                    fill: true, tension: 0.35,
                    pointRadius: 4, pointHoverRadius: 7,
                    pointBackgroundColor: 'white', pointBorderColor: color,
                    pointBorderWidth: 2, borderWidth: 2.5,
                };
            });

            const canvas = this.$el.querySelector('canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            this._chart = new Chart(ctx, {
                type: 'line',
                data: { labels: config.labels, datasets },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20, font: { size: 12 }, color: '#57534e' },
                        },
                        tooltip: {
                            ...tooltipBase(),
                            callbacks: {
                                title: items => items[0].label,
                                label: ctx => {
                                    const val = ctx.parsed.y;
                                    const data = ctx.dataset.data;
                                    const idx = ctx.dataIndex;
                                    const prev = idx > 0 ? data[idx - 1] : null;
                                    const change = pctChange(val, prev);
                                    return ctx.dataset.label + ': ' + fmt(val) + (change ? '  ' + change : '');
                                },
                                footer: items => {
                                    const total = items.reduce((sum, i) => sum + i.parsed.y, 0);
                                    return '💰 Total Laba: ' + fmt(total);
                                },
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#a8a29e' } },
                        y: {
                            min: yMin, max: yMax,
                            grid: { color: '#f5f0e6', drawBorder: false },
                            ticks: { font: { size: 11 }, color: '#a8a29e', callback: v => fmt(v), maxTicksLimit: 6 },
                        },
                    },
                },
            });
        },
        destroy() {
            if (this._chart) this._chart.destroy();
        },
    };
}

export function branchChart(config) {
    return {
        _chart: null,
        init() {
            const allValues = config.branches.flatMap(b => b.data);
            const dataMax = Math.max(...allValues, 1);
            const yMax = Math.ceil(dataMax * 1.2);

            const datasets = config.branches.map((b, i) => ({
                label: b.name,
                data: b.data,
                backgroundColor: BRANCH_PALETTE[i % BRANCH_PALETTE.length] + 'CC',
                borderColor: BRANCH_PALETTE[i % BRANCH_PALETTE.length],
                borderWidth: 0, borderRadius: 2,
                barPercentage: 0.7, categoryPercentage: 0.8,
            }));

            const canvas = this.$el.querySelector('canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            this._chart = new Chart(ctx, {
                type: 'bar',
                data: { labels: config.labels, datasets },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: 800, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 16, font: { size: 11 }, color: '#57534e' },
                        },
                        tooltip: {
                            ...tooltipBase(),
                            callbacks: {
                                title: items => items[0].label,
                                label: ctx => {
                                    const val = ctx.parsed.y;
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ctx.dataset.label + ': ' + fmt(val) + ' (' + pct + '%)';
                                },
                                footer: items => {
                                    const total = items.reduce((sum, i) => sum + i.parsed.y, 0);
                                    return '💰 Total: ' + fmt(total);
                                },
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#a8a29e' } },
                        y: {
                            min: 0, max: yMax,
                            grid: { color: '#f5f0e6', drawBorder: false },
                            ticks: { font: { size: 10 }, color: '#a8a29e', callback: v => fmt(v), maxTicksLimit: 6 },
                        },
                    },
                },
            });
        },
        destroy() {
            if (this._chart) this._chart.destroy();
        },
    };
}

export function topProductsChart(config) {
    return { init() {} };
}
