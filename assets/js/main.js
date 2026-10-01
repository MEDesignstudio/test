/* ==========================================================================
   GemeinsamKI – main.js
   Keine Abhängigkeiten. Alle Daten kommen über data-Attribute aus PHP.
   ========================================================================== */
(() => {
    'use strict';

    const root = document.documentElement;
    root.classList.remove('no-js');
    root.classList.add('js');

    const body = document.body;
    const ENDPOINT = body.dataset.endpoint || 'controller.php';
    const PAGEVIEW = body.dataset.pv || '';
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
    const clamp = (v, min, max) => Math.min(max, Math.max(min, v));
    const rand = (min, max) => min + Math.random() * (max - min);
    const numberFormat = new Intl.NumberFormat('de-DE');
    const spriteUrl = (() => {
        const use = $('use');
        return use ? use.getAttribute('href').split('#')[0] : '';
    })();

    const svgIcon = (name, cls = '') => {
        const ns = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('class', `icon icon-${name} ${cls}`.trim());
        svg.setAttribute('aria-hidden', 'true');
        const use = document.createElementNS(ns, 'use');
        use.setAttribute('href', `${spriteUrl}#i-${name}`);
        svg.appendChild(use);
        return svg;
    };

    /** Führt eine Funktion aus, sobald ein Element sichtbar wird (und optional wieder unsichtbar). */
    const onVisible = (el, enter, leave, options = {}) => {
        if (!('IntersectionObserver' in window)) {
            enter(el);
            return null;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) enter(entry.target);
                else if (leave) leave(entry.target);
            });
        }, { threshold: options.threshold ?? 0.2, rootMargin: options.rootMargin ?? '0px' });
        io.observe(el);
        return io;
    };

    /* ---------------------------------------------------------------------
       Toasts & Konfetti
       --------------------------------------------------------------------- */
    const toastStack = $('#toastStack');
    const toast = (text, iconName = 'check', duration = 3800) => {
        if (!toastStack) return;
        const el = document.createElement('div');
        el.className = 'toast';
        const icon = document.createElement('span');
        icon.className = 'toast-icon';
        icon.appendChild(svgIcon(iconName));
        const label = document.createElement('span');
        label.textContent = text;
        el.append(icon, label);
        toastStack.appendChild(el);
        setTimeout(() => {
            el.classList.add('is-leaving');
            setTimeout(() => el.remove(), 450);
        }, duration);
    };

    const confetti = (x, y, amount = 36) => {
        if (reduceMotion) return;
        const colors = ['#685EFF', '#3EE6B4', '#FFB547', '#FF5C8A', '#8A82FF', '#ffffff'];
        for (let i = 0; i < amount; i++) {
            const piece = document.createElement('span');
            piece.className = 'confetti';
            piece.style.left = `${x}px`;
            piece.style.top = `${y}px`;
            piece.style.background = colors[i % colors.length];
            document.body.appendChild(piece);
            const angle = rand(0, Math.PI * 2);
            const velocity = rand(80, 260);
            const dx = Math.cos(angle) * velocity;
            const dy = Math.sin(angle) * velocity - rand(60, 160);
            const anim = piece.animate([
                { transform: 'translate(0, 0) rotate(0deg)', opacity: 1 },
                { transform: `translate(${dx}px, ${dy}px) rotate(${rand(-200, 200)}deg)`, opacity: 1, offset: 0.55 },
                { transform: `translate(${dx * 1.2}px, ${dy + 260}px) rotate(${rand(-400, 400)}deg)`, opacity: 0 },
            ], { duration: rand(1100, 1700), easing: 'cubic-bezier(.2,.6,.3,1)' });
            anim.onfinish = () => piece.remove();
        }
    };

    /* ---------------------------------------------------------------------
       Header: Zustand beim Scrollen, Ausblenden beim Runterscrollen
       --------------------------------------------------------------------- */
    const header = $('#siteHeader');
    let lastY = window.scrollY;
    const updateHeader = () => {
        if (!header) return;
        const y = window.scrollY;
        header.classList.toggle('is-scrolled', y > 24);
        if (!body.classList.contains('menu-open')) {
            if (y > lastY + 6 && y > 420) header.classList.add('is-hidden');
            else if (y < lastY - 6 || y < 420) header.classList.remove('is-hidden');
        }
        lastY = y;
    };

    /* ---------------------------------------------------------------------
       Megamenü
       --------------------------------------------------------------------- */
    const mega = $('#megaMenu');
    const menuToggle = $('#menuToggle');
    const menuLabel = menuToggle ? $('.menu-toggle-label', menuToggle) : null;
    let lastFocus = null;

    const setMenu = (open) => {
        if (!mega || !menuToggle) return;
        if (open) {
            const rect = menuToggle.getBoundingClientRect();
            mega.style.setProperty('--mx', `${rect.left + rect.width / 2}px`);
            mega.style.setProperty('--my', `${rect.top + rect.height / 2}px`);
            lastFocus = document.activeElement;
        }
        mega.classList.toggle('is-open', open);
        mega.setAttribute('aria-hidden', open ? 'false' : 'true');
        mega.inert = !open;
        menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        body.classList.toggle('menu-open', open);
        body.classList.toggle('is-locked', open);
        if (menuLabel) menuLabel.textContent = open ? menuLabel.dataset.close : menuLabel.dataset.open;
        if (open) {
            header && header.classList.remove('is-hidden');
            setTimeout(() => { const first = $('.mega-link', mega); first && first.focus({ preventScroll: true }); }, 380);
        } else if (lastFocus && lastFocus.focus) {
            lastFocus.focus({ preventScroll: true });
        }
    };

    if (mega && menuToggle) {
        menuToggle.addEventListener('click', () => setMenu(!mega.classList.contains('is-open')));
        mega.addEventListener('click', (event) => {
            const link = event.target.closest('a');
            if (link) setMenu(false);
        });
        document.addEventListener('keydown', (event) => {
            if (!mega.classList.contains('is-open')) return;
            if (event.key === 'Escape') {
                setMenu(false);
                return;
            }
            if (event.key === 'Tab') {
                const focusables = [menuToggle, ...$$('a, button', mega)].filter((el) => el.offsetParent !== null);
                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });
    }

    /* ---------------------------------------------------------------------
       Kontakt-Button mit Aufklappmenü
       --------------------------------------------------------------------- */
    const fab = $('#contactFab');
    if (fab) {
        const fabButton = $('#contactFabButton');
        const panel = $('#contactPanel');
        const close = $('#contactPanelClose');
        const setFab = (open) => {
            fab.classList.toggle('is-open', open);
            fabButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            panel.setAttribute('aria-hidden', open ? 'false' : 'true');
            panel.inert = !open;
            if (open) setTimeout(() => { const first = $('.contact-fab-item', panel); first && first.focus({ preventScroll: true }); }, 250);
        };
        fabButton.addEventListener('click', () => setFab(!fab.classList.contains('is-open')));
        close.addEventListener('click', () => { setFab(false); fabButton.focus(); });
        document.addEventListener('click', (event) => {
            if (fab.classList.contains('is-open') && !fab.contains(event.target)) setFab(false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && fab.classList.contains('is-open')) {
                setFab(false);
                fabButton.focus();
            }
        });
        panel.addEventListener('click', (event) => { if (event.target.closest('a')) setFab(false); });
    }

    /* ---------------------------------------------------------------------
       Back-to-Top: Ring schließt sich im Uhrzeigersinn beim Scrollen
       --------------------------------------------------------------------- */
    const toTop = $('#toTop');
    let celebrated = false;
    const updateToTop = () => {
        if (!toTop) return;
        const max = document.documentElement.scrollHeight - window.innerHeight;
        const progress = max > 0 ? clamp((window.scrollY / max) * 100, 0, 100) : 0;
        toTop.style.setProperty('--progress', progress.toFixed(2));
        toTop.classList.toggle('is-visible', window.scrollY > 420);
        const complete = progress >= 99.4;
        toTop.classList.toggle('is-complete', complete);
        if (complete && !celebrated) {
            celebrated = true;
            toTop.classList.add('is-celebrating');
            toast('Geschafft – du hast alles entdeckt!', 'sparkle');
            setTimeout(() => toTop.classList.remove('is-celebrating'), 1800);
        }
    };
    if (toTop) {
        toTop.addEventListener('click', (event) => {
            event.preventDefault();
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
            const main = $('#inhalt');
            setTimeout(() => main && main.focus({ preventScroll: true }), 600);
        });
    }

    /* ---------------------------------------------------------------------
       Einblendungen & Wort-Animationen
       --------------------------------------------------------------------- */
    const splitWords = (el) => {
        let index = 0;
        const walk = (node) => {
            Array.from(node.childNodes).forEach((child) => {
                if (child.nodeType === Node.TEXT_NODE) {
                    const parts = child.textContent.split(/(\s+)/);
                    const frag = document.createDocumentFragment();
                    parts.forEach((part) => {
                        if (part === '') return;
                        if (/^\s+$/.test(part)) {
                            frag.appendChild(document.createTextNode(part));
                            return;
                        }
                        const outer = document.createElement('span');
                        outer.className = 'w';
                        const inner = document.createElement('span');
                        inner.textContent = part;
                        inner.style.setProperty('--i', index++);
                        outer.appendChild(inner);
                        frag.appendChild(outer);
                    });
                    child.replaceWith(frag);
                } else if (child.nodeType === Node.ELEMENT_NODE && child.tagName !== 'BR') {
                    walk(child);
                }
            });
        };
        walk(el);
        el.classList.add('split-ready');
    };

    if (!reduceMotion) $$('[data-split]').forEach(splitWords);

    const revealTargets = $$('.reveal, [data-split]');
    if ('IntersectionObserver' in window && !reduceMotion) {
        const revealObserver = new IntersectionObserver((entries) => {
            const batch = entries.filter((e) => e.isIntersecting).map((e) => e.target);
            batch.forEach((el, i) => {
                el.style.setProperty('--delay', `${Math.min(i, 6) * 90}ms`);
                el.classList.add('is-in');
                revealObserver.unobserve(el);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        revealTargets.forEach((el) => revealObserver.observe(el));
    } else {
        revealTargets.forEach((el) => el.classList.add('is-in'));
    }

    /* Wort-für-Wort-Aufleuchten beim Scrollen */
    const scrubTexts = $$('[data-scrub-words]').map((el) => {
        const keys = (el.dataset.keys || '').toLowerCase().split(',').map((k) => k.trim()).filter(Boolean);
        const words = el.textContent.trim().split(/\s+/);
        el.textContent = '';
        const spans = words.map((word, i) => {
            const span = document.createElement('span');
            span.className = 'word';
            span.textContent = word;
            const clean = word.toLowerCase().replace(/[.,;:!?–]/g, '');
            if (keys.includes(clean)) span.classList.add('is-key');
            el.appendChild(span);
            if (i < words.length - 1) el.appendChild(document.createTextNode(' '));
            return span;
        });
        if (reduceMotion) spans.forEach((s) => s.classList.add('is-lit'));
        return { el, spans };
    });
    const updateScrub = () => {
        if (reduceMotion) return;
        const vh = window.innerHeight;
        scrubTexts.forEach(({ el, spans }) => {
            const rect = el.getBoundingClientRect();
            const progress = clamp((vh * 0.88 - rect.top) / (rect.height + vh * 0.38), 0, 1);
            const lit = Math.round(progress * spans.length);
            spans.forEach((span, i) => span.classList.toggle('is-lit', i < lit));
        });
    };

    /* Zähler */
    $$('[data-count]').forEach((el) => {
        const target = parseFloat(el.dataset.count);
        const from = parseFloat(el.dataset.countFrom || (target === 0 ? 12 : 0));
        el.textContent = numberFormat.format(reduceMotion ? target : from);
        onVisible(el, () => {
            if (el.dataset.counted) return;
            el.dataset.counted = '1';
            if (reduceMotion) return;
            const start = performance.now();
            const duration = 1600;
            const step = (now) => {
                const t = clamp((now - start) / duration, 0, 1);
                const eased = 1 - Math.pow(1 - t, 3);
                el.textContent = numberFormat.format(Math.round(from + (target - from) * eased));
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }, null, { threshold: 0.6 });
    });

    /* ---------------------------------------------------------------------
       Identicons (Punktmatrix wie im Mesh-Dashboard)
       --------------------------------------------------------------------- */
    const hashString = (str) => {
        let h = 2166136261;
        for (let i = 0; i < str.length; i++) {
            h ^= str.charCodeAt(i);
            h = Math.imul(h, 16777619);
        }
        return h >>> 0;
    };
    const identiconBits = (seed, size) => {
        let h = hashString(seed);
        const bits = [];
        const half = Math.ceil(size / 2);
        for (let y = 0; y < size; y++) {
            const row = [];
            for (let x = 0; x < half; x++) {
                h = Math.imul(h ^ (h >>> 13), 1274126177) >>> 0;
                row.push((h & 7) > 2);
            }
            const mirror = row.slice(0, size - half).reverse();
            bits.push(...row, ...mirror);
        }
        return bits;
    };
    $$('[data-identicon]').forEach((el) => {
        identiconBits(el.dataset.identicon, 7).forEach((on) => {
            const dot = document.createElement('i');
            if (on) dot.className = 'on';
            el.appendChild(dot);
        });
    });

    /* ---------------------------------------------------------------------
       Mesh-Canvas (Hero & Seitenköpfe)
       --------------------------------------------------------------------- */
    class MeshCanvas {
        constructor(canvas) {
            this.canvas = canvas;
            this.ctx = canvas.getContext('2d');
            this.mode = canvas.dataset.mesh || 'soft';
            this.section = canvas.parentElement;
            this.anchor = this.mode === 'hero' ? $('.hero-visual', this.section) : null;
            this.pointer = { x: -9999, y: -9999 };
            this.packets = [];
            this.running = false;
            this.lastSpawn = 0;
            this.resize = this.resize.bind(this);
            this.frame = this.frame.bind(this);
            this.resize();
            if ('ResizeObserver' in window) new ResizeObserver(this.resize).observe(this.section);
            else window.addEventListener('resize', this.resize);
            if (finePointer) {
                this.section.addEventListener('pointermove', (e) => {
                    const r = this.canvas.getBoundingClientRect();
                    this.pointer.x = e.clientX - r.left;
                    this.pointer.y = e.clientY - r.top;
                });
                this.section.addEventListener('pointerleave', () => { this.pointer.x = -9999; this.pointer.y = -9999; });
            }
            if (reduceMotion) {
                this.draw(0);
            } else {
                onVisible(this.section, () => this.start(), () => this.stop(), { threshold: 0 });
                document.addEventListener('visibilitychange', () => (document.hidden ? this.stop() : this.start()));
            }
        }

        resize() {
            const rect = this.section.getBoundingClientRect();
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            this.w = rect.width;
            this.h = rect.height;
            this.canvas.width = Math.round(this.w * dpr);
            this.canvas.height = Math.round(this.h * dpr);
            this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            if (this.anchor) {
                const a = this.anchor.getBoundingClientRect();
                this.hub = { x: a.left - rect.left + a.width / 2, y: a.top - rect.top + a.height / 2 };
                this.orbitBase = Math.min(a.width, a.height, 560) / 2;
            } else {
                this.hub = this.mode === 'hero' ? { x: this.w * 0.7, y: this.h * 0.5 } : null;
                this.orbitBase = Math.min(this.w, this.h) / 3;
            }
            const density = this.mode === 'hero' ? 15000 : 22000;
            const count = Math.round(clamp((this.w * this.h) / density, 18, this.mode === 'hero' ? 90 : 60));
            this.particles = Array.from({ length: count }, () => ({
                x: Math.random() * this.w,
                y: Math.random() * this.h,
                vx: rand(-0.18, 0.18),
                vy: rand(-0.18, 0.18),
                r: rand(0.8, 2),
                tone: Math.random() < 0.25 ? 'mint' : 'brand',
            }));
            if (this.hub) {
                const radii = [0.42, 0.66, 0.9];
                this.macs = Array.from({ length: 7 }, (_, i) => ({
                    ring: radii[i % 3] * this.orbitBase,
                    angle: (i / 7) * Math.PI * 2 + rand(-0.3, 0.3),
                    speed: (i % 2 ? 1 : -1) * rand(0.0006, 0.0013),
                    size: rand(5, 8),
                    pulse: rand(0, Math.PI * 2),
                }));
            } else {
                this.macs = [];
            }
            if (reduceMotion) this.draw(0);
        }

        start() {
            if (this.running) return;
            this.running = true;
            requestAnimationFrame(this.frame);
        }

        stop() {
            this.running = false;
        }

        frame(time) {
            if (!this.running) return;
            this.update(time);
            this.draw(time);
            requestAnimationFrame(this.frame);
        }

        update(time) {
            const { w, h } = this;
            this.particles.forEach((p) => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < -10) p.x = w + 10;
                if (p.x > w + 10) p.x = -10;
                if (p.y < -10) p.y = h + 10;
                if (p.y > h + 10) p.y = -10;
                const dx = this.pointer.x - p.x;
                const dy = this.pointer.y - p.y;
                const dist = Math.hypot(dx, dy);
                if (dist < 160) {
                    p.x += dx * 0.004;
                    p.y += dy * 0.004;
                }
            });
            this.macs.forEach((m) => { m.angle += m.speed * 16; });
            if (this.hub && this.macs.length && time - this.lastSpawn > 650) {
                this.lastSpawn = time;
                const mac = this.macs[Math.floor(Math.random() * this.macs.length)];
                this.packets.push({ mac, t: 0, dir: Math.random() < 0.5 ? 1 : -1, speed: rand(0.008, 0.016) });
            }
            this.packets.forEach((p) => { p.t += p.speed; });
            this.packets = this.packets.filter((p) => p.t < 1);
        }

        macPos(m) {
            return { x: this.hub.x + Math.cos(m.angle) * m.ring, y: this.hub.y + Math.sin(m.angle) * m.ring * 0.82 };
        }

        draw(time) {
            const ctx = this.ctx;
            ctx.clearRect(0, 0, this.w, this.h);
            const link = this.mode === 'hero' ? 130 : 110;

            // Partikel-Netz
            for (let i = 0; i < this.particles.length; i++) {
                const a = this.particles[i];
                for (let j = i + 1; j < this.particles.length; j++) {
                    const b = this.particles[j];
                    const d = Math.hypot(a.x - b.x, a.y - b.y);
                    if (d < link) {
                        ctx.strokeStyle = `rgba(138, 130, 255, ${(1 - d / link) * 0.22})`;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.stroke();
                    }
                }
                const pd = Math.hypot(a.x - this.pointer.x, a.y - this.pointer.y);
                if (pd < 170) {
                    ctx.strokeStyle = `rgba(62, 230, 180, ${(1 - pd / 170) * 0.45})`;
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(this.pointer.x, this.pointer.y);
                    ctx.stroke();
                }
            }
            this.particles.forEach((p) => {
                ctx.fillStyle = p.tone === 'mint' ? 'rgba(62, 230, 180, .75)' : 'rgba(173, 167, 255, .7)';
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();
            });

            if (!this.hub) return;
            const hub = this.hub;

            // Orbits
            ctx.setLineDash([3, 8]);
            ctx.strokeStyle = 'rgba(138, 130, 255, .22)';
            [0.42, 0.66, 0.9].forEach((r) => {
                ctx.beginPath();
                ctx.ellipse(hub.x, hub.y, r * this.orbitBase, r * this.orbitBase * 0.82, 0, 0, Math.PI * 2);
                ctx.stroke();
            });
            ctx.setLineDash([]);

            // Verbindungen Hub <-> Macs
            this.macs.forEach((m) => {
                const p = this.macPos(m);
                ctx.strokeStyle = 'rgba(255, 255, 255, .12)';
                ctx.setLineDash([4, 6]);
                ctx.lineDashOffset = -time / 60;
                ctx.beginPath();
                ctx.moveTo(hub.x, hub.y);
                ctx.lineTo(p.x, p.y);
                ctx.stroke();
                ctx.setLineDash([]);
            });

            // Datenpakete
            this.packets.forEach((pk) => {
                const p = this.macPos(pk.mac);
                const t = pk.dir > 0 ? pk.t : 1 - pk.t;
                const x = hub.x + (p.x - hub.x) * t;
                const y = hub.y + (p.y - hub.y) * t;
                const grad = ctx.createRadialGradient(x, y, 0, x, y, 9);
                const color = pk.dir > 0 ? '62, 230, 180' : '173, 167, 255';
                grad.addColorStop(0, `rgba(${color}, 1)`);
                grad.addColorStop(1, `rgba(${color}, 0)`);
                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(x, y, 9, 0, Math.PI * 2);
                ctx.fill();
            });

            // Hub
            const pulse = 1 + Math.sin(time / 900) * 0.05;
            const glow = ctx.createRadialGradient(hub.x, hub.y, 0, hub.x, hub.y, 130 * pulse);
            glow.addColorStop(0, 'rgba(143, 136, 255, .55)');
            glow.addColorStop(0.45, 'rgba(104, 94, 255, .18)');
            glow.addColorStop(1, 'rgba(104, 94, 255, 0)');
            ctx.fillStyle = glow;
            ctx.beginPath();
            ctx.arc(hub.x, hub.y, 130 * pulse, 0, Math.PI * 2);
            ctx.fill();
            const core = ctx.createRadialGradient(hub.x - 10, hub.y - 12, 4, hub.x, hub.y, 34);
            core.addColorStop(0, '#9C95FF');
            core.addColorStop(1, '#3A3299');
            ctx.fillStyle = core;
            ctx.beginPath();
            ctx.arc(hub.x, hub.y, 34, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = 'rgba(200, 196, 255, .6)';
            ctx.lineWidth = 1.5;
            ctx.stroke();
            ctx.fillStyle = '#fff';
            ctx.font = '700 12px Inter, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('HUB', hub.x, hub.y + 1);

            // Macs
            this.macs.forEach((m) => {
                const p = this.macPos(m);
                const s = m.size + Math.sin(time / 600 + m.pulse) * 1.2;
                ctx.fillStyle = 'rgba(62, 230, 180, .14)';
                ctx.beginPath();
                ctx.arc(p.x, p.y, s * 2.6, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = '#0E1512';
                ctx.strokeStyle = '#3EE6B4';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.arc(p.x, p.y, s * 1.6, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();
                ctx.fillStyle = '#3EE6B4';
                ctx.beginPath();
                ctx.arc(p.x, p.y, s * 0.55, 0, Math.PI * 2);
                ctx.fill();
            });
        }
    }
    $$('canvas[data-mesh]').forEach((canvas) => new MeshCanvas(canvas));

    /* ---------------------------------------------------------------------
       Drei-Schritte-Linie zeichnen
       --------------------------------------------------------------------- */
    const stepsTrack = $('[data-steps-track]');
    const stepsPath = stepsTrack ? $('[data-steps-path]', stepsTrack) : null;
    const updateSteps = () => {
        if (!stepsTrack || !stepsPath) return;
        const rect = stepsTrack.getBoundingClientRect();
        const vh = window.innerHeight;
        const progress = clamp((vh * 0.85 - rect.top) / (rect.height * 0.9), 0, 1);
        stepsPath.style.setProperty('--draw', reduceMotion ? 1 : progress.toFixed(3));
    };

    /* ---------------------------------------------------------------------
       App-Mockup: Play/Stop, Zeitplan, Speicherfreigabe
       --------------------------------------------------------------------- */
    $$('[data-app-mock]').forEach((mock) => {
        const toggle = $('[data-app-toggle]', mock);
        const statusText = $('[data-app-status-text]', mock);
        const headline = $('[data-app-headline]', mock);
        const sub = $('[data-app-sub]', mock);
        const requestsEl = $('[data-app-requests]', mock);
        const ramInput = $('[data-app-ram-input]', mock);
        const ramLabels = $$('[data-app-ram], [data-app-ram-out]', mock);
        const scheduleText = $('[data-app-schedule-text]', mock);
        const arc = $('[data-clock-arc]', mock);
        const hand = $('[data-clock-hand]', mock);
        const caption = $('[data-clock-caption]', mock);
        let requests = 0;
        let timer = null;
        let firstStart = true;

        const tick = () => {
            requests += Math.random() < 0.3 ? 2 : 1;
            requestsEl.textContent = numberFormat.format(requests);
            timer = setTimeout(tick, rand(700, 1900));
        };

        const setActive = (active) => {
            mock.classList.toggle('is-active', active);
            toggle.setAttribute('aria-pressed', active ? 'true' : 'false');
            toggle.setAttribute('aria-label', active ? 'Freigabe stoppen' : 'Freigabe starten');
            statusText.textContent = active ? 'Teilt gerade' : 'Pausiert';
            headline.textContent = active ? 'Dein Mac denkt mit!' : 'Bereit zum Teilen';
            sub.textContent = active ? 'Verbunden mit dem Mesh. Stop beendet die Freigabe sofort.' : 'Drück Play – dein Mac tritt dem Mesh bei.';
            clearTimeout(timer);
            if (active) {
                timer = setTimeout(tick, 900);
                if (firstStart) {
                    firstStart = false;
                    const r = toggle.getBoundingClientRect();
                    confetti(r.left + r.width / 2, r.top + r.height / 2, 28);
                    toast('Willkommen im Mesh! (Demo)', 'mesh');
                }
            }
        };
        toggle.addEventListener('click', () => setActive(!mock.classList.contains('is-active')));

        const setSchedule = (chip) => {
            $$('[data-schedule]', mock).forEach((c) => {
                c.classList.toggle('is-active', c === chip);
                c.setAttribute('aria-checked', c === chip ? 'true' : 'false');
            });
            const from = parseInt(chip.dataset.from, 10);
            const to = parseInt(chip.dataset.to, 10);
            const hours = ((to - from + 24) % 24) || 24;
            arc.style.setProperty('--arc', ((hours / 24) * 1000).toFixed(1));
            arc.style.setProperty('--arc-offset', (-(from / 24) * 1000).toFixed(1));
            scheduleText.textContent = chip.dataset.text;
            caption.textContent = hours === 24 ? 'Teilt rund um die Uhr' : `Teilt ${from}–${to} Uhr`;
        };
        $$('[data-schedule]', mock).forEach((chip) => chip.addEventListener('click', () => setSchedule(chip)));
        setSchedule($('[data-schedule].is-active', mock) || $('[data-schedule]', mock));

        const now = new Date();
        hand.style.setProperty('--hand', `${(now.getHours() + now.getMinutes() / 60) * 15}deg`);

        const updateRam = () => {
            const min = parseFloat(ramInput.min);
            const max = parseFloat(ramInput.max);
            const val = parseFloat(ramInput.value);
            ramInput.style.setProperty('--fill', `${((val - min) / (max - min)) * 100}%`);
            ramLabels.forEach((el) => { el.textContent = val; });
        };
        ramInput.addEventListener('input', updateRam);
        updateRam();
    });

    /* ---------------------------------------------------------------------
       Mac-Check
       --------------------------------------------------------------------- */
    $$('[data-mac-check]').forEach((box) => {
        const chips = JSON.parse(box.dataset.chips || '{}');
        const ramSteps = JSON.parse(box.dataset.ram || '[]');
        const joinBase = box.dataset.join;
        const ramInput = $('[data-ram-input]', box);
        const ramOutput = $('[data-ram-output]', box);
        const maxModel = $('[data-max-model]', box);
        const gpuEl = $('[data-gpu]', box);
        const examples = $('[data-examples]', box);
        const levelName = $('[data-level-name]', box);
        const levelNum = $('[data-level-num]', box);
        const badge = $('[data-level-badge]', box);
        const track = $$('.level-track li', box);
        const joinLink = $('[data-join-link]', box);
        const levels = ['Funke', 'Knoten', 'Kraftwerk', 'Turbine', 'Supernova'];
        const state = {
            gen: ($('[data-gen].is-active', box) || {}).dataset?.gen || 'M4',
            variant: ($('[data-variant].is-active', box) || {}).dataset?.variant ?? 'Pro',
        };
        let currentLevel = 0;
        let shownModel = parseFloat(maxModel.textContent) || 0;

        const exampleFor = (b) => {
            if (b < 6) return 'Llama 3.2 3B, Qwen3 4B';
            if (b < 12) return 'Llama 3.1 8B, Qwen3 8B';
            if (b < 20) return 'Gemma 3 12B, Qwen3 14B';
            if (b < 30) return 'gpt-oss-20b, Mistral Small 24B';
            if (b < 50) return 'Qwen3 32B, Gemma 3 27B';
            if (b < 100) return 'Llama 3.3 70B';
            if (b < 200) return 'gpt-oss-120b';
            return 'Qwen3 235B und größer';
        };

        const animateNumber = (to) => {
            const from = shownModel;
            shownModel = to;
            if (reduceMotion) { maxModel.textContent = numberFormat.format(to); return; }
            const start = performance.now();
            const step = (now) => {
                const t = clamp((now - start) / 600, 0, 1);
                const eased = 1 - Math.pow(1 - t, 3);
                maxModel.textContent = numberFormat.format(Math.round(from + (to - from) * eased));
                if (t < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };

        const update = () => {
            const hasUltra = Object.prototype.hasOwnProperty.call(chips, `${state.gen} Ultra`);
            const ultraBtn = $('[data-variant="Ultra"]', box);
            ultraBtn.disabled = !hasUltra;
            if (!hasUltra && state.variant === 'Ultra') state.variant = 'Max';
            $$('[data-variant]', box).forEach((b) => {
                const on = b.dataset.variant === state.variant;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-checked', on ? 'true' : 'false');
            });
            $$('[data-gen]', box).forEach((b) => {
                const on = b.dataset.gen === state.gen;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-checked', on ? 'true' : 'false');
            });

            const chip = state.variant ? `${state.gen} ${state.variant}` : state.gen;
            const ram = ramSteps[parseInt(ramInput.value, 10)] || 16;
            const raw = (ram * 0.75) / 0.6;
            const billions = raw < 10 ? Math.max(1, Math.floor(raw)) : Math.round(raw / 5) * 5;
            const level = ram < 16 ? 1 : ram < 32 ? 2 : ram < 64 ? 3 : ram < 128 ? 4 : 5;

            ramOutput.textContent = `${ram} GB`;
            ramInput.style.setProperty('--fill', `${(parseInt(ramInput.value, 10) / (ramSteps.length - 1)) * 100}%`);
            animateNumber(billions);
            gpuEl.textContent = `bis ${chips[chip] || '–'}`;
            examples.textContent = exampleFor(billions);
            levelName.textContent = levels[level - 1];
            levelNum.textContent = level;
            track.forEach((li, i) => {
                li.classList.toggle('is-on', i < level);
                li.classList.toggle('is-current', i === level - 1);
            });
            if (level !== currentLevel && currentLevel !== 0) {
                badge.classList.remove('is-bump');
                void badge.offsetWidth;
                badge.classList.add('is-bump');
                if (level === 5) toast('Supernova! Dein Mac ist ein Kraftpaket.', 'star');
            }
            currentLevel = level;
            if (joinLink) {
                const params = new URLSearchParams({ chip, ram: String(ram), rolle: 'bereitstellen' });
                joinLink.href = `${joinBase}?${params.toString()}#anmelden`;
            }
        };

        $$('[data-gen]', box).forEach((b) => b.addEventListener('click', () => { state.gen = b.dataset.gen; update(); }));
        $$('[data-variant]', box).forEach((b) => b.addEventListener('click', () => { if (!b.disabled) { state.variant = b.dataset.variant; update(); } }));
        ramInput.addEventListener('input', update);
        update();
    });

    /* ---------------------------------------------------------------------
       Mesh-Dashboard (Simulation mit Beispieldaten)
       --------------------------------------------------------------------- */
    $$('[data-dashboard]').forEach((dash) => {
        const svgNS = 'http://www.w3.org/2000/svg';
        const linksLayer = $('[data-map-links]', dash);
        const packetLayer = $('[data-map-packets]', dash);
        const nodeLayer = $('[data-map-nodes]', dash);
        const mapToast = $('[data-dash-toast]', dash);
        const statEls = {};
        $$('[data-stat]', dash).forEach((el) => { statEls[el.dataset.stat] = el; });
        const label = $('[data-stat-label]', dash);
        const hub = { x: 300, y: 230 };
        const positions = [{ x: 112, y: 140 }, { x: 488, y: 140 }, { x: 488, y: 330 }, { x: 112, y: 330 }];

        const el = (tag, attrs = {}, parent = null) => {
            const node = document.createElementNS(svgNS, tag);
            Object.entries(attrs).forEach(([k, v]) => node.setAttribute(k, v));
            if (parent) parent.appendChild(node);
            return node;
        };

        const devices = $$('[data-device]', dash).map((li, i) => {
            const pos = positions[i % positions.length];
            const device = {
                li,
                id: li.dataset.device,
                name: li.dataset.name,
                ram: parseInt(li.dataset.ram, 10),
                gpu: parseInt(li.dataset.gpu, 10),
                online: li.classList.contains('is-online'),
                busy: 0,
                requestsEl: $('[data-device-requests]', li),
                stateEl: $('[data-device-state]', li),
                loadEl: $('[data-device-load]', li),
                modelLi: $(`[data-model-of="${li.dataset.device}"]`, dash),
                pos,
            };
            device.requests = parseInt(device.requestsEl.textContent, 10) || 0;
            device.link = el('line', { x1: hub.x, y1: hub.y, x2: pos.x, y2: pos.y, class: 'map-link' }, linksLayer);
            const g = el('g', { class: 'map-node' }, nodeLayer);
            el('circle', { cx: pos.x, cy: pos.y, r: 34, class: 'map-node-halo' }, g);
            el('circle', { cx: pos.x, cy: pos.y, r: 30, class: 'map-node-ring' }, g);
            identiconBits(device.name, 5).forEach((on, k) => {
                const col = k % 5;
                const row = Math.floor(k / 5);
                el('circle', { cx: pos.x - 14 + col * 7, cy: pos.y - 14 + row * 7, r: 2.3, class: on ? 'map-node-dot on' : 'map-node-dot' }, g);
            });
            el('circle', { cx: pos.x + 22, cy: pos.y + 22, r: 10, class: 'map-node-badge' }, g);
            const badgeText = el('text', { x: pos.x + 22, y: pos.y + 26, class: 'map-node-badge-text' }, g);
            badgeText.textContent = '1';
            const name = el('text', { x: pos.x, y: pos.y + 54, class: 'map-node-name' }, g);
            name.textContent = device.name;
            const meta = el('text', { x: pos.x, y: pos.y + 70, class: 'map-node-meta' }, g);
            meta.textContent = `${device.ram} GB · ${device.gpu} GPU-Kerne`;
            device.g = g;
            return device;
        });

        let active = 0;
        const flash = (key) => {
            const s = statEls[key];
            if (!s) return;
            s.classList.add('is-bump');
            setTimeout(() => s.classList.remove('is-bump'), 700);
        };
        const setStat = (key, value) => {
            const s = statEls[key];
            if (!s) return;
            const text = numberFormat.format(value);
            if (s.textContent !== text) {
                s.textContent = text;
                flash(key);
            }
        };
        const render = () => {
            const online = devices.filter((d) => d.online);
            setStat('devices', online.length);
            setStat('models', online.length);
            setStat('models2', online.length);
            setStat('gpu', online.reduce((sum, d) => sum + d.gpu, 0));
            setStat('ram', online.reduce((sum, d) => sum + d.ram, 0));
            setStat('requests', active);
            if (label) label.textContent = `${online.length} online · ${devices.length} bekannt`;
            devices.forEach((d) => {
                d.li.classList.toggle('is-online', d.online);
                d.stateEl.textContent = d.online ? 'online' : 'offline';
                d.g.classList.toggle('is-off', !d.online);
                d.link.classList.toggle('is-off', !d.online);
                d.link.classList.toggle('is-busy', d.busy > 0);
                if (d.modelLi) d.modelLi.classList.toggle('is-online', d.online);
                d.loadEl.style.setProperty('--load', d.busy > 0 ? `${Math.round(rand(55, 92))}%` : `${Math.round(rand(14, 30))}%`);
            });
        };

        const travel = (device, back, done) => {
            const dot = el('circle', { r: 5, class: back ? 'map-packet is-back' : 'map-packet' }, packetLayer);
            const from = back ? device.pos : hub;
            const to = back ? hub : device.pos;
            const start = performance.now();
            const duration = 700;
            const step = (now) => {
                const t = clamp((now - start) / duration, 0, 1);
                const e = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
                dot.setAttribute('cx', from.x + (to.x - from.x) * e);
                dot.setAttribute('cy', from.y + (to.y - from.y) * e);
                if (t < 1) requestAnimationFrame(step);
                else { dot.remove(); done && done(); }
            };
            requestAnimationFrame(step);
        };

        const sendRequest = () => {
            const online = devices.filter((d) => d.online);
            if (!online.length || active >= 6) return;
            const device = online[Math.floor(Math.random() * online.length)];
            active++;
            device.busy++;
            render();
            travel(device, false, () => {
                setTimeout(() => travel(device, true, () => {
                    active = Math.max(0, active - 1);
                    device.busy = Math.max(0, device.busy - 1);
                    device.requests++;
                    device.requestsEl.textContent = numberFormat.format(device.requests);
                    render();
                }), rand(600, 2200));
            });
        };

        const showMapToast = (text) => {
            if (!mapToast) return;
            mapToast.textContent = text;
            mapToast.classList.add('is-visible');
            setTimeout(() => mapToast.classList.remove('is-visible'), 3200);
        };

        let running = false;
        let requestTimer = null;
        let churnTimer = null;
        const loop = () => {
            if (!running) return;
            sendRequest();
            requestTimer = setTimeout(loop, rand(700, 1500));
        };
        const churn = () => {
            if (!running) return;
            const candidate = devices[devices.length - 1];
            if (candidate) {
                candidate.online = !candidate.online;
                if (candidate.online) {
                    candidate.li.classList.add('is-joining');
                    setTimeout(() => candidate.li.classList.remove('is-joining'), 1300);
                    showMapToast(`${candidate.name} ist beigetreten · +${candidate.gpu} GPU-Kerne`);
                } else {
                    showMapToast(`${candidate.name} pausiert (Zeitplan)`);
                }
                render();
            }
            churnTimer = setTimeout(churn, rand(9000, 14000));
        };
        const startDash = () => {
            if (running || reduceMotion) return;
            running = true;
            loop();
            churnTimer = setTimeout(churn, 3500);
        };
        const stopDash = () => {
            running = false;
            clearTimeout(requestTimer);
            clearTimeout(churnTimer);
        };
        render();
        onVisible(dash, startDash, stopDash, { threshold: 0.15 });
        document.addEventListener('visibilitychange', () => (document.hidden ? stopDash() : null));
    });

    /* ---------------------------------------------------------------------
       Mini-Chat & Editor-Animation
       --------------------------------------------------------------------- */
    $$('[data-chat-mini]').forEach((chat) => {
        let timers = [];
        const clear = () => { timers.forEach(clearTimeout); timers = []; };
        const run = () => {
            clear();
            chat.dataset.stage = '0';
            chat.classList.remove('is-answered');
            timers.push(setTimeout(() => { chat.dataset.stage = '1'; }, 400));
            timers.push(setTimeout(() => { chat.dataset.stage = '2'; }, 1300));
            timers.push(setTimeout(() => chat.classList.add('is-answered'), 2900));
            timers.push(setTimeout(() => { chat.dataset.stage = '3'; }, 3400));
            timers.push(setTimeout(run, 10000));
        };
        if (reduceMotion) { chat.dataset.stage = '3'; chat.classList.add('is-answered'); return; }
        onVisible(chat, run, clear, { threshold: 0.4 });
    });

    $$('[data-editor]').forEach((editor) => {
        let timers = [];
        const clear = () => { timers.forEach(clearTimeout); timers = []; };
        const run = () => {
            clear();
            editor.classList.remove('is-typing', 'is-hinting', 'is-accepted');
            void editor.offsetWidth;
            editor.classList.add('is-typing');
            timers.push(setTimeout(() => editor.classList.add('is-hinting'), 3700));
            timers.push(setTimeout(() => { editor.classList.remove('is-typing', 'is-hinting'); editor.classList.add('is-accepted'); }, 5600));
            timers.push(setTimeout(run, 10500));
        };
        if (reduceMotion) { editor.classList.add('is-accepted'); return; }
        onVisible(editor, run, clear, { threshold: 0.4 });
    });

    /* Wochenplan & Installationsring */
    $$('[data-week-plan]').forEach((plan) => {
        $$('.week-cell.is-on', plan).forEach((cell, i) => cell.style.setProperty('transition-delay', `${i * 6}ms`));
        onVisible(plan, () => plan.classList.add('is-lit'), null, { threshold: 0.4 });
    });

    /* ---------------------------------------------------------------------
       Tilt & magnetische Buttons
       --------------------------------------------------------------------- */
    if (finePointer && !reduceMotion) {
        $$('.tilt').forEach((card) => {
            card.addEventListener('pointermove', (e) => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - 0.5;
                const y = (e.clientY - r.top) / r.height - 0.5;
                card.style.transform = `perspective(1100px) rotateX(${(-y * 4).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
            });
            card.addEventListener('pointerleave', () => { card.style.transform = ''; });
        });
        $$('.magnetic').forEach((btn) => {
            btn.addEventListener('pointermove', (e) => {
                const r = btn.getBoundingClientRect();
                const x = e.clientX - (r.left + r.width / 2);
                const y = e.clientY - (r.top + r.height / 2);
                btn.style.translate = `${(x * 0.22).toFixed(1)}px ${(y * 0.32).toFixed(1)}px`;
            });
            btn.addEventListener('pointerleave', () => { btn.style.translate = ''; });
        });
    }

    /* ---------------------------------------------------------------------
       FAQ: animiertes Akkordeon, Suche & Filter
       --------------------------------------------------------------------- */
    $$('[data-faq]').forEach((item) => {
        const summary = $('summary', item);
        const answer = $('.faq-a', item);
        summary.addEventListener('click', (event) => {
            if (reduceMotion || !answer.animate) return;
            event.preventDefault();
            if (item.open) {
                const anim = answer.animate([{ height: `${answer.scrollHeight}px`, opacity: 1 }, { height: '0px', opacity: 0 }], { duration: 380, easing: 'cubic-bezier(.65,0,.35,1)' });
                item.classList.add('is-closing');
                anim.onfinish = () => { item.open = false; item.classList.remove('is-closing'); };
            } else {
                item.open = true;
                answer.animate([{ height: '0px', opacity: 0 }, { height: `${answer.scrollHeight}px`, opacity: 1 }], { duration: 480, easing: 'cubic-bezier(.16,1,.3,1)' });
            }
        });
    });

    const faqTools = $('[data-faq-tools]');
    if (faqTools) {
        const search = $('[data-faq-search]', faqTools);
        const filters = $$('[data-faq-filter]', faqTools);
        const empty = $('[data-faq-empty]');
        const items = $$('[data-faq]');
        let category = '';
        const normalize = (s) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        const apply = () => {
            const q = normalize(search.value.trim());
            let visible = 0;
            items.forEach((item) => {
                const matchCat = !category || item.dataset.category === category;
                const matchText = !q || normalize(item.textContent).includes(q);
                const show = matchCat && matchText;
                item.classList.toggle('is-hidden', !show);
                if (show) {
                    visible++;
                    item.classList.add('is-in');
                }
            });
            empty.hidden = visible > 0;
        };
        search.addEventListener('input', apply);
        filters.forEach((btn) => btn.addEventListener('click', () => {
            category = btn.dataset.faqFilter;
            filters.forEach((b) => b.classList.toggle('is-active', b === btn));
            apply();
        }));
    }

    /* ---------------------------------------------------------------------
       Formulare: Validierung, Schritte, AJAX-Versand
       --------------------------------------------------------------------- */
    const messages = {
        required: 'Bitte ausfüllen.',
        email: 'Bitte gib eine gültige E-Mail-Adresse an.',
        privacy: 'Bitte stimme der Datenschutzerklärung zu.',
    };

    const clearErrors = (scope) => {
        $$('.field-error', scope).forEach((e) => e.remove());
        $$('.has-error', scope).forEach((e) => e.classList.remove('has-error'));
    };

    const showFieldError = (input, text) => {
        const wrapper = input.closest('.field') || input.closest('.check');
        if (!wrapper) return;
        wrapper.classList.add('has-error');
        if (wrapper.classList.contains('field')) {
            const msg = document.createElement('span');
            msg.className = 'field-error';
            msg.textContent = text;
            wrapper.appendChild(msg);
        }
        input.setAttribute('aria-invalid', 'true');
    };

    const validate = (scope) => {
        clearErrors(scope);
        let firstInvalid = null;
        $$('input, select, textarea', scope).forEach((input) => {
            input.removeAttribute('aria-invalid');
            if (input.closest('.hp-field') || input.type === 'hidden') return;
            let error = '';
            if (input.type === 'checkbox' && input.required && !input.checked) error = input.name === 'privacy' ? messages.privacy : messages.required;
            else if (input.type === 'radio' && input.required && !$$(`input[name="${input.name}"]`, scope).some((r) => r.checked)) error = messages.required;
            else if (input.required && !input.value.trim()) error = messages.required;
            else if (input.type === 'email' && input.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(input.value.trim())) error = messages.email;
            if (error) {
                showFieldError(input, error);
                if (!firstInvalid) firstInvalid = input;
            }
        });
        if (firstInvalid) firstInvalid.focus({ preventScroll: false });
        return !firstInvalid;
    };

    const setMessage = (form, text, type) => {
        const box = $('.form-message', form);
        if (!box) return;
        box.className = `form-message is-${type}`;
        box.textContent = text;
    };

    const successView = (form, title, text) => {
        const wrap = document.createElement('div');
        wrap.className = 'form-success';
        wrap.setAttribute('role', 'status');
        wrap.innerHTML = '<span class="form-success-check"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>';
        const h = document.createElement('h3');
        h.textContent = title;
        const p = document.createElement('p');
        p.textContent = text;
        wrap.append(h, p);
        form.replaceChildren(wrap);
        const r = wrap.getBoundingClientRect();
        confetti(r.left + r.width / 2, r.top + 60, 44);
    };

    /* Mehrstufige Formulare */
    $$('[data-steps]').forEach((form) => {
        const steps = $$('.form-step', form);
        const dots = $$('.step-progress li', form);
        const progress = $('.step-progress', form);
        let current = 0;
        const show = (index) => {
            current = clamp(index, 0, steps.length - 1);
            steps.forEach((s, i) => s.classList.toggle('is-active', i === current));
            dots.forEach((d, i) => {
                d.classList.toggle('is-active', i === current);
                d.classList.toggle('is-done', i < current);
            });
            if (progress) progress.style.setProperty('--step-fill', (current / (steps.length - 1)).toFixed(2));
        };
        form.addEventListener('click', (event) => {
            if (event.target.closest('[data-step-next]')) {
                if (validate(steps[current])) {
                    show(current + 1);
                    const first = $('input:not([type="hidden"]), select, textarea', steps[current]);
                    first && first.focus({ preventScroll: true });
                    form.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'nearest' });
                }
            } else if (event.target.closest('[data-step-prev]')) {
                show(current - 1);
            }
        });
        form.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && current < steps.length - 1) {
                event.preventDefault();
                const next = $('[data-step-next]', steps[current]);
                next && next.click();
            }
        });
        // Beim Anspringen über die Rolle (z. B. ?rolle=nutzen) Schritt 2 bei reinen Nutzer:innen überspringen ist nicht nötig – alle Felder sind optional.
        show(0);
    });

    /* Spendenformular: freier Betrag & Hardware ohne Betrag */
    $$('[data-donate-form]').forEach((form) => {
        const free = $('[data-amount-free]', form);
        const group = $('[data-amount-group]', form);
        const update = () => {
            const preset = $('input[name="amountPreset"]:checked', form);
            const type = $('input[name="type"]:checked', form);
            free.hidden = !(preset && preset.value === 'frei');
            group.hidden = !!(type && type.value === 'hardware');
        };
        form.addEventListener('change', update);
        update();
    });

    /* AJAX-Versand */
    $$('.js-ajax-form').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (form.classList.contains('is-busy')) return;
            const steps = $$('.form-step', form);
            if (steps.length) {
                for (let i = 0; i < steps.length; i++) {
                    if (!validate(steps[i])) {
                        const target = steps[i];
                        steps.forEach((s) => s.classList.toggle('is-active', s === target));
                        return;
                    }
                }
            } else if (!validate(form)) {
                form.classList.add('has-errors');
                setMessage(form, 'Bitte prüfe die markierten Angaben.', 'error');
                return;
            }
            form.classList.remove('has-errors');
            form.classList.add('is-busy');
            setMessage(form, '', 'info');
            try {
                const response = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'fetch', Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                const data = await response.json();
                if (data.ok) {
                    successView(form, data.title || 'Danke!', data.message || 'Wir haben deine Nachricht erhalten.');
                } else {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([name, text]) => {
                            const input = form.querySelector(`[name="${name}"]`);
                            if (input) showFieldError(input, text);
                        });
                    }
                    setMessage(form, data.message || 'Das hat leider nicht geklappt.', 'error');
                }
            } catch (error) {
                setMessage(form, 'Verbindung fehlgeschlagen. Bitte versuche es später erneut.', 'error');
            } finally {
                form.classList.remove('is-busy');
            }
        });
    });

    /* ---------------------------------------------------------------------
       Chat (Demo oder live über controller.php)
       --------------------------------------------------------------------- */
    const chatApp = $('[data-chat-app]');
    if (chatApp) {
        const log = $('[data-chat-log]', chatApp);
        const form = $('[data-chat-form]', chatApp);
        const input = $('[data-chat-input]', chatApp);
        const modelSelect = $('[data-chat-model]', chatApp);
        const sendBtn = $('.chat-send', chatApp);
        const avatarSrc = ($('.chat-avatar img', chatApp) || {}).src || '';
        const history = [];
        let busy = false;

        const addMessage = (role, text, meta = '') => {
            const msg = document.createElement('div');
            msg.className = `chat-msg chat-msg-${role === 'user' ? 'user' : 'ai'}`;
            if (role !== 'user') {
                const avatar = document.createElement('span');
                avatar.className = 'chat-avatar';
                const img = document.createElement('img');
                img.src = avatarSrc;
                img.alt = '';
                avatar.appendChild(img);
                msg.appendChild(avatar);
            }
            const wrap = document.createElement('div');
            const bubble = document.createElement('div');
            bubble.className = 'chat-bubble';
            bubble.textContent = text;
            wrap.appendChild(bubble);
            if (meta) {
                const m = document.createElement('small');
                m.className = 'chat-msg-meta';
                m.textContent = meta;
                wrap.appendChild(m);
            }
            msg.appendChild(wrap);
            log.appendChild(msg);
            log.scrollTop = log.scrollHeight;
            return { msg, bubble, wrap };
        };

        const typeOut = (bubble, text) => new Promise((resolve) => {
            if (reduceMotion) { bubble.textContent = text; resolve(); return; }
            bubble.textContent = '';
            let i = 0;
            const step = () => {
                i = Math.min(text.length, i + Math.ceil(rand(1, 4)));
                bubble.textContent = text.slice(0, i);
                log.scrollTop = log.scrollHeight;
                if (i < text.length) setTimeout(step, 14);
                else resolve();
            };
            step();
        });

        const autosize = () => {
            input.style.height = 'auto';
            input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
        };

        const send = async (text) => {
            if (busy || !text.trim()) return;
            busy = true;
            sendBtn.disabled = true;
            addMessage('user', text.trim());
            history.push({ role: 'user', content: text.trim() });
            input.value = '';
            autosize();
            const typing = addMessage('assistant', '');
            typing.bubble.innerHTML = '<span class="typing"><i></i><i></i><i></i></span>';
            const started = performance.now();
            try {
                const fd = new FormData();
                fd.append('action', 'chat');
                fd.append('token', chatApp.dataset.token || '');
                fd.append('model', modelSelect ? modelSelect.value : '');
                fd.append('messages', JSON.stringify(history.slice(-12)));
                const response = await fetch(ENDPOINT, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'fetch', Accept: 'application/json' } });
                const data = await response.json();
                await new Promise((r) => setTimeout(r, Math.max(0, 700 - (performance.now() - started))));
                if (!data.ok) throw new Error(data.message || 'Fehler');
                await typeOut(typing.bubble, data.reply);
                history.push({ role: 'assistant', content: data.reply });
                const seconds = ((performance.now() - started) / 1000).toFixed(1).replace('.', ',');
                const meta = document.createElement('small');
                meta.className = 'chat-msg-meta';
                meta.textContent = data.demo ? `Demo · Antwort aus unseren FAQ` : `${data.model || ''} · ${seconds} s`;
                typing.wrap.appendChild(meta);
            } catch (error) {
                typing.msg.classList.add('is-error');
                typing.bubble.textContent = error.message && error.message !== 'Failed to fetch' ? error.message : 'Das Mesh ist gerade nicht erreichbar. Bitte versuche es gleich noch einmal.';
            } finally {
                busy = false;
                sendBtn.disabled = false;
                input.focus();
            }
        };

        form.addEventListener('submit', (event) => { event.preventDefault(); send(input.value); });
        input.addEventListener('input', autosize);
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                send(input.value);
            }
        });
        $$('[data-suggestion]', chatApp).forEach((chip) => chip.addEventListener('click', () => send(chip.textContent)));
    }

    /* ---------------------------------------------------------------------
       Kopieren-Buttons
       --------------------------------------------------------------------- */
    $$('[data-copy], [data-copy-text]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const text = btn.dataset.copyText || ($(btn.dataset.copy) || {}).textContent || '';
            try {
                await navigator.clipboard.writeText(text.trim());
                btn.classList.add('is-copied');
                toast('In die Zwischenablage kopiert', 'copy', 2400);
                setTimeout(() => btn.classList.remove('is-copied'), 1600);
            } catch (e) {
                toast('Kopieren nicht möglich', 'x', 2400);
            }
        });
    });

    /* ---------------------------------------------------------------------
       Mesh-Rail (Abschnittsnavigation der Startseite)
       --------------------------------------------------------------------- */
    const rail = $('[data-mesh-rail]');
    const railLinks = rail ? $$('[data-rail]', rail) : [];
    const railSections = railLinks.map((a) => document.getElementById(a.dataset.rail)).filter(Boolean);
    const railFill = rail ? $('[data-rail-fill]', rail) : null;
    const updateRail = () => {
        if (!rail || !railSections.length) return;
        const probe = window.innerHeight * 0.42;
        let current = 0;
        railSections.forEach((section, i) => { if (section.getBoundingClientRect().top <= probe) current = i; });
        railLinks.forEach((a, i) => {
            a.classList.toggle('is-current', i === current);
            a.classList.toggle('is-visited', i < current);
            if (i === current) a.setAttribute('aria-current', 'true');
            else a.removeAttribute('aria-current');
        });
        const footer = $('.site-footer');
        const footerVisible = footer && footer.getBoundingClientRect().top < window.innerHeight * 0.8;
        rail.classList.toggle('is-visible', window.scrollY > window.innerHeight * 0.5 && !footerVisible);
        if (railFill) railFill.style.setProperty('--rail', ((current / (railSections.length - 1)) * 100).toFixed(1));
    };

    /* ---------------------------------------------------------------------
       Scroll-Schleife
       --------------------------------------------------------------------- */
    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            updateHeader();
            updateToTop();
            updateScrub();
            updateSteps();
            updateRail();
            ticking = false;
        });
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    onScroll();

    /* ---------------------------------------------------------------------
       Verweildauer & Scrolltiefe (cookielos, per Beacon an controller.php)
       --------------------------------------------------------------------- */
    if (PAGEVIEW && navigator.sendBeacon) {
        let activeMs = 0;
        let since = document.hidden ? 0 : performance.now();
        let maxScroll = 0;
        const measureScroll = () => {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            const pct = max > 0 ? Math.round((window.scrollY / max) * 100) : 100;
            maxScroll = Math.max(maxScroll, pct);
        };
        window.addEventListener('scroll', measureScroll, { passive: true });
        measureScroll();
        const flush = () => {
            if (since) {
                activeMs += performance.now() - since;
                since = 0;
            }
            const fd = new FormData();
            fd.append('action', 'track');
            fd.append('pv', PAGEVIEW);
            fd.append('t', String(Math.round(activeMs / 1000)));
            fd.append('s', String(maxScroll));
            navigator.sendBeacon(ENDPOINT, fd);
        };
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) flush();
            else since = performance.now();
        });
        window.addEventListener('pagehide', flush);
    }
})();
