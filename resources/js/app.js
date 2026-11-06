const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
const rupiah = (value) => 'Rp ' + Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

/* Mobile navigation */
document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-nav-toggle]');
    if (toggle) {
        $('#mobile-menu')?.classList.toggle('hidden');
    }
});

/* Toasts: auto dismiss and manual close */
$$('[data-toast]').forEach((toast, index) => {
    const close = () => {
        toast.style.transition = 'opacity .3s, transform .3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-6px)';
        setTimeout(() => toast.remove(), 300);
    };
    $('[data-toast-close]', toast)?.addEventListener('click', close);
    if (toast.dataset.toast !== 'error') {
        setTimeout(close, 4500 + index * 600);
    }
});

/* Confirm dialog for destructive forms: <form data-confirm="Question?"> */
const dialog = $('#confirm-dialog');
let pendingForm = null;

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm]');
    if (!form || !dialog || form.dataset.confirmed === '1' || typeof dialog.showModal !== 'function') {
        return;
    }
    event.preventDefault();
    pendingForm = form;
    $('#confirm-message', dialog).textContent = form.dataset.confirm;
    dialog.showModal();
});

$('#confirm-yes')?.addEventListener('click', () => {
    dialog.close();
    if (pendingForm) {
        pendingForm.dataset.confirmed = '1';
        pendingForm.requestSubmit();
    }
});
$('#confirm-no')?.addEventListener('click', () => {
    dialog.close();
    pendingForm = null;
});

/* Live filter: search box + category chips on the menu page, search box on tables */
const filterRoot = $('[data-filter-root]');
if (filterRoot) {
    const input = $('[data-filter-input]', filterRoot);
    const chips = $$('[data-filter-chip]', filterRoot);
    const items = $$('[data-filter-item]', filterRoot);
    const groups = $$('[data-filter-group]', filterRoot);
    const empty = $('[data-filter-empty]', filterRoot);
    let category = 'all';

    const apply = () => {
        const query = (input?.value || '').trim().toLowerCase();
        let visible = 0;

        items.forEach((item) => {
            const matchText = !query || (item.dataset.search || '').includes(query);
            const matchCat = category === 'all' || item.dataset.cat === category;
            const show = matchText && matchCat;
            item.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        groups.forEach((group) => {
            const any = $$('[data-filter-item]', group).some((item) => !item.classList.contains('hidden'));
            group.classList.toggle('hidden', !any);
        });

        empty?.classList.toggle('hidden', visible > 0);
    };

    input?.addEventListener('input', apply);
    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            category = chip.dataset.filterChip;
            chips.forEach((other) => {
                const active = other === chip;
                other.classList.toggle('chip-active', active);
                other.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            apply();
        });
    });
}

/* Cart on the menu page: quantity steppers, preferences, live summary and total */
const cart = $('[data-cart]');
if (cart) {
    const cards = $$('[data-menu-card]');
    const list = $('[data-cart-list]', cart);
    const emptyText = $('[data-cart-empty]', cart);
    const totalEl = $$('[data-cart-total]');
    const countEls = $$('[data-cart-count]');
    const submit = $('[data-cart-submit]', cart);
    const prefs = new Map();
    const choices = {
        drink: ['Normal', 'Less sugar', 'No sugar', 'Less ice', 'No ice'],
        food: ['Normal', 'Extra spicy', 'Not spicy'],
    };

    const clamp = (value, max) => Math.max(0, Math.min(max, Number.isFinite(value) ? value : 0));

    const render = () => {
        let total = 0;
        let count = 0;
        list.replaceChildren();

        cards.forEach((card) => {
            const input = $('[data-qty-input]', card);
            if (!input) return;
            const max = parseInt(card.dataset.max || '20', 10);
            const qty = clamp(parseInt(input.value, 10), max);
            input.value = qty;
            card.classList.toggle('ring-2', qty > 0);
            card.classList.toggle('ring-brand-500', qty > 0);

            if (qty > 0) {
                const id = input.name.match(/\d+/)[0];
                const price = parseInt(card.dataset.price, 10);
                total += qty * price;
                count += qty;

                const row = document.createElement('li');
                row.className = 'space-y-1.5 text-sm';
                const line = document.createElement('div');
                line.className = 'flex items-start justify-between gap-3';
                const name = document.createElement('span');
                name.className = 'text-stone-700';
                name.textContent = qty + '× ' + card.dataset.name;
                const sub = document.createElement('span');
                sub.className = 'whitespace-nowrap font-medium';
                sub.textContent = rupiah(qty * price);
                line.append(name, sub);
                row.append(line);

                const set = choices[card.dataset.opts];
                if (set) {
                    const select = document.createElement('select');
                    select.name = 'options[' + id + ']';
                    select.className = 'w-full rounded-lg border border-stone-200 bg-white px-2 py-1 text-xs text-stone-600';
                    set.forEach((choice) => {
                        const option = document.createElement('option');
                        option.value = choice === 'Normal' ? '' : choice;
                        option.textContent = (window.__t && window.__t[choice]) || choice;
                        select.append(option);
                    });
                    select.value = prefs.get(id) || '';
                    select.addEventListener('change', () => prefs.set(id, select.value));
                    row.append(select);
                }
                list.append(row);
            }
        });

        emptyText.classList.toggle('hidden', count > 0);
        totalEl.forEach((el) => (el.textContent = rupiah(total)));
        countEls.forEach((el) => (el.textContent = count));
        $$('[data-cart-bar]').forEach((bar) => bar.classList.toggle('hidden', count === 0));
        if (submit) submit.disabled = count === 0;
    };

    cards.forEach((card) => {
        const input = $('[data-qty-input]', card);
        if (!input) return;
        const max = () => parseInt(card.dataset.max || '20', 10);
        $$('[data-qty-step]', card).forEach((button) => {
            button.addEventListener('click', () => {
                input.value = clamp(parseInt(input.value, 10) + parseInt(button.dataset.qtyStep, 10), max());
                render();
            });
        });
        input.addEventListener('input', render);
    });

    $('[data-cart-clear]', cart)?.addEventListener('click', () => {
        cards.forEach((card) => {
            const input = $('[data-qty-input]', card);
            if (input) input.value = 0;
        });
        prefs.clear();
        render();
    });

    /* Table number only matters when eating in */
    const tableField = $('[data-table-field]', cart);
    const types = $$('[data-order-type]', cart);
    const syncType = () => {
        const dineIn = types.find((radio) => radio.checked)?.value !== 'takeaway';
        tableField?.classList.toggle('hidden', !dineIn);
    };
    types.forEach((radio) => radio.addEventListener('change', syncType));
    syncType();

    render();
}

/* Photo preview in the admin menu form */
$$('[data-image-input]').forEach((input) => {
    const preview = $(input.dataset.imageInput);
    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file || !preview) return;
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        $('[data-image-placeholder]')?.classList.add('hidden');
    });
});

/* Simulated payment: short "processing" state before the form is sent */
$$('form[data-processing]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.sent === '1') return;
        event.preventDefault();
        form.dataset.sent = '1';
        const button = $('button[type=submit]', form);
        if (button) {
            button.disabled = true;
            button.textContent = form.dataset.processing;
        }
        setTimeout(() => form.submit(), 1400);
    });
});

/* Copy to clipboard: <button data-copy="text"> */
$$('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copy);
            const original = button.textContent;
            button.textContent = button.dataset.copied || 'Copied';
            setTimeout(() => (button.textContent = original), 1500);
        } catch (e) {
            /* clipboard not available: ignore */
        }
    });
});

/* Order page: refresh when the cashier changes the status or the payment */
const tracker = $('[data-order-poll]');
if (tracker) {
    const finished = ['completed', 'cancelled'];
    const timer = setInterval(async () => {
        try {
            const response = await fetch(tracker.dataset.orderPoll, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const state = await response.json();
            if (state.status !== tracker.dataset.status || state.payment_status !== tracker.dataset.payment) {
                clearInterval(timer);
                const toast = $('#live-toast');
                toast?.classList.remove('hidden');
                setTimeout(() => window.location.reload(), 1200);
            } else if (finished.includes(state.status) && state.payment_status !== 'unpaid') {
                clearInterval(timer);
            }
        } catch (e) {
            /* offline: try again next time */
        }
    }, 6000);
}

/* Staff queue: tell the cashier when new orders arrive */
const queue = $('[data-queue-poll]');
if (queue) {
    const banner = $('#new-orders-banner');
    setInterval(async () => {
        try {
            const response = await fetch(queue.dataset.queuePoll + '?after=' + queue.dataset.latest, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const state = await response.json();
            if (state.new_orders > 0 && banner) {
                $('[data-new-count]', banner).textContent = state.new_orders;
                banner.classList.remove('hidden');
            }
        } catch (e) {
            /* ignore */
        }
    }, 10000);
}

/* Table search: <input data-table-search="#table-id"> filters rows with data-row */
$$('[data-table-search]').forEach((input) => {
    const table = $(input.dataset.tableSearch);
    if (!table) return;
    const rows = $$('[data-row]', table);
    const empty = $('[data-table-empty]', table.closest('[data-table-wrap]') || document);
    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        let visible = 0;
        rows.forEach((row) => {
            const show = !query || (row.dataset.row || '').includes(query);
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        empty?.classList.toggle('hidden', visible > 0);
    });
});
