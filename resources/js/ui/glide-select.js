// Glide select (port of React Bits "GlideSelect"): the menu pops from the
// trigger and a highlight pill glides between rows. Replaces native selects.
const ROW = 34;
const PAD = 4;
const GAP = 1;
const STEP = ROW + GAP;

function build(root) {
    const options = JSON.parse(root.dataset.options || '[]');
    const name = root.dataset.name;
    const placeholder = root.dataset.placeholder || 'Select…';
    const listId = `gs-${Math.random().toString(36).slice(2, 8)}`;
    let value = root.dataset.value ?? '';
    let phase = 'closed';
    let active = null;
    let menu, pill, closeTimer, typed = '', typedTimer;

    const input = document.createElement('input');
    input.type = 'hidden';
    if (name) input.name = name;
    root.append(input);

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'gs-trigger';
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.setAttribute('aria-controls', listId);
    trigger.setAttribute('aria-label', root.dataset.label || placeholder);
    trigger.innerHTML = '<span class="gs-label"></span><svg class="gs-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
    root.append(trigger);
    const label = trigger.querySelector('.gs-label');

    const selectedIndex = () => options.findIndex((o) => String(o.value) === String(value));
    const dot = (o) => (o.dot ? `<span class="gs-dot" style="background:${o.dot}"></span>` : '');
    const esc = (t) => String(t ?? '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

    const paintTrigger = (swap = false) => {
        const i = selectedIndex();
        input.value = i >= 0 ? options[i].value : '';
        label.innerHTML = i >= 0 ? `${dot(options[i])}<span>${esc(options[i].label)}</span>` : `<span class="gs-empty">${esc(placeholder)}</span>`;
        if (swap) { root.dataset.swap = ''; setTimeout(() => delete root.dataset.swap, 200); }
    };

    const movePill = (instant) => {
        if (!pill) return;
        if (active === null) { pill.style.opacity = '0'; return; }
        if (instant || pill.style.opacity !== '1') pill.style.transition = 'opacity 150ms ease';
        pill.style.transform = `translateY(${active * STEP}px)`;
        pill.style.opacity = '1';
        requestAnimationFrame(() => (pill.style.transition = ''));
        menu.querySelectorAll('.gs-option')[active]?.scrollIntoView({ block: 'nearest' });
    };

    const open = (viaKey) => {
        if (phase === 'open') return;
        clearTimeout(closeTimer);
        document.dispatchEvent(new CustomEvent('gs:open', { detail: root }));
        menu = document.createElement('div');
        menu.className = 'gs-menu';
        menu.dataset.state = 'closed';
        menu.innerHTML = `<div class="gs-list" id="${listId}" role="listbox"><span class="gs-pill"></span>${options
            .map((o, i) => `<div class="gs-option" role="option" id="${listId}-${i}" data-index="${i}" aria-selected="${String(o.value) === String(value)}">${dot(o)}<span class="gs-grow">${esc(o.label)}</span>${o.tag ? `<span class="gs-tag">${esc(o.tag)}</span>` : ''}<svg class="gs-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></div>`)
            .join('')}</div>`;
        root.append(menu);
        pill = menu.querySelector('.gs-pill');

        const r = root.getBoundingClientRect();
        const need = Math.min(options.length * STEP + PAD * 2, 288) + 8;
        menu.dataset.side = r.bottom + need > window.innerHeight && r.top > need ? 'top' : 'bottom';

        active = selectedIndex() >= 0 ? selectedIndex() : viaKey ? 0 : null;
        phase = 'open';
        trigger.setAttribute('aria-expanded', 'true');
        void menu.offsetHeight;
        menu.dataset.state = 'open';
        if (active !== null) {
            pill.style.transition = 'none';
            pill.style.transform = `translateY(${active * STEP}px)`;
            pill.style.opacity = '1';
            void pill.offsetHeight;
            pill.style.transition = '';
            menu.querySelectorAll('.gs-option')[active]?.scrollIntoView({ block: 'nearest' });
        }
    };

    const close = (instant = false) => {
        if (phase === 'closed') return;
        phase = 'closed';
        active = null;
        trigger.setAttribute('aria-expanded', 'false');
        trigger.removeAttribute('aria-activedescendant');
        const m = menu;
        if (!m) return;
        if (instant) { m.remove(); return; }
        m.dataset.state = 'closed';
        closeTimer = setTimeout(() => m.remove(), 140);
    };

    const pick = (i, viaKey) => {
        const o = options[i];
        if (!o) return close(true);
        const changed = String(o.value) !== String(value);
        value = o.value;
        paintTrigger(changed && !viaKey);
        close(true);
        trigger.focus({ preventScroll: true });
        if (changed) {
            root.dataset.value = String(value);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    trigger.addEventListener('pointerdown', (e) => {
        if (e.button !== 0) return;
        e.preventDefault();
        trigger.focus({ preventScroll: true });
        phase === 'open' ? close() : open(false);
    });

    root.addEventListener('pointerover', (e) => {
        if (phase !== 'open' || e.pointerType === 'touch') return;
        const row = e.target.closest('.gs-option');
        if (row && Number(row.dataset.index) !== active) { active = Number(row.dataset.index); movePill(); }
    });
    root.addEventListener('click', (e) => {
        const row = e.target.closest('.gs-option');
        if (row) pick(Number(row.dataset.index), false);
    });

    trigger.addEventListener('keydown', (e) => {
        const n = options.length;
        const k = e.key;
        if (phase !== 'open') {
            if (['Enter', ' ', 'ArrowDown', 'ArrowUp'].includes(k)) { e.preventDefault(); open(true); }
            return;
        }
        const go = (i) => {
            e.preventDefault();
            active = Math.min(n - 1, Math.max(0, i));
            trigger.setAttribute('aria-activedescendant', `${listId}-${active}`);
            movePill(true);
        };
        const cur = active ?? Math.max(0, selectedIndex());
        if (k === 'ArrowDown') go(active === null ? cur : cur + 1);
        else if (k === 'ArrowUp') go(active === null ? cur : cur - 1);
        else if (k === 'Home') go(0);
        else if (k === 'End') go(n - 1);
        else if (k === 'Enter' || k === ' ') { e.preventDefault(); pick(cur, true); }
        else if (k === 'Escape') { e.preventDefault(); close(true); }
        else if (k === 'Tab') close(true);
        else if (k.length === 1 && !e.metaKey && !e.ctrlKey && !e.altKey) {
            typed += k.toLowerCase();
            clearTimeout(typedTimer);
            typedTimer = setTimeout(() => (typed = ''), 600);
            const from = typed.length > 1 ? cur - 1 : cur;
            for (let s = 1; s <= n; s++) {
                const i = (from + s + n) % n;
                if (String(options[i].label).toLowerCase().startsWith(typed)) { go(i); break; }
            }
        }
    });

    document.addEventListener('pointerdown', (e) => { if (phase === 'open' && !root.contains(e.target)) close(); }, true);
    document.addEventListener('gs:open', (e) => { if (e.detail !== root) close(true); });

    root.glideSet = (v) => { value = v; root.dataset.value = String(v); paintTrigger(false); };
    paintTrigger();
}

export function initGlideSelects(root = document) {
    root.querySelectorAll('[data-glide-select]').forEach(build);
}
