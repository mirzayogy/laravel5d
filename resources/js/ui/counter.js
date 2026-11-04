// Odometer-style counter (port of React Bits "Counter"): every digit is a
// vertical strip 0-9 that slides to its value with a spring easing.
const instances = new WeakMap();

export function renderCounter(el, value) {
    const text = String(value);
    const chars = [...text];
    const signature = chars.map((c) => (/\d/.test(c) ? 'd' : c)).join('');
    const state = instances.get(el);

    el.classList.add('ctr');
    el.setAttribute('role', 'text');
    el.setAttribute('aria-label', text);

    if (state && state.signature === signature) {
        state.cols.forEach((col, i) => col.move(Number(chars.filter((c) => /\d/.test(c))[i])));
        return;
    }

    el.replaceChildren();
    const cols = [];

    chars.forEach((c) => {
        if (!/\d/.test(c)) {
            const sym = document.createElement('span');
            sym.className = 'ctr-sym';
            sym.setAttribute('aria-hidden', 'true');
            sym.textContent = c;
            el.append(sym);
            return;
        }
        const digit = document.createElement('span');
        digit.className = 'ctr-digit';
        digit.setAttribute('aria-hidden', 'true');
        const strip = document.createElement('span');
        strip.className = 'ctr-strip';
        for (let n = 0; n < 10; n++) {
            const cell = document.createElement('span');
            cell.textContent = n;
            strip.append(cell);
        }
        digit.append(strip);
        el.append(digit);
        cols.push({ move: (d) => (strip.style.transform = `translateY(${-d}em)`), target: Number(c) });
    });

    instances.set(el, { signature, cols });

    // Start at 0 and roll up to the value on the next frame.
    requestAnimationFrame(() => requestAnimationFrame(() => cols.forEach((col) => col.move(col.target))));
}

export function initCounters(root = document) {
    root.querySelectorAll('[data-counter]').forEach((el) => renderCounter(el, el.dataset.counter));
}
