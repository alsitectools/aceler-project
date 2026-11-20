{{--
    «Reportar un problema»: enlace sobre la línea del pie del menú (sidebar) y formulario.
    - Por ahora solo fallos (ProblemReportController@store, type=problem).
    - Se añaden solos la página, el navegador, el tamaño de pantalla y los últimos errores de la página.
    - La captura se pega con Ctrl+V o se elige como imagen.
    - «Mis reportes» enseña lo enviado y su estado. Soporte (users.is_support) los gestiona en su perfil
      (sección «Informes de errores»); no se usan las notificaciones de la campana.
    Cualquier elemento con [data-problem-report-open] abre el formulario y [data-problem-report-mine] la lista.
--}}
@auth('web')
@php
    $aprUser = Auth::user();
    $aprIsSupport = (bool) ($aprUser->is_support ?? false);
    $aprTexts = [
        'page' => __('Page'),
        'workspace' => __('Workspace'),
        'screen' => __('Screen'),
        'sending' => __('Sending...'),
        'send' => __('Send'),
        'cancel' => __('Cancel'),
        'close' => __('Close'),
        'error' => __('It could not be sent. Try again in a moment.'),
        'tooShort' => __('Write a little more so we can understand it.'),
        'tooBig' => __('The image is too big (maximum 5 MB).'),
        'notImage' => __('Only images can be attached (PNG, JPG or WEBP).'),
        'none' => __('You have not sent any report yet.'),
        'loadError' => __('Your reports could not be loaded.'),
        'answer' => __('Answer'),
    ];
    $aprUrls = ['store' => route('problem_reports.store'), 'mine' => route('problem_reports.mine')];
@endphp

{{-- data-rail-title: el tooltip solo sale con el menú colapsado (acelerRailTitles en sidebar.blade.php) --}}
<a href="#" class="sb-report" data-problem-report-open data-rail-title="{{ __('Report a problem') }}" aria-label="{{ __('Report a problem') }}">
    <i class="fa-regular fa-flag" aria-hidden="true"></i><span>{{ __('Report a problem') }}</span>
</a>

<div class="apr" id="aprDialog" role="dialog" aria-modal="true" aria-labelledby="aprTitle" hidden>
    <div class="apr-backdrop" data-apr-close></div>
    <div class="apr-panel">
        <header class="apr-head">
            <span class="apr-icon"><i class="fa-regular fa-flag" aria-hidden="true"></i></span>
            <div>
                <h3 id="aprTitle">{{ __('Report a problem') }}</h3>
                <p>{{ __('Tell us what went wrong. The aCeler team reviews it.') }}</p>
            </div>
            <button type="button" class="apr-x" data-apr-close aria-label="{{ __('Close') }}">&times;</button>
        </header>

        {{-- Formulario --}}
        <form class="apr-body" id="aprForm" data-apr-view="form" novalidate>
            {{-- Por ahora solo fallos (la tabla admite más tipos para más adelante) --}}
            <label class="apr-lbl" for="aprDescription">{{ __('What happened?') }}</label>
            <textarea id="aprDescription" name="description" rows="4" maxlength="2000" required
                placeholder="{{ __('E.g.: when I save the hours of Monday, the page stays loading.') }}"></textarea>

            <label class="apr-lbl" for="aprExpected">{{ __('What did you expect to happen?') }} <small>{{ __('(optional)') }}</small></label>
            <textarea id="aprExpected" name="expected" rows="2" maxlength="2000"></textarea>

            <div class="apr-lbl">{{ __('Screenshot') }} <small>{{ __('(optional)') }}</small></div>
            <div class="apr-shot" id="aprShot" tabindex="0">
                <div class="apr-shot-empty">
                    <i class="fa-regular fa-image" aria-hidden="true"></i>
                    <span>{!! __('Paste a screenshot with <b>Ctrl+V</b> or') !!}
                        <button type="button" class="apr-link" id="aprPick">{{ __('choose an image') }}</button></span>
                    <small>{{ __('Tip: Windows + Shift + S takes a screenshot of part of the screen.') }}</small>
                </div>
                <div class="apr-shot-full" hidden>
                    <img id="aprShotImg" alt="">
                    <button type="button" class="apr-link" id="aprShotRemove">{{ __('Remove') }}</button>
                </div>
                <input type="file" id="aprFile" accept="image/png,image/jpeg,image/webp" hidden>
            </div>

            <div class="apr-ctx" id="aprCtx" aria-label="{{ __('Added automatically') }}"></div>
            <p class="apr-error" id="aprError" role="alert" hidden></p>
        </form>

        {{-- Enviado --}}
        <div class="apr-body apr-done" data-apr-view="done" hidden>
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            <p id="aprDoneText"></p>
        </div>

        {{-- Mis reportes --}}
        <div class="apr-body" data-apr-view="mine" hidden>
            <ul class="apr-list" id="aprList"><li class="apr-muted">{{ __('Loading...') }}</li></ul>
        </div>

        <footer class="apr-foot">
            <span class="apr-foot-links">
                <button type="button" class="apr-link" data-apr-show="mine">{{ __('My reports') }}</button>
                <button type="button" class="apr-link" data-apr-show="form" hidden>&larr; {{ __('New report') }}</button>
                @if ($aprIsSupport)
                    <a class="apr-link" href="{{ route('users.my.account') }}#reports">{{ __('Error reports') }}</a>
                @endif
            </span>
            <span class="apr-actions">
                <button type="button" class="apr-btn" data-apr-close>{{ __('Cancel') }}</button>
                <button type="submit" form="aprForm" class="apr-btn apr-btn-primary" id="aprSend">{{ __('Send') }}</button>
            </span>
        </footer>
    </div>
</div>

<style>
    /* Enlace sobre la línea del pie del menú: texto claro, sin caja */
    .sb-report { display: none; }
    @media (min-width: 1025px) {
        .dash-sidebar .sb-report {
            display: flex; align-items: center; gap: 10px; flex-shrink: 0;
            margin: 0 8px 8px; padding: 8px 12px; border-radius: 9px;
            color: rgba(255, 255, 255, .78) !important; font-size: 13px; text-decoration: none; white-space: nowrap;
            transition: background-color .15s, color .15s;
        }
        .dash-sidebar .sb-report i { width: 18px; font-size: 14px; text-align: center; }
        .dash-sidebar .sb-report:hover, .dash-sidebar .sb-report:focus-visible { background: rgba(255, 255, 255, .1); color: #fff !important; }
        body.aCeler-menu-icons .dash-sidebar .sb-report { width: 44px; justify-content: center; padding: 8px 0; }
        body.aCeler-menu-icons .dash-sidebar .sb-report span { display: none; }
        /* En escritorio ya está el enlace del menú: la opción del menú de usuario es para móvil */
        .apr-mobile-item { display: none !important; }
    }

    /* Soporte: banderita sobre la foto de perfil cuando hay informes de errores nuevos */
    .drp-company > .dash-head-link { position: relative; }
    .apr-avatar-flag {
        position: absolute; top: -2px; left: 26px; z-index: 2;
        display: inline-flex; align-items: center; justify-content: center;
        width: 18px; height: 18px; border-radius: 50%;
        background: #fff; color: #AA182C; font-size: 9.5px;
        box-shadow: 0 0 0 2px #AA182C, 0 1px 3px rgba(0, 0, 0, .25);
        pointer-events: none;
    }
    .apr-avatar-flag[hidden] { display: none; }
    .sb-slot-user .apr-avatar-flag { top: 0; left: 28px; }
    .apr-menu-count { margin-left: auto; padding: 0 7px; border-radius: 999px; background: #AA182C; color: #fff; font-size: 11px; line-height: 18px; }
    .apr-menu-count[hidden] { display: none; }

    .apr { position: fixed; inset: 0; z-index: 1085; display: flex; align-items: flex-start; justify-content: center; padding: 8vh 16px 16px; font-size: 14px; color: #1f2937; }
    .apr[hidden] { display: none; }
    /* Con el formulario abierto, la burbuja del chatbot no tapa los botones */
    body.apr-open #chatbase-bubble-button,
    body.apr-open #chatbase-bubble-window { display: none !important; }
    .apr-backdrop { position: absolute; inset: 0; background: rgba(17, 24, 39, .35); }
    .apr-panel { position: relative; width: 600px; max-width: 100%; max-height: 84vh; display: flex; flex-direction: column; background: #fff; border-radius: 16px; box-shadow: 0 24px 60px rgba(0, 0, 0, .25); overflow: hidden; }
    .apr-head { display: flex; align-items: flex-start; gap: 12px; padding: 20px 22px 10px; }
    .apr-head h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1f2937; }
    .apr-head p { margin: 3px 0 0; font-size: 13px; color: #6b7280; }
    .apr-icon { flex: 0 0 38px; height: 38px; border-radius: 10px; background: #fbe9ec; color: #AA182C; display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .apr-x { margin-left: auto; border: 0; background: none; color: #9ca3af; font-size: 24px; line-height: 1; cursor: pointer; }
    .apr-x:hover { color: #374151; }
    .apr-body { padding: 4px 22px 16px; overflow-y: auto; }
    .apr-lbl { display: block; margin: 14px 0 6px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; }
    .apr-lbl small { text-transform: none; letter-spacing: 0; font-weight: 400; color: #9ca3af; }
    .apr textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; font: inherit; font-size: 14px; color: #1f2937; resize: vertical; outline: 0; }
    .apr textarea:focus { border-color: #AA182C; box-shadow: 0 0 0 3px rgba(170, 24, 44, .1); }
    .apr-shot { border: 1px dashed #d1d5db; border-radius: 10px; padding: 12px; outline: 0; }
    .apr-shot:focus, .apr-shot.is-over { border-color: #AA182C; background: #fdf7f8; }
    .apr-shot-empty { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; color: #4b5563; font-size: 13px; }
    .apr-shot-empty i { font-size: 18px; color: #9ca3af; }
    .apr-shot-empty small { flex-basis: 100%; color: #9ca3af; font-size: 12px; padding-left: 26px; }
    .apr-shot-full { display: flex; align-items: flex-end; gap: 12px; }
    .apr-shot-full[hidden] { display: none; }
    .apr-shot-full img { max-width: 260px; max-height: 140px; border-radius: 6px; border: 1px solid #e5e7eb; object-fit: contain; }
    .apr-ctx { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
    .apr-ctx span { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; background: #f3f4f6; color: #4b5563; font-size: 12px; }
    .apr-ctx i { color: #9ca3af; }
    .apr-error { margin: 12px 0 0; padding: 8px 12px; border-radius: 8px; background: #fef2f2; color: #b91c1c; font-size: 13px; }
    .apr-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 22px; background: #fafafb; border-top: 1px solid #f0f1f4; }
    .apr-foot-links { display: flex; gap: 14px; flex-wrap: wrap; }
    .apr-actions { display: flex; gap: 8px; }
    .apr-link { padding: 0; border: 0; background: none; color: #AA182C; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; }
    .apr-link:hover { text-decoration: underline; color: #AA182C; }
    .apr-link[hidden] { display: none; }
    .apr-btn { padding: 8px 16px; border: 1px solid #e5e7eb; border-radius: 9px; background: #fff; color: #4b5563; font-size: 14px; font-weight: 600; cursor: pointer; }
    .apr-btn:hover { background: #f9fafb; }
    .apr-btn-primary { border-color: #AA182C; background: #AA182C; color: #fff; }
    .apr-btn-primary:hover { background: #8f1425; }
    .apr-btn[disabled] { opacity: .6; cursor: default; }
    .apr-btn[hidden] { display: none; }
    .apr-done { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 26px 22px 30px; text-align: center; }
    .apr-done i { font-size: 34px; color: #22c55e; }
    .apr-done p { margin: 0; font-size: 15px; color: #374151; }
    .apr-list { list-style: none; margin: 4px 0 0; padding: 0; }
    .apr-list li { padding: 10px 0; border-bottom: 1px solid #f4f5f7; }
    .apr-list li:last-child { border-bottom: 0; }
    .apr-li-top { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #9ca3af; }
    .apr-li-top b { font-weight: 600; color: #6b7280; }
    .apr-li-desc { margin-top: 3px; font-size: 13.5px; color: #1f2937; }
    .apr-li-note { margin-top: 6px; padding: 6px 10px; border-left: 3px solid #AA182C; background: #fdf7f8; font-size: 13px; color: #4b5563; }
    .apr-muted { color: #9ca3af; font-size: 13px; }
    .apr-st { display: inline-flex; align-items: center; gap: 5px; margin-left: auto; color: #374151; font-weight: 600; }
    .apr-st i { width: 8px; height: 8px; border-radius: 50%; background: #9ca3af; }
    .apr-st--received i { background: #60a5fa; }
    .apr-st--in_review i { background: #fbbf24; }
    .apr-st--done i { background: #22c55e; }
    .apr-st--discarded i { background: #9ca3af; }
    @media (max-width: 560px) {
        .apr { padding: 0; align-items: flex-end; }
        .apr-panel { max-height: 92vh; border-radius: 16px 16px 0 0; }
    }
</style>

<script>
    (function () {
        // Errores de la página (para depurar el problema): se guardan los últimos 10
        var errors = window.__acelerPageErrors = window.__acelerPageErrors || [];
        var pushError = function (text) {
            errors.push(new Date().toTimeString().slice(0, 8) + ' ' + String(text).slice(0, 300));
            if (errors.length > 10) errors.shift();
        };
        window.addEventListener('error', function (e) {
            pushError((e.message || 'Error') + (e.filename ? ' @ ' + e.filename.split('/').pop() + ':' + e.lineno : ''));
        });
        window.addEventListener('unhandledrejection', function (e) {
            pushError('Promise: ' + (e.reason && e.reason.message ? e.reason.message : e.reason));
        });

        if (window.__aprInit) return;
        window.__aprInit = true;

        var T = @json($aprTexts);
        var URLS = @json($aprUrls);

        var dialog, form, shotFile = null, lastFocus = null;
        var $ = function (s, root) { return (root || dialog).querySelector(s); };
        var $$ = function (s, root) { return Array.prototype.slice.call((root || dialog).querySelectorAll(s)); };
        var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };

        function init() {
            dialog = document.getElementById('aprDialog');
            if (!dialog) return false;
            // Fuera del sidebar (que es fijo y recorta), directamente en el body
            if (dialog.parentNode !== document.body) document.body.appendChild(dialog);
            form = document.getElementById('aprForm');

            form.addEventListener('submit', submit);
            $$('[data-apr-close]').forEach(function (b) { b.addEventListener('click', close); });
            $$('[data-apr-show]').forEach(function (b) { b.addEventListener('click', function () { show(b.dataset.aprShow); }); });
            dialog.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

            // Captura: pegar, arrastrar o elegir
            var shot = $('#aprShot'), file = $('#aprFile');
            $('#aprPick').addEventListener('click', function () { file.click(); });
            file.addEventListener('change', function () { if (file.files[0]) setShot(file.files[0]); file.value = ''; });
            $('#aprShotRemove').addEventListener('click', function () { setShot(null); });
            dialog.addEventListener('paste', function (e) {
                var items = (e.clipboardData || {}).items || [];
                for (var i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image/') === 0) { setShot(items[i].getAsFile()); e.preventDefault(); return; }
                }
            });
            shot.addEventListener('dragover', function (e) { e.preventDefault(); shot.classList.add('is-over'); });
            shot.addEventListener('dragleave', function () { shot.classList.remove('is-over'); });
            shot.addEventListener('drop', function (e) {
                e.preventDefault(); shot.classList.remove('is-over');
                if (e.dataTransfer.files[0]) setShot(e.dataTransfer.files[0]);
            });
            return true;
        }

        function pageName() {
            var crumb = document.querySelector('.page-header-title h4, .page-header-title h5, .page-header h4');
            var name = crumb ? crumb.textContent.trim() : '';
            return name || document.title.replace(/^.*~\s*/, '').trim() || location.pathname;
        }

        function context() {
            var ws = document.getElementById('workspaceName');
            return {
                page: pageName(),
                workspace: ws ? ws.textContent.trim() : '',
                viewport: window.innerWidth + ' × ' + window.innerHeight,
            };
        }

        function open(view) {
            if (!dialog && !init()) return;
            lastFocus = document.activeElement;
            var c = context();
            $('#aprCtx').innerHTML =
                '<span><i class="fa-solid fa-location-dot"></i>' + esc(T.page) + ': ' + esc(c.page) + '</span>' +
                (c.workspace ? '<span><i class="fa-solid fa-layer-group"></i>' + esc(c.workspace) + '</span>' : '') +
                '<span><i class="fa-solid fa-display"></i>' + esc(c.viewport) + '</span>';
            dialog.hidden = false;
            document.documentElement.style.overflow = 'hidden';
            document.body.classList.add('apr-open');
            show(view || 'form');
            if ((view || 'form') === 'form') setTimeout(function () { $('#aprDescription').focus(); }, 30);
        }

        function close() {
            if (!dialog || dialog.hidden) return;
            dialog.hidden = true;
            document.documentElement.style.overflow = '';
            document.body.classList.remove('apr-open');
            if (lastFocus && lastFocus.focus) lastFocus.focus();
        }

        function show(view) {
            $$('[data-apr-view]').forEach(function (v) { v.hidden = v.dataset.aprView !== view; });
            $('[data-apr-show="mine"]').hidden = view === 'mine';
            $('[data-apr-show="form"]').hidden = view === 'form';
            $('#aprSend').hidden = view !== 'form';
            $('.apr-actions [data-apr-close]').textContent = view === 'form' ? T.cancel : T.close;
            if (view === 'mine') loadMine();
        }


        function setShot(f) {
            var err = $('#aprError');
            if (f && !/^image\/(png|jpe?g|webp)$/.test(f.type)) { err.textContent = T.notImage; err.hidden = false; return; }
            if (f && f.size > 5 * 1024 * 1024) { err.textContent = T.tooBig; err.hidden = false; return; }
            err.hidden = true;
            shotFile = f;
            $('.apr-shot-empty').hidden = !!f;
            $('.apr-shot-full').hidden = !f;
            var img = $('#aprShotImg');
            if (img.src) URL.revokeObjectURL(img.src);
            img.src = f ? URL.createObjectURL(f) : '';
        }

        function submit(e) {
            e.preventDefault();
            var err = $('#aprError'), btn = $('#aprSend');
            var desc = $('#aprDescription').value.trim();
            if (desc.length < 5) { err.textContent = T.tooShort; err.hidden = false; $('#aprDescription').focus(); return; }
            err.hidden = true;

            var c = context();
            var fd = new FormData();
            fd.append('type', 'problem');
            fd.append('description', desc);
            fd.append('expected', $('#aprExpected').value.trim());
            fd.append('page_url', location.href.slice(0, 500));
            fd.append('page_title', c.page.slice(0, 255));
            fd.append('viewport', c.viewport);
            if (errors.length) fd.append('js_errors', errors.join('\n').slice(0, 4000));
            if (shotFile) fd.append('screenshot', shotFile, shotFile.name || 'captura.png');

            btn.disabled = true; btn.textContent = T.sending;
            fetch(URLS.store, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '', 'Accept': 'application/json' },
                body: fd,
                credentials: 'same-origin',
            }).then(function (r) {
                return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, j: j }; });
            }).then(function (res) {
                if (!res.ok || !res.j.success) {
                    var first = res.j.errors ? Object.values(res.j.errors)[0] : null;
                    throw new Error((first && first[0]) || res.j.message || T.error);
                }
                form.reset(); setShot(null);
                $('#aprDoneText').textContent = res.j.message;
                show('done');
            }).catch(function (ex) {
                err.textContent = ex && ex.message ? ex.message : T.error; err.hidden = false;
            }).then(function () { btn.disabled = false; btn.textContent = T.send; });
        }

        function loadMine() {
            var list = $('#aprList');
            fetch(URLS.mine, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                .then(function (j) {
                    var rows = j.reports || [];
                    list.innerHTML = rows.length ? rows.map(function (r) {
                        return '<li><div class="apr-li-top"><span>' + esc(r.date) + '</span>' +
                            '<span class="apr-st apr-st--' + esc(r.status) + '"><i></i>' + esc(r.status_label) + '</span></div>' +
                            '<div class="apr-li-desc">' + esc(r.description) + '</div>' +
                            (r.admin_note ? '<div class="apr-li-note"><b>' + esc(T.answer) + ':</b> ' + esc(r.admin_note) + '</div>' : '') + '</li>';
                    }).join('') : '<li class="apr-muted">' + esc(T.none) + '</li>';
                })
                .catch(function () { list.innerHTML = '<li class="apr-muted">' + esc(T.loadError) + '</li>'; });
        }

        document.addEventListener('click', function (e) {
            var opener = e.target.closest && e.target.closest('[data-problem-report-open], [data-problem-report-mine]');
            if (!opener) return;
            e.preventDefault();
            open(opener.hasAttribute('data-problem-report-mine') ? 'mine' : 'form');
        });
        window.acelerReportProblem = open;
    })();
</script>
@endauth
