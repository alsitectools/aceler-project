{{--
    Ayuda del resumen del espacio de trabajo (home): botón ❓ junto al título, informativo y visita guiada.
    El recorrido enseña las dos vistas («Resumen global» y «Mi actividad»): cambia de vista sin animación
    y guarda la elección igual que el selector, para que este siga funcionando después.
    Motor: custom/js/aceler-tour.js · estilos: custom/css/aceler-help.css (reutiliza .ua-help del perfil).
--}}
<link rel="stylesheet" href="{{ asset('assets/custom/css/aceler-help.css') }}?v={{ filemtime(public_path('assets/custom/css/aceler-help.css')) }}">
<span class="board-legend-wrap ua-help hs-help">
    <button type="button" class="board-legend-toggle" id="boardLegendToggle" aria-expanded="false"
        aria-controls="boardLegend" title="{{ __('Help') }}">
        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        <span class="board-legend-dot" hidden></span>
    </button>

    <div class="board-legend" id="boardLegend" role="dialog" aria-label="{{ __('Help') }}" hidden>
        <div class="board-legend-head">
            <span class="board-legend-title">{{ __('How the summary works') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-solid fa-chart-column"></i>{{ __('Resumen global') }}</h6>
                <p class="board-legend-sub">{{ __('The whole workspace') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Projects') }}</b>{{ __('in progress, on hold and finished') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Milestones') }}</b>{{ __('active, under review, finished, unassigned and paused') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Global tasks') }}</b>{{ __('how many of each type') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-user"></i>{{ __('My activity') }}</h6>
                <p class="board-legend-sub">{{ __('Only what is yours') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Mis encargos asignados') }}</b>{{ __('on time, overdue, in review, finished, paused') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Prioridad de mis encargos') }}</b>{{ __('high, medium and low') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Mis tareas') }}</b>{{ __('and the hours you logged this month (click to see them)') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-regular fa-chart-bar"></i>{{ __('Statistics') }}</h6>
                <p class="board-legend-sub">{{ __('Average time of finished order forms') }}</p>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#cbd5e1"></span>{{ __('Starting time') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#5cc45a"></span>{{ __('On time') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#e06c71"></span>{{ __('Delay') }}</div>
                <p class="board-legend-hint">{{ __('By months, quarters or year, of the whole workspace.') }}</p>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('The bar on each card shows how its total is split between the states below.') }}</span>
        </div>
    </div>
</span>

@php
    $hsTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New summary'),
        'toastText' => __('We will show you how to read your workspace in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Summary tour'),
        'introText' => __('The state of the workspace and of your work, at a glance. You can leave at any time with Esc.'),
        'viewTitle' => __('Two views'),
        'viewText' => __('«Resumen global»: the whole workspace. «My activity»: only your order forms and tasks. Your choice is remembered.'),
        'projectsTitle' => __('Projects'),
        'projectsText' => __('All the projects of the workspace. The bar shows how they are split between in progress, on hold and finished.'),
        'milestonesTitle' => __('Milestones'),
        'milestonesText' => __('The order forms of the workspace by state: active, under review, finished, unassigned and paused.'),
        'tasksTitle' => __('Global tasks'),
        'tasksText' => __('How many tasks of each type there are, from most to fewest. «+N more types» shows the rest.'),
        'myTitle' => __('Mis encargos asignados'),
        'myText' => __('Your order forms: on time, overdue, in review, finished and paused. Only the states with order forms are shown.'),
        'prioTitle' => __('Prioridad de mis encargos'),
        'prioText' => __('Your order forms by priority: high, medium and low.'),
        'myTasksTitle' => __('Mis tareas'),
        'myTasksText' => __('Your tasks in progress or in review, the reviewed ones and the ones with a change requested.'),
        'hoursTitle' => __('Hours imputed this month'),
        'hoursText' => __('Click to see every hour you logged this month in this workspace.'),
        'statsTitle' => __('Statistics'),
        'statsText' => __('Average time of the finished order forms of the workspace: starting time, on time and delay, in days.'),
        'periodTitle' => __('Year and period'),
        'periodText' => __('Choose the year and whether to see it by months, quarters or the whole year. «Show values» writes the days on each bar.'),
        'teamTitle' => __('Team'),
        'teamText' => __('Sales managers and technicians of the workspace. Search by name; the envelope shows the email, and clicking it copies it.'),
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
        if (!window.AcelerTour || window.__homeTourInit) return;
        window.__homeTourInit = true;
        const T = @json($hsTourTexts);
        const workspaceKey = 'summaryView_' + @json((string) (optional($currentWorkspace ?? null)->id ?? 'default'));
        const $q = (s) => document.querySelector(s);
        const $all = (s) => Array.from(document.querySelectorAll(s));

        // Cambia de vista al momento (sin la animación del selector) y lo guarda como él
        function setView(view) {
            const wrapper = $q('#summaryWrapper');
            if (!wrapper) return;
            wrapper.classList.toggle('expanded', view === 'activity');
            $all('#viewSelector .view-segment').forEach((b) => b.classList.toggle('active', b.dataset.view === view));
            $all('#summaryTrack .summary-page').forEach((p) => p.classList.remove('page-out', 'page-in-start'));
            try { localStorage.setItem(workspaceKey, view); } catch (e) {}
        }
        const showView = (view) => () => setView(view);

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));
            const globalCards = $all('#summaryTrack .summary-page:first-child .tabs.ctr');
            const myCards = $all('#summaryTrack .summary-page:last-child .tabs.ctr');
            const g = { show: showView('global') };
            const a = { show: showView('activity') };

            add($q('#viewSelector'), T.viewTitle, T.viewText, Object.assign({ position: 'left' }, g));
            add(globalCards[0], T.projectsTitle, T.projectsText, g);
            add(globalCards[1], T.milestonesTitle, T.milestonesText, g);
            add(globalCards[2], T.tasksTitle, T.tasksText, Object.assign({ position: 'left' }, g));
            add(myCards[0], T.myTitle, T.myText, a);
            add(myCards[1], T.prioTitle, T.prioText, a);
            add(myCards[2], T.myTasksTitle, T.myTasksText, Object.assign({ position: 'left' }, a));
            add($q('#monthHoursRow'), T.hoursTitle, T.hoursText, Object.assign({ position: 'left' }, a));
            add($q('.hs .hs-stats .card-header'), T.statsTitle, T.statsText, g);
            add($q('.hs .divStatisticsButtons'), T.periodTitle, T.periodText, g);
            const team = $all('.hs .hs-team');
            if (team.length) add(team[0], T.teamTitle, T.teamText, Object.assign({ position: 'top', spanTo: team[team.length - 1] }, g));
            return steps;
        }

        window.acelerHomeTour = window.AcelerTour.setup({
            seenKey: 'aceler.homeTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!document.getElementById('summaryWrapper'),
            beforeStart: () => {
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
                window.scrollTo(0, 0);
            },
            buildSteps,
        }).start;
    })();
</script>
