// "Today's habits": filter (All / To do / Done) through the rubber segment and
// show only a few rows, with a per-person row limit remembered in the browser.
import { t } from './i18n.js';

const KEY = 'habitude.todayRows';
const DEFAULT = '5';

const readLimit = () => { try { return localStorage.getItem(KEY) || DEFAULT; } catch { return DEFAULT; } };
const writeLimit = (v) => { try { localStorage.setItem(KEY, v); } catch { /* storage unavailable */ } };

function build(box) {
    const rows = () => [...box.querySelectorAll('[data-habit-row]')];
    const empty = box.querySelector('[data-today-empty]');
    const more = box.querySelector('[data-today-more]');
    const summary = box.querySelector('[data-today-summary]');
    const segment = box.querySelector('[data-rubber-segment]');
    let filter = segment?.dataset.value || 'all';
    let limit = readLimit();
    let expanded = false;
    let timer = 0;

    const matches = (row) => filter === 'all' || (filter === 'todo' ? row.dataset.done !== '1' : row.dataset.done === '1');

    const render = () => {
        const all = rows();
        const visible = all.filter(matches);
        const max = limit === 'all' || expanded ? Infinity : Number(limit);
        all.forEach((row) => row.classList.add('hidden'));
        visible.forEach((row, i) => row.classList.toggle('hidden', i >= max));

        // a day-part header is shown only while one of its rows is
        box.querySelectorAll('[data-today-group]').forEach((h) => {
            h.classList.toggle('hidden', !all.some((r) => r.dataset.group === h.dataset.todayGroup && !r.classList.contains('hidden')));
        });

        const shown = Math.min(visible.length, max);
        if (empty) empty.hidden = visible.length > 0;
        if (more) {
            const rest = visible.length - shown;
            more.hidden = rest <= 0 && !expanded;
            more.textContent = expanded ? t('Show fewer') : t('Show :n more', { n: rest });
            more.setAttribute('aria-expanded', String(expanded));
        }
        if (summary) summary.textContent = visible.length ? t('Showing :shown of :total', { shown, total: visible.length }) : '';

        const counts = { all: all.length, todo: all.filter((r) => r.dataset.done !== '1').length, done: all.filter((r) => r.dataset.done === '1').length };
        Object.entries(counts).forEach(([k, n]) => box.querySelectorAll(`[data-count="${k}"]`).forEach((c) => (c.textContent = n)));
    };

    segment?.addEventListener('rubber:change', (e) => { filter = e.detail.value; expanded = false; render(); });

    // the row limit is a glide select; show the saved choice in it, and listen for changes
    box.querySelector('[data-today-limit] [data-glide-select]')?.glideSet?.(limit);
    box.addEventListener('change', (e) => {
        if (!e.target.closest('[data-today-limit]')) return;
        limit = e.target.value || DEFAULT;
        writeLimit(limit);
        expanded = false;
        render();
    });
    more?.addEventListener('click', () => { expanded = !expanded; render(); });

    // a row was checked or unchecked: refresh counts, and let the "To do" view drop it after the spring has played
    box.addEventListener('today:refresh', () => {
        clearTimeout(timer);
        timer = setTimeout(render, filter === 'all' ? 0 : 750);
        const all = rows();
        ['todo', 'done'].forEach((k) => {
            const n = all.filter((r) => (k === 'todo' ? r.dataset.done !== '1' : r.dataset.done === '1')).length;
            box.querySelectorAll(`[data-count="${k}"]`).forEach((c) => (c.textContent = n));
        });
    });

    render();
}

export function initTodayLists(root = document) {
    root.querySelectorAll('[data-today]').forEach(build);
}
