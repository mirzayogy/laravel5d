// Bell toggle (port of React Bits "BellToggle"): a pill whose label swaps
// between off and on while the bell rings. Used for reminders.
import { t } from './i18n.js';
import { reducedMotion } from './spring.js';
import { toast } from './toast.js';

const EASE = 'cubic-bezier(0.77, 0, 0.175, 1)';
const WARP = 0.6;
const RING = { amplitude: 17, passes: 5, decay: 1, duration: 820 };
const BELL = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>';
const WAVE_L = '<svg class="bt-wave bt-wave-l" viewBox="0 0 14 14"><path d="M14 8a6 6 0 0 0-6 6"/><path d="M14 4A10 10 0 0 0 4 14"/></svg>';
const WAVE_R = '<svg class="bt-wave bt-wave-r" viewBox="0 0 14 14"><path d="M0 8a6 6 0 0 1 6 6"/><path d="M0 4a10 10 0 0 1 10 10"/></svg>';

const passOffset = (k, passes) => 1 - Math.pow(1 - (k + 2 / 3) / (passes + 1), WARP);
const ringKeyframes = (from, amplitude, passes, decay) => {
    const frames = [{ transform: `rotate(${from}deg)`, offset: 0, easing: EASE }];
    for (let k = 0; k < passes; k++) {
        const angle = amplitude * Math.pow(1 - k / passes, decay) * (k % 2 ? 1 : -1);
        frames.push({ transform: `rotate(${angle.toFixed(2)}deg)`, offset: passOffset(k, passes), easing: EASE });
    }
    frames.push({ transform: 'rotate(0deg)', offset: 1 });
    return frames;
};
const liveAngle = (el) => {
    const tf = getComputedStyle(el).transform;
    if (!tf || tf === 'none') return 0;
    const m = new DOMMatrix(tf);
    return (Math.atan2(m.b, m.a) * 180) / Math.PI;
};
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

function build(root) {
    let on = root.dataset.on === 'true';
    const offLabel = root.dataset.offLabel || 'Off';
    const onLabel = root.dataset.onLabel || 'On';

    root.classList.add('bt');
    root.innerHTML = `
        <button type="button" class="bt-btn" aria-pressed="${on}" aria-label="${root.dataset.label || offLabel}">
            <span class="bt-icon" aria-hidden="true"><span class="bt-glyph">${BELL}</span>${WAVE_L}${WAVE_R}</span>
            <span class="bt-labels" aria-hidden="true"><span class="bt-off">${offLabel}</span><span class="bt-on">${onLabel}</span></span>
        </button>`;
    const btn = root.querySelector('.bt-btn');
    const glyph = root.querySelector('.bt-glyph');
    const labels = root.querySelector('.bt-labels');
    const waves = [root.querySelector('.bt-wave-l'), root.querySelector('.bt-wave-r')];

    const fit = (animate) => {
        const w = root.querySelector(on ? '.bt-on' : '.bt-off').offsetWidth;
        if (!animate) labels.style.transition = 'none';
        labels.style.width = w + 'px';
        if (!animate) { void labels.offsetWidth; labels.style.transition = ''; }
    };

    const ring = () => {
        glyph.getAnimations().forEach((a) => a.cancel());
        glyph.animate(ringKeyframes(liveAngle(glyph), RING.amplitude, RING.passes, RING.decay), { duration: RING.duration, easing: 'linear' });
        for (let k = 0; k < RING.passes; k++) {
            waves[k % 2].animate(
                [{ opacity: 0, transform: 'scale(0.55)' }, { opacity: 0.9 * Math.pow(1 - k / RING.passes, RING.decay), offset: 0.3 }, { opacity: 0, transform: 'scale(1.25)' }],
                { duration: 380, delay: passOffset(k, RING.passes) * RING.duration, easing: 'ease-out' },
            );
        }
    };

    const apply = (next, { animate = true } = {}) => {
        on = next;
        root.dataset.on = String(on);
        btn.setAttribute('aria-pressed', String(on));
        fit(animate && !reducedMotion());
        if (on && animate && !reducedMotion()) ring();
    };

    btn.addEventListener('pointerdown', (e) => { if (e.button === 0) root.dataset.pressed = ''; });
    ['pointerup', 'pointercancel', 'pointerleave'].forEach((t) => btn.addEventListener(t, () => delete root.dataset.pressed));
    btn.addEventListener('click', async () => {
        const next = !on;
        apply(next);
        root.dispatchEvent(new CustomEvent('bell:change', { bubbles: true, detail: { on: next } }));
        if (!root.dataset.url) return;
        btn.disabled = true;
        try {
            const res = await fetch(root.dataset.url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ is_enabled: next }),
            });
            if (!res.ok) throw new Error(String(res.status));
        } catch {
            apply(!next, { animate: false });
            toast({ title: t('Could not update the reminder'), body: t('Check your connection and try again.'), icon: 'alert' });
        } finally {
            btn.disabled = false;
        }
    });

    fit(false);
    document.fonts?.ready.then(() => fit(false));
}

export function initBellToggles(root = document) {
    root.querySelectorAll('[data-bell-toggle]').forEach(build);
}
