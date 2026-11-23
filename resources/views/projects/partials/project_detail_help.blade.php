{{--
    Ayuda del detalle del proyecto (projects/show): botón ❓ en la miga de pan, informativo y visita guiada.
    Motor del recorrido: custom/js/aceler-tour.js · estilos comunes: custom/css/aceler-help.css
    (los colores de estado y fechas del informativo están en assets/css/project_detail.css, bloque .pd-help).
--}}
<link rel="stylesheet" href="{{ asset('assets/custom/css/aceler-help.css') }}?v={{ filemtime(public_path('assets/custom/css/aceler-help.css')) }}">
<span class="board-legend-wrap pd-help">
    <button type="button" class="board-legend-toggle" id="boardLegendToggle" aria-expanded="false"
        aria-controls="boardLegend" title="{{ __('Help') }}">
        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        <span class="board-legend-dot" hidden></span>
    </button>

    <div class="board-legend" id="boardLegend" role="dialog" aria-label="{{ __('Help') }}" hidden>
        <div class="board-legend-head">
            <span class="board-legend-title">{{ __('How to read the project detail') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-solid fa-circle-half-stroke"></i>{{ __('Order form status') }}</h6>
                <p class="board-legend-sub">{{ __('Status column and tabs') }}</p>
                <div class="board-legend-item"><span class="pd-help-dot" style="background:#cbd5e1"></span>{{ __('To Do') }}</div>
                <div class="board-legend-item"><span class="pd-help-dot" style="background:#60a5fa"></span>{{ __('Ongoing') }}</div>
                <div class="board-legend-item"><span class="pd-help-dot" style="background:#fbbf24"></span>{{ __('For Review') }}</div>
                <div class="board-legend-item"><span class="pd-help-dot" style="background:#22c55e"></span>{{ __('Finished') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-regular fa-calendar"></i>{{ __('Dates') }}</h6>
                <p class="board-legend-sub">{{ __('Same colours as the board cards') }}</p>
                <div class="board-legend-item"><span class="pd-help-date">30-11-2026</span>{{ __('Planned date') }}</div>
                <div class="board-legend-item"><span class="pd-help-date is-warn">12-11-2026</span>{{ __('Task started late') }}</div>
                <div class="board-legend-item"><span class="pd-help-date is-ok">16-11-2026</span>{{ __('Delivered on schedule') }}</div>
                <div class="board-legend-item"><span class="pd-help-date is-late">20-11-2026</span>{{ __('Delivered, out of date') }}</div>
                <div class="board-legend-item"><span class="pd-help-date is-na">—</span>{{ __('No date') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-gauge"></i>{{ __('Indicators') }}</h6>
                <p class="board-legend-sub">{{ __('Average of the order forms, in days') }}</p>
                <div class="board-legend-item pd-help-def"><b>{{ __('Delivery') }}</b>{{ __('from creation to completion') }}</div>
                <div class="board-legend-item pd-help-def"><b>{{ __('Start-up') }}</b>{{ __('until the first task starts') }}</div>
                <div class="board-legend-item pd-help-def"><b>{{ __('Delay') }}</b>{{ __('days after the desired delivery') }}</div>
                <p class="board-legend-hint">{{ __('Unfinished order forms count up to today.') }}</p>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('Click on an order form to see its details. The filters of each column, the tabs and the search can be combined.') }}</span>
        </div>
    </div>
</span>

@php
    $pdTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New to the project detail?'),
        'toastText' => __('We will show you where everything is in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Project detail tour'),
        'introText' => __('Let us see the parts of the project page. You can leave at any time with Esc.'),
        'headTitle' => __('Project data'),
        'headText' => __('Type, Master Obras reference, delegation, workspace, creation date and members.'),
        'actionsTitle' => __('Shortcuts'),
        'actionsText' => __('Open the order forms board or the timesheet of this project, or create a new order form.'),
        'kpisTitle' => __('Indicators'),
        'kpisText' => __('Order forms by status, logged hours and average times. The ❓ button explains how each one is calculated.'),
        'tabsTitle' => __('Status tabs'),
        'tabsText' => __('Show only the order forms in one status. The number is how many there are.'),
        'searchTitle' => __('Search'),
        'searchText' => __('Type part of the name to filter the order forms as you type.'),
        'columnsTitle' => __('Columns'),
        'columnsText' => __('Choose which columns of the table you want to see.'),
        'phasesTitle' => __('Phases'),
        'phasesText' => __('Edit the phases of the project (I+D projects and developments).'),
        'filterTitle' => __('Column filters'),
        'filterText' => __('Hover over a column header to see its funnel and filter by its values. A filtered column turns red.'),
        'rowTitle' => __('Order form'),
        'rowText' => __('Click anywhere on the row to open the order form details.'),
        'datesTitle' => __('Dates'),
        'datesText' => __('Desired and planned delivery, start of the first task and completion, with the same colours as the board.'),
        'rowActionsTitle' => __('Edit or delete'),
        'rowActionsText' => __('Edit the order form or delete it (deleting cannot be undone).'),
        'filesTitle' => __('Files'),
        'filesText' => __('Project files and those of each order form. Use «Upload file» to add new ones; the folders open with a click.'),
        'teamTitle' => __('Team'),
        'teamText' => __('Who created order forms and who logged hours in this project, with their totals.'),
        'activityTitle' => __('Activity'),
        'activityText' => __('Latest changes in the project, grouped by day. «See all activity» shows the rest.'),
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
        if (!window.AcelerTour || window.__projectDetailTourInit) return;
        window.__projectDetailTourInit = true;
        const T = @json($pdTourTexts);
        const { firstVisible } = window.AcelerTour;
        const $ = (s) => document.querySelector(s);

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));

            add($('.pd .pd-hero-main'), T.headTitle, T.headText);
            add($('.pd .pd-hero-actions'), T.actionsTitle, T.actionsText, { position: 'left' });
            add($('.pd .pd-kpis'), T.kpisTitle, T.kpisText);

            // Encargos: la tabla vuelve arriba para que la primera fila se vea entera
            const scroller = $('.pd .project-order-table-shell');
            if (scroller) scroller.scrollTop = 0;
            const keep = scroller ? { keepScroll: scroller, keepScrollTop: 0 } : {};
            add($('.pd .pd-search'), T.searchTitle, T.searchText, { position: 'left' });
            add(document.getElementById('orderFormsColumnsToggleBtn'), T.columnsTitle, T.columnsText, { position: 'left' });
            add($('.pd .pd-btn-tool'), T.phasesTitle, T.phasesText, { position: 'left' });
            add($('.pd .pd-tabs'), T.tabsTitle, T.tabsText);
            add($('.pd .project-order-th[data-col-key="status"]'), T.filterTitle, T.filterText, keep);
            const row = firstVisible('.pd #orderFormsTableBody .project-order-body-row');
            if (row) {
                const q = (s) => row.querySelector(s);
                add(row, T.rowTitle, T.rowText, keep);
                // fechas: de «entrega deseada» a «completado» como una sola zona
                const from = q('[data-col-key="desired_delivery"]');
                const to = q('[data-col-key="completion"]');
                if (from && to) add(from, T.datesTitle, T.datesText, Object.assign({ spanTo: to }, keep));
                add(q('[data-col-key="action"]'), T.rowActionsTitle, T.rowActionsText, Object.assign({ position: 'left' }, keep));
            }

            add($('.pd .files-section'), T.filesTitle, T.filesText, { position: 'right' });
            add($('.pd .pd-main-col > section:not(.files-section)'), T.teamTitle, T.teamText, { position: 'right' });
            add($('.pd .pd-activity-card'), T.activityTitle, T.activityText, { position: 'left' });
            return steps;
        }

        window.acelerProjectDetailTour = window.AcelerTour.setup({
            seenKey: 'aceler.projectDetailTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!document.querySelector('.pd'),
            beforeStart: () => { if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click(); },
            buildSteps,
        }).start;
    })();
</script>
