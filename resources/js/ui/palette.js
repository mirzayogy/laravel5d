// Command palette (Cmd/Ctrl + K): jump to any page, add a habit, log a mood, or open one of your habits by typing.
import { canInstall, install } from './pwa.js';

const MAX = 9;

function score(item, q) {
    if (!q) return 1;
    const hay = `${item.label} ${item.hint ?? ''} ${item.keywords ?? ''}`.toLowerCase();
    let total = 0;
    for (const token of q.toLowerCase().split(/\s+/).filter(Boolean)) {
        const i = hay.indexOf(token);
        if (i < 0) return 0;
        total += i === 0 || hay[i - 1] === ' ' ? 3 : 1; // a word start beats a middle-of-word match
    }
    return total;
}

export function initPalette() {
    const dialog = document.getElementById('palette');
    if (!dialog) return;
    const input = dialog.querySelector('[data-palette-input]');
    const list = dialog.querySelector('[data-palette-list]');
    const empty = dialog.querySelector('[data-palette-empty]');
    const items = JSON.parse(document.getElementById('palette-data').textContent);
    let shown = [];
    let active = 0;

    const run = (item) => {
        dialog.close();
        if (item.url) return void (location.href = item.url);
        switch (item.action) {
            case 'quick-add': {
                const qa = document.getElementById('quick-add');
                if (qa) qa.showModal(); else location.href = item.fallback || '/habits?add=1';
                break;
            }
            case 'sidebar': document.querySelector('[data-sidebar-toggle]')?.click(); break;
            case 'install': install(); break;
            case 'logout': document.getElementById('logout-form')?.requestSubmit(); break;
        }
    };

    const render = () => {
        const q = input.value.trim();
        const pool = items.filter((it) => it.action !== 'install' || canInstall());
        shown = pool.map((it) => [it, score(it, q)]).filter(([, s]) => s > 0).sort((a, b) => b[1] - a[1]).slice(0, q ? MAX : 14).map(([it]) => it);
        active = Math.min(active, Math.max(0, shown.length - 1));
        list.innerHTML = '';
        let group = null;
        shown.forEach((it, i) => {
            if (!q && it.group !== group) {
                group = it.group;
                const h = document.createElement('li');
                h.className = 'pal-group';
                h.textContent = group;
                h.setAttribute('role', 'presentation');
                list.append(h);
            }
            const li = document.createElement('li');
            li.className = 'pal-item';
            li.setAttribute('role', 'option');
            li.id = `pal-${i}`;
            li.dataset.index = String(i);
            li.setAttribute('aria-selected', String(i === active));
            li.innerHTML = '<span class="pal-label"></span><span class="pal-hint"></span>';
            li.querySelector('.pal-label').textContent = it.label;
            li.querySelector('.pal-hint').textContent = it.hint ?? (q ? it.group : '');
            list.append(li);
        });
        empty.hidden = shown.length > 0;
        input.setAttribute('aria-activedescendant', shown.length ? `pal-${active}` : '');
        list.querySelector('[aria-selected="true"]')?.scrollIntoView({ block: 'nearest' });
    };

    const open = () => {
        if (dialog.open) return;
        input.value = '';
        active = 0;
        dialog.showModal();
        render();
        input.focus();
    };

    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); dialog.open ? dialog.close() : open(); }
    });
    document.addEventListener('click', (e) => { if (e.target.closest('[data-palette-open]')) open(); });
    document.addEventListener('pwa:installable', () => dialog.open && render());

    input.addEventListener('input', () => { active = 0; render(); });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(shown.length - 1, active + 1); render(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(0, active - 1); render(); }
        else if (e.key === 'Enter' && shown[active]) { e.preventDefault(); run(shown[active]); }
    });
    list.addEventListener('click', (e) => { const li = e.target.closest('.pal-item'); if (li) run(shown[Number(li.dataset.index)]); });
    list.addEventListener('pointermove', (e) => {
        const li = e.target.closest('.pal-item');
        if (li && Number(li.dataset.index) !== active) { active = Number(li.dataset.index); render(); }
    });
}
