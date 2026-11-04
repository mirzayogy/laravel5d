// Dashboard behaviour: check-ins, +1 progress, timers and the quick mood
// rating. Everything updates in place from the JSON the server returns.
import { renderCounter } from './counter.js';
import { celebrate } from './celebrate.js';
import { t } from './i18n.js';
import { setChecked } from './spring-check.js';
import { toast } from './toast.js';

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

async function post(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(data),
    });
    if (!res.ok) throw new Error(`Request failed (${res.status})`);
    return res.json();
}

const stat = (name) => document.querySelector(`[data-stat="${name}"]`);

function applyResult(row, res) {
    const target = Number(row.dataset.target);
    const unit = row.dataset.unit;
    const text = row.querySelector('[data-progress-text]');
    if (text) text.textContent = target > 1 ? `${res.value} / ${target} ${t(unit)}` : res.completed ? t('Done') : t('Not yet');
    const bar = row.querySelector('[data-progress-bar]');
    if (bar) bar.style.width = `${Math.min(100, (res.value / target) * 100)}%`;
    const list = row.closest('[data-today]');
    const wasAllDone = allDone(list);
    row.dataset.done = res.completed ? '1' : '0';
    row.dataset.value = String(res.value);
    row.dispatchEvent(new CustomEvent('today:refresh', { bubbles: true }));
    const check = row.querySelector('[data-spring-check]');
    if (check && (check.getAttribute('aria-checked') === 'true') !== res.completed) setChecked(check, res.completed, { silent: true });
    const streak = row.querySelector('[data-streak]');
    if (streak) {
        renderCounter(streak, res.habit_streak);
        streak.closest('[data-streak-wrap]')?.toggleAttribute('hidden', res.habit_streak === 0);
    }

    const week = row.querySelector('[data-week-text]');
    if (week && res.week_done !== null && res.week_done !== undefined) {
        week.textContent = t(':done of :goal days this week', { done: res.week_done, goal: row.dataset.goal });
    }

    const s = stat('streak');
    if (s) renderCounter(s, res.streak.current);
    const b = stat('best');
    if (b) renderCounter(b, res.streak.best);
    const d = stat('done');
    if (d) renderCounter(d, res.done);
    const total = stat('total');
    if (total) renderCounter(total, res.total);

    const cell = document.querySelector(`[data-cal-date="${res.date}"]`);
    if (cell && res.total > 0) {
        const ratio = Math.min(1, res.done / res.total);
        cell.style.setProperty('--r', ratio);
        cell.toggleAttribute('data-high', ratio > 0.55);
    }

    res.earned.forEach((a) => toast({ title: t('Achievement unlocked: :name', { name: a.name }), body: a.description ?? '', icon: 'trophy', duration: 6000 }));

    // every habit of today is done: celebrate once
    if (!wasAllDone && allDone(list) && list?.dataset.todayDate === res.date) {
        celebrate(row.querySelector('[data-spring-check]'));
        toast({ title: t('All done for today'), body: t('Every habit is checked off. Nice work!'), icon: 'trophy', duration: 6000 });
    }
}

const allDone = (list) => {
    const rows = list ? [...list.querySelectorAll('[data-habit-row]')] : [];
    return rows.length > 0 && rows.every((r) => r.dataset.done === '1');
};

async function send(row, data) {
    row.setAttribute('aria-busy', 'true');
    try {
        applyResult(row, await post(row.dataset.url, { date: row.dataset.date, ...data }));
    } catch (e) {
        toast({ title: t('Could not save'), body: t('Check your connection and try again.'), icon: 'alert' });
        row.querySelector('[data-spring-check]') && setChecked(row.querySelector('[data-spring-check]'), row.dataset.wasDone === '1', { silent: true });
    } finally {
        row.removeAttribute('aria-busy');
    }
}

const fmt = (secs) => `${String(Math.floor(secs / 60)).padStart(2, '0')}:${String(secs % 60).padStart(2, '0')}`;

export function initTracker() {
    document.addEventListener('springcheck:change', (e) => {
        const row = e.target.closest('[data-habit-row]');
        if (!row) return;
        row.dataset.wasDone = e.detail.checked ? '0' : '1';
        const before = Number(row.dataset.value || 0);
        send(row, { mode: 'set', value: e.detail.checked ? Number(row.dataset.target) : 0 });
        // taking a check back is easy to do by accident, so offer to undo it
        if (!e.detail.checked && before > 0) {
            toast({
                title: t('Unchecked :name', { name: row.dataset.name }),
                icon: 'info',
                duration: 5000,
                action: { label: t('Undo'), onClick: () => send(row, { mode: 'set', value: before }) },
            });
        }
    });

    document.addEventListener('click', (e) => {
        const add = e.target.closest('[data-add]');
        if (add) send(add.closest('[data-habit-row]'), { mode: 'add', value: 1 });

        const start = e.target.closest('[data-timer-start]');
        if (start) {
            const row = start.closest('[data-habit-row]');
            const panel = row.querySelector('[data-timer-panel]');
            const display = row.querySelector('[data-timer-display]');
            let secs = 0;
            renderCounter(display, fmt(0));
            panel.hidden = false;
            start.hidden = true;
            row.timer = setInterval(() => renderCounter(display, fmt(++secs)), 1000);
            row.timerSecs = () => secs;
        }

        const stop = e.target.closest('[data-timer-stop]');
        if (stop) {
            const row = stop.closest('[data-habit-row]');
            clearInterval(row.timer);
            const minutes = Math.max(1, Math.round(row.timerSecs() / 60));
            row.querySelector('[data-timer-panel]').hidden = true;
            row.querySelector('[data-timer-start]').hidden = false;
            send(row, { mode: 'add', value: minutes });
        }
        const cancel = e.target.closest('[data-timer-cancel]');
        if (cancel) {
            const row = cancel.closest('[data-habit-row]');
            clearInterval(row.timer);
            row.querySelector('[data-timer-panel]').hidden = true;
            row.querySelector('[data-timer-start]').hidden = false;
        }
    });

    // Quick mood rating on the dashboard
    document.addEventListener('peek:change', async (e) => {
        const box = e.target.closest('[data-mood-quick]');
        if (!box) return;
        const out = box.querySelector('[data-mood-label]');
        try {
            const res = await post(box.dataset.url, { entry_date: box.dataset.date, mood_level: e.detail.value });
            if (out) out.textContent = t('Saved: :label', { label: res.label });
            if (!res.has_journal) {
                toast({ title: t('Feeling :label today', { label: res.label.toLowerCase() }), body: t('Want to write a few lines about it?'), icon: 'info', duration: 8000, action: { label: t('Write'), onClick: () => { location.href = res.journal_url; } } });
            }
            res.earned.forEach((a) => toast({ title: t('Achievement unlocked: :name', { name: a.name }), body: a.description ?? '', icon: 'trophy', duration: 6000 }));
        } catch {
            if (out) out.textContent = t('Could not save, try again.');
        }
    });
}
