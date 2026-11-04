// Browser notifications for reminders. They fire while Habitude is open in a tab
// or installed as an app; emails (sent by the scheduler) cover the rest.
import { t } from './i18n.js';

const KEY = 'habitude.notify';
const FIRED = 'habitude.notify.fired';
const WINDOW_MIN = 4;

const read = (k) => { try { return localStorage.getItem(k); } catch { return null; } };
const write = (k, v) => { try { localStorage.setItem(k, v); } catch { /* storage unavailable */ } };
const minutes = (hhmm) => { const [h, m] = hhmm.split(':').map(Number); return h * 60 + m; };

const supported = () => 'Notification' in window;
const enabled = () => supported() && Notification.permission === 'granted' && read(KEY) === 'on';

async function show(item) {
    const title = t('Time for :habit', { habit: item.habit });
    const options = { body: `${item.detail} · ${item.time}`, icon: '/icons/icon-192.png', badge: '/icons/icon-192.png', tag: `habit-${item.id}`, data: { url: '/' } };
    const reg = await navigator.serviceWorker?.getRegistration();
    if (reg) reg.showNotification(title, options); else new Notification(title, options);
}

async function tick(url) {
    if (!enabled()) return;
    let data;
    try {
        const res = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        if (!res.ok) return;
        data = await res.json();
    } catch { return; }

    let fired = [];
    try { fired = JSON.parse(read(FIRED) || '[]'); } catch { fired = []; }
    fired = fired.filter((k) => k.startsWith(data.date));
    const now = minutes(data.now);

    for (const item of data.items) {
        const key = `${data.date}|${item.id}`;
        const due = minutes(item.time);
        if (due <= now && now - due <= WINDOW_MIN && !fired.includes(key)) {
            fired.push(key);
            show(item);
        }
    }
    write(FIRED, JSON.stringify(fired));
}

export function initNotifier() {
    const url = document.body.dataset.remindersUrl;
    if (!url || !supported()) return;
    tick(url);
    setInterval(() => tick(url), 60_000);
}

// The switch on the profile page
export function initNotifySetting() {
    const box = document.querySelector('[data-notify-setting]');
    if (!box) return;
    const status = box.querySelector('[data-notify-status]');
    const btn = box.querySelector('[data-notify-toggle]');
    const label = btn.querySelector('span');

    const paint = () => {
        btn.hidden = false;
        if (!supported()) { status.textContent = t('This browser does not support notifications.'); btn.hidden = true; return; }
        if (Notification.permission === 'denied') { status.textContent = t('Blocked for this site. Allow it in your browser settings to use this.'); btn.hidden = true; return; }
        const on = enabled();
        status.textContent = on ? t('On in this browser.') : t('Off in this browser.');
        label.textContent = on ? t('Turn off') : t('Allow notifications');
    };

    btn.addEventListener('click', async () => {
        if (enabled()) { write(KEY, 'off'); return paint(); }
        const permission = Notification.permission === 'granted' ? 'granted' : await Notification.requestPermission();
        if (permission === 'granted') {
            write(KEY, 'on');
            show({ id: 0, habit: t('your habits'), detail: t('Notifications are on'), time: t('now') });
        }
        paint();
    });
    paint();
}
