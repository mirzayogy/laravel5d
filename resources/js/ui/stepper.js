// Stepper (port of React Bits "Stepper"): segmented header with a gliding
// tile, spring-slid panes and a progress bar per step.
function build(root) {
    const segs = [...root.querySelectorAll('[data-step-seg]')];
    const panes = [...root.querySelectorAll('[data-step-pane]')];
    const bars = [...root.querySelectorAll('[data-step-bar]')];
    const track = root.querySelector('.stp-track');
    const glider = root.querySelector('.stp-glider');
    const slider = root.querySelector('.stp-panes');
    const back = root.querySelector('[data-step-back]');
    const next = root.querySelector('[data-step-next]');
    const submit = root.querySelector('[data-step-submit]');
    let index = 0;
    let reached = 0;

    const placeGlider = (instant) => {
        const seg = segs[index];
        if (!seg) return;
        if (instant) glider.style.transition = 'none';
        glider.style.width = `${seg.offsetWidth}px`;
        glider.style.transform = `translateX(${seg.offsetLeft}px)`;
        glider.style.opacity = '1';
        if (instant) { void glider.offsetWidth; glider.style.transition = ''; }
    };

    const go = (i, { instant = false } = {}) => {
        index = Math.min(panes.length - 1, Math.max(0, i));
        reached = Math.max(reached, index);
        segs.forEach((s, k) => {
            s.setAttribute('aria-selected', String(k === index));
            s.toggleAttribute('data-complete', k < index);
            s.disabled = k > reached;
            const badge = s.querySelector('.stp-badge');
            badge.innerHTML = k < index ? '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>' : String(k + 1);
        });
        bars.forEach((b, k) => b.toggleAttribute('data-filled', k <= index));
        panes.forEach((p, k) => { p.inert = k !== index; p.setAttribute('aria-hidden', String(k !== index)); });
        if (instant) slider.style.transition = 'none';
        slider.style.transform = `translateX(${-index * 100}%)`;
        if (instant) { void slider.offsetWidth; slider.style.transition = ''; }
        if (back) back.hidden = index === 0;
        if (next) next.hidden = index === panes.length - 1;
        if (submit) submit.hidden = index !== panes.length - 1;
        placeGlider(instant);
        root.dispatchEvent(new CustomEvent('stepper:change', { detail: { index } }));
    };

    const advance = () => {
        const fields = [...panes[index].querySelectorAll('input, select, textarea')];
        const bad = fields.find((f) => !f.checkValidity());
        if (bad) { bad.reportValidity(); return; }
        go(index + 1);
        panes[index].querySelector('input:not([type=hidden]), button')?.focus({ preventScroll: true });
    };

    segs.forEach((s, k) => s.addEventListener('click', () => k <= reached && go(k)));
    back?.addEventListener('click', () => go(index - 1));
    next?.addEventListener('click', advance);
    root.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.matches('input:not([type=checkbox])') && index < panes.length - 1) { e.preventDefault(); advance(); }
    });
    window.addEventListener('resize', () => placeGlider(true));
    go(0, { instant: true });
    track && document.fonts?.ready.then(() => placeGlider(true));
}

export function initSteppers(root = document) {
    root.querySelectorAll('[data-stepper]').forEach(build);
}
