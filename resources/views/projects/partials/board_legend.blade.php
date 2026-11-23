{{--
    Leyenda de los tablones de encargos (milestoneboard y my_milestone_board).
    Explica lo que se ve en la card: color de las etiquetas de fecha, borde de la card y pestaña de prioridad.
    Los colores salen de las mismas clases que usa la card (milestoneboard.css), así no se desincronizan.
    Parámetro: $detail = texto final que explica qué encargos aparecen en ese tablón.
--}}
<link rel="stylesheet" href="{{ asset('assets/custom/css/aceler-help.css') }}?v={{ filemtime(public_path('assets/custom/css/aceler-help.css')) }}">
<span class="board-legend-wrap">
    <button type="button" class="board-legend-toggle" id="boardLegendToggle" aria-expanded="false"
        aria-controls="boardLegend" title="{{ __('Color legend') }}">
        <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
        {{-- Punto de «ayuda nueva»: lo muestra board_tour.blade.php mientras no se haya visto el recorrido --}}
        <span class="board-legend-dot" hidden></span>
    </button>

    <div class="board-legend" id="boardLegend" role="dialog" aria-label="{{ __('Color legend') }}" hidden>
        <div class="board-legend-head">
            <span class="board-legend-title">{{ __('Color legend') }}</span>
            <span class="board-legend-actions">
                <button type="button" class="board-legend-tour" data-tour-start>
                    <i class="fa-solid fa-play"></i>{{ __('View tour') }}
                </button>
                <button type="button" class="board-legend-close" aria-label="{{ __('Close') }}">&times;</button>
            </span>
        </div>

        <div class="board-legend-body">
            <div class="board-legend-block">
                <h6><i class="fa-regular fa-calendar"></i>{{ __('Dates') }}</h6>
                <p class="board-legend-sub">{{ __('Desired') }} · {{ __('Proposed') }} · {{ __('Completed') }}</p>
                <div class="board-legend-item"><span class="calendar-badge badge-default">30 Nov</span>{{ __('Pending, on schedule') }}</div>
                <div class="board-legend-item"><span class="calendar-badge badge-warning">08 Nov</span>{{ __('Pending, out of date') }}</div>
                <div class="board-legend-item"><span class="calendar-badge badge-success">12 Nov</span>{{ __('Delivered on schedule') }}</div>
                <div class="board-legend-item"><span class="calendar-badge badge-danger">20 Nov</span>{{ __('Delivered, out of date') }}</div>
                <div class="board-legend-item"><span class="calendar-badge badge-na">—</span>{{ __('No date') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-regular fa-square"></i>{{ __('Card border') }}</h6>
                <p class="board-legend-sub">{{ __('Order status') }}</p>
                <div class="board-legend-item"><span class="board-legend-card is-unassigned"></span>{{ __('Unassigned') }}</div>
                <div class="board-legend-item"><span class="board-legend-card is-paused"></span>{{ __('Paused') }}</div>
            </div>

            <div class="board-legend-block">
                <h6><i class="fa-solid fa-flag"></i>{{ __('Priority') }}</h6>
                <p class="board-legend-sub">{{ __('Top tab of the card') }}</p>
                <div class="board-legend-item"><span class="board-legend-priority priority-low">{{ __('Low priority') }}</span></div>
                <div class="board-legend-item"><span class="board-legend-priority priority-medium">{{ __('Medium priority') }}</span></div>
                <div class="board-legend-item"><span class="board-legend-priority priority-high">{{ __('High priority') }}</span></div>
                <p class="board-legend-hint">{{ __('It shows the urgency, not whether it is on schedule.') }}</p>
            </div>
        </div>

        <div class="board-legend-foot">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ $detail }}</span>
        </div>
    </div>
</span>

@include('projects.partials.board_tour')

<script>
    (function () {
        const toggle = document.getElementById('boardLegendToggle');
        const panel = document.getElementById('boardLegend');
        if (!toggle || !panel || toggle.dataset.bound) return;
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
                hideTimer = setTimeout(() => { panel.hidden = true; }, 200); // duración de la transición
            }
        };
        const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

        toggle.addEventListener('click', (e) => { e.stopPropagation(); setOpen(!isOpen()); });
        panel.querySelector('.board-legend-close').addEventListener('click', () => setOpen(false));
        panel.addEventListener('click', (e) => e.stopPropagation());
        document.addEventListener('click', () => { if (isOpen()) setOpen(false); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen()) setOpen(false); });
    })();
</script>
