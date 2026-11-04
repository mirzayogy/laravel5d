// Installable app: register the service worker and offer an "Install" button when the browser allows it.
let deferred = null;

export function initPwa() {
    if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
        navigator.serviceWorker.register('/sw.js').catch(() => { /* the app works without it */ });
    }

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferred = e;
        document.querySelectorAll('[data-install]').forEach((b) => (b.hidden = false));
        document.dispatchEvent(new CustomEvent('pwa:installable'));
    });
    window.addEventListener('appinstalled', () => {
        deferred = null;
        document.querySelectorAll('[data-install]').forEach((b) => (b.hidden = true));
    });
    document.addEventListener('click', async (e) => {
        if (!e.target.closest('[data-install]') || !deferred) return;
        deferred.prompt();
        await deferred.userChoice;
        deferred = null;
        document.querySelectorAll('[data-install]').forEach((b) => (b.hidden = true));
    });
}

export const canInstall = () => deferred !== null;
export const install = async () => { if (!deferred) return; deferred.prompt(); await deferred.userChoice; deferred = null; };
