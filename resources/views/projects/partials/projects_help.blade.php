{{--
    Ayuda del listado de proyectos (Proyectos y Mis proyectos): botón ❓ en la miga de pan,
    panel informativo y visita guiada (motor en custom/js/aceler-tour.js, estilos en custom/css/aceler-help.css).
    Parámetros:
      $detail       texto final: qué proyectos aparecen en esta vista
      $showActions  (opcional) explica unirse / salir / menú ⋯ (solo en Proyectos)
--}}
@php
    $helpTypes = [1 => 'Jobsite', 3 => 'I+D Project', 4 => 'Office', 2 => 'Innovation', 5 => 'I+D Development'];
    $showActions = !empty($showActions);
@endphp
<link rel="stylesheet" href="{{ asset('assets/custom/css/aceler-help.css') }}?v={{ filemtime(public_path('assets/custom/css/aceler-help.css')) }}">
<span class="board-legend-wrap">
    <button type="button" class="board-legend-toggle" id="boardLegendToggle" aria-expanded="false"
        aria-controls="boardLegend" title="{{ __('Help') }}">
        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        <span class="board-legend-dot" hidden></span>
    </button>

    <div class="board-legend" id="boardLegend" role="dialog" aria-label="{{ __('Help') }}" hidden>
        <div class="board-legend-head">
            <span class="board-legend-title">{{ __('How to read the project list') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-solid fa-circle-half-stroke"></i>{{ __('Status') }}</h6>
                <p class="board-legend-sub">{{ __('Right-hand column of each row') }}</p>
                <div class="board-legend-item"><span class="pl-dot pl-dot-ongoing"></span>{{ __('Ongoing') }}</div>
                <div class="board-legend-item"><span class="pl-dot pl-dot-onhold"></span>{{ __('OnHold') }}</div>
                <div class="board-legend-item"><span class="pl-dot pl-dot-finished"></span>{{ __('Finished') }}</div>
                <p class="board-legend-hint">{{ __('Click on the status of a project to change it.') }}</p>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-shapes"></i>{{ __('Project type') }}</h6>
                <p class="board-legend-sub">{{ __('Icon and colour of each row') }}</p>
                @foreach ($helpTypes as $typeId => $typeName)
                    <div class="board-legend-item">
                        <img class="help-type-icon" src="{{ asset('assets/img/types/' . $typeName . '.svg') }}" alt="">
                        <span class="pl-type-{{ $typeId }}">{{ __($typeName) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-list"></i>{{ __('In each row') }}</h6>
                <p class="board-legend-sub">{{ __('Click on a row to open the project') }}</p>
                <div class="board-legend-item"><span class="help-chip">MO</span>{{ __('Master Obras reference') }}</div>
                <div class="board-legend-item"><span class="help-avatars"><span class="pl-av pl-av-0">AB</span><span class="pl-av pl-av-1">CD</span></span>{{ __('Project members') }}</div>
                @if ($showActions)
                    <div class="board-legend-item">
                        <span class="join-leave-btn join-leave-btn--join help-static"><span class="jl-stack"><i class="fa-solid fa-user"></i><span class="jl-badge"><i class="fa-solid fa-plus"></i></span></span></span>
                        {{ __('Join the project') }}
                    </div>
                    <div class="board-legend-item">
                        <span class="join-leave-btn join-leave-btn--leave help-static"><span class="jl-stack"><i class="fa-solid fa-user"></i><span class="jl-badge"><i class="fa-solid fa-check"></i></span></span></span>
                        {{ __('You are a member: click to leave') }}
                    </div>
                    <div class="board-legend-item"><span class="pl-menu-btn help-static"><i class="fa-solid fa-ellipsis-h"></i></span>{{ __('Delete (only whoever created it)') }}</div>
                @endif
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ $detail }}</span>
        </div>
    </div>
</span>

@php
    $projectsTourTexts = [
        'stepOf' => __('Step :current of :total'),
        'next' => __('Next'),
        'prev' => __('Previous'),
        'done' => __('Got it'),
        'toastTitle' => __('New to the project list?'),
        'toastText' => __('We will show you how to find and read your projects in 30 seconds.'),
        'toastStart' => __('Start tour'),
        'toastLater' => __('Not now'),
        'introTitle' => __('Project list tour'),
        'introText' => __('Let us see how to find a project and read its row. You can leave at any time with Esc.'),
        'searchTitle' => __('Search'),
        'searchText' => __('Type part of the name or the M.O reference to filter the list as you type.'),
        'filtersTitle' => __('Filters'),
        'filtersText' => __('Filter by type, delegation or workspace. They can be combined, and the × of each filter clears it.'),
        'sortTitle' => __('Sort'),
        'sortText' => __('Sort by most recent or by name. Your choice is remembered next time.'),
        'newTitle' => __('New project'),
        'newText' => __('Create a new project in the current workspace.'),
        'tabsTitle' => __('Status tabs'),
        'tabsText' => __('Show only the projects in one status. The number is how many there are with the current filters.'),
        'rowTitle' => __('Project row'),
        'rowText' => __('Click anywhere on the row to open the project.'),
        'metaTitle' => __('Project data'),
        'metaText' => __('Type, Master Obras reference, delegation and last update.'),
        'membersTitle' => __('Members'),
        'membersText' => __('People who take part in the project. Hover over them to see their names.'),
        'statusTitle' => __('Status'),
        'statusText' => __('Current status of the project. Click on it to change it.'),
        'joinTitle' => __('Join or leave'),
        'joinText' => __('Join a project to appear as a member, or leave it if you no longer take part.'),
        'menuTitle' => __('Project actions'),
        'menuText' => __('Only whoever created the project can delete it from here.'),
        'footTitle' => __('End of the list'),
        'footText' => __('How many projects are shown with the current filters, and a shortcut back to the top.'),
    ];
@endphp
<script src="{{ asset('assets/custom/js/aceler-tour.js') }}?v={{ filemtime(public_path('assets/custom/js/aceler-tour.js')) }}"></script>
<script>
    (function () {
        // --- panel informativo ---
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
        if (!window.AcelerTour || window.__projectsTourInit) return;
        window.__projectsTourInit = true;
        const T = @json($projectsTourTexts);
        const { firstVisible } = window.AcelerTour;
        const ROW = '.pl-list-box .grid .pl-row';

        function buildSteps() {
            const steps = [{ title: T.introTitle, text: T.introText }];
            steps.push({ element: document.getElementById('searchInputProjects'), title: T.searchTitle, text: T.searchText });
            steps.push({ element: firstVisible('.pl-toolbar .pl-facet'), title: T.filtersTitle, text: T.filtersText });
            steps.push({ element: document.querySelector('.pl-toolbar .pl-sort'), title: T.sortTitle, text: T.sortText, position: 'left' });
            const create = document.querySelector('.pl-toolbar .pl-new-btn');
            if (create) steps.push({ element: create, title: T.newTitle, text: T.newText, position: 'left' });
            steps.push({ element: document.querySelector('.pl-tabs'), title: T.tabsTitle, text: T.tabsText });

            // Fila de ejemplo: una de las primeras que se ven (Isotope reordena, así que se mira la posición, no el HTML),
            // mejor si tiene acciones. El listado vuelve arriba para que la fila quede dentro del cuadro con scroll.
            const scroller = document.getElementById('plListScroll');
            if (scroller) scroller.scrollTop = 0;
            const top = Array.from(document.querySelectorAll(ROW))
                .filter(window.AcelerTour.visible)
                .sort((a, b) => a.getBoundingClientRect().top - b.getBoundingClientRect().top)
                .slice(0, 4);
            const row = top.find((r) => r.querySelector('.pl-menu-btn')) || top.find((r) => r.querySelector('.join-leave-btn')) || top[0];
            if (row) {
                // La fila ya está a la vista: el cuadro de la lista se mantiene arriba (si no, intro.js corta la fila)
                row.scrollIntoView({ block: 'nearest' });
                const q = (s) => row.querySelector(s);
                const base = { lift: [row], keepScroll: scroller, keepScrollTop: scroller ? scroller.scrollTop : 0 };
                const add = (element, title, text, more) => steps.push(Object.assign({ element, title, text }, base, more || {}));
                add(row, T.rowTitle, T.rowText);
                add(q('.pl-meta'), T.metaTitle, T.metaText);
                if (q('.pl-avatars .pl-av')) add(q('.pl-avatars'), T.membersTitle, T.membersText, { position: 'left' });
                add(q('.pl-status'), T.statusTitle, T.statusText, { position: 'left' });
                if (q('.join-leave-btn')) add(q('.join-leave-btn'), T.joinTitle, T.joinText, { position: 'left' });
                const menu = q('.pl-menu-btn');
                if (menu) add(menu.closest('.btn-group'), T.menuTitle, T.menuText, { position: 'left', menu: true });
            }
            steps.push({ element: document.querySelector('.pl-list-foot'), title: T.footTitle, text: T.footText, position: 'top' });
            return steps;
        }

        window.acelerProjectsTour = window.AcelerTour.setup({
            seenKey: 'aceler.projectsTour.seen',
            assets: { css: @json(asset('assets/css/plugins/introjs.min.css')), js: @json(asset('assets/js/plugins/intro.min.js')) },
            texts: T,
            dot: document.querySelector('#boardLegendToggle .board-legend-dot'),
            hasContent: () => !!firstVisible(ROW),
            beforeStart: () => { if (toggle && toggle.getAttribute('aria-expanded') === 'true') toggle.click(); },
            buildSteps,
        }).start;
    })();
</script>
