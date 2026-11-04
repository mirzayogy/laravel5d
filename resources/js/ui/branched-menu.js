// Branched menu (port of React Bits "BranchedMenu"): sections fold open, a
// trunk line branches to every child, and the line to the current page is
// drawn in ink. A marker glides to the section that holds the current page.
const MARK = 16;

function build(nav) {
    const marker = nav.querySelector('[data-bm-marker]');
    const sections = [...nav.querySelectorAll('[data-bm-section]')];

    const currentHead = () => {
        const leaf = nav.querySelector('.bm-head[aria-current="page"]');
        if (leaf) return { head: leaf, visible: true };
        const section = sections.find((s) => s.querySelector('.bm-item[aria-current="page"]'));
        return section ? { head: section.querySelector('.bm-head'), visible: section.hasAttribute('data-open') } : { head: null, visible: false };
    };

    const place = (glide) => {
        const { head, visible } = currentHead();
        if (!glide) marker.style.transition = 'none';
        if (head && visible) marker.style.top = `${head.offsetTop + (head.offsetHeight - MARK) / 2}px`;
        marker.toggleAttribute('data-on', Boolean(head && visible));
        if (!glide) { void marker.offsetHeight; marker.style.transition = ''; }
    };

    nav.addEventListener('click', (e) => {
        const head = e.target.closest('[data-bm-toggle]');
        if (!head) return;
        const section = head.closest('[data-bm-section]');
        const open = !section.hasAttribute('data-open');
        section.toggleAttribute('data-open', open);
        head.setAttribute('aria-expanded', String(open));
        section.querySelectorAll('.bm-item').forEach((a) => (a.tabIndex = open ? 0 : -1));
        place(true);
    });

    let first = true;
    new ResizeObserver(() => { if (first) { first = false; return; } place(false); }).observe(nav);

    place(false);
    // Draw the line to the current page once the menu is on screen.
    requestAnimationFrame(() => requestAnimationFrame(() => nav.classList.add('is-ready')));
}

export function initBranchedMenus(root = document) {
    root.querySelectorAll('[data-branched-menu]').forEach(build);
}
