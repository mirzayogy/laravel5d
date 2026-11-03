import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* Countdown to a moment in time, e.g. the end of a competition. */
Alpine.data('countdown', (target) => ({
    target: new Date(target).getTime(),
    now: Date.now(),
    init() {
        setInterval(() => {
            this.now = Date.now();
        }, 1000);
    },
    get left() {
        return Math.max(0, this.target - this.now);
    },
    get done() {
        return this.left === 0;
    },
    get days() {
        return Math.floor(this.left / 86400000);
    },
    get hours() {
        return Math.floor(this.left / 3600000) % 24;
    },
    get minutes() {
        return Math.floor(this.left / 60000) % 60;
    },
    get seconds() {
        return Math.floor(this.left / 1000) % 60;
    },
    pad(value) {
        return String(value).padStart(2, '0');
    },
}));

/* Copy a text to the clipboard and show a short "copied" state. */
Alpine.data('copy', (text) => ({
    copied: false,
    async copy() {
        try {
            await navigator.clipboard.writeText(text);
        } catch (error) {
            const input = document.createElement('textarea');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
        }
        this.copied = true;
        setTimeout(() => {
            this.copied = false;
        }, 1800);
    },
}));

/* Write / preview tabs for Markdown fields. The preview is rendered by the server. */
Alpine.data('mdPreview', (url) => ({
    tab: 'write',
    html: '',
    loading: false,
    async show(content) {
        this.tab = 'preview';
        this.loading = true;
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({ content }),
            });
            this.html = (await response.json()).html ?? '';
        } catch (error) {
            this.html = '<p>The preview could not be loaded.</p>';
        }
        this.loading = false;
    },
}));

const palette = ['#0ea5e9', '#ff6b4a', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899', '#14b8a6', '#64748b'];

/*
 * Scoreboard: draws the score chart and refreshes the table and the chart
 * from the JSON endpoint every few seconds while the competition is running.
 */
Alpine.data('scoreboard', ({ url, live, interval = 20000, myTeamId = null, series = null }) => {
    // The chart lives outside of Alpine's reactive state, because Chart.js does not work through proxies.
    let chart = null;

    return {
        updatedAt: null,
        init() {
            this.$nextTick(() => {
                if (series) {
                    this.renderChart(series);
                }

                this.refresh();

                if (live) {
                    setInterval(() => this.refresh(), interval);
                }
            });
        },
        async refresh() {
            try {
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                this.renderTable(data.ranking);
                this.renderChart(data.series);
                this.updatedAt = new Date().toLocaleTimeString();
            } catch (error) {
                console.error('Scoreboard refresh failed', error);
            }
        },
        renderTable(rows) {
            const body = this.$refs.body;
            if (!body || !rows.length) {
                return;
            }
            body.replaceChildren(
                ...rows.map((row, index) => {
                    const tr = document.createElement('tr');
                    tr.className =
                        'border-t border-sky-50 ' + (row.team_id === myTeamId ? 'bg-coral-50 font-medium' : 'hover:bg-sky-50/60');
                    const cells = [index + 1, row.name, row.solves, row.points, row.hint_cost, row.score, row.last_solved_at ?? '-'];
                    cells.forEach((value, position) => {
                        const td = document.createElement('td');
                        td.className = 'px-4 py-3' + (position >= 2 && position <= 5 ? ' text-right tabular-nums' : '');
                        if (position === 5) {
                            td.className += ' font-extrabold';
                        }
                        if (position === 6) {
                            td.className += ' text-slate-500';
                        }
                        td.textContent = value;
                        tr.appendChild(td);
                    });
                    return tr;
                }),
            );
        },
        renderChart(data) {
            const canvas = this.$refs.chart;
            if (!canvas) {
                return;
            }
            const datasets = data.map((team, index) => ({
                label: team.name,
                data: team.points.map(([time, score]) => ({ x: time, y: score })),
                borderColor: palette[index % palette.length],
                backgroundColor: palette[index % palette.length],
                stepped: true,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 5,
            }));
            if (chart) {
                chart.data.datasets = datasets;
                chart.update('none');
                return;
            }
            chart = new Chart(canvas, {
                type: 'line',
                data: { datasets },
                options: {
                    parsing: false,
                    animation: false,
                    maintainAspectRatio: false,
                    interaction: { mode: 'nearest', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
                    scales: {
                        x: {
                            type: 'linear',
                            grid: { display: false },
                            ticks: {
                                maxTicksLimit: 6,
                                callback: (value) =>
                                    new Date(value).toLocaleString([], { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }),
                            },
                        },
                        y: { beginAtZero: true, grid: { color: '#e0f2fe' } },
                    },
                },
            });
        },
    };
});

/* Small bar chart, used on the admin dashboard. */
Alpine.data('barChart', (labels, values, color = '#0ea5e9') => ({
    init() {
        new Chart(this.$refs.canvas, {
            type: 'bar',
            data: { labels, datasets: [{ data: values, backgroundColor: color, borderRadius: 8, maxBarThickness: 28 }] },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#e0f2fe' }, ticks: { precision: 0 } } },
            },
        });
    },
}));

Alpine.start();
