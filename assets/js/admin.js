/* GemeinsamKI – admin.js */
(() => {
    'use strict';
    const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

    // Balkenbreiten (keine Inline-Styles im Markup)
    $$('[data-w]').forEach((el) => el.style.setProperty('width', `${el.dataset.w}%`));

    // Löschen bestätigen
    $$('[data-confirm]').forEach((btn) => btn.addEventListener('click', (e) => {
        if (!window.confirm(btn.dataset.confirm)) e.preventDefault();
    }));

    // Tabellen filtern
    $$('[data-filter]').forEach((input) => {
        const table = document.querySelector(input.dataset.filter);
        if (!table) return;
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase();
            $$('tbody tr', table).forEach((tr) => { tr.hidden = q !== '' && !tr.textContent.toLowerCase().includes(q); });
        });
    });
    $$('[data-filter-records]').forEach((input) => {
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase();
            $$('[data-record]').forEach((r) => { r.hidden = q !== '' && !r.textContent.toLowerCase().includes(q); });
        });
    });

    // Diagramm-Tooltip
    $$('.chart-wrap').forEach((wrap) => {
        const tip = wrap.querySelector('.chart-tip');
        $$('[data-tip]', wrap).forEach((bar) => {
            bar.addEventListener('pointerenter', () => { tip.textContent = bar.dataset.tip; tip.hidden = false; bar.classList.add('is-hover'); });
            bar.addEventListener('pointermove', (e) => {
                const r = wrap.getBoundingClientRect();
                tip.style.setProperty('left', `${Math.min(e.clientX - r.left + 14, r.width - 220)}px`);
                tip.style.setProperty('top', `${e.clientY - r.top - 44}px`);
            });
            bar.addEventListener('pointerleave', () => { tip.hidden = true; bar.classList.remove('is-hover'); });
        });
    });
})();
