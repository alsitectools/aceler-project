@php
    use Carbon\Carbon;
    use App\Models\CustomTasks;

    /*
     * Hoja de horas semanal como tabla compacta: una sola cabecera de días (fija al hacer scroll),
     * grupo (proyecto o encargo) → subgrupo (encargo o usuario) → una fila por tarea.
     * Estilos: assets/css/timesheet_week.css (bloque .tsg). Clics: projects/timesheet.blade.php.
     */
    $holidayDates = $holidayDates ?? [];
    $intensiveHoursByDate = $intensiveHoursByDate ?? [];
    $popupTasksByDate = $popupTasksByDate ?? [];
    $workHoursWeek = $workHoursWeek ?? [];
    $today = Carbon::today();
    $authId = Auth::id();

    $toMinutes = function ($time) {
        $parts = explode(':', (string) $time);
        return ((int) ($parts[0] ?? 0)) * 60 + (int) ($parts[1] ?? 0);
    };
    $fmt = function ($minutes) {
        return sprintf('%d:%02d', intdiv((int) $minutes, 60), ((int) $minutes) % 60);
    };

    // --- días de la semana: fecha, jornada esperada, horas imputadas (todas las del usuario) y color ---
    $dayMeta = [];
    foreach ($days['datePeriod'] as $index => $datePeriod) {
        $dateObj = Carbon::parse($datePeriod);
        $dateKey = $dateObj->toDateString();
        $dayName = strtolower($dateObj->format('l'));
        $isWorkday = isset($workHoursWeek[$dayName]);
        $isHoliday = in_array($dateKey, $holidayDates, true);
        $isFuture = $dateObj->greaterThan($today);
        $worked = $totalDateTimes[$index] ?? '00:00';
        $expectedRaw = $intensiveHoursByDate[$dateKey] ?? ($isWorkday ? $workHoursWeek[$dayName] : null);
        $expectedMin = $expectedRaw ? $toMinutes($expectedRaw) : 0;
        $workedMin = $toMinutes($worked);

        if ($isHoliday) {
            $state = 'holiday';
        } elseif (!$isWorkday || $isFuture) {
            $state = 'off';
        } elseif ($workedMin === 0) {
            $state = 'none';
        } elseif ($workedMin < $expectedMin) {
            $state = 'partial';
        } elseif ($workedMin === $expectedMin) {
            $state = 'complete';
        } else {
            $state = 'overtime';
        }

        $dayMeta[] = [
            'date' => $dateKey,
            'short' => mb_strtoupper(rtrim($dateObj->isoFormat('ddd'), '.')),
            'num' => $dateObj->format('j'),
            'formatted' => ucfirst($dateObj->isoFormat('ddd DD MMM')),
            'isToday' => $dateObj->isSameDay($today),
            'isFuture' => $isFuture,
            'isWorkday' => $isWorkday,
            'isHoliday' => $isHoliday,
            'dayName' => $dayName,
            'worked' => $worked,
            'workedMin' => $workedMin,
            'expected' => ($isWorkday && !$isHoliday && $expectedMin > 0) ? $fmt($expectedMin) : null,
            'expectedMin' => $expectedMin,
            'state' => $state,
            'pct' => $state === 'none' ? 100 : ($expectedMin > 0 ? min(100, round($workedMin * 100 / $expectedMin)) : ($workedMin > 0 ? 100 : 0)),
        ];
    }
    $weekWorkedMin = array_sum(array_column($dayMeta, 'workedMin'));
    $weekExpectedMin = array_sum(array_map(fn ($d) => $d['expected'] ? $d['expectedMin'] : 0, $dayMeta));

    // --- nombres de tareas «custom» en una sola consulta ---
    $customIds = [];
    foreach ($timesheetArray as $g) {
        foreach (($allProjects ? ($g['milestoneArray'] ?? []) : ($g['usersArray'] ?? [])) as $s) {
            foreach ($s['taskArray'] as $t) {
                if (strtolower($t['task_name']) === 'custom') {
                    $customIds[] = $t['task_id'];
                }
            }
        }
    }
    $customNames = $customIds ? CustomTasks::whereIn('id_task', $customIds)->pluck('name', 'id_task')->toArray() : [];

    // --- estructura común: grupo (proyecto | encargo) → subgrupo (encargo | usuario) → tareas ---
    $groups = [];
    foreach ($timesheetArray as $key => $g) {
        $subsSrc = $allProjects ? ($g['milestoneArray'] ?? []) : ($g['usersArray'] ?? []);
        $group = [
            'id' => $allProjects ? 'p' . ($g['project_id'] ?? $key) : 'm' . ($g['milestone_id'] ?? $key),
            'title' => $allProjects ? $g['project_name'] : ($g['milestone_name'] ?? __('Unknown Milestone')),
            'chip' => $allProjects ? trim((string) ($g['ref_delegation'] ?? '')) : '',
            'subs' => [],
            'dayMin' => array_fill(0, 7, 0),
        ];
        foreach ($subsSrc as $s) {
            $sub = [
                'title' => $allProjects ? $s['milestone_name'] : $s['user_name'],
                'milestone_id' => $allProjects ? ($s['milestone_id'] ?? null) : ($g['milestone_id'] ?? null),
                'locked' => false,
                'tasks' => [],
            ];
            foreach ($s['taskArray'] as $t) {
                $name = $t['task_name'];
                if (strtolower($name) === 'custom') {
                    $name = $customNames[$t['task_id']] ?? 'custom';
                }
                $cells = array_values($t['dateArray']['week'] ?? []);
                foreach ($cells as $i => $c) {
                    $group['dayMin'][$i] += $toMinutes($c['time']);
                    if (!empty($c['locked'])) {
                        $sub['locked'] = true;
                    }
                }
                $sub['tasks'][] = [
                    'id' => $t['task_id'],
                    'name' => __($name),
                    'ref' => $t['referencia'] ?? '',
                    'cells' => $cells,
                    'total' => $t['totaltime'],
                    'hasHours' => $toMinutes($t['totaltime']) > 0,
                ];
            }
            $group['subs'][] = $sub;
        }
        $group['totalMin'] = array_sum($group['dayMin']);
        $groups[] = $group;
    }
    $subLabel = function ($n) use ($allProjects) {
        return $allProjects ? ($n === 1 ? __('order form') : __('order forms')) : ($n === 1 ? __('user') : __('users'));
    };

    // Los totales por día suman todas las horas del usuario (todos los espacios de trabajo);
    // las filas solo muestran el espacio actual. Si no cuadran, se avisa de la diferencia.
    $hiddenMinutes = $weekWorkedMin - $toMinutes($calculatedTotalTaskTime);
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/timesheet_week.css') }}?v={{ @filemtime(public_path('assets/css/timesheet_week.css')) }}">

<div class="tsg" data-tsg-scope="{{ $currentWorkspace->slug }}:{{ $allProjects ? 'all' : 'project' }}">
    {{-- Herramientas --}}
    <div class="tsg-tools">
        <label class="tsg-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" data-tsg-search placeholder="{{ $allProjects ? __('Search project, order form or task…') : __('Search order form, user or task…') }}" autocomplete="off">
        </label>
        <button type="button" class="tsg-pill" data-tsg-filter="hours" aria-pressed="false">
            <i class="fa-solid fa-check"></i>{{ __('With hours this week') }}
        </button>
        <button type="button" class="tsg-pill" data-tsg-filter="finished" aria-pressed="false">
            <i class="fa-solid fa-check"></i>{{ __('Hide finished') }}
        </button>
        <span class="tsg-tools-sp"></span>
        <button type="button" class="tsg-link" data-tsg-all="collapse"><i class="fa-solid fa-angles-up"></i>{{ __('Collapse all') }}</button>
        <button type="button" class="tsg-link tsg-link--muted" data-tsg-all="expand"><i class="fa-solid fa-angles-down"></i>{{ __('Expand all') }}</button>
    </div>

    {{-- Cabecera de días (fija al hacer scroll) --}}
    <div class="tsg-row tsg-head">
        <div class="tsg-name tsg-head-label">
            {{ __('Week') }} {{ Carbon::parse($days['first_day'])->isoWeek() }}
            · {{ count($groups) }} {{ $allProjects ? mb_strtolower(__('Projects')) : mb_strtolower(__('order forms')) }}
        </div>
        @foreach ($dayMeta as $d)
            <div class="tsg-day day-total-hours {{ $d['isToday'] ? 'is-today' : '' }} {{ $d['state'] === 'off' ? 'is-off' : '' }}"
                role="button" tabindex="0"
                data-date="{{ $d['date'] }}" data-date-formatted="{{ $d['formatted'] }}" data-hours="{{ $d['worked'] }}"
                data-is-holiday="{{ $d['isHoliday'] ? '1' : '0' }}"
                data-tasks="{{ json_encode($popupTasksByDate[$d['date']] ?? []) }}"
                title="{{ $d['expected'] ? __('Logged') . ' ' . $fmt($d['workedMin']) . ' / ' . __('Expected') . ' ' . $d['expected'] . ' · ' : '' }}{{ __('Click to view day details') }}">
                <div class="tsg-day-name">{{ $d['short'] }}@if ($d['isToday']) · {{ __('Today') }}@endif</div>
                <div class="tsg-day-num">{{ $d['num'] }}</div>
                <div class="tsg-day-bar"><i class="tsg-st--{{ $d['state'] }}" style="width: {{ $d['state'] === 'off' ? 0 : $d['pct'] }}%"></i></div>
                <div class="tsg-day-sum">
                    @if ($d['state'] === 'off' && $d['workedMin'] === 0)
                        {{ $d['expected'] ?? '—' }}
                    @else
                        <b>{{ $fmt($d['workedMin']) }}</b>@if ($d['expected']) / {{ $d['expected'] }}@endif
                    @endif
                </div>
            </div>
        @endforeach
        <div class="tsg-total tsg-head-total">
            <div class="tsg-day-name">{{ __('Total') }}</div>
            <div class="tsg-day-num">{{ $fmt($weekWorkedMin) }}</div>
            @if ($weekExpectedMin > 0)
                <div class="tsg-day-sum">/ {{ $fmt($weekExpectedMin) }}</div>
            @endif
        </div>
    </div>

    {{-- Grupos --}}
    @foreach ($groups as $group)
        <div class="tsg-group" data-tsg-group="{{ $group['id'] }}">
            <div class="tsg-row tsg-row--group" role="button" tabindex="0" aria-expanded="true" data-tsg-toggle>
                <div class="tsg-name">
                    <i class="fa-solid fa-chevron-down tsg-caret"></i>
                    <span class="tsg-group-title" title="{{ $group['title'] }}">{{ $group['title'] }}</span>
                    @if ($group['chip'] !== '')
                        <span class="tsg-chip" title="{{ __('Delegation ID') }}">{{ $group['chip'] }}</span>
                    @endif
                    <span class="tsg-count">{{ count($group['subs']) }} {{ $subLabel(count($group['subs'])) }}</span>
                </div>
                @foreach ($group['dayMin'] as $min)
                    <div class="tsg-sumcell">{!! $min ? e($fmt($min)) : '<span class="tsg-z">—</span>' !!}</div>
                @endforeach
                <div class="tsg-total">{!! $group['totalMin'] ? e($fmt($group['totalMin'])) : '<span class="tsg-z">0:00</span>' !!}</div>
            </div>

            <div class="tsg-body">
                @foreach ($group['subs'] as $sub)
                    <div class="tsg-sub" data-tsg-locked="{{ $sub['locked'] ? '1' : '0' }}">
                        <div class="tsg-sub-title">
                            <span>{{ $sub['title'] }}</span>
                            @if ($sub['locked'])
                                <span class="tsg-badge"><i class="fa-solid fa-lock"></i>{{ __('Finished') }}</span>
                            @endif
                        </div>
                        @foreach ($sub['tasks'] as $task)
                            <div class="tsg-row tsg-row--task" data-tsg-hours="{{ $task['hasHours'] ? '1' : '0' }}"
                                data-tsg-text="{{ mb_strtolower($group['title'] . ' ' . $sub['title'] . ' ' . $task['name'] . ' ' . $task['ref']) }}">
                                <div class="tsg-name tsg-task">
                                    <span role="button" class="taskName" data-title="{{ __('Task') }}"
                                        data-url="{{ route('show.task', [$currentWorkspace->slug, $task['id'], $days['first_day'], $days['seventh_day']]) }}"
                                        data-ajax-popup="true" data-task-name="{{ $task['name'] }}"
                                        data-modal-id="commonModalModified">{{ $task['name'] }}</span>
                                    @if ($task['ref'] !== '')
                                        <span class="tsg-ref">{{ $task['ref'] }}</span>
                                    @endif
                                </div>
                                @foreach ($task['cells'] as $i => $c)
                                    @php
                                        $d = $dayMeta[$i];
                                        $isLocked = !empty($c['locked']);
                                        $isAllowed = $d['isWorkday'] && !$d['isFuture'] && !$d['isHoliday'] && !$isLocked;
                                        $hasHours = $c['time'] !== '00:00';
                                        $offReason = $isLocked ? 'locked' : ($d['isHoliday'] ? 'holiday' : ($d['isFuture'] ? 'future' : ($isAllowed ? null : 'nonworkday')));
                                        $cellClasses = 'tsg-cell'
                                            . ($hasHours ? ' is-filled' : '')
                                            . ($d['isToday'] ? ' is-today' : '')
                                            . ($offReason ? ' is-off is-' . $offReason : '');
                                        $cellTitle = $isLocked ? __('The order form is finished: this day cannot be edited')
                                            : ($d['isHoliday'] ? __('This day is marked as holiday and cannot be edited')
                                            : ($d['isFuture'] ? __('This day is in the future and cannot be edited')
                                            : ($isAllowed ? __('Click to Add/Edit Timesheet') : __('This day is not available or is in the future'))));
                                    @endphp
                                    <div class="tsg-cellwrap">
                                        @if ($authId == $c['user_id'])
                                            <div role="button" class="{{ $cellClasses }}" title="{{ $cellTitle }}"
                                                data-ajax-timesheet-popup="{{ $isAllowed ? 'true' : 'false' }}"
                                                data-type="{{ $c['type'] }}" data-user-id="{{ $c['user_id'] }}"
                                                data-project-id="{{ $c['project_id'] }}" data-task-id="{{ $c['task_id'] }}"
                                                data-date="{{ $c['date'] }}" data-url="{{ $c['url'] }}"
                                                @unless ($allProjects) data-milestone-id="{{ $sub['milestone_id'] }}" @endunless>
                                                @if ($offReason === 'locked')
                                                    <i class="fa-solid fa-lock tsg-cell-icon"></i>
                                                @elseif ($offReason === 'holiday')
                                                    <i class="fa-solid fa-umbrella-beach tsg-cell-icon"></i>
                                                @endif
                                                @if ($hasHours)
                                                    {{ $c['time'] }}
                                                @elseif ($offReason === 'future' || $offReason === 'nonworkday')
                                                    ·
                                                @elseif ($offReason !== 'locked')
                                                    —
                                                @endif
                                            </div>
                                        @else
                                            <div class="tsg-cell is-readonly {{ $hasHours ? 'is-filled' : '' }}">{{ $hasHours ? $c['time'] : '—' }}</div>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="tsg-total {{ $task['hasHours'] ? '' : 'tsg-z' }}">{{ $fmt($toMinutes($task['total'])) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="tsg-empty" hidden>{{ __('No tasks match the filters.') }}</div>

    {{-- Total de cada día (clic: detalle del día) --}}
    <div class="tsg-row tsg-foot">
        <div class="tsg-name tsg-foot-label">{{ __('Day total') }}</div>
        @foreach ($dayMeta as $d)
            <div class="tsg-sumcell">
                <span class="day-total-hours tsg-foot-day tsg-st-text--{{ $d['state'] }}" role="button"
                    data-date="{{ $d['date'] }}" data-date-formatted="{{ $d['formatted'] }}" data-hours="{{ $d['worked'] }}"
                    data-is-holiday="{{ $d['isHoliday'] ? '1' : '0' }}"
                    data-tasks="{{ json_encode($popupTasksByDate[$d['date']] ?? []) }}"
                    title="{{ __('Click to view day details') }}">{{ $d['workedMin'] || $d['state'] !== 'off' ? $fmt($d['workedMin']) : '—' }}</span>
            </div>
        @endforeach
        <div class="tsg-total">{{ $fmt($weekWorkedMin) }}</div>
    </div>
</div>

@if (!empty($allProjects) && $hiddenMinutes > 0)
    <div class="ts-hidden-hours-notice">
        <i class="fa-solid fa-circle-info"></i>
        {{ __('Day totals include :hours h logged in other workspaces or projects that are not shown here.', ['hours' => $fmt($hiddenMinutes)]) }}
    </div>
@endif

<div class="ts-legend">
    <span><i class="ts-legend-swatch" style="background:#e06c71"></i>{{ __('No hours') }}</span>
    <span><i class="ts-legend-swatch" style="background:#fcf75e"></i>{{ __('Partial') }}</span>
    <span><i class="ts-legend-swatch" style="background:#89e186"></i>{{ __('Complete') }}</span>
    <span><i class="ts-legend-swatch" style="background:#b2e2f2"></i>{{ __('Overtime') }}</span>
    <span><i class="ts-legend-swatch" style="background:rgba(145, 221, 207, 0.4)"></i>{{ __('Holiday') }}</span>
    <span><i class="ts-legend-swatch" style="background:#d3d3d3"></i>{{ __('Non-working or future day') }}</span>
    <span><i class="fa-solid fa-lock"></i> {{ __('Order form finished') }}</span>
</div>

<script>
    (function () {
        // La vista llega por AJAX en cada cambio de semana: los eventos se enlazan una sola vez (delegados)
        // y el estado guardado (contraídos y filtros) se aplica en cada carga.
        const root = document.querySelector('.tsg');
        if (!root) return;
        const scope = root.dataset.tsgScope;
        const store = {
            get(k, d) { try { const v = localStorage.getItem('aceler.timesheet.' + k + '.' + scope); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
            set(k, v) { try { localStorage.setItem('aceler.timesheet.' + k + '.' + scope, JSON.stringify(v)); } catch (e) {} },
        };

        function setCollapsed(group, collapsed) {
            group.classList.toggle('is-collapsed', collapsed);
            const head = group.querySelector('[data-tsg-toggle]');
            if (head) head.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }
        function saveCollapsed() {
            store.set('collapsed', Array.from(document.querySelectorAll('.tsg-group.is-collapsed')).map((g) => g.dataset.tsgGroup));
        }

        function applyFilters() {
            const tsg = document.querySelector('.tsg');
            if (!tsg) return;
            const q = (tsg.querySelector('[data-tsg-search]').value || '').trim().toLowerCase();
            const onlyHours = tsg.querySelector('[data-tsg-filter="hours"]').getAttribute('aria-pressed') === 'true';
            const hideFinished = tsg.querySelector('[data-tsg-filter="finished"]').getAttribute('aria-pressed') === 'true';
            let anyVisible = false;
            tsg.querySelectorAll('.tsg-group').forEach((group) => {
                let groupVisible = false;
                group.querySelectorAll('.tsg-sub').forEach((sub) => {
                    let subVisible = false;
                    const subHidden = hideFinished && sub.dataset.tsgLocked === '1';
                    sub.querySelectorAll('.tsg-row--task').forEach((row) => {
                        const show = !subHidden
                            && (!onlyHours || row.dataset.tsgHours === '1')
                            && (!q || row.dataset.tsgText.indexOf(q) !== -1);
                        row.hidden = !show;
                        if (show) subVisible = true;
                    });
                    sub.hidden = !subVisible;
                    if (subVisible) groupVisible = true;
                });
                group.hidden = !groupVisible;
                // Al buscar se abren los grupos con coincidencias para ver la tarea encontrada
                group.classList.toggle('is-searching', !!q && groupVisible);
                if (groupVisible) anyVisible = true;
            });
            tsg.querySelector('.tsg-empty').hidden = anyVisible;
        }

        // --- estado guardado ---
        const collapsed = store.get('collapsed', []);
        root.querySelectorAll('.tsg-group').forEach((g) => setCollapsed(g, collapsed.indexOf(g.dataset.tsgGroup) !== -1));
        const filters = store.get('filters', {});
        root.querySelectorAll('[data-tsg-filter]').forEach((b) => b.setAttribute('aria-pressed', filters[b.dataset.tsgFilter] ? 'true' : 'false'));
        applyFilters();

        // Cabecera fija por debajo de la barra superior del layout
        const topbar = document.querySelector('.dash-header, header.dash-header, .pc-header');
        if (topbar && getComputedStyle(topbar).position === 'fixed') {
            root.style.setProperty('--tsg-sticky-top', topbar.getBoundingClientRect().height + 'px');
        }

        if (window.__tsgBound) return;
        window.__tsgBound = true;

        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('.tsg [data-tsg-toggle]');
            if (toggle) {
                const group = toggle.closest('.tsg-group');
                setCollapsed(group, !group.classList.contains('is-collapsed'));
                saveCollapsed();
                return;
            }
            const all = e.target.closest('.tsg [data-tsg-all]');
            if (all) {
                document.querySelectorAll('.tsg-group').forEach((g) => setCollapsed(g, all.dataset.tsgAll === 'collapse'));
                saveCollapsed();
                return;
            }
            const pill = e.target.closest('.tsg [data-tsg-filter]');
            if (pill) {
                const on = pill.getAttribute('aria-pressed') !== 'true';
                pill.setAttribute('aria-pressed', on ? 'true' : 'false');
                const f = store.get('filters', {});
                f[pill.dataset.tsgFilter] = on;
                store.set('filters', f);
                applyFilters();
            }
        });
        document.addEventListener('keydown', function (e) {
            if ((e.key === 'Enter' || e.key === ' ') && e.target.matches && e.target.matches('.tsg [data-tsg-toggle]')) {
                e.preventDefault();
                e.target.click();
            }
        });
        document.addEventListener('input', function (e) {
            if (e.target.matches && e.target.matches('.tsg [data-tsg-search]')) applyFilters();
        });
    })();
</script>
