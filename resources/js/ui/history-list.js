// A history list that shows a few rows and can be extended: a row-limit select (remembered in the
// browser) and a "Show more" button. Markup: [data-history] with [data-history-row] rows,
// [data-history-limit] holding the glide select, [data-history-summary] and [data-history-more].
import { t } from './i18n.js';

function build(box) {
    const key = box.dataset.historyKey || 'habitude.historyRows';
    const fallback = box.dataset.historyDefault || '5';
    const rows = [...box.querySelectorAll('[data-history-row]')];
    const more = box.querySelector('[data-history-more]');
    const summary = box.querySelector('[data-history-summary]');

    let limit = fallback;
    try { limit = localStorage.getItem(key) || fallback; } catch { /* storage unavailable */ }
    let expanded = false;

    const render = () => {
        const max = limit === 'all' || expanded ? Infinity : Number(limit);
        rows.forEach((row, i) => row.classList.toggle('hidden', i >= max));
        const shown = Math.min(rows.length, max);
        if (summary) summary.textContent = rows.length ? t('Showing :shown of :total', { shown, total: rows.length }) : '';
        if (more) {
            const rest = rows.length - shown;
            more.hidden = rest <= 0 && !expanded;
            more.textContent = expanded ? t('Show fewer') : t('Show :n more', { n: rest });
            more.setAttribute('aria-expanded', String(expanded));
        }
    };

    box.querySelector('[data-history-limit] [data-glide-select]')?.glideSet?.(limit);
    box.addEventListener('change', (e) => {
        if (!e.target.closest('[data-history-limit]')) return;
        limit = e.target.value || fallback;
        try { localStorage.setItem(key, limit); } catch { /* storage unavailable */ }
        expanded = false;
        render();
    });
    more?.addEventListener('click', () => { expanded = !expanded; render(); });

    render();
}

export function initHistoryLists(root = document) {
    root.querySelectorAll('[data-history]').forEach(build);
}
