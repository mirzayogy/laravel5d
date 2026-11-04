// Contribution Skyline (port of the React component): a year of activity as a
// GitHub-style heat map that folds up into an isometric skyline and back.
// One canvas scene: the 2D view is the grid seen from above, the 3D view the
// same grid seen from the corner, and one camera swings between the two.

import { t as tr } from './i18n.js'; // `t` is the animation time inside mount()

// ---------- pure helpers: dates, grid, stats, camera, colour ----------
export const DAY_MS = 86400000;
export const clamp01 = (v) => (v > 0 ? (v < 1 ? v : 1) : 0);
export const lerp = (a, b, t) => a + (b - a) * t;
export const easeInOutCubic = (x) => {
    const t = clamp01(x);
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
};
export const easeOutCubic = (x) => 1 - Math.pow(1 - clamp01(x), 3);
export const smoothstep = (a, b, x) => {
    const t = clamp01((x - a) / (b - a));
    return t * t * (3 - 2 * t);
};

export const toKey = (ms) => new Date(ms).toISOString().slice(0, 10);

/** Any date-ish value to UTC midnight of its calendar day ("YYYY-MM-DD" is read literally). */
export const dayMs = (v) => {
    if (typeof v === 'number') return Math.floor(v / DAY_MS) * DAY_MS;
    if (typeof v === 'string') {
        const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(v);
        if (m) return Date.UTC(+m[1], +m[2] - 1, +m[3]);
        v = new Date(v);
    }
    return Date.UTC(v.getFullYear(), v.getMonth(), v.getDate());
};

export const levelOf = (count, busy) => (count <= 0 ? 0 : busy <= 0 ? 4 : 1 + Math.min(3, Math.floor((count / busy) * 4)));

/** Columns are weeks, rows are weekdays. Levels 1-4 split non-zero days by their share of a busy (95th percentile) day. */
export const buildGrid = (data, endMs, weekStart = 0) => {
    const counts = new Map();
    for (const d of data) {
        if (!d || typeof d.date !== 'string') continue;
        const ms = dayMs(d.date);
        const c = Number(d.count);
        if (!Number.isFinite(ms) || !(c > 0) || !Number.isFinite(c)) continue;
        const k = toKey(ms);
        counts.set(k, (counts.get(k) ?? 0) + c);
    }
    let start = endMs - 364 * DAY_MS;
    start -= ((new Date(start).getUTCDay() - weekStart + 7) % 7) * DAY_MS;
    const cells = [];
    for (let ms = start, i = 0; ms <= endMs; ms += DAY_MS, i++) {
        const date = toKey(ms);
        cells.push({ date, count: counts.get(date) ?? 0, level: 0, week: Math.floor(i / 7), day: i % 7 });
    }
    const nz = cells.map((c) => c.count).filter((c) => c > 0).sort((a, b) => a - b);
    const busy = nz.length ? nz[Math.floor(0.95 * (nz.length - 1))] : 0;
    for (const c of cells) c.level = levelOf(c.count, busy);
    return { cells, weeks: cells.length ? cells[cells.length - 1].week + 1 : 0, max: nz.length ? nz[nz.length - 1] : 0 };
};

/** Total, busiest day, longest run, and the run that reaches today (or yesterday, today is not over). */
export const computeStats = (cells) => {
    let total = 0, best = 0, bestDate = null, run = 0, runStart = null;
    let longest = { days: 0, start: null, end: null };
    for (const c of cells) {
        total += c.count;
        if (c.count > best) { best = c.count; bestDate = c.date; }
        if (c.count > 0) {
            if (run === 0) runStart = c.date;
            run++;
            if (run > longest.days) longest = { days: run, start: runStart, end: c.date };
        } else run = 0;
    }
    let j = cells.length - 1;
    if (j >= 0 && cells[j].count === 0) j--;
    const endAt = j;
    while (j >= 0 && cells[j].count > 0) j--;
    const days = endAt - j;
    const current = days > 0 ? { days, start: cells[j + 1].date, end: cells[endAt].date } : { days: 0, start: null, end: null };
    return {
        total,
        first: cells.length ? cells[0].date : null,
        last: cells.length ? cells[cells.length - 1].date : null,
        busiest: { count: best, date: bestDate },
        longest,
        current,
    };
};

export const monthLabels = (cells, weeks, locale = 'en-US') => {
    const fmt = new Intl.DateTimeFormat(locale, { month: 'short', timeZone: 'UTC' });
    const out = [];
    let prev = -1;
    for (let w = 0; w < weeks; w++) {
        const c = cells[w * 7];
        if (!c) break;
        const m = +c.date.slice(5, 7);
        if (m !== prev) out.push({ week: w, label: fmt.format(dayMs(c.date)) });
        prev = m;
    }
    if (out.length > 1 && out[1].week - out[0].week < 3) out.shift();
    return out;
};

export const barHeight = (count, max, scale = 1) => (count > 0 && max > 0 ? 0.4 + Math.pow(count / max, 0.85) * 7.2 * scale : 0.2);
export const WAVE = 0.42;
export const riseAt = (t, week, weeks, day) => {
    const d = (weeks > 1 ? week / (weeks - 1) : 0) * 0.36 + (day / 6) * 0.06;
    return easeOutCubic((t - d) / (1 - WAVE));
};

export const YAW_3D = Math.PI / 4;
export const ELEV_3D = (34 * Math.PI) / 180;
export const YAW_RANGE = [(8 * Math.PI) / 180, (82 * Math.PI) / 180];
export const ELEV_RANGE = [(18 * Math.PI) / 180, (62 * Math.PI) / 180];

/** e=0 looks straight down (a plain heat map); e=1 is the isometric corner view. */
export const camera = (e, dYaw = 0, dElev = 0) => {
    const yaw = Math.min(YAW_RANGE[1], Math.max(0, lerp(0, YAW_3D + dYaw, e)));
    const elev = lerp(Math.PI / 2, Math.min(ELEV_RANGE[1], Math.max(ELEV_RANGE[0], ELEV_3D + dElev)), e);
    return { cs: Math.cos(yaw), sn: Math.sin(yaw), se: Math.sin(elev), ce: Math.cos(elev) };
};
export const project = (c, x, y, z) => [x * c.cs - y * c.sn, (x * c.sn + y * c.cs) * c.se - z * c.ce];

export const mixRGB = (a, b, t) => [a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, a[2] + (b[2] - a[2]) * t];
export const luminance = (c) => (0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]) / 255;

const FG_FALLBACK = [23, 23, 23];
const BG_FALLBACK = [255, 255, 255];

// Any CSS colour to sRGB, by letting the browser paint it.
let probe = null;
const toRGB = (color, fallback) => {
    if (!probe) {
        const c = document.createElement('canvas');
        c.width = c.height = 1;
        probe = c.getContext('2d', { willReadFrequently: true });
    }
    if (!probe) return fallback;
    probe.clearRect(0, 0, 1, 1);
    probe.fillStyle = 'rgba(0,0,0,0)';
    probe.fillStyle = color;
    probe.fillRect(0, 0, 1, 1);
    const d = probe.getImageData(0, 0, 1, 1).data;
    return d[3] < 8 ? fallback : [d[0], d[1], d[2]];
};
const rgbString = (r, g, b) => 'rgb(' + Math.round(r) + ',' + Math.round(g) + ',' + Math.round(b) + ')';

const pointInQuad = (p, o, x, y) => {
    let sign = 0;
    for (let k = 0; k < 4; k++) {
        const ax = p[o + k * 2], ay = p[o + k * 2 + 1];
        const bx = p[o + ((k + 1) % 4) * 2], by = p[o + ((k + 1) % 4) * 2 + 1];
        const cross = (bx - ax) * (y - ay) - (by - ay) * (x - ax);
        if (Math.abs(cross) < 1e-9) continue;
        const s = cross > 0 ? 1 : -1;
        if (sign === 0) sign = s;
        else if (s !== sign) return false;
    }
    return sign !== 0;
};

const quadPath = (ctx, p, o, r) => {
    if (r < 0.3) {
        ctx.moveTo(p[o], p[o + 1]);
        ctx.lineTo(p[o + 2], p[o + 3]);
        ctx.lineTo(p[o + 4], p[o + 5]);
        ctx.lineTo(p[o + 6], p[o + 7]);
        ctx.closePath();
        return;
    }
    ctx.moveTo((p[o + 6] + p[o]) / 2, (p[o + 7] + p[o + 1]) / 2);
    for (let k = 0; k < 4; k++) {
        const b = (k + 1) % 4;
        ctx.arcTo(p[o + k * 2], p[o + k * 2 + 1], p[o + b * 2], p[o + b * 2 + 1], r);
    }
    ctx.closePath();
};

const ICON_GRID = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1" fill="currentColor"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1" fill="currentColor"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1" fill="currentColor"/><rect x="9" y="9" width="5.5" height="5.5" rx="1" fill="currentColor"/></svg>';
const ICON_CUBE = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path d="M8 1.2 14.2 4.6v6.8L8 14.8 1.8 11.4V4.6Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M1.8 4.6 8 8l6.2-3.4M8 8v6.8" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>';

const esc = (t) => String(t).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

function mount(host) {
    const opts = JSON.parse(host.dataset.skyline || '{}');
    const data = JSON.parse(host.querySelector('script[type="application/json"]')?.textContent || '[]');
    const locale = opts.locale || 'en-US';
    const unit = opts.unit || 'contribution';
    const plural = opts.unitPlural || unit + 's';
    const weekStart = opts.weekStart ?? 1;
    const duration = opts.duration ?? 1300;
    const heightScale = opts.heightScale ?? 1;
    const palette = (opts.palette || []).slice(0, 4);
    const storeKey = 'habitude.skyline';
    const ease = 'cubic-bezier(0.65, 0, 0.35, 1)';

    let view = opts.defaultView || '3d';
    try { const saved = localStorage.getItem(storeKey); if (saved === '2d' || saved === '3d') view = saved; } catch { /* storage unavailable */ }

    const end = opts.endDate ? dayMs(opts.endDate) : dayMs(new Date());
    const model = (() => {
        const grid = buildGrid(data, end, weekStart);
        return { ...grid, stats: computeStats(grid.cells), months: monthLabels(grid.cells, grid.weeks, locale) };
    })();

    const nf = new Intl.NumberFormat(locale);
    const df = new Intl.DateTimeFormat(locale, { month: 'short', day: 'numeric', timeZone: 'UTC' });
    const dfy = new Intl.DateTimeFormat(locale, { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
    const dfl = new Intl.DateTimeFormat(locale, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric', timeZone: 'UTC' });
    const noun = (n) => tr(n === 1 ? unit : plural);
    const range = (a, b, withYear = false) => (!a || !b ? '-' : (withYear ? dfy : df).format(dayMs(a)) + ' ' + tr('to') + ' ' + (withYear ? dfy : df).format(dayMs(b)));
    const describe = (i) => {
        const c = model.cells[i];
        return c ? (c.count ? tr(':count :unit on :date', { count: nf.format(c.count), unit: noun(c.count), date: dfl.format(dayMs(c.date)) }) : tr('No :plural on :date', { plural: tr(plural), date: dfl.format(dayMs(c.date)) })) : '';
    };
    const levelNames = [tr('No :plural', { plural: tr(plural) }), tr('Light'), tr('Moderate'), tr('Heavy'), tr('Heaviest')];
    const hints = [tr('Hover a day for details, click to open it. Arrow keys to explore.'), tr('Drag to orbit, double-click to reset.')];
    const { stats } = model;
    const blocks = [
        { label: tr('1 year total'), value: nf.format(stats.total), unit: noun(stats.total), sub: range(stats.first, stats.last, true) },
        { label: tr('Busiest day'), value: nf.format(stats.busiest.count), unit: noun(stats.busiest.count), sub: stats.busiest.date ? df.format(dayMs(stats.busiest.date)) : '-' },
        { label: tr('Longest streak'), value: nf.format(stats.longest.days), unit: tr(stats.longest.days === 1 ? 'day' : 'days'), sub: range(stats.longest.start, stats.longest.end) },
        { label: tr('Current streak'), value: nf.format(stats.current.days), unit: tr(stats.current.days === 1 ? 'day' : 'days'), sub: range(stats.current.start, stats.current.end) },
    ];

    const statHTML = (b, size, align) => {
        if (align === 'stack') {
            return `<div class="sk-stat"><div class="sk-label">${esc(b.label)}</div><div class="sk-stack"><span class="sk-num" style="font-size:${size}px">${esc(b.value)}</span><span class="sk-unit">${esc(b.unit)}</span></div><div class="sk-sub">${esc(b.sub)}</div></div>`;
        }
        const head = align === 'end'
            ? `<div class="sk-label" style="text-align:right">${esc(b.label)}</div><div></div>`
            : `<div class="sk-label" style="grid-column:span 2">${esc(b.label)}</div>`;
        return `<div class="sk-stat sk-grid" style="justify-content:${align === 'end' ? 'end' : 'start'}">${head}<div class="sk-num sk-big" style="font-size:${size}px">${esc(b.value)}</div><div class="sk-side"><div class="sk-unit-lg">${esc(b.unit)}</div><div class="sk-sub">${esc(b.sub)}</div></div></div>`;
    };

    // ---------- DOM ----------
    host.classList.add('sk');
    host.style.setProperty('--sk-dur', duration + 'ms');
    host.innerHTML = `
        <header class="sk-head">
            <h3 class="sk-title"><strong>${esc(nf.format(stats.total))}</strong> ${esc(noun(stats.total))} ${esc(tr('in the last year'))}</h3>
            <div class="sk-toggle" role="group" aria-label="${esc(tr('Chart view'))}" data-view="${view}">
                <span class="sk-glider" aria-hidden="true"></span>
                <button type="button" class="sk-tbtn" data-v="2d" aria-label="${esc(tr('Flat heat map'))}" title="${esc(tr('Flat heat map'))}">${ICON_GRID}</button>
                <button type="button" class="sk-tbtn" data-v="3d" aria-label="${esc(tr('3D skyline'))}" title="${esc(tr('3D skyline'))}">${ICON_CUBE}</button>
            </div>
        </header>
        <div class="sk-box">
            <div class="sk-pad">
                <div class="sk-stage" style="height:150px">
                    <canvas class="sk-canvas" tabindex="0" role="img" aria-label="${esc(nf.format(stats.total) + ' ' + noun(stats.total) + ' ' + range(stats.first, stats.last, true))}"></canvas>
                    <div class="sk-corner sk-tr" aria-hidden="true"></div>
                    <div class="sk-corner sk-bl" aria-hidden="true"></div>
                </div>
                <div class="sk-tip" role="tooltip" aria-hidden="true"><span class="sk-tip-text"></span><span class="sk-tip-arrow" aria-hidden="true"></span></div>
            </div>
            <div class="sk-rowwrap"><div class="sk-rowinner"><div class="sk-rowgrid"></div></div></div>
            <div class="sk-foot">
                <span class="sk-hints">${hints.map((h) => `<span>${esc(h)}</span>`).join('')}</span>
                <div class="sk-legend"><span>${esc(tr('Less'))}</span><span class="sk-swatches"></span><span>${esc(tr('More'))}</span></div>
            </div>
        </div>
        <p class="sr-only" aria-live="polite"></p>`;

    const $ = (s) => host.querySelector(s);
    const stage = $('.sk-stage');
    const canvas = $('.sk-canvas');
    const tip = $('.sk-tip');
    const tipText = $('.sk-tip-text');
    const toggle = $('.sk-toggle');
    const cornerTR = $('.sk-tr');
    const cornerBL = $('.sk-bl');
    const rowWrap = $('.sk-rowwrap');
    const rowGrid = $('.sk-rowgrid');
    const swatchHost = $('.sk-swatches');
    const live = host.querySelector('p.sr-only');
    const hintEls = [...host.querySelectorAll('.sk-hints > span')];
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // ---------- engine state ----------
    const reduceMq = window.matchMedia('(prefers-reduced-motion: reduce)');
    let reduced = reduceMq.matches;
    let t = 0, target = 0, entered = false;
    let yaw = 0, elev = 0, yawGoal = 0, elevGoal = 0;
    let W = 0, H2 = 0, H3 = 0, Hmax = 0, lastH = -1, dpr = 1, gutter = 30, labelW = 30;
    let font = '10px sans-serif';
    const col = new Float32Array(15);
    const colGoal = new Float32Array(15);
    let colReady = false, fg = FG_FALLBACK, bg = BG_FALLBACK, isDark = false;
    const n = model.cells.length;
    const weeks = model.weeks;
    const wk = new Float32Array(n), dy = new Float32Array(n), hgt = new Float32Array(n), zs = new Float32Array(n);
    const hover = new Float32Array(n), dim = new Float32Array(n), polys = new Float32Array(n * 24);
    const lv = new Uint8Array(n), faces = new Uint8Array(n);
    const order = Array.from({ length: n }, (_, i) => i);
    let weekdayRows = [];
    let hovered = -1, pinned = -1, activeIdx = -1, tipW = 0, legendLevel = -1;
    let raf = 0, last = 0, corners = false, accent = '#000', width = 0;

    for (let i = 0; i < n; i++) {
        const c = model.cells[i];
        wk[i] = c.week; dy[i] = c.day; lv[i] = c.level;
        hgt[i] = barHeight(c.count, model.max, heightScale);
    }
    const wf = new Intl.DateTimeFormat(locale, { weekday: 'short', timeZone: 'UTC' });
    for (let d = 0; d < 7 && d < n; d++) {
        const dow = new Date(dayMs(model.cells[d].date)).getUTCDay();
        if (dow === 1 || dow === 3 || dow === 5) weekdayRows.push({ day: d, label: wf.format(dayMs(model.cells[d].date)) });
    }

    // ---------- theme ----------
    const retheme = () => {
        const cs = getComputedStyle(host);
        fg = toRGB(cs.color, FG_FALLBACK) ?? FG_FALLBACK;
        bg = toRGB(cs.backgroundColor, null) ?? (luminance(fg) > 0.5 ? [10, 10, 10] : BG_FALLBACK);
        isDark = luminance(bg) < 0.45;
        font = '400 10px ' + (cs.fontFamily || 'sans-serif');
        const base = [FG_FALLBACK, FG_FALLBACK, FG_FALLBACK, FG_FALLBACK];
        const pal = [0, 1, 2, 3].map((i) => toRGB(palette[i] ?? palette[palette.length - 1] ?? '#171717', base[i]) ?? base[i]);
        const all = [mixRGB(bg, fg, isDark ? 0.11 : 0.075), ...pal];
        for (let k = 0; k < 5; k++) for (let ch = 0; ch < 3; ch++) colGoal[k * 3 + ch] = all[k][ch];
        if (!colReady || reduced) { col.set(colGoal); colReady = true; }
        ctx.font = font;
        labelW = Math.ceil(Math.max(20, ...weekdayRows.map((r) => ctx.measureText(r.label).width))) + 8;
        const sw = all.map((c) => rgbString(c[0], c[1], c[2]));
        accent = sw[4];
        swatchHost.innerHTML = sw.map((c, i) => `<button type="button" class="sk-sw" data-i="${i}" style="background:${c}" aria-label="${esc(tr('Highlight :level days', { level: levelNames[i].toLowerCase() }))}" aria-pressed="false" title="${esc(levelNames[i])}"></button>`).join('');
        renderStats();
        kick();
    };

    // ---------- stats (corners in wide 3D, a row otherwise) ----------
    const renderStats = () => {
        const size = Math.round(Math.max(30, Math.min(56, width * 0.058)));
        cornerTR.innerHTML = statHTML(blocks[0], size, 'end') + statHTML(blocks[1], size, 'end');
        cornerBL.innerHTML = statHTML(blocks[2], size, 'start') + statHTML(blocks[3], size, 'start');
        rowGrid.innerHTML = blocks.map((b) => statHTML(b, 28, 'stack')).join('');
        host.querySelectorAll('.sk-num').forEach((el) => (el.style.color = accent));
    };
    const syncChrome = () => {
        const is3d = view === '3d';
        toggle.dataset.view = view;
        toggle.querySelectorAll('.sk-tbtn').forEach((b) => b.setAttribute('aria-pressed', String(b.dataset.v === view)));
        host.dataset.view = view;
        host.toggleAttribute('data-corners', width >= 560);
        const showRow = !(is3d && width >= 560);
        rowWrap.toggleAttribute('data-open', showRow);
        rowWrap.setAttribute('aria-hidden', String(!showRow));
        cornerTR.style.transitionDelay = is3d ? Math.round(duration * 0.55) + 'ms' : '0ms';
        cornerBL.style.transitionDelay = is3d ? Math.round(duration * 0.65) + 'ms' : '0ms';
        const hint = view === '3d' ? 1 : 0;
        hintEls.forEach((h, i) => (h.style.opacity = i === hint ? '1' : '0'));
        canvas.style.touchAction = is3d ? 'pan-y' : 'auto';
    };

    // ---------- layout & draw ----------
    const extent = (cam, e, full) => {
        const w = lerp(0.78, 0.9, e);
        const off = (1 - w) / 2;
        let minx = Infinity, maxx = -Infinity, miny = Infinity, maxy = -Infinity;
        const add = (x, y, z) => {
            const p = project(cam, x, y, z);
            if (p[0] < minx) minx = p[0];
            if (p[0] > maxx) maxx = p[0];
            if (p[1] < miny) miny = p[1];
            if (p[1] > maxy) maxy = p[1];
        };
        for (let i = 0; i < n; i++) {
            const x0 = wk[i] + off, y0 = dy[i] + off;
            const z = full ? hgt[i] * e : zs[i];
            add(x0, y0, z); add(x0 + w, y0, z); add(x0, y0 + w, z);
            add(x0 + w, y0 + w, 0); add(x0, y0 + w, 0); add(x0 + w, y0, 0);
        }
        add(0, 7 + 1.5 * e, 0);
        add(weeks, 7 + 1.5 * e, 0);
        return { minx, maxx, miny, maxy };
    };

    const relayout = () => {
        const w = Math.round(stage.clientWidth);
        if (!w || !n) return;
        W = w;
        gutter = W < 520 ? 0 : labelW;
        dpr = Math.min(2, window.devicePixelRatio || 1);
        const b2 = extent(camera(0), 0, true);
        H2 = 20 + 4 + ((b2.maxy - b2.miny) / (b2.maxx - b2.minx)) * (W - gutter - 4);
        const b3 = extent(camera(1), 1, true);
        const natural = ((b3.maxy - b3.miny) / (b3.maxx - b3.minx)) * (W - 40) + 40;
        H3 = Math.max(Math.min(natural, W * 0.72, 620), Math.min(natural, 240));
        Hmax = Math.ceil(Math.max(H2, H3));
        canvas.width = Math.round(W * dpr);
        canvas.height = Math.round(Hmax * dpr);
        canvas.style.width = W + 'px';
        canvas.style.height = Hmax + 'px';
        lastH = -1;
        if (width !== W) { width = W; renderStats(); syncChrome(); }
        draw();
    };

    const draw = () => {
        if (!W || !n) return;
        const e = easeInOutCubic(t);
        const cam = camera(e, yaw, elev);
        const Hc = lerp(H2, H3, e);
        if (Math.abs(Hc - lastH) > 0.2) { stage.style.height = Hc.toFixed(1) + 'px'; lastH = Hc; }
        for (let i = 0; i < n; i++) zs[i] = riseAt(t, wk[i], weeks, dy[i]) * hgt[i];
        const b = extent(cam, e, false);
        const pad = lerp(2, 20, e);
        const left = pad + gutter * (1 - e);
        const top = pad + 20 * (1 - e);
        const aw = W - left - pad;
        const ah = Hc - top - pad;
        const bw = Math.max(1e-6, b.maxx - b.minx);
        const bh = Math.max(1e-6, b.maxy - b.miny);
        const s = Math.min(aw / bw, ah / bh);
        const ox = left + (aw - bw * s) / 2 - b.minx * s;
        const oy = top + (ah - bh * s) / 2 - b.miny * s;
        const { cs, sn, se, ce } = cam;
        const px = (x, y) => ox + (x * cs - y * sn) * s;
        const py = (x, y, z) => oy + ((x * sn + y * cs) * se - z * ce) * s;

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, W, Hmax);
        order.sort((a, c) => (wk[a] + 0.5) * sn + (dy[a] + 0.5) * cs - ((wk[c] + 0.5) * sn + (dy[c] + 0.5) * cs));

        const w = lerp(0.78, 0.9, e);
        const off = (1 - w) / 2;
        const radius = lerp(0.17, 0.03, e) * s;
        const outline = (1 - e) * 0.07;
        const lift = 0.7 * e;
        const ex = col[0], ey = col[1], ez = col[2];

        for (let k = 0; k < n; k++) {
            const i = order[k];
            const x0 = wk[i] + off, y0 = dy[i] + off, x1 = x0 + w, y1 = y0 + w;
            const z = zs[i] + hover[i] * lift;
            const o = i * 24;
            polys[o] = px(x0, y0); polys[o + 1] = py(x0, y0, z);
            polys[o + 2] = px(x1, y0); polys[o + 3] = py(x1, y0, z);
            polys[o + 4] = px(x1, y1); polys[o + 5] = py(x1, y1, z);
            polys[o + 6] = px(x0, y1); polys[o + 7] = py(x0, y1, z);
            polys[o + 8] = px(x0, y1); polys[o + 9] = py(x0, y1, 0);
            polys[o + 10] = px(x1, y1); polys[o + 11] = py(x1, y1, 0);
            polys[o + 12] = polys[o + 4]; polys[o + 13] = polys[o + 5];
            polys[o + 14] = polys[o + 6]; polys[o + 15] = polys[o + 7];
            polys[o + 16] = px(x1, y0); polys[o + 17] = py(x1, y0, 0);
            polys[o + 18] = polys[o + 10]; polys[o + 19] = polys[o + 11];
            polys[o + 20] = polys[o + 4]; polys[o + 21] = polys[o + 5];
            polys[o + 22] = polys[o + 2]; polys[o + 23] = polys[o + 3];

            const tall = z * ce * s;
            let f = 0;
            if (tall > 0.35 && w * cs * s > 0.35) f |= 1;
            if (tall > 0.35 && w * sn * s > 0.35) f |= 2;
            faces[i] = f;

            const L = lv[i] * 3;
            let r = col[L], g = col[L + 1], bl = col[L + 2];
            const d = dim[i];
            if (d > 0.002) { r += (ex - r) * 0.72 * d; g += (ey - g) * 0.72 * d; bl += (ez - bl) * 0.72 * d; }
            const hv = hover[i];
            if (hv > 0.002) { const m = 0.16 * hv; r += (fg[0] - r) * m; g += (fg[1] - g) * m; bl += (fg[2] - bl) * m; }
            if (f & 1) { ctx.beginPath(); quadPath(ctx, polys, o + 8, 0); ctx.fillStyle = rgbString(r * 0.84, g * 0.84, bl * 0.84); ctx.fill(); }
            if (f & 2) { ctx.beginPath(); quadPath(ctx, polys, o + 16, 0); ctx.fillStyle = rgbString(r * 0.68, g * 0.68, bl * 0.68); ctx.fill(); }
            ctx.beginPath();
            quadPath(ctx, polys, o, radius);
            ctx.fillStyle = rgbString(r, g, bl);
            ctx.fill();
            if (outline > 0.004) { ctx.strokeStyle = 'rgba(' + fg[0] + ',' + fg[1] + ',' + fg[2] + ',' + outline.toFixed(3) + ')'; ctx.lineWidth = 1; ctx.stroke(); }
            if (hv > 0.02) { ctx.strokeStyle = 'rgba(' + fg[0] + ',' + fg[1] + ',' + fg[2] + ',' + (0.85 * hv).toFixed(3) + ')'; ctx.lineWidth = 1.5; ctx.stroke(); }
        }

        // labels: top and left in 2D, along the front edge in 3D; they fade, never pop
        const muted = mixRGB(bg, fg, 0.55);
        ctx.font = font;
        const a2 = 1 - smoothstep(0, 0.4, e);
        const a3 = smoothstep(0.62, 1, e);
        const mc = (a) => 'rgba(' + Math.round(muted[0]) + ',' + Math.round(muted[1]) + ',' + Math.round(muted[2]) + ',' + a.toFixed(3) + ')';
        if (a2 > 0.004) {
            ctx.fillStyle = mc(a2);
            ctx.textAlign = 'left';
            ctx.textBaseline = 'bottom';
            let edge = -Infinity;
            for (const m of model.months) {
                const x = px(m.week + off, -0.3);
                const tw = ctx.measureText(m.label).width;
                if (x < edge || x + tw > W) continue;
                ctx.fillText(m.label, x, py(m.week + off, -0.3, 0) - 3);
                edge = x + tw + 6;
            }
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            if (gutter > 0) for (const r of weekdayRows) ctx.fillText(r.label, px(0, r.day + 0.5) - 6, py(0, r.day + 0.5, 0));
        }
        if (a3 > 0.004) {
            ctx.fillStyle = mc(a3);
            ctx.textAlign = 'left';
            ctx.textBaseline = 'top';
            let edge = -Infinity;
            for (const m of model.months) {
                const x = px(m.week + 0.5, 7.3);
                if (x < edge || x + ctx.measureText(m.label).width > W) continue;
                ctx.fillText(m.label, x, py(m.week + 0.5, 7.3, 0) + 2);
                edge = x + ctx.measureText(m.label).width + 10;
            }
        }

        // the tooltip rides the active cell through morphs and orbits
        if (activeIdx >= 0 && activeIdx < n) {
            const i = activeIdx;
            const z = zs[i] + hover[i] * lift;
            const tx = px(wk[i] + 0.5, dy[i] + 0.5);
            const ty = Math.min(py(wk[i] + off, dy[i] + off, z), py(wk[i] + off + w, dy[i] + off, z), py(wk[i] + off, dy[i] + off + w, z));
            const half = tipW / 2;
            const cx = Math.min(W - half - 2, Math.max(half + 2, tx));
            tip.style.transform = 'translate(' + (cx - half).toFixed(1) + 'px,' + (ty - 8).toFixed(1) + 'px) translateY(-100%)';
            tip.style.setProperty('--arrow', (tx - cx + half).toFixed(1) + 'px');
        }
    };

    const tick = (now) => {
        raf = 0;
        const dt = Math.min(0.05, Math.max(0, (now - last) / 1000));
        last = now;
        let moving = false;
        if (t !== target) {
            const step = reduced ? 1 : (dt * 1000) / Math.max(1, duration);
            t = target > t ? Math.min(target, t + step) : Math.max(target, t - step);
            moving = true;
        }
        const ko = reduced ? 1 : 1 - Math.exp(-dt * 12);
        yaw += (yawGoal - yaw) * ko;
        elev += (elevGoal - elev) * ko;
        if (Math.abs(yawGoal - yaw) > 1e-4 || Math.abs(elevGoal - elev) > 1e-4) moving = true;
        else { yaw = yawGoal; elev = elevGoal; }
        const kc = reduced ? 1 : 1 - Math.exp(-dt * 7);
        for (let k = 0; k < 15; k++) {
            const d = colGoal[k] - col[k];
            if (Math.abs(d) > 0.4) { col[k] += d * kc; moving = true; } else col[k] = colGoal[k];
        }
        const kh = reduced ? 1 : 1 - Math.exp(-dt * 16);
        const kd = reduced ? 1 : 1 - Math.exp(-dt * 10);
        for (let i = 0; i < n; i++) {
            const hg = i === activeIdx ? 1 : 0;
            const dg = legendLevel >= 0 && lv[i] !== legendLevel ? 1 : 0;
            if (hover[i] !== hg) { hover[i] = Math.abs(hg - hover[i]) < 0.003 ? hg : hover[i] + (hg - hover[i]) * kh; moving = true; }
            if (dim[i] !== dg) { dim[i] = Math.abs(dg - dim[i]) < 0.003 ? dg : dim[i] + (dg - dim[i]) * kd; moving = true; }
        }
        draw();
        if (moving) raf = requestAnimationFrame(tick);
    };
    const kick = () => {
        if (raf) return;
        last = performance.now();
        raf = requestAnimationFrame(tick);
    };

    // ---------- interaction ----------
    const showTip = () => {
        const c = model.cells[activeIdx];
        if (!c) { tip.removeAttribute('data-on'); tip.setAttribute('aria-hidden', 'true'); return; }
        tipText.innerHTML = '<strong>' + esc(c.count ? nf.format(c.count) + ' ' + noun(c.count) : tr('No :plural', { plural: tr(plural) })) + '</strong><span style="opacity:.75"> ' + esc(tr('on')) + ' ' + esc(dfy.format(dayMs(c.date))) + '</span>';
        tip.setAttribute('data-on', '');
        tip.setAttribute('aria-hidden', 'false');
        tipW = tip.offsetWidth;
    };
    const refreshActive = () => {
        const next = hovered >= 0 ? hovered : pinned;
        if (next === activeIdx) return;
        activeIdx = next;
        showTip();
        kick();
    };
    const hit = (x, y) => {
        for (let k = n - 1; k >= 0; k--) {
            const i = order[k];
            const o = i * 24;
            if (pointInQuad(polys, o, x, y)) return i;
            if (faces[i] & 1 && pointInQuad(polys, o + 8, x, y)) return i;
            if (faces[i] & 2 && pointInQuad(polys, o + 16, x, y)) return i;
        }
        return -1;
    };
    const local = (ev) => { const r = canvas.getBoundingClientRect(); return [ev.clientX - r.left, ev.clientY - r.top]; };
    const open = (i) => {
        const c = model.cells[i];
        if (!c || !opts.href) return;
        const url = new URL(opts.href, location.href);
        url.searchParams.set('date', c.date);
        location.href = url.toString();
    };

    let drag = null;
    const orbitOn = () => (opts.orbit ?? true) && target === 1;
    canvas.addEventListener('pointerdown', (ev) => {
        if (ev.button !== 0) return;
        const can = orbitOn();
        drag = { id: ev.pointerId, x: ev.clientX, y: ev.clientY, yaw: yawGoal, elev: elevGoal, moved: false, orbit: can, mouse: ev.pointerType === 'mouse' };
        if (can) { try { canvas.setPointerCapture(ev.pointerId); } catch { /* capture is a nicety */ } }
    });
    canvas.addEventListener('pointermove', (ev) => {
        if (drag && drag.orbit && ev.pointerId === drag.id) {
            const dx = ev.clientX - drag.x;
            const dyy = ev.clientY - drag.y;
            if (drag.moved || Math.hypot(dx, dyy) > 4) {
                drag.moved = true;
                yawGoal = Math.min(YAW_RANGE[1] - YAW_3D, Math.max(YAW_RANGE[0] - YAW_3D, drag.yaw + dx * 0.006));
                if (drag.mouse) elevGoal = Math.min(ELEV_RANGE[1] - ELEV_3D, Math.max(ELEV_RANGE[0] - ELEV_3D, drag.elev + dyy * 0.004));
                canvas.style.cursor = 'grabbing';
                hovered = -1;
                refreshActive();
                kick();
                return;
            }
        }
        if (ev.pointerType !== 'mouse') return;
        const [x, y] = local(ev);
        const i = hit(x, y);
        if (i !== hovered) { hovered = i; refreshActive(); }
        canvas.style.cursor = orbitOn() ? 'grab' : i >= 0 ? 'pointer' : 'default';
    });
    canvas.addEventListener('pointerup', (ev) => {
        if (!drag || ev.pointerId !== drag.id) return;
        const wasMoved = drag.moved;
        drag = null;
        if (canvas.hasPointerCapture(ev.pointerId)) canvas.releasePointerCapture(ev.pointerId);
        canvas.style.cursor = orbitOn() ? 'grab' : 'default';
        if (wasMoved) return;
        const [x, y] = local(ev);
        const i = hit(x, y);
        const second = i >= 0 && i === pinned;
        pinned = i === pinned ? -1 : i;
        if (ev.pointerType !== 'mouse') hovered = -1;
        refreshActive();
        // a mouse click opens the day; on touch the first tap shows the tooltip, the second opens it
        if (i >= 0 && (ev.pointerType === 'mouse' || second)) open(i);
    });
    canvas.addEventListener('pointercancel', () => { drag = null; });
    canvas.addEventListener('pointerleave', () => { if (drag) return; hovered = -1; refreshActive(); });
    canvas.addEventListener('dblclick', () => { yawGoal = 0; elevGoal = 0; kick(); });
    canvas.addEventListener('blur', () => { pinned = -1; refreshActive(); });
    canvas.addEventListener('keydown', (ev) => {
        const keys = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End', 'Escape', 'Enter', ' '];
        if (!keys.includes(ev.key) || !n) return;
        ev.preventDefault();
        if (ev.key === 'Escape') { pinned = -1; hovered = -1; refreshActive(); return; }
        let i = pinned >= 0 ? pinned : activeIdx >= 0 ? activeIdx : n - 1;
        if (ev.key === 'Enter' || ev.key === ' ') { open(i); return; }
        if (pinned >= 0 || activeIdx >= 0) {
            if (ev.key === 'ArrowLeft') i -= 7;
            if (ev.key === 'ArrowRight') i += 7;
            if (ev.key === 'ArrowUp') i -= 1;
            if (ev.key === 'ArrowDown') i += 1;
            if (ev.key === 'Home') i = 0;
            if (ev.key === 'End') i = n - 1;
        }
        i = Math.max(0, Math.min(n - 1, i));
        pinned = i;
        hovered = -1;
        refreshActive();
        live.textContent = describe(i);
    });

    // view toggle and legend
    const setTarget = () => {
        const goal = view === '3d' ? 1 : 0;
        if (!entered || goal === target) return;
        target = goal;
        if (goal === 0) { yawGoal = 0; elevGoal = 0; }
        canvas.style.cursor = orbitOn() ? 'grab' : 'default';
        kick();
    };
    toggle.addEventListener('click', (ev) => {
        const b = ev.target.closest('.sk-tbtn');
        if (!b || b.dataset.v === view) return;
        view = b.dataset.v;
        try { localStorage.setItem(storeKey, view); } catch { /* ignore */ }
        syncChrome();
        setTarget();
    });
    const setLegend = (i) => {
        legendLevel = i;
        swatchHost.querySelectorAll('.sk-sw').forEach((s) => s.setAttribute('aria-pressed', String(Number(s.dataset.i) === i)));
        kick();
    };
    swatchHost.addEventListener('mouseover', (e) => { const s = e.target.closest('.sk-sw'); if (s) setLegend(Number(s.dataset.i)); });
    swatchHost.addEventListener('focusin', (e) => { const s = e.target.closest('.sk-sw'); if (s) setLegend(Number(s.dataset.i)); });
    swatchHost.addEventListener('mouseleave', () => setLegend(-1));
    swatchHost.addEventListener('focusout', () => setLegend(-1));
    swatchHost.addEventListener('click', (e) => { const s = e.target.closest('.sk-sw'); if (s) setLegend(legendLevel === Number(s.dataset.i) ? -1 : Number(s.dataset.i)); });

    // ---------- start ----------
    retheme();
    relayout();
    syncChrome();
    const enter = () => {
        if (entered) return;
        entered = true;
        if (reduced) t = view === '3d' ? 1 : 0;
        setTarget();
    };
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            if (entries.some((en) => en.isIntersecting)) { enter(); io.disconnect(); }
        }, { threshold: 0.35 });
        io.observe(stage);
    } else enter();
    new ResizeObserver(() => { if (Math.round(stage.clientWidth) !== W) relayout(); }).observe(stage);
    reduceMq.addEventListener('change', () => { reduced = reduceMq.matches; kick(); });
}

export function initSkylines(root = document) {
    root.querySelectorAll('[data-skyline]').forEach(mount);
}
