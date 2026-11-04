// Swipe row (port of React Bits "SwipeRow"): drag a row sideways to reveal
// its actions. Works with touch, trackpad and mouse; the "more" button and
// Escape make it usable from the keyboard.
const HYST = 8;
const rubber = (o, dim, c = 0.55) => (o * dim * c) / (dim + c * Math.abs(o));

function build(row) {
    const surface = row.querySelector('.sw-surface');
    const actions = row.querySelector('.sw-actions');
    const toggle = row.querySelector('[data-swipe-toggle]');
    const D = actions.offsetWidth;
    let open = false;
    let x = 0;
    let grip = null;
    let moved = false;

    const set = (value, animate) => {
        x = value;
        surface.style.transform = `translateX(${value}px)`;
        if (!animate) return;
    };
    const settle = (next) => {
        open = next;
        row.classList.remove('is-dragging');
        set(open ? -D : 0, true);
        actions.inert = !open;
        toggle?.setAttribute('aria-expanded', String(open));
        if (open) document.dispatchEvent(new CustomEvent('swipe:open', { detail: row }));
    };
    actions.inert = true;

    surface.addEventListener('pointerdown', (e) => {
        if (e.button !== 0 || e.target.closest('[data-no-swipe], input, textarea, select')) return;
        grip = { id: e.pointerId, x0: e.clientX, y0: e.clientY, start: x, hist: [[performance.now(), e.clientX]], drag: false };
        moved = false;
    });
    surface.addEventListener('pointermove', (e) => {
        if (!grip || grip.id !== e.pointerId) return;
        const dx = e.clientX - grip.x0;
        const dy = e.clientY - grip.y0;
        if (!grip.drag) {
            if (Math.abs(dy) > HYST && Math.abs(dy) > Math.abs(dx)) { grip = null; return; }
            if (Math.abs(dx) < HYST) return;
            grip.drag = true;
            moved = true;
            row.classList.add('is-dragging');
            try { surface.setPointerCapture(e.pointerId); } catch { /* ignore */ }
        }
        grip.hist.push([performance.now(), e.clientX]);
        if (grip.hist.length > 6) grip.hist.shift();
        const raw = grip.start + dx;
        set(raw > 0 ? rubber(raw, D) : raw < -D ? -D + rubber(raw + D, D) : raw, false);
    });
    const end = (e) => {
        if (!grip || grip.id !== e.pointerId) return;
        const g = grip;
        grip = null;
        if (!g.drag) return;
        const a = g.hist[0];
        const b = g.hist[g.hist.length - 1];
        const v = ((b[1] - a[1]) / Math.max(1, b[0] - a[0])) * 1000;
        settle(x + v * 0.25 < -D / 2);
    };
    surface.addEventListener('pointerup', end);
    surface.addEventListener('pointercancel', end);

    // A drag must not count as a click on the link inside the row.
    surface.addEventListener('click', (e) => {
        if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
        else if (open && !e.target.closest('[data-swipe-toggle]')) settle(false);
    }, true);

    toggle?.addEventListener('click', () => settle(!open));
    row.addEventListener('keydown', (e) => { if (e.key === 'Escape' && open) { settle(false); toggle?.focus(); } });
    document.addEventListener('swipe:open', (e) => { if (e.detail !== row && open) settle(false); });
    document.addEventListener('pointerdown', (e) => { if (open && !row.contains(e.target)) settle(false); });
}

export function initSwipeRows(root = document) {
    root.querySelectorAll('[data-swipe-row]').forEach(build);
}
