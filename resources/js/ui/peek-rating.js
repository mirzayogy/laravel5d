// Peek rating (port of React Bits "PeekRating"): hovering lifts every glyph up
// to the pointer and a tooltip follows with the label. Click commits.
import { t } from './i18n.js';

const SHAPES = {
    star: '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>',
    heart: '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
};
const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function build(root) {
    const count = Number(root.dataset.count || 5);
    const labels = JSON.parse(root.dataset.labels || '[]');
    const shape = SHAPES[root.dataset.shape] ? root.dataset.shape : 'star';
    const allowClear = root.dataset.clear === 'true';
    const name = root.dataset.name;
    let value = Number(root.dataset.value || 0);
    let hover = null;
    let pressing = false;

    root.classList.add('pr');
    root.setAttribute('role', 'radiogroup');
    root.setAttribute('aria-label', root.dataset.label || 'Rating');

    const row = document.createElement('div');
    row.className = 'pr-row';
    const tip = document.createElement('span');
    tip.className = 'pr-tip';
    tip.setAttribute('aria-hidden', 'true');
    const stars = document.createElement('div');
    stars.className = 'pr-stars';
    stars.style.cssText = 'display:inline-flex;align-items:flex-end';
    row.append(tip, stars);

    const lifts = [];
    const glyphs = [];
    const buttons = [];
    for (let i = 0; i < count; i++) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'pr-star';
        b.setAttribute('role', 'radio');
        b.setAttribute('aria-label', labels[i] ? t(':n of :total, :label', { n: i + 1, total: count, label: labels[i] }) : t(':n of :total', { n: i + 1, total: count }));
        const lift = document.createElement('span');
        lift.className = 'pr-lift';
        const glyph = document.createElement('span');
        glyph.className = 'pr-glyph';
        glyph.innerHTML = `<svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">${SHAPES[shape]}</svg>`;
        lift.append(glyph);
        b.append(lift);
        stars.append(b);
        buttons.push(b);
        lifts.push(lift);
        glyphs.push(glyph);
    }
    root.append(row);

    let input = null;
    if (name) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        root.append(input);
    }

    const paint = () => {
        const previewing = hover !== null;
        const shown = previewing ? hover + 1 : value;
        glyphs.forEach((g, i) => (g.dataset.lit = String(i < shown)));
        lifts.forEach((l, i) => {
            const up = previewing && !reduced() && i <= hover;
            l.style.transform = up ? `translateY(-6px) scale(${i === hover ? 1.15 : 1})` : '';
        });
        buttons.forEach((b, i) => {
            b.setAttribute('aria-checked', String(value === i + 1));
            b.tabIndex = (value === 0 ? i === 0 : value === i + 1) ? 0 : -1;
        });
        if (input) input.value = value || '';
        if (previewing) {
            const slot = stars.clientWidth / count;
            const first = tip.dataset.show !== 'true';
            if (first) tip.style.transition = 'none';
            tip.textContent = labels[hover] ?? String(hover + 1);
            tip.style.transform = `translate(calc(${slot * (hover + 0.5)}px - 50%), 0)`;
            if (first) { void tip.offsetWidth; tip.style.transition = ''; }
            tip.dataset.show = 'true';
        } else {
            tip.dataset.show = 'false';
        }
    };

    const indexAt = (x) => {
        const r = stars.getBoundingClientRect();
        return Math.min(count - 1, Math.max(0, Math.floor(((x - r.left) / r.width) * count)));
    };

    const commit = (next, pop = true) => {
        value = next;
        paint();
        const g = glyphs[next - 1];
        if (pop && g && g.animate && !reduced()) {
            g.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.3)', offset: 0.35 }, { transform: 'scale(1)' }], { duration: 300, easing: 'cubic-bezier(0.23,1,0.32,1)' });
        }
        root.dataset.value = String(next);
        root.dispatchEvent(new CustomEvent('peek:change', { bubbles: true, detail: { value: next, label: labels[next - 1] } }));
    };

    row.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse' && !pressing) return;
        hover = indexAt(e.clientX);
        paint();
    });
    row.addEventListener('pointerdown', (e) => {
        if (e.button !== 0) return;
        pressing = true;
        try { row.setPointerCapture(e.pointerId); } catch { /* ignore */ }
        hover = indexAt(e.clientX);
        paint();
    });
    row.addEventListener('pointerup', (e) => {
        if (!pressing) return;
        pressing = false;
        const picked = hover === null ? null : hover + 1;
        if (picked !== null) commit(allowClear && picked === value ? 0 : picked);
        if (e.pointerType !== 'mouse') hover = null;
        paint();
    });
    row.addEventListener('pointercancel', () => { pressing = false; hover = null; paint(); });
    row.addEventListener('pointerleave', () => { if (!pressing) { hover = null; paint(); } });

    root.addEventListener('keydown', (e) => {
        const min = allowClear ? 0 : 1;
        let next;
        if (['ArrowRight', 'ArrowUp'].includes(e.key)) next = Math.min(count, value + 1);
        else if (['ArrowLeft', 'ArrowDown'].includes(e.key)) next = Math.max(min, value - 1);
        else if (e.key === 'Home') next = 1;
        else if (e.key === 'End') next = count;
        else return;
        e.preventDefault();
        hover = null;
        root.dataset.instant = '';
        commit(next, false);
        buttons[Math.max(next, 1) - 1].focus();
        setTimeout(() => delete root.dataset.instant, 50);
    });

    paint();
}

export function initPeekRatings(root = document) {
    root.querySelectorAll('[data-peek-rating]').forEach(build);
}
