/*
 * Visita guiada de las vistas (intro.js) + aviso de primera visita + punto de «ayuda nueva».
 * Lo usan los tablones de encargos (projects/partials/board_tour) y el listado de proyectos
 * (projects/partials/projects_help). Estilos en custom/css/aceler-help.css.
 *
 * Uso:
 *   AcelerTour.setup({
 *     seenKey: 'aceler.xxxTour.seen',          // localStorage: ya visto (no vuelve el aviso ni el punto)
 *     assets: { css, js },                     // intro.js, se carga solo al empezar
 *     texts: { next, prev, done, stepOf, toastTitle, toastText, toastStart, toastLater },
 *     dot: elemento del punto rojo del botón ❓ (opcional),
 *     hasContent: () => bool,                  // si no hay nada que enseñar no sale el aviso
 *     beforeStart: () => {},                   // p. ej. cerrar el panel informativo
 *     buildSteps: () => [{ element?, title, text, extra?, position?, menu?, lift?, keepScroll?, keepScrollTop? }],
 *   });
 * - Un paso sin «element» es una burbuja centrada; si «element» no está visible, el paso se salta.
 * - menu: true  → abre el desplegable Bootstrap que haya dentro de «element» y lo cierra al salir.
 * - lift: [elementos] → se les quita el z-index mientras dura el recorrido para que lo resaltado
 *   quede por encima del oscurecido (las cards crean su propia capa).
 * - keepScroll: contenedor con scroll que intro.js no debe mover en ese paso (vuelve a keepScrollTop, 0 por defecto).
 * Los botones con [data-tour-start] lanzan el recorrido.
 */
(function () {
    'use strict';
    if (window.AcelerTour) return;

    const storage = {
        get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
        set(k, v) { try { localStorage.setItem(k, v); } catch (e) {} },
    };
    const visible = (el) => !!el && el.offsetParent !== null && el.getClientRects().length > 0;
    const firstVisible = (selector, root) => {
        try { return Array.from((root || document).querySelectorAll(selector)).find(visible) || null; }
        catch (e) { return null; } // selector no soportado (p. ej. :has en navegadores antiguos)
    };
    const escapeHtml = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    let loader = null;
    function loadIntro(assets) {
        if (window.introJs) return Promise.resolve();
        if (loader) return loader;
        loader = new Promise((resolve, reject) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = assets.css;
            document.head.appendChild(link);
            const script = document.createElement('script');
            script.src = assets.js;
            script.onload = () => resolve();
            script.onerror = () => { loader = null; reject(new Error('intro.js')); };
            document.head.appendChild(script);
        });
        return loader;
    }

    function setup(cfg) {
        const T = cfg.texts || {};
        let toast = null;
        let openMenu = null;
        let lifted = [];

        // --- aviso de primera visita (se crea aquí para no repetir el marcado en cada vista) ---
        function buildToast() {
            const el = document.createElement('div');
            el.className = 'board-tour-toast';
            el.setAttribute('role', 'dialog');
            el.hidden = true;
            el.innerHTML =
                '<div class="board-tour-toast-icon"><i class="fa-solid fa-route"></i></div>' +
                '<div><strong>' + escapeHtml(T.toastTitle) + '</strong>' +
                '<p>' + escapeHtml(T.toastText) + '</p>' +
                '<div class="board-tour-toast-actions">' +
                '<button type="button" class="board-tour-btn-primary" data-tour-start><i class="fa-solid fa-play"></i>' + escapeHtml(T.toastStart) + '</button>' +
                '<button type="button" class="board-tour-btn-secondary" data-tour-dismiss>' + escapeHtml(T.toastLater) + '</button>' +
                '</div></div>';
            document.body.appendChild(el);
            return el;
        }
        function markSeen() {
            storage.set(cfg.seenKey, '1');
            if (cfg.dot) cfg.dot.hidden = true;
        }
        function hideToast() {
            if (!toast || toast.hidden) return;
            toast.classList.remove('is-open');
            setTimeout(() => { toast.hidden = true; }, 200);
        }
        function maybeShowToast() {
            if (storage.get(cfg.seenKey) || (cfg.hasContent && !cfg.hasContent())) return;
            toast = toast || buildToast();
            toast.hidden = false;
            requestAnimationFrame(() => toast.classList.add('is-open'));
        }

        // --- recorrido ---
        function closeMenu() {
            if (!openMenu) return;
            try { bootstrap.Dropdown.getOrCreateInstance(openMenu).hide(); } catch (e) {}
            openMenu = null;
        }
        function unlift() {
            lifted.forEach((el) => el.classList.remove('aceler-tour-lift'));
            lifted = [];
        }
        function bubble(step, i, total) {
            return '<div class="board-tour-step">' + escapeHtml(String(T.stepOf || ':current / :total').replace(':current', i + 1).replace(':total', total)) + '</div>' +
                '<strong class="board-tour-title">' + escapeHtml(step.title) + '</strong>' +
                '<p class="board-tour-text">' + escapeHtml(step.text) + '</p>' + (step.extra || '');
        }

        function start() {
            markSeen();
            hideToast();
            if (cfg.beforeStart) cfg.beforeStart();
            loadIntro(cfg.assets).then(() => {
                const raw = (cfg.buildSteps() || []).filter((s) => !('element' in s) || visible(s.element));
                if (!raw.length) return;
                raw.forEach((s) => (s.lift || []).filter(Boolean).forEach((el) => { el.classList.add('aceler-tour-lift'); lifted.push(el); }));
                const steps = raw.map((s, i) => {
                    const out = { intro: bubble(s, i, raw.length), position: s.position || 'bottom' };
                    if (s.element) out.element = s.element;
                    return out;
                });

                const tour = introJs();
                tour.setOptions({
                    steps,
                    nextLabel: T.next,
                    prevLabel: T.prev,
                    doneLabel: T.done,
                    showBullets: false,
                    showProgress: true,
                    exitOnOverlayClick: true,
                    disableInteraction: true,
                    scrollToElement: true,
                    scrollPadding: 80,
                    tooltipClass: 'board-tour-tooltip',
                    highlightClass: 'board-tour-highlight',
                });
                tour.onbeforechange(function () { closeMenu(); return true; });
                tour.onafterchange(function () {
                    const idx = this._currentStep;
                    const step = raw[idx];
                    if (!step) return;
                    // intro.js desplaza siempre el contenedor con scroll del elemento; si la vista ya lo dejó colocado,
                    // se devuelve a su sitio (si no, corta la fila por arriba)
                    if (step.keepScroll && step.keepScroll.scrollTop !== (step.keepScrollTop || 0)) {
                        step.keepScroll.scrollTop = step.keepScrollTop || 0;
                        tour.refresh();
                    }
                    if (!step.menu) return;
                    const btn = step.element.querySelector('[data-bs-toggle="dropdown"]');
                    if (!btn) return;
                    // Con retraso: el mismo clic en «Siguiente» llega luego al document y Bootstrap cerraría el menú
                    setTimeout(() => {
                        if (tour._currentStep !== idx || !document.querySelector('.introjs-tooltip')) return;
                        try { bootstrap.Dropdown.getOrCreateInstance(btn).show(); openMenu = btn; } catch (e) {}
                        tour.refresh();
                    }, 120);
                });
                tour.onexit(function () { closeMenu(); unlift(); });
                tour.start();
            }).catch(() => { unlift(); });
        }

        if (cfg.dot) cfg.dot.hidden = !!storage.get(cfg.seenKey);

        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-tour-start]')) { e.preventDefault(); e.stopPropagation(); start(); }
            else if (e.target.closest('[data-tour-dismiss]')) { markSeen(); hideToast(); }
        }, true);

        const onReady = () => setTimeout(maybeShowToast, 1200);
        if (document.readyState === 'complete') onReady(); else window.addEventListener('load', onReady);

        return { start };
    }

    window.AcelerTour = { setup, visible, firstVisible };
})();
