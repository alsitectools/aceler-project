{{--
    Ayuda de «Mi resumen» (projects/my_summary): botón ❓ en la miga de pan, informativo y visita guiada.
    Las secciones se recargan por AJAX al filtrar: los pasos se buscan al empezar el recorrido.
    Motor: custom/js/aceler-tour.js · estilos: custom/css/aceler-help.css (reutiliza .ua-help del perfil).
--}}
<link rel="stylesheet" href="{{ asset('assets/custom/css/aceler-help.css') }}?v={{ filemtime(public_path('assets/custom/css/aceler-help.css')) }}">
<span class="board-legend-wrap ua-help">
    <button type="button" class="board-legend-toggle" id="boardLegendToggle" aria-expanded="false"
        aria-controls="boardLegend" title="{{ __('Help') }}">
        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        <span class="board-legend-dot" hidden></span>
    </button>

    <div class="board-legend" id="boardLegend" role="dialog" aria-label="{{ __('Help') }}" hidden>
        <div class="board-legend-head">
            <span class="board-legend-title">{{ __('How your summary works') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-solid fa-star"></i>{{ __('Score and hours') }}</h6>
                <p class="board-legend-sub">{{ __('Of the period chosen in the hours section') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Score') }}</b>{{ __('points of the tasks you logged hours on') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Imputed hours') }}</b>{{ __('everything you logged, and in how many projects') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-folder-open"></i>{{ __('My assigned milestones') }}</h6>
                <p class="board-legend-sub">{{ __('Filtered by expected delivery') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('To do') }}</b>{{ __('order forms assigned to you') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('In progress') }}</b>{{ __('also the ones with your tasks, listed inside') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#b91c1c"></span>{{ __('Delivery overdue') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#b7791f"></span>{{ __('Delivery within 7 days') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#15803d"></span>{{ __('Delivery later on') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-regular fa-clock"></i>{{ __('Imputed Hours By Project') }}</h6>
                <p class="board-legend-sub">{{ __('Two views of the same data') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Card view') }}</b>{{ __('one card per project') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Graph view') }}</b>{{ __('bars from most to fewest hours, with the % of the period') }}</div>
                <p class="board-legend-hint">{{ __('Click a card or a bar to open the project.') }}</p>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('The funnel next to each title hides or shows its filters.') }}</span>
        </div>
    </div>
</span>

@php
    $msTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New summary'),
        'toastText' => __('We will show you what you can see here in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Summary tour'),
        'introText' => __('Your work at a glance: your score, the hours you logged and the order forms you have pending. You can leave at any time with Esc.'),
        'scoreTitle' => __('Score'),
        'scoreText' => __('Points of the tasks you logged hours on, in the period chosen in «Imputed Hours By Project». The label says which period it is.'),
        'hoursTitle' => __('Imputed hours'),
        'hoursText' => __('All the hours you logged in that same period and in how many projects.'),
        'milestonesTitle' => __('My assigned milestones'),
        'milestonesText' => __('The order forms you have to work on. The number on the right is how many match the filters.'),
        'statusTitle' => __('To do or in progress'),
        'statusText' => __('«To do»: order forms assigned to you. «In progress»: also the ones where you have tasks, which are listed inside each card.'),
        'dueTitle' => __('Expected delivery'),
        'dueText' => __('Show only the order forms to deliver today, this week, this month, in any range or at any time.'),
        'cardTitle' => __('Milestone'),
        'cardText' => __('Who created it, when it must be delivered (red: overdue; amber: within 7 days) and your tasks. Click it to open its board.'),
        'emptyTitle' => __('Nothing to show'),
        'emptyText' => __('No order forms match these filters. Try «Anytime» or the other status.'),
        'funnelTitle' => __('Hide filters'),
        'funnelText' => __('The funnel folds or unfolds the filters of the section.'),
        'viewTitle' => __('Cards or chart'),
        'viewText' => __('The same hours per project, as cards or as bars ordered from most to fewest.'),
        'periodTitle' => __('Period'),
        'periodText' => __('Last week, month, quarter, year or a custom range. It also changes the score and hours at the top.'),
        'projectTitle' => __('Project'),
        'projectText' => __('Its hours in the period and, in the chart, its % of the total. Click it to open the project.'),
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
        if (!window.AcelerTour || window.__mySummaryTourInit) return;
        window.__mySummaryTourInit = true;
        const T = @json($msTourTexts);
        const $q = (s) => document.querySelector(s);
        const firstVisible = window.AcelerTour.firstVisible;

        // Abre los filtros de una sección si estaban plegados (para poder enseñarlos)
        function openFilters(toggleId, wrapperId) {
            const wrapper = document.getElementById(wrapperId);
            const button = document.getElementById(toggleId);
            if (wrapper && button && !wrapper.classList.contains('is-open')) button.click();
        }

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));

            const kpis = document.querySelectorAll('#mySummaryScore .my-summary-kpi');
            add(kpis[0], T.scoreTitle, T.scoreText);
            add(kpis[1], T.hoursTitle, T.hoursText);

            // Encargos asignados
            add($q('#mySummaryDay .my-day-header'), T.milestonesTitle, T.milestonesText);
            add($q('#mySummaryDay .my-summary-view-switch'), T.statusTitle, T.statusText);
            const dueButtons = document.querySelectorAll('#mySummaryDay .my-day-range-button');
            if (dueButtons.length) add(dueButtons[0], T.dueTitle, T.dueText, { spanTo: dueButtons[dueButtons.length - 1] });
            const card = firstVisible('#mySummaryDay .my-day-card');
            if (card) add(card, T.cardTitle, T.cardText, { position: 'right' });
            else add($q('#mySummaryDay .my-day-empty-card'), T.emptyTitle, T.emptyText);
            add($q('#myDayFilterToggle'), T.funnelTitle, T.funnelText);

            // Horas por proyecto
            add($q('#mySummaryViewSwitch'), T.viewTitle, T.viewText);
            const rangeButtons = document.querySelectorAll('#mySummarySection .my-summary-range-button');
            if (rangeButtons.length) add(rangeButtons[0], T.periodTitle, T.periodText, { spanTo: rangeButtons[rangeButtons.length - 1] });
            const project = firstVisible('#mySummaryChart .my-summary-hbar') || firstVisible('#mySummaryContent .my-summary-project-card');
            if (project) add(project, T.projectTitle, T.projectText, { position: 'top' });
            return steps;
        }

        window.acelerMySummaryTour = window.AcelerTour.setup({
            seenKey: 'aceler.mySummaryTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!document.getElementById('mySummarySection'),
            beforeStart: () => {
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
                openFilters('myDayFilterToggle', 'myDayFilterWrapper');
                openFilters('mySummaryFilterToggle', 'mySummaryFilterWrapper');
                window.scrollTo(0, 0);
            },
            buildSteps,
        }).start;
    })();
</script>
