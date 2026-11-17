{{--
    Ayuda de la hoja de horas (projects/timesheet): botón ❓ en la miga de pan, informativo y visita guiada.
    La tabla (projects/timesheet-week) llega por AJAX: los pasos se buscan al empezar el recorrido.
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
            <span class="board-legend-title">{{ __('How the timesheet works') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-regular fa-clock"></i>{{ __('Hour cells') }}</h6>
                <p class="board-legend-sub">{{ __('One per task and day') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('White') }}</b>{{ __('click to log or edit the hours of that day') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Red border') }}</b>{{ __('today') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Grey with lock') }}</b>{{ __('the order form is finished') }}</div>
                <div class="board-legend-item ua-help-def"><b>·</b>{{ __('future or non-working day: cannot be edited') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-chart-simple"></i>{{ __('Each day') }}</h6>
                <p class="board-legend-sub">{{ __('Bar in the day header') }}</p>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#e06c71"></span>{{ __('No hours') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#fcf75e"></span>{{ __('Fewer hours than your timetable') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#89e186"></span>{{ __('Exactly your timetable') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#b2e2f2"></span>{{ __('Overtime') }}</div>
                <p class="board-legend-hint">{{ __('Click a day to see what you logged in it.') }}</p>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-sliders"></i>{{ __('Find your tasks') }}</h6>
                <p class="board-legend-sub">{{ __('Tools above the table') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Search') }}</b>{{ __('by project, order form or task') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Filters') }}</b>{{ __('only tasks with hours, or without finished order forms') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Collapse') }}</b>{{ __('a collapsed project still shows its hours per day') }}</div>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('Collapsed projects and filters are remembered the next time you come back.') }}</span>
        </div>
    </div>
</span>

@php
    $tsTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New timesheet'),
        'toastText' => __('We will show you how to log your hours in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Timesheet tour'),
        'introText' => __('Here you log the hours of each task, day by day. You can leave at any time with Esc.'),
        'addTitle' => __('Add a task'),
        'addText' => __('Creates a new task in one of your order forms so you can log hours on it.'),
        'wsTitle' => __('Workspace'),
        'wsText' => __('Shows the tasks of the current workspace or of all of them.'),
        'weekTitle' => __('Change week'),
        'weekText' => __('Arrows for the previous or next week, the calendar to jump to any date, and «Today» to come back to the current week.'),
        'searchTitle' => __('Search'),
        'searchText' => __('Type part of a project, order form or task name to find it.'),
        'filtersTitle' => __('Filters'),
        'filtersText' => __('Show only the tasks with hours this week, or hide the finished order forms.'),
        'collapseTitle' => __('Collapse or expand'),
        'collapseText' => __('Close or open all projects at once.'),
        'daysTitle' => __('The week'),
        'daysText' => __('Each day with its hours against your workday and a bar in the colour of its state. It stays visible while you scroll. Click a day to see its detail.'),
        'projectTitle' => __('Project'),
        'projectText' => __('Its hours per day and total. Click it to collapse it: it stays as one line with its hours.'),
        'cellTitle' => __('Log hours'),
        'cellText' => __('Click a cell to log or edit the hours of that task on that day. Today has a red border.'),
        'taskTitle' => __('Task'),
        'taskText' => __('Click the name to see the task detail. Next to it, its reference.'),
        'lockTitle' => __('Finished order form'),
        'lockText' => __('Its days after the finish date are locked and cannot be edited.'),
        'footTitle' => __('Day total'),
        'footText' => __('All the hours of each day. Click one to see its detail.'),
        'legendTitle' => __('Legend'),
        'legendText' => __('What each colour of the days means.'),
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
        if (!window.AcelerTour || window.__timesheetTourInit) return;
        window.__timesheetTourInit = true;
        const T = @json($tsTourTexts);
        const $q = (s) => document.querySelector(s);
        const visible = window.AcelerTour.visible;
        const firstVisible = window.AcelerTour.firstVisible;

        // Primer proyecto abierto y visible (si están todos contraídos, el primero visible)
        function sampleGroup() {
            const groups = Array.from(document.querySelectorAll('.tsg .tsg-group')).filter((g) => !g.hidden);
            return groups.find((g) => !g.classList.contains('is-collapsed')) || groups[0] || null;
        }

        // Celda editable de ejemplo: la de hoy en una tarea visible; si no hay, cualquier celda editable
        function sampleCell(group) {
            const root = group || document;
            return firstVisible('.tsg-cell.is-today:not(.is-off)', root)
                || firstVisible('.tsg-cell[data-ajax-timesheet-popup="true"]', root)
                || firstVisible('.tsg-cell.is-today', root);
        }

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));
            const group = sampleGroup();
            const pills = document.querySelectorAll('.tsg [data-tsg-filter]');
            const links = document.querySelectorAll('.tsg [data-tsg-all]');

            add($q('#add_task'), T.addTitle, T.addText);
            add($q('#workspace-filter-select'), T.wsTitle, T.wsText);
            add($q('.weekly-dates-div'), T.weekTitle, T.weekText, { position: 'left' });
            add($q('.tsg .tsg-search'), T.searchTitle, T.searchText);
            if (pills.length) add(pills[0], T.filtersTitle, T.filtersText, { spanTo: pills[pills.length - 1] });
            if (links.length) add(links[0], T.collapseTitle, T.collapseText, { spanTo: links[links.length - 1], position: 'left' });
            add($q('.tsg .tsg-head'), T.daysTitle, T.daysText);
            if (group) {
                add(group.querySelector('.tsg-row--group'), T.projectTitle, T.projectText);
                if (!group.classList.contains('is-collapsed')) {
                    const cell = sampleCell(group);
                    if (cell) add(cell, T.cellTitle, T.cellText);
                    const name = firstVisible('.tsg-task .taskName', group);
                    if (name) add(name.closest('.tsg-task'), T.taskTitle, T.taskText, { position: 'right' });
                }
            }
            const badge = firstVisible('.tsg .tsg-badge');
            if (badge) add(badge.closest('.tsg-sub-title'), T.lockTitle, T.lockText);
            add($q('.tsg .tsg-foot'), T.footTitle, T.footText, { position: 'top' });
            add($q('.ts-legend'), T.legendTitle, T.legendText, { position: 'top' });
            return steps;
        }

        window.acelerTimesheetTour = window.AcelerTour.setup({
            seenKey: 'aceler.timesheetTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            // La tabla llega por AJAX: basta con que exista su contenedor para ofrecer el aviso
            hasContent: () => !!document.getElementById('timesheet-table-view'),
            beforeStart: () => {
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
                window.scrollTo(0, 0);
            },
            buildSteps,
        }).start;
    })();
</script>
