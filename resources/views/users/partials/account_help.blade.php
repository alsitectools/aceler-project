{{--
    Ayuda del perfil de usuario (users/account): botón ❓ en la miga de pan, informativo y visita guiada.
    Motor del recorrido: custom/js/aceler-tour.js · estilos comunes: custom/css/aceler-help.css.
    El perfil enseña una sección a la vez: cada paso abre su sección (window.uaShowSection) antes de mostrarse.
--}}
@php $uaHelpExporter = (bool) optional(Auth::user())->is_exporter; @endphp
<link rel="stylesheet" href="{{ asset('assets/custom/css/aceler-help.css') }}?v={{ filemtime(public_path('assets/custom/css/aceler-help.css')) }}">
<span class="board-legend-wrap ua-help">
    <button type="button" class="board-legend-toggle" id="boardLegendToggle" aria-expanded="false"
        aria-controls="boardLegend" title="{{ __('Help') }}">
        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        <span class="board-legend-dot" hidden></span>
    </button>

    <div class="board-legend" id="boardLegend" role="dialog" aria-label="{{ __('Help') }}" hidden>
        <div class="board-legend-head">
            <span class="board-legend-title">{{ __('How your profile works') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body" @unless ($uaHelpExporter) style="grid-template-columns: 1fr 1.2fr;" @endunless>
            <div class="board-legend-block">
                <h6><i class="fa-solid fa-layer-group"></i>{{ __('Workspaces') }}</h6>
                <p class="board-legend-sub">{{ __('The ones in the side menu selector') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Current') }}</b>{{ __('the workspace you are working in now') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('New') }}</b>{{ __('added but not saved yet') }}</div>
                <p class="board-legend-hint">{{ __('Changes are only applied when you click «Save changes».') }}</p>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-regular fa-clock"></i>{{ __('Timetable') }}</h6>
                <p class="board-legend-sub">{{ __('Colours of your days in the timesheet') }}</p>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#e06c71"></span>{{ __('No hours') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#fcf75e"></span>{{ __('Fewer hours than your timetable') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#89e186"></span>{{ __('Exactly your timetable') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#b2e2f2"></span>{{ __('Overtime') }}</div>
                <p class="board-legend-hint">{{ __('Holidays and intensive workdays are managed in My calendar.') }}</p>
            </div>

            @if ($uaHelpExporter)
                <div class="board-legend-block">
                    <h6><i class="fa-solid fa-file-export"></i>{{ __('Axapta') }}</h6>
                    <p class="board-legend-sub">{{ __('What is exported') }}</p>
                    <div class="board-legend-item ua-help-def"><b>{{ __('Which hours') }}</b>{{ __('those of finished order forms of Jobsite projects') }}</div>
                    <div class="board-legend-item ua-help-def"><b>{{ __('Only changes') }}</b>{{ __('what was already sent is not sent again; corrections go as a difference') }}</div>
                    <div class="board-legend-item ua-help-def"><b>{{ __('Cannot be exported') }}</b>{{ __('order forms with missing data; the list says why') }}</div>
                </div>
            @endif
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('Use the side menu of your profile to switch between sections.') }}</span>
        </div>
    </div>
</span>

@php
    $uaTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New to your profile?'),
        'toastText' => __('We will show you what you can set up here in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Profile tour'),
        'introText' => __('Let us see what you can set up in your profile. You can leave at any time with Esc.'),
        'navTitle' => __('Sections'),
        'navText' => __('Your profile is split into sections. The number next to each one is a quick summary.'),
        'mineTitle' => __('My workspaces'),
        'mineText' => __('The workspaces you can switch to from the side menu. The × removes one; «Current» is the one you are in.'),
        'searchTitle' => __('Search'),
        'searchText' => __('Find a workspace by its name or its country.'),
        'groupsTitle' => __('Add workspaces'),
        'groupsText' => __('Workspaces are grouped by country. Click one to add it; it will appear as «New».'),
        'wsSaveTitle' => __('Save or discard'),
        'wsSaveText' => __('Changes are not applied until you save. «Discard» undoes what you have not saved.'),
        'dayTitle' => __('Your working days'),
        'dayText' => __('Turn each day on or off and choose its hours. The bar shows the hours of the day.'),
        'copyTitle' => __('Copy Monday'),
        'copyText' => __('Copies Monday hours to the other active days, so you do not have to set them one by one.'),
        'totalTitle' => __('Weekly total'),
        'totalText' => __('The sum of the hours of your active days.'),
        'exceptionsTitle' => __('Exceptions'),
        'exceptionsText' => __('Intensive workday and holidays. They are managed in My calendar.'),
        'ttSaveTitle' => __('Save the timetable'),
        'ttSaveText' => __('Your days in the timesheet are coloured according to the saved timetable.'),
        'kpisTitle' => __('Before exporting'),
        'kpisText' => __('How many lines and hours are pending, and how many order forms cannot be exported. «See list» explains why.'),
        'batchesTitle' => __('Latest batches'),
        'batchesText' => __('The last exports, with their lines, hours and status.'),
        'exportTitle' => __('Export'),
        'exportText' => __('Downloads the file for Axapta with what is pending. Afterwards the summary is updated.'),
    ];
@endphp
<script src="{{ asset('assets/custom/js/aceler-tour.js') }}?v={{ filemtime(public_path('assets/custom/js/aceler-tour.js')) }}"></script>
<script>
    (function () {
        // --- informativo ---
        const toggle = document.getElementById('boardLegendToggle');
        const panel = document.getElementById('boardLegend');
        if (toggle && panel && !toggle.dataset.bound) {
            toggle.dataset.bound = '1';
            let hideTimer = null;
            const setOpen = (open) => {
                clearTimeout(hideTimer);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    panel.hidden = false;
                    requestAnimationFrame(() => panel.classList.add('is-open'));
                } else {
                    panel.classList.remove('is-open');
                    hideTimer = setTimeout(() => { panel.hidden = true; }, 200);
                }
            };
            const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';
            toggle.addEventListener('click', (e) => { e.stopPropagation(); setOpen(!isOpen()); });
            panel.querySelector('.board-legend-close').addEventListener('click', () => setOpen(false));
            panel.addEventListener('click', (e) => e.stopPropagation());
            document.addEventListener('click', () => { if (isOpen()) setOpen(false); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen()) setOpen(false); });
        }

        // --- visita guiada ---
        if (!window.AcelerTour || window.__profileTourInit) return;
        window.__profileTourInit = true;
        const T = @json($uaTourTexts);
        const $q = (s) => document.querySelector(s);
        const showSection = (name) => () => { if (window.uaShowSection) window.uaShowSection(name); };

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));
            const ws = { show: showSection('workspaces') };
            const tt = { show: showSection('timetable') };
            const ax = { show: showSection('axapta') };

            add($q('.ua .ua-nav'), T.navTitle, T.navText, Object.assign({ position: 'right' }, ws));

            add($q('.ua #workspaceChips'), T.mineTitle, T.mineText, ws);
            add($q('.ua .ua-search'), T.searchTitle, T.searchText, ws);
            add($q('.ua .ua-group'), T.groupsTitle, T.groupsText, ws);
            add($q('[data-ua-section="workspaces"] .ua-card-foot'), T.wsSaveTitle, T.wsSaveText, Object.assign({ position: 'top' }, ws));

            add($q('.ua #timetable-content .dayToggle'), T.dayTitle, T.dayText, tt);
            add($q('.ua #uaCopyMonday'), T.copyTitle, T.copyText, Object.assign({ position: 'left' }, tt));
            add($q('.ua .ua-total'), T.totalTitle, T.totalText, Object.assign({ position: 'top' }, tt));
            add($q('.ua .ua-exceptions'), T.exceptionsTitle, T.exceptionsText, Object.assign({ position: 'top' }, tt));
            add($q('.ua #saveTimetable'), T.ttSaveTitle, T.ttSaveText, Object.assign({ position: 'left' }, tt));

            if ($q('.ua [data-ua-section="axapta"]')) {
                add($q('.ua .ua-kpis'), T.kpisTitle, T.kpisText, ax);
                add($q('.ua .ua-table'), T.batchesTitle, T.batchesText, Object.assign({ position: 'top' }, ax));
                add($q('.ua #exportAxapta'), T.exportTitle, T.exportText, Object.assign({ position: 'left' }, ax));
            }
            return steps;
        }

        window.acelerProfileTour = window.AcelerTour.setup({
            seenKey: 'aceler.profileTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!document.querySelector('.ua'),
            beforeStart: () => {
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
                if (window.uaShowSection) window.uaShowSection('workspaces');
            },
            buildSteps,
        }).start;
    })();
</script>
