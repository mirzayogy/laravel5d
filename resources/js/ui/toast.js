// Swipe toast (port of React Bits "SwipeToast"): slides up from the corner,
// burns a fuse while it waits, pauses while you look at it, and can be thrown
// away by swiping it down. Upward drags resist like a rubber band.
import { t } from './i18n.js';
import { reducedMotion, springTo } from './spring.js';

const FLICK = 0.11;       // px per ms
const DEAD_ZONE = 3;
const RESIST_PX = 24;
const SWIPE_DISTANCE = 40;
const SLIDE_MS = 400;
const EXIT = 0.7;
const COLLAPSE_MS = 200;

const rubberband = (over, dim, c = 0.55) => (over * dim * c) / (dim + c * Math.abs(over));
const velocityOf = (hist) => {
    if (hist.length < 2) return 0;
    const [t0, y0] = hist[0];
    const [t1, y1] = hist[hist.length - 1];
    return performance.now() - t1 > 100 ? 0 : (y1 - y0) / Math.max(1, t1 - t0);
};

const ICONS = {
    trophy: '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>',
    info: '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
    check: '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
    alert: '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>',
    bell: '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
};
const CLOSE = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';
const svg = (name, size = 18) => `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONS[name] ?? ICONS.info}</svg>`;

export function toast({ title, body = '', icon = 'trophy', duration = 4000, action = null, closeButton = true } = {}) {
    const host = document.getElementById('toasts');
    if (!host || !title) return;

    const el = document.createElement('div');
    el.className = 'toast';
    el.dataset.phase = 'open';
    el.dataset.mounted = 'false';
    el.innerHTML = `
        <div class="toast-clip"><div class="toast-slide">
            <div class="toast-card" role="status" aria-live="polite" aria-atomic="true" tabindex="0">
                <span class="toast-icon">${svg(icon)}</span>
                <span class="toast-text"><span class="toast-title"></span><span class="toast-desc"></span></span>
                ${action ? '<button type="button" class="toast-btn toast-action"></button>' : ''}
                ${closeButton ? `<button type="button" class="toast-btn toast-close" aria-label="${t('Close')}">${CLOSE}</button>` : ''}
                <i class="toast-fuse" aria-hidden="true"></i>
            </div>
        </div></div>`;
    el.querySelector('.toast-title').textContent = title;
    const desc = el.querySelector('.toast-desc');
    desc.textContent = body;
    desc.hidden = !body;
    if (action) el.querySelector('.toast-action').textContent = action.label;
    host.append(el);

    const card = el.querySelector('.toast-card');
    const fuse = el.querySelector('.toast-fuse');
    const flags = { hover: false, interacting: false, focus: false, hidden: document.hidden };
    let anim = null;
    let y = 0;
    let ySpring = null;
    let drag = null;
    let leaving = false;
    let closed = false;
    let pendingClose = null;
    let timer = 0;

    const paintY = (v) => { y = v; card.style.transform = `translateY(${v}px)`; };
    const syncFuse = () => {
        if (!anim) return;
        if (flags.hover || flags.interacting || flags.focus || flags.hidden) anim.pause();
        else if (anim.playState === 'paused') anim.play();
    };
    const remove = () => {
        document.removeEventListener('visibilitychange', onVisibility);
        el.dataset.phase = 'gone';
        setTimeout(() => el.remove(), COLLAPSE_MS);
    };
    const close = (why) => {
        if (closed || leaving) return;
        if (drag) { pendingClose = why; return; }
        closed = true;
        anim?.pause();
        const instant = why === 'escape' || reducedMotion();
        el.dataset.phase = 'closing';
        if (instant) el.dataset.instant = '';
        clearTimeout(timer);
        timer = setTimeout(remove, instant ? 0 : SLIDE_MS * EXIT + 60);
    };
    const onVisibility = () => { flags.hidden = document.hidden; syncFuse(); };
    document.addEventListener('visibilitychange', onVisibility);

    // slide in on the next frame
    requestAnimationFrame(() => requestAnimationFrame(() => (el.dataset.mounted = 'true')));

    if (duration > 0) {
        anim = fuse.animate([{ transform: 'scaleX(1)' }, { transform: 'scaleX(0)' }], { duration, easing: 'linear', fill: 'forwards' });
        anim.onfinish = () => close('timeout');
        syncFuse();
    } else fuse.style.opacity = '0';

    // swipe down to dismiss
    const swipeOut = (dy, v) => {
        anim?.pause();
        leaving = true;
        pendingClose = null;
        if (!reducedMotion()) {
            ySpring = springTo({ from: dy, to: dy + card.offsetHeight, velocity: v * 1000, duration: 0.3, bounce: 0, onUpdate: paintY });
        }
        card.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 200, easing: 'cubic-bezier(0.23,1,0.32,1)', fill: 'forwards' }).onfinish = remove;
    };
    card.addEventListener('pointerdown', (e) => {
        if (e.button !== 0 || drag || leaving || e.target.closest('button')) return;
        try { card.setPointerCapture(e.pointerId); } catch { /* capture is a nicety */ }
        ySpring?.stop();
        drag = { id: e.pointerId, startY: e.clientY, grab: null, moved: false, hist: [[performance.now(), y]] };
        flags.interacting = true;
        syncFuse();
    });
    card.addEventListener('pointermove', (e) => {
        if (!drag || drag.id !== e.pointerId) return;
        if (drag.grab === null) {
            if (Math.abs(e.clientY - drag.startY) < DEAD_ZONE) return;
            drag.grab = e.clientY - y;
            card.dataset.swiping = '';
        }
        const raw = e.clientY - drag.grab;
        const next = raw >= 0 ? raw : rubberband(raw, RESIST_PX);
        paintY(next);
        drag.moved = true;
        drag.hist.push([performance.now(), next]);
        if (drag.hist.length > 4) drag.hist.shift();
    });
    const up = (e) => {
        if (!drag || drag.id !== e.pointerId) return;
        const d = drag;
        drag = null;
        delete card.dataset.swiping;
        try { card.releasePointerCapture(e.pointerId); } catch { /* ignore */ }
        flags.interacting = false;
        const v = velocityOf(d.hist);
        if (y > 0 && (v > FLICK || (y >= SWIPE_DISTANCE && v >= 0))) return swipeOut(y, v);
        if (d.moved) ySpring = springTo({ from: y, to: 0, velocity: v * 1000, duration: reducedMotion() ? 0.2 : 0.5, bounce: reducedMotion() ? 0 : 0.2, onUpdate: paintY });
        const queued = pendingClose;
        pendingClose = null;
        if (queued) close(queued); else syncFuse();
    };
    card.addEventListener('pointerup', up);
    card.addEventListener('pointercancel', up);

    card.addEventListener('pointerenter', (e) => { if (e.pointerType === 'mouse') { flags.hover = true; syncFuse(); } });
    card.addEventListener('pointerleave', (e) => { if (e.pointerType === 'mouse') { flags.hover = false; syncFuse(); } });
    card.addEventListener('focusin', () => { flags.focus = true; syncFuse(); });
    card.addEventListener('focusout', (e) => { if (!card.contains(e.relatedTarget)) { flags.focus = false; syncFuse(); } });
    card.addEventListener('keydown', (e) => { if (e.key === 'Escape') { e.stopPropagation(); close('escape'); } });
    el.querySelector('.toast-close')?.addEventListener('click', () => close('close'));
    el.querySelector('.toast-action')?.addEventListener('click', () => { action.onClick?.(); close('action'); });

    return { close: () => close('programmatic') };
}

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

/** POST to a URL (used by "Undo") and say what happened. */
export async function post(url, body = {}) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(body),
    });
    if (!res.ok) throw new Error(String(res.status));
    return res.json();
}

// Server flash messages become toasts too; some carry an Undo or a link.
export function flashToToasts() {
    document.querySelectorAll('[data-flash]').forEach((n) => {
        const error = n.dataset.flash === 'error';
        let action = null;
        if (n.dataset.undo) {
            action = {
                label: t('Undo'),
                onClick: async () => {
                    try { await post(n.dataset.undo); location.reload(); }
                    catch { toast({ title: t('Could not undo'), body: t('It may already be gone.'), icon: 'alert' }); }
                },
            };
        } else if (n.dataset.link) {
            action = { label: n.dataset.linkLabel || 'Open', onClick: () => { location.href = n.dataset.link; } };
        }
        toast({ title: n.dataset.message, icon: error ? 'alert' : 'check', duration: error ? 7000 : action ? 8000 : 4500, action });
        n.remove();
    });
}
