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
 * - spanTo: otro elemento; el paso resalta la zona que va de «element» a «spanTo» como un solo bloque.
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
        let spans = [];

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
        // Capa de un paso con spanTo: rodea a «element» y «spanTo». Va dentro del contenedor con scroll más cercano
        // (no en el body ni en el «offsetParent»: intro.js pone y quita position:relative a lo que resalta, y la
        // referencia cambiaría entre pasos). Así la capa se mueve siempre con el contenido.
        function spanHost(el) {
            for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) {
                const o = getComputedStyle(p).overflowY;
                if (o === 'auto' || o === 'scroll') return p;
            }
            return document.body;
        }
        function placeSpan(s) {
            if (s.keepScroll) s.keepScroll.scrollTop = s.keepScrollTop || 0;
            const host = s.spanHost || (s.spanHost = spanHost(s.element));
            if (host !== document.body && getComputedStyle(host).position === 'static') host.classList.add('aceler-tour-span-host');
            if (s.spanEl.parentElement !== host) host.appendChild(s.spanEl);
            const h = host.getBoundingClientRect();
            const a = s.element.getBoundingClientRect();
            const b = s.spanTo.getBoundingClientRect();
            const left = Math.min(a.left, b.left), top = Math.min(a.top, b.top);
            Object.assign(s.spanEl.style, {
                left: (left - h.left - host.clientLeft + host.scrollLeft) + 'px',
                top: (top - h.top - host.clientTop + host.scrollTop) + 'px',
                width: (Math.max(a.right, b.right) - left) + 'px',
                height: (Math.max(a.bottom, b.bottom) - top) + 'px',
            });
        }
        function removeSpans() {
            spans.forEach((el) => el.remove());
            spans = [];
            document.querySelectorAll('.aceler-tour-span-host').forEach((el) => el.classList.remove('aceler-tour-span-host'));
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
                // spanTo: resaltar como una sola zona desde «element» hasta «spanTo» (p. ej. varias columnas de una fila).
                // Se usa una capa invisible del tamaño de esa zona, que se recoloca al llegar al paso.
                raw.forEach((s) => {
                    if (!s.spanTo || !visible(s.spanTo)) return;
                    s.spanEl = document.createElement('div');
                    s.spanEl.className = 'aceler-tour-span';
                    document.body.appendChild(s.spanEl);
                    placeSpan(s);
                    spans.push(s.spanEl);
                });
                const steps = raw.map((s, i) => {
                    const out = { intro: bubble(s, i, raw.length), position: s.position || 'bottom' };
                    if (s.element) out.element = s.spanEl || s.element;
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
                tour.onbeforechange(function (target) {
                    closeMenu();
                    const s = raw.find((r) => r.spanEl && r.spanEl === target);
                    if (s) placeSpan(s);
                    return true;
                });
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
                tour.onexit(function () { closeMenu(); unlift(); removeSpans(); });
                tour.start();
            }).catch(() => { unlift(); removeSpans(); });
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
