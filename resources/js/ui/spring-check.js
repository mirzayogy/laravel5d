// Spring check (port of React Bits "SpringCheck"). Visual state lives in CSS
// through aria-checked; this only toggles it and tells the page.
export function setChecked(btn, checked, { silent = false } = {}) {
    btn.setAttribute('aria-checked', String(checked));
    // An unchecked box is not submitted with the form, like a native checkbox.
    const input = btn.parentElement?.querySelector('input[data-sc-input]');
    if (input) input.disabled = !checked;
    if (!silent) btn.dispatchEvent(new CustomEvent('springcheck:change', { bubbles: true, detail: { checked } }));
}

export function initSpringChecks() {
    document.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-spring-check]');
        if (!btn || btn.disabled) return;
        const next = btn.getAttribute('aria-checked') !== 'true';
        btn.classList.remove('sc-pop');
        void btn.offsetWidth;
        if (next) btn.classList.add('sc-pop');
        setChecked(btn, next);
    });
}
