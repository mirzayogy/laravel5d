// Tiny spring animator standing in for `motion`'s animate(): a damped harmonic
// oscillator with the same "duration + bounce" parametrisation.
export function springTo({ from, to, velocity = 0, duration = 0.3, bounce = 0, onUpdate, onDone }) {
    const k = (2 * Math.PI / duration) ** 2;
    const c = 2 * (1 - bounce) * Math.sqrt(k);
    let x = from;
    let v = velocity;
    let last = performance.now();
    let raf = 0;
    let stopped = false;

    const step = (now) => {
        if (stopped) return;
        const dt = Math.min(0.032, (now - last) / 1000);
        last = now;
        const subs = Math.max(1, Math.ceil(dt / 0.004));
        const h = dt / subs;
        for (let i = 0; i < subs; i++) {
            v += (-k * (x - to) - c * v) * h;
            x += v * h;
        }
        if (Math.abs(x - to) < 0.01 && Math.abs(v) < 0.05) {
            onUpdate(to);
            onDone?.();
            return;
        }
        onUpdate(x);
        raf = requestAnimationFrame(step);
    };
    raf = requestAnimationFrame(step);

    return {
        stop() { stopped = true; cancelAnimationFrame(raf); },
        get velocity() { return v; },
    };
}

/** Plain eased tween (ease-out quart), for the short "stretch" moves. */
export function tween({ from, to, duration = 0.2, onUpdate, onDone }) {
    const start = performance.now();
    let raf = 0;
    let stopped = false;
    const step = (now) => {
        if (stopped) return;
        const p = Math.min(1, (now - start) / (duration * 1000));
        onUpdate(from + (to - from) * (1 - Math.pow(1 - p, 4)));
        if (p < 1) raf = requestAnimationFrame(step);
        else onDone?.();
    };
    raf = requestAnimationFrame(step);
    return { stop() { stopped = true; cancelAnimationFrame(raf); } };
}

export const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
