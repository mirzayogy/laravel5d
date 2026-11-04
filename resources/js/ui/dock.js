// Dock (port of React Bits "Dock"), used as the mobile navigation: tiles
// magnify around the finger or pointer and spring back on release.
const REACH = 70;
const MAX = 0.45;

function build(dock) {
    const items = [...dock.querySelectorAll('.dock-item')];
    const tiles = items.map((i) => i.querySelector('.dock-tile'));

    const magnify = (x) => {
        dock.classList.add('is-live');
        items.forEach((item, i) => {
            const r = item.getBoundingClientRect();
            const d = Math.abs(x - (r.left + r.width / 2));
            const k = Math.exp(-(d * d) / (2 * (REACH / 1.6) ** 2));
            tiles[i].style.transform = `translateY(${-k * 10}px) scale(${1 + MAX * k})`;
            item.classList.toggle('is-hot', k > 0.6);
            item.style.zIndex = String(Math.round(k * 10));
        });
    };
    const reset = () => {
        dock.classList.remove('is-live');
        items.forEach((item, i) => { tiles[i].style.transform = ''; item.classList.remove('is-hot'); item.style.zIndex = ''; });
    };

    dock.addEventListener('pointermove', (e) => magnify(e.clientX));
    dock.addEventListener('pointerdown', (e) => magnify(e.clientX));
    dock.addEventListener('pointerleave', reset);
    dock.addEventListener('pointerup', () => setTimeout(reset, 120));
    dock.addEventListener('pointercancel', reset);
}

export function initDocks(root = document) {
    root.querySelectorAll('[data-dock]').forEach(build);
}
