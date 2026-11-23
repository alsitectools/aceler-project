{{--
    Ayuda del calendario (calendar/index): botón ❓ en la miga de pan, informativo y visita guiada.
    Motor del recorrido: custom/js/aceler-tour.js · estilos comunes: custom/css/aceler-help.css
    (reutiliza los del informativo del perfil: .ua-help, .ua-help-def, .ua-help-sw).
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
            <span class="board-legend-title">{{ __('How the calendar works') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-regular fa-calendar"></i>{{ __('Each day') }}</h6>
                <p class="board-legend-sub">{{ __('Hours logged / your workday') }}</p>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#e06c71"></span>{{ __('No hours') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#e8c800"></span>{{ __('Fewer hours than your timetable') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#5cc45a"></span>{{ __('Exactly your timetable') }}</div>
                <div class="board-legend-item"><span class="ua-help-sw" style="background:#63b6dc"></span>{{ __('Overtime') }}</div>
                <p class="board-legend-hint"><i class="fa-solid fa-triangle-exclamation" style="color:#d08700"></i> {{ __('More than 12 h in one day: worth checking.') }}</p>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-umbrella-beach"></i>{{ __('Special days') }}</h6>
                <p class="board-legend-sub">{{ __('They change your workday') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Holiday') }}</b>{{ __('no hours are expected that day') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Intensive workday') }}</b>{{ __('a shorter workday that day') }}</div>
                <p class="board-legend-hint">{{ __('Turn on «Edit mode» and use the + of each day to add them.') }}</p>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-list-check"></i>{{ __('Day detail') }}</h6>
                <p class="board-legend-sub">{{ __('Click a day to see it on the right') }}</p>
                <div class="board-legend-item ua-help-def"><b>{{ __('Tasks') }}</b>{{ __('what you logged, grouped by project') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Log hours') }}</b>{{ __('opens the timesheet on that week') }}</div>
                <div class="board-legend-item ua-help-def"><b>{{ __('Pending days') }}</b>{{ __('working days of the month still without hours') }}</div>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('The summary at the top counts only the month you are viewing, up to today.') }}</span>
        </div>
    </div>
</span>

@php
    $calTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New calendar'),
        'toastText' => __('We will show you how to read your month in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Calendar tour'),
        'introText' => __('Your month at a glance: how many hours you logged each day and which days are missing. You can leave at any time with Esc.'),
        'kpisTitle' => __('Month summary'),
        'kpisText' => __('Hours logged against what you should have logged up to today, days without hours, overtime and holidays of the month you are viewing.'),
        'navTitle' => __('Change month'),
        'navText' => __('Arrows to move between months; «Today» brings you back to the current one.'),
        'dayTitle' => __('Each day'),
        'dayText' => __('Hours logged / your workday, with a bar in the colour of its state. Red: no hours; yellow: fewer; green: exact; blue: overtime.'),
        'weekTitle' => __('Week total'),
        'weekText' => __('In the first day of each week: the hours of that week against the expected ones.'),
        'legendTitle' => __('Legend'),
        'legendText' => __('What each colour means, plus holidays, intensive workdays and non-working days.'),
        'editTitle' => __('Edit mode'),
        'editText' => __('Turn it on to mark holidays or intensive workdays with the + of each day, one by one or several at once.'),
        'wsTitle' => __('Workspace'),
        'wsText' => __('Shows only the hours of the current workspace or of all of them.'),
        'detailTitle' => __('Day detail'),
        'detailText' => __('The selected day: hours logged against your workday and what is missing.'),
        'logTitle' => __('Log hours'),
        'logText' => __('Opens the timesheet directly on the week of this day.'),
        'tasksTitle' => __('Tasks of the day'),
        'tasksText' => __('What you logged that day, grouped by project.'),
        'pendingTitle' => __('Pending days'),
        'pendingText' => __('Working days of the month still without hours. Click one to see it.'),
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
        if (!window.AcelerTour || window.__calendarTourInit) return;
        window.__calendarTourInit = true;
        const T = @json($calTourTexts);
        const $q = (s) => document.querySelector(s);

        // Día de ejemplo: hoy si tiene contenido; si no, el último día con horas visible del mes
        function sampleDay() {
            const today = $q('#calendar .fc-day-today:not(.fc-day-other)');
            if (today && today.querySelector('.cal-h')) return today;
            const days = Array.from(document.querySelectorAll('#calendar .fc-daygrid-day:not(.fc-day-other)'))
                .filter((d) => d.querySelector('.cal-h'));
            return days.length ? days[days.length - 1] : today;
        }

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, more || {}));
            const side = { position: 'left' };

            add($q('#cal-kpis'), T.kpisTitle, T.kpisText);
            add($q('#calendar .fc-header-toolbar .fc-toolbar-chunk'), T.navTitle, T.navText);
            add(sampleDay(), T.dayTitle, T.dayText, { position: 'right' });
            add($q('#calendar .fc-daygrid-week-number'), T.weekTitle, T.weekText, { position: 'right' });
            add($q('.cal-legend'), T.legendTitle, T.legendText, { position: 'top' });
            add($q('.calendar-edit-toggle'), T.editTitle, T.editText);
            add($q('#workspace-select'), T.wsTitle, T.wsText);
            add($q('#cal-day'), T.detailTitle, T.detailText, side);
            add($q('#cal-day-log'), T.logTitle, T.logText, side);
            if ($q('#task-list .cal-task')) add($q('#task-list'), T.tasksTitle, T.tasksText, side);
            add($q('.cal-pending'), T.pendingTitle, T.pendingText, side);
            return steps;
        }

        window.acelerCalendarTour = window.AcelerTour.setup({
            seenKey: 'aceler.calendarTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            // El calendario se pinta tras una llamada AJAX; basta con que exista su contenedor para ofrecer el aviso
            hasContent: () => !!document.getElementById('calendar'),
            beforeStart: () => {
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
            },
            buildSteps,
        }).start;
    })();
</script>
