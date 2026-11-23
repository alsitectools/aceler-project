{{--
    Visita guiada de los tablones de encargos. Se incluye desde board_legend.blade.php.
    El motor (aviso de primera visita, intro.js, menú ⋯, punto rojo) está en custom/js/aceler-tour.js;
    aquí solo se definen los textos y los pasos. El recorrido se hace sobre una card real del tablón.
--}}
@php
    $tourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New to the board?'),
        'toastText' => __('We will show you how to read an order form card in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Board tour'),
        'introText' => __('Let us see how to read an order form card. You can leave at any time with Esc.'),
        'priorityTitle' => __('Priority'),
        'priorityText' => __('The top tab shows the urgency of the order: low, medium or high. It does not mean that it is late.'),
        'typeTitle' => __('Project type'),
        'typeText' => __('Icon and type of the project the order form belongs to.'),
        'nameTitle' => __('Order form name'),
        'nameText' => __('Click on it to open the order form details.'),
        'datesTitle' => __('Dates'),
        'datesText' => __('Desired (requested by the manager), Proposed (estimated delivery) and Completed. The colour shows how it is going:'),
        'onSchedule' => __('on schedule'),
        'outOfDate' => __('out of date'),
        'deliveredOnTime' => __('delivered on time'),
        'deliveredLate' => __('delivered late'),
        'tasksTitle' => __('Tasks'),
        'tasksText' => __('Tasks of the order form. Click on the box to show them all.'),
        'leftClickLabel' => __('Left click'),
        'leftClickTitle' => __('Log hours'),
        'leftClickText' => __('Click on a task to log the hours you have worked on it.'),
        'rightClickLabel' => __('Right click'),
        'rightClickTitle' => __('Review a task'),
        'rightClickText' => __('Right-click on a task to mark it as reviewed or to request a change with a comment.'),
        'rightClickNote' => __('Only in order forms that are In progress or In review.'),
        'noTasksText' => __('This order form has no tasks yet. You can add them from the ⋯ menu.'),
        'peopleTitle' => __('People'),
        'peopleText' => __('Who requested the order form and who it is assigned to. Hover over a photo to see the name.'),
        'footTitle' => __('Next step and project'),
        'footText' => __('On the left, what the order form needs to move forward; on the right, the project it belongs to.'),
        'menuTitle' => __('Order form actions'),
        'menuText' => __('From the three dots you can view, assign, add tasks, pause or edit the order form (depending on your permissions).'),
        'columnsTitle' => __('Columns'),
        'columnsText' => __('Each column is a status. Drag a card to another column to change its status.'),
        'borderTitle' => __('Card border'),
        'borderText' => __('A dark red border means the order form is unassigned; a dashed grey border means it is paused.'),
    ];
@endphp
<script src="{{ asset('assets/custom/js/aceler-tour.js') }}?v={{ filemtime(public_path('assets/custom/js/aceler-tour.js')) }}"></script>
<script>
    (function () {
        if (!window.AcelerTour || window.__boardTourInit) return;
        window.__boardTourInit = true;

        const T = @json($tourTexts);
        const { firstVisible } = window.AcelerTour;
        const CARD = '.kanban-box .milestone-card';

        function dateChips() {
            const chip = (cls, label) => '<span><i class="calendar-badge ' + cls + '"></i>' + label + '</span>';
            return '<div class="board-tour-chips">' +
                chip('badge-default', T.onSchedule) + chip('badge-warning', T.outOfDate) +
                chip('badge-success', T.deliveredOnTime) + chip('badge-danger', T.deliveredLate) + '</div>';
        }

        // Cada columna tiene su propio scroll: una card puede existir pero estar escondida más abajo.
        const boxOf = (card) => card.closest('.kanban-box');
        function fullyInView(card) {
            const box = boxOf(card);
            const r = card.getBoundingClientRect();
            const b = box ? box.getBoundingClientRect() : { top: 0, bottom: window.innerHeight };
            return r.top >= Math.max(b.top, 0) - 1 && r.bottom <= Math.min(b.bottom, window.innerHeight) + 1;
        }
        // scrollTop de su columna que deja la card arriba del todo, a la vista
        function columnTopFor(card) {
            const box = boxOf(card);
            if (!box) return 0;
            return Math.max(0, Math.round(box.scrollTop + card.getBoundingClientRect().top - box.getBoundingClientRect().top - 12));
        }
        const hasTasks = (c) => !!c.querySelector('.milestone-task');
        const hasPriority = (c) => !!c.querySelector('.priority-badge');
        // El clic derecho (revisar tarea) solo funciona en encargos En curso (2) o En revisión (3)
        const inReviewable = (c) => ['2', '3'].includes(c.getAttribute('data-status'));

        // La card de ejemplo: mejor una que ya se vea entera; entre ellas, la que enseñe más cosas
        // (prioridad y tareas, y que admita el clic derecho). Si ninguna se ve entera, la mejor de todas,
        // y su columna se desplaza hasta ella.
        function pickCard() {
            const all = Array.from(document.querySelectorAll(CARD)).filter(window.AcelerTour.visible);
            const pick = (list) =>
                list.find((c) => hasPriority(c) && hasTasks(c) && inReviewable(c))
                || list.find((c) => hasTasks(c) && inReviewable(c))
                || list.find((c) => hasPriority(c) && hasTasks(c))
                || list.find(hasTasks) || list.find(hasPriority) || list[0] || null;
            return pick(all.filter(fullyInView)) || pick(all);
        }

        // Etiqueta con el ratón para los pasos de clic (izquierdo / derecho)
        function clickBadge(label, side, note) {
            const esc = (s) => String(s).replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
            return '<div class="board-tour-click is-' + side + '"><span class="board-tour-mouse"><i></i></span>' + esc(label) + '</div>' +
                (note ? '<p class="board-tour-note">' + esc(note) + '</p>' : '');
        }
        // Pasos sobre una card: su columna se queda donde la card se ve (intro.js la movería)
        function keepColumn(card) {
            const box = boxOf(card);
            if (!box) return {};
            const top = fullyInView(card) ? box.scrollTop : columnTopFor(card);
            box.scrollTop = top;
            return { keepScroll: box, keepScrollTop: top };
        }

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            const card = pickCard();
            if (card) {
                const q = (s) => card.querySelector(s);
                const lift = [card, q('.card-header-right')];
                const keep = keepColumn(card);
                const tasks = q('.milestone-task-list');
                const add = (element, title, text, position, more) => steps.push(Object.assign({ element, title, text, position, lift }, keep, more || {}));
                add(q('.priority-badge'), T.priorityTitle, T.priorityText, 'bottom');
                add(q('.milestone-type'), T.typeTitle, T.typeText, 'right');
                add(q('.milestone-title'), T.nameTitle, T.nameText, 'right');
                add(q('.milestone-calendar'), T.datesTitle, T.datesText, 'right', { extra: dateChips() });
                add(tasks, T.tasksTitle, tasks && tasks.querySelector('.empty-state') ? T.noTasksText : T.tasksText, 'right');
                // Una tarea concreta: clic izquierdo = imputar horas; clic derecho = revisarla
                const task = Array.from(card.querySelectorAll('.milestone-task:not(.task-inactive)')).find(window.AcelerTour.visible)
                    || Array.from(card.querySelectorAll('.milestone-task')).find(window.AcelerTour.visible);
                if (task) {
                    add(task, T.leftClickTitle, T.leftClickText, 'right', { extra: clickBadge(T.leftClickLabel, 'left') });
                    add(task, T.rightClickTitle, T.rightClickText, 'right', {
                        extra: clickBadge(T.rightClickLabel, 'right', T.rightClickNote),
                    });
                }
                add(q('.milestone-avatars'), T.peopleTitle, T.peopleText, 'right');
                add(q('.milestone-foot'), T.footTitle, T.footText, 'bottom');
                if (q('.card-option [data-bs-toggle="dropdown"]')) add(q('.card-option'), T.menuTitle, T.menuText, 'right', { menu: true });
            }
            const header = firstVisible('.kanban-wrapper .card-list > .card-header');
            if (header) steps.push({ element: header, title: T.columnsTitle, text: T.columnsText, position: 'bottom' });
            // Card con borde especial: mejor una que ya se vea; si no, su columna se desplaza hasta ella en ese paso
            const flaggedAll = Array.from(document.querySelectorAll(CARD + '.notAsignedMilestone, ' + CARD + '.waitingMilestone')).filter(window.AcelerTour.visible);
            const flagged = flaggedAll.find(fullyInView) || flaggedAll[0];
            if (flagged) {
                const box = boxOf(flagged);
                const extra = box ? { keepScroll: box, keepScrollTop: fullyInView(flagged) ? box.scrollTop : columnTopFor(flagged) } : {};
                steps.push(Object.assign({ element: flagged, title: T.borderTitle, text: T.borderText, position: 'right', lift: [flagged] }, extra));
            }
            return steps;
        }

        window.acelerBoardTour = window.AcelerTour.setup({
            seenKey: 'aceler.boardTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!firstVisible(CARD),
            beforeStart: () => {
                const toggle = document.getElementById('boardLegendToggle');
                if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
            },
            buildSteps,
        }).start;
    })();
</script>
