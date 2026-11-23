<style>
    .waitingMilestone {
        border: 2px dashed #bdbdbd !important;
        background-color: #f0f0f0 !important;
        opacity: 0.65;
        filter: grayscale(100%) brightness(0.9);
        position: relative;
    }
</style>

<div class="card milestone-card
        {{ empty($milestone['assined_to_user']) ? 'notAsignedMilestone' : '' }}
        {{ !empty($milestone['is_waiting']) && $milestone['is_waiting'] == 1 ? 'waitingMilestone' : '' }}
        {{ $extraClass ?? '' }}"
    id="{{ $milestone['id'] }}" data-status="{{ $status->id }}" data-project-id="{{ $milestone['project_id'] }}"
    data-project-type-id="{{ $milestone['project_type_id'] ?? '' }}"
    data-project-name="{{ $milestone['project_name'] ?? '' }}"
    data-project-type="{{ strtolower($milestone['project_type'] ?? '') }}"
    data-project-type-label="{{ __($milestone['project_type'] ?? '') }}"
    data-assign-to="{{ $milestone['asiggned_user_data']->id ?? '' }}" data-is-waiting="{{ $milestone['is_waiting'] }}"
    data-priority="{{ strtolower($milestone['priority'] ?? '') }}" data-milestone-title="{{ $milestone['title'] }}"
    style="{{ $inlineStyle ?? '' }}" data-created-by="{{ $milestone['created_by'] ?? '' }}"
    data-requested-by="{{ $milestone['assign_to'] ?? '' }}"
    data-requested-by-name="{{ $milestone['sales']->name ?? '' }}"
    data-assign-to-name="{{ $milestone['asiggned_user_data']->name ?? '' }}"
    data-has-my-tasks="{{ $milestone['has_my_tasks'] ?? 0 }}"
    data-desired-delivery-date="{{ $milestone['end_date'] ?? '' }}"
    data-planned-delivery-date="{{ $milestone['planned_end_date'] ?? '' }}"
    data-completed-date="{{ $milestone['finalization_date'] ?? '' }}"
    data-workspace-name="{{ $milestone['workspace_name'] ?? '' }}"
    data-workspace-slug="{{ $milestone['workspace_slug'] ?? $currentWorkspace->slug }}">

    {{-- ================================== --}}
    {{--  1. PRIORITY BADGE                --}}
    {{-- ================================== --}}
    @if ($milestone['priority'] && $milestone['priority'] !== '')
        @php
            $priorityLower = strtolower($milestone['priority']);
            if ($priorityLower === 'baja' || $priorityLower === 'low') {
                $pClass = 'priority-low';
                $pLabel = 'Prioridad: baja';
            } elseif ($priorityLower === 'media' || $priorityLower === 'medium') {
                $pClass = 'priority-medium';
                $pLabel = 'Prioridad: media';
            } elseif ($priorityLower === 'alta' || $priorityLower === 'high') {
                $pClass = 'priority-high';
                $pLabel = 'Prioridad: alta';
            } else {
                $pClass = 'priority-low';
                $pLabel = 'Prioridad: ' . $milestone['priority'];
            }
        @endphp
        <div class="priority-badge {{ $pClass }}">{{ $pLabel }}</div>
    @endif

    {{-- ================================== --}}
    {{--  2. PROJECT IMAGE                 --}}
    {{-- ================================== --}}
    @php
        $typeLabel = __($milestone['project_type'] ?? '');
        $typeLen = mb_strlen($typeLabel);
        // Textos largos ("Desarrollo I+D", "I+D Development"...): letra algo más pequeña
        $typeSize = $typeLen >= 15 ? 'is-xlong' : ($typeLen >= 12 ? 'is-long' : '');
    @endphp
    <div class="milestone-type">
        <img class="milestone-image"
             src="{{ asset('assets/img/types/' . $milestone['project_type'] . '.svg') }}"
             alt="{{ $milestone['project_name'] }}">
        @if ($typeLabel !== '')
            <span class="milestone-type-label {{ $typeSize }}" title="{{ $typeLabel }}">{{ $typeLabel }}</span>
        @endif
    </div>

    {{-- ================================== --}}
    {{--  4. TITLE                         --}}
    {{-- ================================== --}}
    <div class="milestone-title milestone-title-tooltip"
         data-ajax-popup="true"
         data-title="{{ __('Order form details') }}"
         data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}"
         data-tooltip-title="{{ __('Milestone') }}">
        {{ $milestone['title'] }}
    </div>

    {{-- ================================== --}}
    {{--  5. PHASE + STAGE                 --}}
    {{-- ================================== --}}
    @if (in_array((int) $milestone['project_type_id'], [3, 5], true) && (!empty($milestone['phases']) || !empty($milestone['stage'])))
        <div class="milestone-meta">
            @if (!empty($milestone['phases']))
                @foreach ($milestone['phases'] as $phase)
                    <span><span class="phase-dot"></span> {{ __(\App\Models\MilestonePhases::translationKey($phase)) }}</span>
                @endforeach
            @endif
            @if (!empty($milestone['stage']))
                <span><span class="stage-dot"></span> {{ $milestone['stage'] }}</span>
            @endif
        </div>
    @endif

    {{-- ================================== --}}
    {{--  6. CALENDAR                      --}}
    {{-- ================================== --}}
    @php
        $statusNumber = (int) $status->id;

        if (!function_exists('dateParts')) {
            function dateParts($dateStr) {
                if (!$dateStr || $dateStr === '0000-00-00') return null;
                $dt = new DateTime($dateStr);
                return [$dt->format('d'), $dt->format('M y')];
            }
        }

        // --- Desired date ---
        $desiredParts = dateParts($milestone['end_date'] ?? '');
        if ($desiredParts !== null) {
            [$desiredDay, $desiredMonth] = $desiredParts;
            $target = new DateTime($milestone['end_date']);
            if (!$milestone['finalization_date'] || $milestone['finalization_date'] === '0000-00-00') {
                $isOverdue = new DateTime() > $target;
            } else {
                $isOverdue = new DateTime($milestone['finalization_date']) > $target;
            }
            $desiredClass = $statusNumber <= 2
                ? ($isOverdue ? 'badge-warning' : 'badge-default')
                : ($isOverdue ? 'badge-danger' : 'badge-success');
        } else {
            $desiredDay = null;
            $desiredClass = 'badge-na';
        }

        // --- Proposed date ---
        $proposedParts = dateParts($milestone['planned_end_date'] ?? '');
        if ($proposedParts !== null) {
            [$proposedDay, $proposedMonth] = $proposedParts;
            $target = new DateTime($milestone['planned_end_date']);
            if (!$milestone['finalization_date'] || $milestone['finalization_date'] === '0000-00-00') {
                $isOverdue = new DateTime() > $target;
            } else {
                $isOverdue = new DateTime($milestone['finalization_date']) > $target;
            }
            $proposedClass = $statusNumber <= 2
                ? ($isOverdue ? 'badge-warning' : 'badge-default')
                : ($isOverdue ? 'badge-danger' : 'badge-success');
        } else {
            $proposedDay = null;
            $proposedClass = 'badge-na';
        }

        // --- Completed date ---
        $completedParts = dateParts($milestone['finalization_date'] ?? '');
        if ($completedParts !== null) {
            [$completedDay, $completedMonth] = $completedParts;
            $completed = new DateTime($milestone['finalization_date']);
            $isOverdue = $completed > new DateTime($milestone['end_date'])
                      && $completed > new DateTime($milestone['planned_end_date']);
            $completedClass = $statusNumber <= 2
                ? ($isOverdue ? 'badge-warning' : 'badge-default')
                : ($isOverdue ? 'badge-danger' : 'badge-success');
        } else {
            $completedDay = null;
            $completedClass = 'badge-na';
        }
    @endphp
    <div class="milestone-calendar">
        <div class="calendar-col">
            <span class="calendar-label">{{ __('Desired') }}</span>
            @if ($desiredDay !== null)
                <span class="calendar-badge {{ $desiredClass }}">
                    <span class="badge-day">{{ $desiredDay }}</span>
                    <span class="badge-month">{{ $desiredMonth }}</span>
                </span>
            @else
                <span class="calendar-badge badge-na">—</span>
            @endif
        </div>
        <div class="calendar-col">
            <span class="calendar-label">{{ __('Proposed') }}</span>
            @if ($proposedDay !== null)
                <span class="calendar-badge {{ $proposedClass }}">
                    <span class="badge-day">{{ $proposedDay }}</span>
                    <span class="badge-month">{{ $proposedMonth }}</span>
                </span>
            @else
                <span class="calendar-badge badge-na">—</span>
            @endif
        </div>
        <div class="calendar-col">
            <span class="calendar-label">{{ __('Completed') }}</span>
            @if ($completedDay !== null)
                <span class="calendar-badge {{ $completedClass }}">
                    <span class="badge-day">{{ $completedDay }}</span>
                    <span class="badge-month">{{ $completedMonth }}</span>
                </span>
            @else
                <span class="calendar-badge badge-na">—</span>
            @endif
        </div>
    </div>

    {{-- ================================== --}}
    {{--  7. TASK LIST                     --}}
    {{-- ================================== --}}
    @php $taskCount = count($milestone['tasks'] ?? []); @endphp
    <div class="milestone-task-list" id="taskList-{{ $milestone['id'] }}">
        @if ($taskCount > 0)
            <div class="milestone-task-box" id="box-{{ $milestone['id'] }}">
                <div class="milestone-task-inner">
                    @foreach ($milestone['tasks'] as $i => $task)
                        @php
                            $reviewState = $task['review_state'] ?? null;
                            $reviewClass = match ($reviewState) {
                                'reviewed' => 'task-reviewed',
                                'changes'  => 'task-changes',
                                default    => '',
                            };
                        @endphp
                        <div class="milestone-task tooltipCusTask {{ $i > 1 ? 'milestone-task-extra' : '' }} {{ $reviewClass }} {{ ($status->id == 1 && (int) ($milestone['project_type_id'] ?? 0) === 3) ? 'task-inactive' : '' }}" role="button"
                             data-task-id="{{ $task['id'] }}"
                             data-task-name="{{ $task['display_name'] ?? $task['name'] }}"
                             data-milestone-id="{{ $milestone['id'] }}"
                             data-project-id="{{ $milestone['project_id'] }}"
                             data-project-name="{{ $milestone['project_name'] }}"
                             data-technician-name="{{ $task['technician']->id }}"
                             data-url="{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}"
                             data-ajax-timesheet-popup="true"
                             data-tooltip-content="{{ $task['technician']->name }} - {{ __('Total imputed') }}: {{ $task['logged_hours'] }}">
                            <i class="ms-2 me-2 fa-solid fa-hourglass-start fa-xs"></i>
                            <span class="milestone-task-title">{{ __($task['display_name'] ?? $task['name']) }}</span>
                            @if ($reviewState === 'changes' && in_array((int) $status->id, [2, 3], true))
                                <button type="button" class="task-ack-btn"
                                        data-task-id="{{ $task['id'] }}"
                                        data-task-name="{{ $task['display_name'] ?? $task['name'] }}"
                                        data-review-comment="{{ $task['review_comment'] ?? '' }}"
                                        data-review-user="{{ $task['review_user'] ?? '' }}">
                                    {{ __('Revisar') }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($taskCount > 2)
                    <span class="milestone-dropdown-toggle" data-target="taskList-{{ $milestone['id'] }}">&#9660;</span>
                @endif
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-clipboard-list icon"></i>
                <span class="title">{{ __('No tasks in progress') }}...</span>
            </div>
        @endif
    </div>

    {{-- ================================== --}}
    {{--  9. FOOTER AVATARS                --}}
    {{-- ================================== --}}
    <div class="milestone-footer">
        <div class="milestone-avatars" id="milestoneReqName"
             data-technician-id="{{ $milestone['asiggned_user_data']->id ?? '' }}"
             data-project-name="{{ $milestone['project_name'] }}"
             data-milestone-id="{{ $milestone['id'] }}">
            <img alt="image" class="user-groupTasks tooltipCus"
                 title="{{ __('Requested by') }} {{ $milestone['sales']->name ?? 'N/A' }}"
                 @if ($milestone['sales']->avatar)
                     src="{{ asset($milestone['sales']->avatar) }}"
                 @else
                     avatar="{{ $milestone['sales']->name }}"
                 @endif>
            @if (isset($milestone['asiggned_user_data']))
                <img alt="image" class="user-groupTasks tooltipCus"
                     title="{{ __('Assigned to') }} {{ $milestone['asiggned_user_data']->name }}"
                     @if ($milestone['asiggned_user_data']->avatar)
                         src="{{ asset($milestone['asiggned_user_data']->avatar) }}"
                     @else
                         avatar="{{ $milestone['asiggned_user_data']->name }}"
                     @endif>
            @endif
        </div>
    </div>

    {{-- ================================== --}}
    {{--  9. PIE: estado + proyecto        --}}
    {{-- ================================== --}}
    {{-- replaceMilestoneExtraInfo() sustituye solo .milestone-extra-info: el proyecto se mantiene --}}
    <div class="milestone-foot">
        @include('projects.partials.milestone_extra_info', ['milestone' => $milestone, 'status' => $status])
        <div class="milestone-project" title="{{ $milestone['project_name'] }}">
            <i class="fa-solid fa-diagram-project"></i>
            <span class="milestone-project-viewport"><span class="milestone-project-track"><span class="milestone-project-text">{{ $milestone['project_name'] }}</span></span></span>
        </div>
    </div>

    {{-- ================================== --}}
    {{--  10. THREE-DOT MENU               --}}
    {{-- ================================== --}}
    <div class="card-header-right">
        <div class="btn-group card-option">
            @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')
                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-ellipsis-h"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="#" class="dropdown-item" data-ajax-popup="true"
                       data-title="{{ __('Order form details') }}"
                       data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}">
                        <i class="ti ti-eye"></i> {{ __('View') }}
                    </a>
                    @if ($milestone['is_waiting'])
                        <a href="#" class="dropdown-item"
                           onclick="event.preventDefault(); document.getElementById('resume-milestone-{{ $milestone['id'] }}').submit();">
                            <i class="fa-solid fa-play"></i> {{ __('Resume Milestone') }}
                        </a>
                        <form id="resume-milestone-{{ $milestone['id'] }}"
                              action="{{ route('projects.milestone.resume', [$currentWorkspace->slug, $milestone['id']]) }}"
                              method="POST" style="display:none;">
                            @csrf
                        </form>
                    @else
                        <a href="#" class="dropdown-item" data-ajax-popup="true"
                           data-title="{{ __('Assign Milestone') }}"
                           data-url="{{ route('projects.milestone.assign', [$currentWorkspace->slug, $milestone['id']]) }}">
                            <i class="fa-solid fa-user-plus"></i> {{ __('Assign Milestone') }}
                        </a>
                        @if ($status->id != 1 || ((int) ($milestone['project_type_id'] ?? 0) === 3 && $status->id == 1))
                            @php
                                $isMyMilestoneBoardUrl = strpos(request()->url(), 'my-milestone-board') !== false;
                                $taskCreateRoute = $isMyMilestoneBoardUrl
                                    ? route('my_milestone.tasks.create', $currentWorkspace->slug)
                                    : route('tasks.create', $currentWorkspace->slug);
                                $taskCreateRoute .= '?project_id=' . $milestone['project_id'] . '&projectName=' . urlencode($milestone['project_name'] ?? '') . '&milestoneTitle=' . urlencode($milestone['title']) . '&milestone_id=' . $milestone['id'] . '&fromMyMilestoneBoard=' . ($isMyMilestoneBoardUrl ? 1 : 0);
                            @endphp
                            <a href="#" class="dropdown-item" data-ajax-popup="true"
                               data-title="{{ __('Add Task') }}" data-url="{{ $taskCreateRoute }}">
                                <i class="fas fa-tasks"></i> {{ __('Add Task') }}
                            </a>
                        @endif
                        <a href="#" class="dropdown-item"
                           onclick="event.preventDefault(); openPauseMilestoneModal({{ $milestone['id'] }}, '{{ $currentWorkspace->slug }}');">
                            <i class="fa-regular fa-circle-pause"></i> {{ __('Wait Milestone') }}
                        </a>
                        <form id="wait-milestone-{{ $milestone['id'] }}"
                              action="{{ route('projects.milestone.wait', [$currentWorkspace->slug, $milestone['id']]) }}"
                              method="POST" style="display:none;">
                            @csrf
                        </form>
                        @if ($currentWorkspace->permission == 'Owner' || ($currentWorkspace->permission == 'Member' && Auth::user()->type == 'user'))
                            <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="lg"
                               data-title="{{ __('Edit Milestone') }}"
                               data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone['id']]) }}">
                                <i class="ti ti-edit"></i>{{ __('Edit') }}
                            </a>
                            <a href="#" class="dropdown-item bs-pass-para"
                               data-confirm="{{ __('Are You Sure?') }}"
                               data-text="{{ __('This action cannot be undone.') }}"
                               data-confirm-yes="delete-form-{{ $milestone['id'] }}">
                                <i class="ti ti-trash"></i> {{ __('Delete') }}
                            </a>
                            <form id="delete-form-{{ $milestone['id'] }}" method="POST"
                                  action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone['id']]) }}"
                                  style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<span class="empty-container" data-placeholder="Empty"></span>

{{-- Modal para pausa de milestone con comentario --}}
<div class="modal fade" id="pauseMilestoneModal" tabindex="-1" role="dialog"
     aria-labelledby="pauseMilestoneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pauseMilestoneModalLabel">{{ __('Pause Milestone') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pauseMilestoneForm" method="POST" style="display:none;">@csrf</form>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pauseComment">{{ __('Pause Reason / Note') }}</label>
                    <textarea class="form-control" id="pauseComment" name="pause_comment" rows="4"
                              placeholder="{{ __('Enter the reason for pausing this milestone...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="submitPauseMilestone()">{{ __('Pause') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openPauseMilestoneModal(milestoneId, slug) {
        document.getElementById('pauseMilestoneModal').dataset.milestoneId = milestoneId;
        document.getElementById('pauseMilestoneModal').dataset.slug = slug;
        document.getElementById('pauseComment').value = '';
        $('#pauseMilestoneModal').modal('show');
    }

    function submitPauseMilestone() {
        const milestoneId = document.getElementById('pauseMilestoneModal').dataset.milestoneId;
        const slug = document.getElementById('pauseMilestoneModal').dataset.slug;
        const comment = document.getElementById('pauseComment').value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('projects.milestone.wait', [':slug', ':id']) }}'.replace(':slug', slug).replace(':id', milestoneId);
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        const commentInput = document.createElement('input');
        commentInput.type = 'hidden';
        commentInput.name = 'pause_comment';
        commentInput.value = comment;
        form.appendChild(commentInput);
        document.body.appendChild(form);
        form.submit();
        $('#pauseMilestoneModal').modal('hide');
    }

    function toggleMilestoneTaskList(toggle) {
        var taskList = document.getElementById(toggle.getAttribute('data-target'));
        if (!taskList) return;
        var inner = taskList.querySelector('.milestone-task-inner');
        if (!inner) return;
        var isExpanded = taskList.classList.toggle('expanded');
        inner.style.maxHeight = isExpanded ? (inner.scrollHeight + 'px') : '52px';
    }

    // Partes de la caja que conservan su propia acción (abrir la imputación, revisar, la flecha)
    var TASK_BOX_OWN_ACTIONS = '.milestone-task-title, .milestone-task > i, .task-ack-btn, .milestone-dropdown-toggle, a, button';

    function bindMilestoneDropdownToggles(scope) {
        var containers = scope && scope.querySelectorAll ?
            scope.querySelectorAll('.milestone-dropdown-toggle') :
            document.querySelectorAll('.milestone-dropdown-toggle');
        containers.forEach(function(toggle) {
            if (toggle.getAttribute('data-bound') === '1') return;
            toggle.setAttribute('data-bound', '1');
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMilestoneTaskList(toggle);
            });

            // Toda la caja de tareas despliega/pliega, salvo el nombre de cada tarea
            // (abre la imputación) y los botones. Se captura antes que el clic de la tarea.
            var box = toggle.closest('.milestone-task-box') ||
                (document.getElementById(toggle.getAttribute('data-target')) || {}).querySelector?.('.milestone-task-box');
            if (!box || box.getAttribute('data-toggle-bound') === '1') return;
            box.setAttribute('data-toggle-bound', '1');
            box.classList.add('is-toggleable');
            box.addEventListener('click', function(e) {
                var current = box.closest('.milestone-task-list')?.querySelector('.milestone-dropdown-toggle');
                if (!current || e.target.closest(TASK_BOX_OWN_ACTIONS)) return;
                e.preventDefault();
                e.stopPropagation();
                toggleMilestoneTaskList(current);
            }, true);
        });
    }

    window.bindMilestoneTaskTooltip = function(taskEl) {
        if (!taskEl || taskEl.getAttribute('data-tooltip-bound') === '1') return;
        taskEl.setAttribute('data-tooltip-bound', '1');
        taskEl.addEventListener('mouseenter', function() {
            var content = this.getAttribute('data-tooltip-content');
            if (!content) return;
            if (window.__milestoneActiveTooltip) window.__milestoneActiveTooltip.remove();
            var tooltip = document.createElement('div');
            tooltip.className = 'tooltipTaskContent visible';
            tooltip.textContent = content;
            document.body.appendChild(tooltip);
            var rect = this.getBoundingClientRect();
            var top = rect.top - tooltip.offsetHeight - 6;
            var left = rect.left + (rect.width - tooltip.offsetWidth) / 2;
            tooltip.style.top = Math.max(4, top) + 'px';
            tooltip.style.left = Math.max(4, left) + 'px';
            window.__milestoneActiveTooltip = tooltip;
        });
        taskEl.addEventListener('mouseleave', function() {
            if (window.__milestoneActiveTooltip) {
                window.__milestoneActiveTooltip.remove();
                window.__milestoneActiveTooltip = null;
            }
        });
    };

    // --- Nombre del proyecto en el pie: si no cabe, marca is-overflowing y calcula el desplazamiento
    //     que usa la animación al pasar el ratón por la card (milestoneboard.css) ---
    if (!window.__milestoneProjectScroll) {
        window.__milestoneProjectScroll = true;

        // Solo se desplaza el texto: el icono queda fijo fuera de .milestone-project-viewport
        // Desplazamiento en un solo sentido y en bucle: una copia del texto lo sigue a 24px
        // (gap de .milestone-project-track); al avanzar ancho + 24px la copia ocupa el sitio
        // del original y la animación vuelve a empezar sin salto.
        var MARQUEE_GAP = 24;
        var measureMilestoneProject = function(el) {
            var txt = el.querySelector('.milestone-project-text:not(.milestone-project-clone)');
            var track = el.querySelector('.milestone-project-track');
            var viewport = el.querySelector('.milestone-project-viewport');
            if (!txt || !track || !viewport) return;
            var visible = viewport.clientWidth;
            var width = txt.offsetWidth;
            var overflowing = visible > 0 && width - visible > 1;
            el.classList.toggle('is-overflowing', overflowing);
            if (!overflowing) return;
            if (!track.querySelector('.milestone-project-clone')) {
                var clone = txt.cloneNode(true);
                clone.classList.add('milestone-project-clone');
                clone.setAttribute('aria-hidden', 'true');
                track.appendChild(clone);
            }
            var shift = width + MARQUEE_GAP;
            el.style.setProperty('--scroll-shift', shift + 'px');
            // ~35px por segundo (+12% de pausa al inicio de cada vuelta)
            el.style.setProperty('--scroll-time', Math.max(4, shift / 35 / 0.88).toFixed(1) + 's');
        };

        var observer = window.ResizeObserver ? new ResizeObserver(function(entries) {
            entries.forEach(function(entry) { measureMilestoneProject(entry.target); });
        }) : null;

        var watchMilestoneProjects = function(scope) {
            (scope || document).querySelectorAll('.milestone-project:not([data-scroll-watch])').forEach(function(el) {
                el.setAttribute('data-scroll-watch', '1');
                measureMilestoneProject(el);
                if (observer) observer.observe(el);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { watchMilestoneProjects(); });
        } else {
            watchMilestoneProjects();
        }
        window.addEventListener('load', function() { watchMilestoneProjects(); });

        // Cards añadidas o movidas después de cargar: se mide al pasar el ratón
        document.addEventListener('mouseover', function(e) {
            var card = e.target && e.target.closest ? e.target.closest('.milestone-card') : null;
            if (!card) return;
            var el = card.querySelector('.milestone-project');
            if (!el) return;
            if (!el.hasAttribute('data-scroll-watch')) watchMilestoneProjects(card);
            else measureMilestoneProject(el);
        });
    }

    // --- Shared helpers (definidos una vez; usados por milestoneboard y my_milestone_board) ---
    if (typeof window.replaceMilestoneExtraInfo !== 'function') {
        window.replaceMilestoneExtraInfo = function(card, html) {
            if (!html) return;
            var existing = card.querySelector('.milestone-extra-info');
            var foot = card.querySelector('.milestone-foot');
            if (existing) {
                existing.outerHTML = html;
            } else if (foot) {
                foot.insertAdjacentHTML('afterbegin', html);
            } else {
                card.insertAdjacentHTML('beforeend', html);
            }
        };
    }

    if (typeof window.moveMilestoneCardToStatus !== 'function') {
        window.moveMilestoneCardToStatus = function(card, statusId) {
            var currentStatus = card.getAttribute('data-status');
            if (String(statusId) === String(currentStatus)) return;
            var oldBox = card.closest('.kanban-box');
            var newBox = document.querySelector(".kanban-box[data-status='" + statusId + "']");
            if (!oldBox || !newBox) return;
            card.setAttribute('data-status', statusId);
            newBox.appendChild(card);
            [oldBox, newBox].forEach(function(box) {
                var cardList = box.closest('.card-list');
                if (!cardList) return;
                var countEl = cardList.querySelector('.count');
                var total = box.querySelectorAll(':scope > .card').length;
                if (countEl) countEl.textContent = total;
                var emptyStateEl = box.querySelector('.noNotificationsContainer');
                if (emptyStateEl) emptyStateEl.style.display = total > 0 ? 'none' : '';
            });
        };
    }

    if (typeof window.getTargetExtraInfo !== 'function') {
        window.getTargetExtraInfo = function(card, statusId) {
            var s = parseInt(statusId);
            var isWaiting = card.getAttribute('data-is-waiting') === '1';

            if (isWaiting) {
                return '<div class="milestone-extra-info milestone-extra-info--paused">'
                    + '<i class="far fa-pause-circle"></i>'
                    + '<span>{{ __("Encargo pausado") }}</span>'
                    + '<a href="#" class="milestone-resume-btn" onclick="event.preventDefault();document.getElementById(\'resume-milestone-' + card.id + '\').submit();">{{ __("Activar") }}</a>'
                    + '</div>';
            }

            if (s === 1) {
                return '<div class="milestone-extra-info">'
                    + '<i class="fa-solid fa-arrows-alt"></i>'
                    + '<span>{{ __("Mover a en curso") }}</span>'
                    + '</div>';
            }

            if (s === 2) {
                var tasks = card.querySelectorAll('.milestone-task');
                var hasChanges = !!card.querySelector('.task-changes');
                if (hasChanges) {
                    return '<div class="milestone-extra-info milestone-extra-info--changes">'
                        + '<i class="fa-solid fa-triangle-exclamation"></i>'
                        + '<span>{{ __("Tareas por corregir") }}</span>'
                        + '</div>';
                }
                var statusClass2 = '';
                var statusIcon2 = 'fa-solid fa-arrow-circle-right';
                var statusLabel2 = '{{ __("Mover a revisión") }}';
                if (tasks.length === 0) {
                    statusClass2 = 'milestone-extra-info--no-tasks';
                    statusIcon2 = 'fa-solid fa-exclamation-triangle';
                    statusLabel2 = '{{ __("Añadir tareas") }}';
                } else {
                    var allHaveHours = true;
                    for (var i = 0; i < tasks.length; i++) {
                        var tip = tasks[i].getAttribute('data-tooltip-content') || '';
                        if (/[: ]00:00\s*$/.test(tip)) {
                            allHaveHours = false;
                            break;
                        }
                    }
                    if (!allHaveHours) {
                        statusClass2 = 'milestone-extra-info--hours';
                        statusIcon2 = 'far fa-clock';
                        statusLabel2 = '{{ __("Imputar horas") }}';
                    }
                }
                return '<div class="milestone-extra-info milestone-extra-info--ready ' + statusClass2 + '">'
                    + '<i class="' + statusIcon2 + '"></i>'
                    + '<span>' + statusLabel2 + '</span>'
                    + '</div>';
            }

            if (s === 3) {
                return '<div class="milestone-extra-info milestone-extra-info--orange">'
                    + '<i class="fa-solid fa-magnifying-glass"></i>'
                    + '<span>{{ __("Encargo en revisión") }}</span>'
                    + '</div>';
            }

            if (s === 4) {
                var completed = card.getAttribute('data-completed-date') || '';
                var desired   = card.getAttribute('data-desired-delivery-date') || '';
                var isLate = false;
                if (desired && desired !== '0000-00-00') {
                    if (completed && completed !== '0000-00-00') {
                        isLate = new Date(completed) > new Date(desired);
                    } else {
                        isLate = new Date() > new Date(desired);
                    }
                }
                var cls  = 'milestone-extra-info milestone-extra-info--check' + (isLate ? ' milestone-check-late' : '');
                var lbl  = isLate ? '{{ __("Entregado fuera de plazo") }}' : '{{ __("Entregado en plazo") }}';
                return '<div class="' + cls + '">'
                    + '<i class="far fa-check-circle"></i>'
                    + '<span>' + lbl + '</span>'
                    + '</div>';
            }

            return null;
        };
    }

    if (typeof window.milestoneBoardAppendTask !== 'function') {
        window.milestoneBoardAppendTask = function(response) {
            if (!response || !response.milestone_id) return;

            var card = document.getElementById(String(response.milestone_id));
            if (!card) return;

            var taskListEl = document.getElementById('taskList-' + response.milestone_id);
            if (!taskListEl) return;

            var reviewClass = response.review_state === 'reviewed' ? 'task-reviewed' : (response.review_state === 'changes' ? 'task-changes' : '');
            var inactiveClass = (String(response.status) === '1' && String(response.project_type_id) === '3') ? 'task-inactive' : '';
            var extraClass = response.task_count > 2 ? 'milestone-task-extra' : '';
            var technicianName = response.technician_name || '';
            var tooltipContent = technicianName + ' - {{ __("Total imputed") }}: ' + (response.logged_hours || '00:00');
            var timesheetBaseUrl = '{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, ':PID']) }}';

            var taskEl = document.createElement('div');
            taskEl.className = 'milestone-task tooltipCusTask ' + extraClass + ' ' + reviewClass + ' ' + inactiveClass;
            taskEl.setAttribute('role', 'button');
            taskEl.setAttribute('data-task-id', response.task_id);
            taskEl.setAttribute('data-task-name', response.display_name || '');
            taskEl.setAttribute('data-milestone-id', response.milestone_id);
            taskEl.setAttribute('data-project-id', response.project_id);
            taskEl.setAttribute('data-technician-name', response.technician_id);
            taskEl.setAttribute('data-url', timesheetBaseUrl.replace(':PID', response.project_id));
            taskEl.setAttribute('data-ajax-timesheet-popup', 'true');
            taskEl.setAttribute('data-tooltip-content', tooltipContent);

            var icon = document.createElement('i');
            icon.className = 'ms-2 me-2 fa-solid fa-hourglass-start fa-xs';
            var nameSpan = document.createElement('span');
            nameSpan.className = 'milestone-task-title';
            nameSpan.textContent = response.display_name || '';

            taskEl.appendChild(icon);
            taskEl.appendChild(nameSpan);

            // Quitar el estado vacío si existía
            var emptyState = taskListEl.querySelector('.empty-state');
            if (emptyState) emptyState.remove();

            var taskBox = taskListEl.querySelector('.milestone-task-box');
            if (!taskBox) {
                taskBox = document.createElement('div');
                taskBox.className = 'milestone-task-box';
                taskBox.id = 'box-' + response.milestone_id;
                var inner = document.createElement('div');
                inner.className = 'milestone-task-inner';
                taskBox.appendChild(inner);
                taskListEl.appendChild(taskBox);
            }
            var innerEl = taskBox.querySelector('.milestone-task-inner');
            innerEl.appendChild(taskEl);

            // Mostrar/ocultar toggle de colapso según el nº de tareas
            var toggle = taskListEl.querySelector('.milestone-dropdown-toggle');
            if (response.task_count > 2) {
                if (!toggle) {
                    toggle = document.createElement('span');
                    toggle.className = 'milestone-dropdown-toggle';
                    toggle.setAttribute('data-target', 'taskList-' + response.milestone_id);
                    toggle.innerHTML = '&#9660;';
                    taskListEl.appendChild(toggle);
                }
            } else if (toggle) {
                toggle.remove();
            }

            // Refrescar el aviso inferior de la tarjeta
            window.replaceMilestoneExtraInfo(card, response.milestone_extra_info);

            // Si el status del encargo cambió, mover la tarjeta a la columna correcta
            window.moveMilestoneCardToStatus(card, response.status);

            // Re-enlazar eventos del nuevo elemento
            if (typeof window.bindMilestoneTaskClick === 'function') window.bindMilestoneTaskClick(taskEl);
            if (typeof window.bindMilestoneTaskTooltip === 'function') window.bindMilestoneTaskTooltip(taskEl);
            if (typeof window.bindMilestoneDropdownToggles === 'function') window.bindMilestoneDropdownToggles(taskListEl);
        };
    }

    if (typeof window.milestoneBoardUpdateTaskHours !== 'function') {
        window.milestoneBoardUpdateTaskHours = function(response) {
            if (!response || !response.milestone_id) return;

            var card = document.getElementById(String(response.milestone_id));
            if (!card) return;

            // Actualizar el tooltip de la tarea con las horas imputadas
            if (response.task_id) {
                var taskEl = card.querySelector('.milestone-task[data-task-id="' + response.task_id + '"]');
                if (taskEl) {
                    var technicianName = response.technician_name || '';
                    taskEl.setAttribute('data-tooltip-content', technicianName + ' - {{ __("Total imputed") }}: ' + (response.logged_hours || '00:00'));
                }
            }

            // Refrescar el aviso inferior de la tarjeta
            window.replaceMilestoneExtraInfo(card, response.milestone_extra_info);

            // Si el status del encargo cambió, mover la tarjeta a la columna correcta
            window.moveMilestoneCardToStatus(card, response.status);
        };
    }

    if (typeof window.updateTaskReviewState !== 'function') {
        window.updateTaskReviewState = function(response) {
            if (!response || !response.task_id) return;

            var taskEl = document.querySelector('.milestone-task[data-task-id="' + response.task_id + '"]');
            if (!taskEl) return;

            // 1. Actualizar clases de estado
            taskEl.classList.remove('task-reviewed', 'task-changes');
            if (response.review_state !== 'cleared') {
                taskEl.classList.add('task-' + response.review_state); // 'task-reviewed' o 'task-changes'
            }

            // 2. Mostrar/ocultar/crear botón "Revisar"
            var ackBtn = taskEl.querySelector('.task-ack-btn');
            if (response.show_revisar_btn) {
                if (!ackBtn) {
                    // Crear botón dinámicamente si no existe
                    ackBtn = document.createElement('button');
                    ackBtn.type = 'button';
                    ackBtn.className = 'task-ack-btn';
                    ackBtn.setAttribute('data-task-id', response.task_id);
                    ackBtn.setAttribute('data-task-name', taskEl.getAttribute('data-task-name') || '');
                    // review-comment y review-user vienen en la respuesta AJAX para 'changes'
                    if (response.review_state === 'changes') {
                        ackBtn.setAttribute('data-review-comment', response.review_comment || '');
                        ackBtn.setAttribute('data-review-user', response.review_user || '');
                    }
                    ackBtn.textContent = '{{ __("Revisar") }}';
                    taskEl.appendChild(ackBtn);
                }
                ackBtn.style.display = '';
            } else if (ackBtn) {
                ackBtn.style.display = 'none';
            }

            // 3. Actualizar extra-info del milestone
            var card = taskEl.closest('.milestone-card') || taskEl.closest('.card');
            if (card && response.milestone_extra_info) {
                window.replaceMilestoneExtraInfo(card, response.milestone_extra_info);
            }

            // 4. Re-bind tooltip si existe
            if (typeof window.bindMilestoneTaskTooltip === 'function') {
                window.bindMilestoneTaskTooltip(taskEl);
            }
        };
    }

    bindMilestoneDropdownToggles(document);
    document.querySelectorAll('.milestone-task[data-tooltip-content]').forEach(window.bindMilestoneTaskTooltip);
</script>
