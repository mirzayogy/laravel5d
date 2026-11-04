// A short burst of confetti in the brand colours, for the moment every habit of the day is done.
import { reducedMotion } from './spring.js';

const COLORS = ['#ff5900', '#ff8340', '#ffac80', '#ffd1b8', '#000000', '#b9bbc6'];

export function celebrate(origin) {
    if (reducedMotion()) return;
    const r = origin?.getBoundingClientRect?.() ?? { left: innerWidth / 2, top: innerHeight / 3, width: 0, height: 0 };
    const cx = r.left + r.width / 2;
    const cy = r.top + r.height / 2;
    const layer = document.createElement('div');
    layer.setAttribute('aria-hidden', 'true');
    layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:70;overflow:hidden';
    document.body.append(layer);

    for (let i = 0; i < 70; i++) {
        const p = document.createElement('i');
        const size = 5 + Math.random() * 6;
        p.style.cssText = `position:absolute;left:${cx}px;top:${cy}px;width:${size}px;height:${size * (Math.random() > 0.5 ? 1 : 0.45)}px;border-radius:${Math.random() > 0.6 ? '50%' : '2px'};background:${COLORS[i % COLORS.length]}`;
        layer.append(p);
        const angle = (Math.random() * Math.PI) + Math.PI; // fan upwards
        const speed = 160 + Math.random() * 280;
        const dx = Math.cos(angle) * speed;
        const dy = Math.sin(angle) * speed;
        p.animate([
            { transform: 'translate(0,0) rotate(0deg)', opacity: 1 },
            { transform: `translate(${dx}px,${dy}px) rotate(${Math.random() * 360}deg)`, opacity: 1, offset: 0.45 },
            { transform: `translate(${dx * 1.25}px,${dy + 360 + Math.random() * 200}px) rotate(${Math.random() * 720}deg)`, opacity: 0 },
        ], { duration: 1300 + Math.random() * 600, easing: 'cubic-bezier(0.22, 0.8, 0.4, 1)', fill: 'forwards' });
    }
    setTimeout(() => layer.remove(), 2200);
}
