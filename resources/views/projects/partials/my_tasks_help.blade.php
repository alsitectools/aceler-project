{{--
    Ayuda de «Mis tareas» (projects/my_tasks): botón ❓ en la miga de pan, informativo y visita guiada.
    El recorrido se hace sobre la vista en tabla (si estaba en diagrama, cambia a tabla) y termina enseñando el diagrama.
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
            <span class="board-legend-title">{{ __('How My tasks works') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-solid fa-computer-mouse"></i>{{ __('Each row') }}</h6>
                <p class="board-legend-sub">{{ __('One of your tasks') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Click') }}</b>{{ __('log or edit hours of the task') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Right click') }}</b>{{ __('mark it as Revisado or Solicitar cambio') }}</div>
                <div class="board-legend-item ua-help-def"><b>✓ / ⚠</b>{{ __('reviewed, or change requested: click the badge to read the comment') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Finalization date') }}</b>{{ __('green on time, red after the estimated date') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-filter"></i>{{ __('Find your tasks') }}</h6>
                <p class="board-legend-sub">{{ __('All of them can be combined') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Quick filters') }}</b>{{ __('not finished, overdue, change requested…') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Funnel') }}</b>{{ __('filter by the values of a column') }}</div>
                <div class="board-legend-item ua-help-def"><b><i class="fa-regular fa-eye"></i></b>{{ __('show or hide columns') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Dates') }}</b>{{ __('click the header to sort') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-chart-simple"></i>{{ __('Diagram view') }}</h6>
                <p class="board-legend-sub">{{ __('Your projects in the chosen period') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Tasks') }}</b>{{ __('created, split by order form status') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Hours') }}</b>{{ __('logged in each project') }}</div>
                <p class="board-legend-hint">{{ __('Click a project to see its tasks in the table.') }}</p>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('The quick filter you choose is remembered the next time you come back.') }}</span>
        </div>
    </div>
</span>

@php
    $mtTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New My tasks'),
        'toastText' => __('We will show you how to find and log your tasks in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('My tasks tour'),
        'introText' => __('All the tasks assigned to you, in a table you can search, filter and sort. You can leave at any time with Esc.'),
        'kpiTitle' => __('Summary'),
        'kpiText' => __('How many tasks you have assigned and how many you logged this week and in 30 days. Click those two to see the list.'),
        'viewTitle' => __('Table or diagram'),
        'viewText' => __('The table lists your tasks; the diagram shows your projects in a period. We will see it at the end.'),
        'searchTitle' => __('Search'),
        'searchText' => __('Type part of a project, order form, task or reference.'),
        'quickTitle' => __('Quick filters'),
        'quickText' => __('One click: not finished (the default), overdue, change requested, finished or all. The number is how many there are.'),
        'eyeTitle' => __('Columns'),
        'eyeText' => __('Show or hide columns of the table.'),
        'funnelTitle' => __('Filter a column'),
        'funnelText' => __('The funnel lets you choose which values of that column to see. Applied filters appear above the table and can be removed there.'),
        'sortTitle' => __('Sort by date'),
        'sortText' => __('Click a date header: ascending, descending and back to the original order.'),
        'rowTitle' => __('Your task'),
        'rowText' => __('Click to log hours. Right click to mark it as Revisado or Solicitar cambio. Hovering the order form shows «Revisar card».'),
        'statusTitle' => __('Order form status'),
        'statusText' => __('To do, in progress, review or done.'),
        'dueTitle' => __('Estimated date'),
        'dueText' => __('With how much time is left or how late it is. Overdue tasks have a red mark on the left.'),
        'footTitle' => __('Shown tasks'),
        'footText' => __('How many tasks match the filters, and a reminder of what the click does.'),
        'diagramTitle' => __('Diagram view'),
        'diagramText' => __('Your projects in a period, by tasks or by hours. Click a project to come back to the table filtered by it.'),
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
        if (!window.AcelerTour || window.__myTasksTourInit) return;
        window.__myTasksTourInit = true;
        const T = @json($mtTourTexts);
        const $q = (s) => document.querySelector(s);
        const firstVisible = window.AcelerTour.firstVisible;

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));
            const scroll = $q('.my-tasks-table-scroll');
            const keep = scroll ? { keepScroll: scroll } : {};

            add($q('.my-tasks-hero .my-tasks-kpi'), T.kpiTitle, T.kpiText);
            add($q('#overviewViewMode'), T.viewTitle, T.viewText, { position: 'left' });
            add($q('#tableViewCard .my-tasks-search'), T.searchTitle, T.searchText);
            add($q('#myTasksQuickFilters'), T.quickTitle, T.quickText);
            add($q('#myTasksColumnsToggleBtn'), T.eyeTitle, T.eyeText, { position: 'left' });
            add(firstVisible('#tableViewCard .my-tasks-filter-btn'), T.funnelTitle, T.funnelText, keep);
            add(firstVisible('#tableViewCard .my-tasks-sort-btn[data-sort-key="estimated"]'), T.sortTitle, T.sortText, keep);

            // Fila de ejemplo: la primera visible de la tabla
            const row = firstVisible('#tableViewCard .my-tasks-body-row');
            if (row) {
                add(row, T.rowTitle, T.rowText, keep);
                const status = row.querySelector('[data-col-key="status"]');
                if (status && status.offsetParent) add(status, T.statusTitle, T.statusText, keep);
                const due = row.querySelector('[data-col-key="estimated_date"]');
                if (due && due.offsetParent) add(due, T.dueTitle, T.dueText, Object.assign({ position: 'left' }, keep));
            }
            add($q('#tableViewCard .my-tasks-table-foot'), T.footTitle, T.footText, { position: 'top' });
            add($q('#overviewViewMode [data-view-mode="diagram"]'), T.diagramTitle, T.diagramText, { position: 'left' });
            return steps;
        }

        window.acelerMyTasksTour = window.AcelerTour.setup({
            seenKey: 'aceler.myTasksTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!document.querySelector('#tableViewCard .my-tasks-body-row'),
            beforeStart: () => {
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
                // El recorrido va sobre la tabla: si estaba en diagrama, se vuelve a la tabla
                const tableButton = $q('#overviewViewMode [data-view-mode="table"]');
                if (tableButton && !tableButton.classList.contains('is-active')) tableButton.click();
                const scroll = $q('.my-tasks-table-scroll');
                if (scroll) scroll.scrollTop = 0;
                window.scrollTo(0, 0);
            },
            buildSteps,
        }).start;
    })();
</script>
