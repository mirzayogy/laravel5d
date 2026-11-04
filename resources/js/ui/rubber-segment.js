// Rubber segment (port of React Bits "RubberSegment"): a segmented control
// whose thumb stretches towards the next segment, squashes on arrival, and can
// be dragged or flicked. Edges stretch like rubber past the ends.
import { reducedMotion, springTo, tween } from './spring.js';

const DILATE = 0.19;
const HANDOFF = 0.15;
const FLICK = 110;
const MAX_VELOCITY = 2000;
const DEADZONE = 4;
const SLOP = 10;
const RUBBER = 0.55;
const STRETCH = 1;       // 100%
const SQUASH = 3;        // px
const GLIDE = 75;

const clamp = (v, lo, hi) => Math.min(hi, Math.max(lo, v));
const rubber = (over, dim) => (over * dim * RUBBER) / (dim + RUBBER * Math.abs(over));
const project = (v, glide) => {
    const d = 1 - 0.1 * Math.pow(0.05, glide / 100);
    return ((v / 1000) * d) / (1 - d);
};
const velocityOf = (hist, now) => {
    const recent = hist.filter(([t]) => now - t <= 100);
    if (recent.length < 2) return 0;
    const [t0, x0] = recent[0];
    const [t1, x1] = recent[recent.length - 1];
    return t1 - t0 >= 8 ? ((x1 - x0) / (t1 - t0)) * 1000 : 0;
};
const nearestSlot = (slots, x) => {
    let best = 0;
    for (let i = 1; i < slots.length; i++) {
        if (Math.abs((slots[i].l + slots[i].r) / 2 - x) < Math.abs((slots[best].l + slots[best].r) / 2 - x)) best = i;
    }
    return best;
};

function build(root) {
    const items = [...root.querySelectorAll('.rs-item')];
    const inset = 0; // keep in step with --rs-inset in the CSS
    const radius = root.dataset.size === 'sm' ? 9 : 10;
    const thumbRadius = Math.max(0, radius - inset);

    root.setAttribute('role', 'radiogroup');
    const thumb = document.createElement('div');
    thumb.className = 'rs-thumb';
    thumb.setAttribute('aria-hidden', 'true');
    thumb.innerHTML = items.map((it) => `<span class="rs-copy">${it.innerHTML}</span>`).join('');
    root.append(thumb);

    let index = Math.max(0, items.findIndex((it) => it.dataset.value === root.dataset.value));
    let committed = index;
    let slots = [];
    let box = null;
    let innerW = 0;
    let edgeL = 0;
    let edgeR = 0;
    const anims = { l: null, r: null };
    let handoff = 0;
    let gen = 0;
    let drag = null;

    const paint = () => {
        thumb.style.clipPath = `inset(0 ${Math.max(0, innerW - edgeR)}px 0 ${Math.max(0, edgeL)}px round ${thumbRadius}px)`;
    };
    const stopAnims = () => { anims.l?.stop(); anims.r?.stop(); anims.l = anims.r = null; };
    const setEdges = (l, r) => { edgeL = l; edgeR = r; paint(); };
    const jumpTo = (i) => {
        const s = slots[i];
        if (!s) return;
        clearTimeout(handoff);
        gen++;
        stopAnims();
        setEdges(s.l, s.r);
    };
    const measure = () => {
        const rect = root.getBoundingClientRect();
        box = rect;
        slots = items.map((el) => {
            const r = el.getBoundingClientRect();
            return { l: r.left - rect.left - inset, r: r.right - rect.left - inset };
        });
        innerW = rect.width - inset * 2;
        jumpTo(committed);
    };

    // spring one edge: key is 'l' or 'r'
    const springEdge = (key, to, opts) => {
        anims[key]?.stop();
        anims[key] = springTo({
            from: key === 'l' ? edgeL : edgeR,
            to,
            velocity: opts.velocity ?? anims[key]?.velocity ?? 0,
            duration: opts.duration,
            bounce: opts.bounce ?? 0,
            onUpdate: (v) => (key === 'l' ? setEdges(v, edgeR) : setEdges(edgeL, v)),
            onDone: opts.onDone,
        });
    };

    const land = (to, v, flick, withSquash) => {
        const b = slots[to];
        if (!b) return;
        const g = ++gen;
        const dir = Math.sign((b.l + b.r) / 2 - (edgeL + edgeR) / 2) || 1;
        const [lead, leadTo, trail, trailTo] = dir > 0 ? ['r', b.r, 'l', b.l] : ['l', b.l, 'r', b.r];
        const vel = (key) => clamp(v === null ? (anims[key]?.velocity ?? 0) : v, -MAX_VELOCITY, MAX_VELOCITY);
        const trailV = vel(trail);
        springEdge(lead, leadTo, { duration: flick ? 0.4 : 0.3, bounce: flick ? 0.2 : 0, velocity: vel(lead) });
        if (!withSquash || SQUASH <= 0) return springEdge(trail, trailTo, { duration: 0.3, velocity: trailV });
        springEdge(trail, trailTo + dir * SQUASH, {
            duration: 0.3,
            velocity: trailV,
            onDone: () => { if (gen === g) springEdge(trail, trailTo, { duration: 0.16, velocity: 0 }); },
        });
    };

    const travel = (from, to) => {
        const a = slots[from];
        const b = slots[to];
        if (!a || !b) return;
        clearTimeout(handoff);
        gen++;
        stopAnims();
        if (reducedMotion()) return setEdges(b.l, b.r);
        anims.l = tween({ from: edgeL, to: b.l + (Math.min(a.l, b.l) - b.l) * STRETCH, duration: DILATE, onUpdate: (v) => setEdges(v, edgeR) });
        anims.r = tween({ from: edgeR, to: b.r + (Math.max(a.r, b.r) - b.r) * STRETCH, duration: DILATE, onUpdate: (v) => setEdges(edgeL, v) });
        handoff = setTimeout(() => land(to, null, false, true), HANDOFF * 1000);
    };

    const select = (i) => {
        committed = i;
        if (i === index) return;
        index = i;
        items.forEach((it, k) => { it.setAttribute('aria-checked', String(k === i)); it.tabIndex = k === i ? 0 : -1; });
        root.dataset.value = items[i].dataset.value;
        root.dispatchEvent(new CustomEvent('rubber:change', { bubbles: true, detail: { value: items[i].dataset.value, index: i, href: items[i].dataset.href } }));
        // link mode: let the thumb finish its move, then go
        if (items[i].dataset.href) setTimeout(() => (location.href = items[i].dataset.href), reducedMotion() ? 0 : 320);
    };

    const localX = (e) => e.clientX - (box ? box.left : 0) - inset;

    items.forEach((it, i) => {
        it.setAttribute('role', 'radio');
        it.setAttribute('aria-checked', String(i === index));
        it.tabIndex = i === index ? 0 : -1;

        it.addEventListener('pointerdown', (e) => {
            if (drag || e.button !== 0) return;
            box = root.getBoundingClientRect();
            try { it.setPointerCapture(e.pointerId); } catch { /* ignore */ }
            const x = localX(e);
            const onThumb = x >= edgeL && x <= edgeR;
            drag = { id: e.pointerId, x0: x, slot: i, onThumb, live: false, offset: 0, w: 0, hist: [[e.timeStamp, x]] };
            if (onThumb) { clearTimeout(handoff); gen++; stopAnims(); }
            else if (!reducedMotion()) it.dataset.pressed = '';
        });
        it.addEventListener('keydown', (e) => {
            const last = items.length - 1;
            let next = null;
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next = Math.min(last, index + 1);
            else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') next = Math.max(0, index - 1);
            else if (e.key === 'Home') next = 0;
            else if (e.key === 'End') next = last;
            if (next === null) return;
            e.preventDefault();
            if (next === index) return;
            select(next);
            jumpTo(next);
            items[next].focus();
        });
    });

    root.addEventListener('pointermove', (e) => {
        const d = drag;
        if (!d || e.pointerId !== d.id || !d.onThumb) return;
        const x = localX(e);
        d.hist.push([e.timeStamp, x]);
        if (d.hist.length > 8) d.hist.shift();
        if (!d.live) {
            if (Math.abs(x - d.x0) < DEADZONE) return;
            d.live = true;
            d.offset = x - edgeL;
            d.w = edgeR - edgeL;
            root.dataset.held = '';
        }
        const l = x - d.offset;
        const maxL = innerW - d.w;
        if (reducedMotion()) { const c = clamp(l, 0, maxL); setEdges(c, c + d.w); }
        else if (l < 0) setEdges(0, d.w - rubber(-l, d.w));
        else if (l > maxL) setEdges(maxL + rubber(l - maxL, d.w), innerW);
        else setEdges(l, l + d.w);
    });

    const release = () => {
        const d = drag;
        drag = null;
        delete root.dataset.held;
        if (d) delete items[d.slot].dataset.pressed;
        return d;
    };
    root.addEventListener('pointerup', (e) => {
        const d = drag;
        if (!d || e.pointerId !== d.id) return;
        release();
        const x = localX(e);
        if (!d.live) {
            if (Math.abs(x - d.x0) <= SLOP && d.slot !== committed) {
                const from = committed;
                select(d.slot);
                travel(from, d.slot);
            }
            return;
        }
        const v = velocityOf(d.hist, e.timeStamp);
        const flick = Math.abs(v) > FLICK;
        let to = nearestSlot(slots, (edgeL + edgeR) / 2 + project(v, GLIDE));
        if (flick && to === committed) to = clamp(to + Math.sign(v), 0, items.length - 1);
        select(to);
        if (reducedMotion()) jumpTo(to); else land(to, v, flick, flick);
    });
    const cancel = (e) => {
        const d = drag;
        if (!d || e.pointerId !== d.id) return;
        release();
        if (!d.live) return;
        if (reducedMotion()) jumpTo(committed); else land(committed, null, false, false);
    };
    root.addEventListener('pointercancel', cancel);
    root.addEventListener('lostpointercapture', cancel);

    new ResizeObserver(measure).observe(root);
    document.fonts?.ready.then(measure);
    measure();

    root.rubber = { set: (value) => { const i = items.findIndex((it) => it.dataset.value === value); if (i >= 0 && i !== index) { const from = committed; select(i); travel(from, i); } } };
}

export function initRubberSegments(root = document) {
    root.querySelectorAll('[data-rubber-segment]').forEach(build);
}
