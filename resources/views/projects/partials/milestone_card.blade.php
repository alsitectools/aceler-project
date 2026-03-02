<style>
    .waitingMilestone {
        border: 2px dashed #bdbdbd !important;
        background-color: #f0f0f0 !important;
        opacity: 0.65;
        filter: grayscale(100%) brightness(0.9);
        position: relative;
    }

    .phasesDiv {
        min-width: 60%;
        height: 55%;
        border-radius: 6px;
        align-content: center;
        background-color: #493d3f;
        font-size: 12.5px;
        display: inline-block;
        color: white;
        padding: 6px;
        font-weight: 600;
    }

    .stageDiv {
        min-width: 60%;
        border-radius: 6px;
        align-content: center;
        background-color: rgb(25 24 24 / 35%);
        font-size: 12px;
        display: inline-block;
        color: white;
        padding: 5px 8px;
        font-weight: 600;
        margin-top: 4px;
    }

    .centerPhaseLabel {
        justify-content: center;
        align-items: center;
        align-content: center;
        display: flex;
    }

    .dateDiv {
        flex: 0 0 auto;
        width: 40%;
    }

    .fatherDateDivAlign {
        display: flex;
        justify-content: center;
        align-items: center;
        align-content: center;
    }
</style>

{{-- aqui esta el dedeo --}}
{{-- @dump($milestone) --}}

<div class="card 
        {{ empty($milestone['assined_to_user']) ? 'notAsignedMilestone' : '' }} 
        {{ !empty($milestone['is_waiting']) && $milestone['is_waiting'] == 1 ? 'waitingMilestone' : '' }}
        {{ $extraClass ?? '' }}"
    id="{{ $milestone['id'] }}" data-status="{{ $status->id }}" data-project-id="{{ $milestone['project_id'] }}"
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


    {{-- ========================= --}}
    {{--   HEADER DEL MILESTONE   --}}
    {{-- ========================= --}}
    <div class="card-header border-0 pb-0 col-sm-12">
        <div class="d-flex">
            <div class="col-sm-9 text-center tooltipCus" data-title="{{ __('Milestone') }}">
                <b class="mileTitle cursor-pointer" id="milestoneTitleForNotification"
                    data-header="{{ $milestone['title'] }}" data-milestone-id="{{ $milestone['id'] }}"
                    data-is-waiting="{{ $milestone['is_waiting'] }}"
                    data-project-slug="{{ $milestone['workspace_slug'] ?? $currentWorkspace->slug }}">
                    {{ $milestone['title'] }}
                </b>

                {{-- Mostrar Phase si es proyecto tipo 3 o 5 --}}
                @if (in_array((int) $milestone['project_type_id'], [3, 5], true) && !empty($milestone['phases']))
                    <div class="centerPhaseLabel" style="margin-top: 5px;">
                        @foreach ($milestone['phases'] as $phase)
                            <span class="phasesDiv">{{ $phase }}</span>
                        @endforeach
                    </div>

                    @if (!empty($milestone['stage']))
                        <div class="centerPhaseLabel">
                            <span class="stageDiv">{{ $milestone['stage'] }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <div class="col-sm-2 pt-1 text-center">
                <a href="#" id="milestoneReqName" data-milestone-id="{{ $milestone['id'] }}"
                    data-technician-id="{{ $milestone['assign_to'] }}"
                    data-project-name="{{ $milestone['project_name'] }}">

                    {{-- Avatar del usuario que lo solicitó --}}
                    <img alt="image" class="user-groupTasks tooltipCus"
                        title="{{ __('Requested by') }} {{ $milestone['sales']->name ?? 'N/A' }}"
                        style="margin-top:-10px;"
                        @if ($milestone['sales']->avatar) src="{{ asset($milestone['sales']->avatar) }}"
                         @else
                             avatar="{{ $milestone['sales']->name }}" @endif>

                    {{-- Avatar del usuario asignado --}}
                    @if (isset($milestone['asiggned_user_data']))
                        <img alt="image" class="user-groupTasks tooltipCus"
                            title="{{ __('Assigned to') }} {{ $milestone['asiggned_user_data']->name }}"
                            style="margin-top:-10px;"
                            @if ($milestone['asiggned_user_data']->avatar) src="{{ asset($milestone['asiggned_user_data']->avatar) }}"
                             @else
                                 avatar="{{ $milestone['asiggned_user_data']->name }}" @endif>
                    @endif
                </a>
            </div>
        </div>

        <hr class="border border-2 opacity-50">

        {{-- Menú de opciones --}}
        <div class="card-header-right col-sm-1 text-end">
            <div class="btn-group card-option">
                @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')

                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="feather icon-more-vertical"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">

                        {{-- ALWAYS AVAILABLE: VIEW --}}
                        <a href="#" class="dropdown-item" data-ajax-popup="true"
                            data-title="{{ __('Order form details') }}"
                            data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}">
                            <i class="ti ti-eye"></i> {{ __('View') }}
                        </a>

                        @if ($milestone['is_waiting'])
                            {{-- ====================================================== --}}
                            {{--   MILESTONE EN ESPERA → SOLO MOSTRAR "RESUME"         --}}
                            {{-- ====================================================== --}}

                            <a href="#" class="dropdown-item"
                                onclick="event.preventDefault(); document.getElementById('resume-milestone-{{ $milestone['id'] }}').submit();">
                                <i class="fa-solid fa-play"></i>
                                {{ __('Resume Milestone') }}
                            </a>

                            <form id="resume-milestone-{{ $milestone['id'] }}"
                                action="{{ route('projects.milestone.resume', [$currentWorkspace->slug, $milestone['id']]) }}"
                                method="POST" style="display:none;">
                                @csrf
                            </form>
                        @else
                            {{-- ================================================================= --}}
                            {{--   MILESTONE NORMAL → TODAS LAS OPCIONES + PAUSAR                 --}}
                            {{-- ================================================================= --}}

                            {{-- Asignar --}}
                            <a href="#" class="dropdown-item" data-ajax-popup="true"
                                data-title="{{ __('Assign Milestone') }}"
                                data-url="{{ route('projects.milestone.assign', [$currentWorkspace->slug, $milestone['id']]) }}">
                                <i class="fa-solid fa-user-plus"></i>
                                {{ __('Assign Milestone') }}
                            </a>

                            {{-- Crear Task --}}
                            @php
                                $isMyMilestoneBoardUrl = strpos(request()->url(), 'my-milestone-board') !== false;
                                $taskCreateRoute = $isMyMilestoneBoardUrl 
                                    ? route('my_milestone.tasks.create', $currentWorkspace->slug)
                                    : route('tasks.create', $currentWorkspace->slug);
                                
                                // Agregar parámetros a la ruta
                                $taskCreateRoute .= '?project_id=' . $milestone['project_id'] 
                                    . '&projectName=' . urlencode($milestone['project_name'] ?? '')
                                    . '&milestoneTitle=' . urlencode($milestone['title'])
                                    . '&milestone_id=' . $milestone['id']
                                    . '&fromMyMilestoneBoard=' . ($isMyMilestoneBoardUrl ? 1 : 0);
                            @endphp
                            <a href="#" class="dropdown-item" data-ajax-popup="true"
                                data-title="{{ __('Add Task') }}"
                                data-url="{{ $taskCreateRoute }}"
                                >
                                <i class="fas fa-tasks"></i>
                                {{ __('Add Task') }}
                            </a>

                            {{-- Pausar --}}
                            <a href="#" class="dropdown-item"
                                onclick="event.preventDefault(); openPauseMilestoneModal({{ $milestone['id'] }}, '{{ $currentWorkspace->slug }}');">
                                <i class="fa-regular fa-circle-pause"></i>
                                {{ __('Wait Milestone') }}
                            </a>

                            <form id="wait-milestone-{{ $milestone['id'] }}"
                                action="{{ route('projects.milestone.wait', [$currentWorkspace->slug, $milestone['id']]) }}"
                                method="POST" style="display:none;">
                                @csrf
                            </form>

                            {{-- Editar / Borrar --}}
                            @if (
                                $currentWorkspace->permission == 'Owner' ||
                                    ($currentWorkspace->permission == 'Member' && Auth::user()->type == 'user'))
                                {{-- Edit --}}
                                <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="lg"
                                    data-title="{{ __('Edit Milestone') }}"
                                    data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone['id']]) }}">
                                    <i class="ti ti-edit"></i>{{ __('Edit') }}
                                </a>

                                {{-- Delete --}}
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

    {{-- ================================== --}}
    {{--      BODY - LISTA DE TAREAS        --}}
    {{-- ================================== --}}
    <div class="card-body pt-1">
        <div class="row">
            @if ($milestone['tasks'])
                <div class="col-sm-12 p-3">
                    @foreach ($milestone['tasks'] as $task)
                        <div class="taskList tooltipCusTask p-target mb-2 col-sm-12 marginText" role="button"
                            data-task-id="{{ $task['id'] }}"
                            data-task-name="{{ $task['display_name'] ?? $task['name'] }}"
                            data-milestone-id="{{ $milestone['id'] }}"
                            data-project-id="{{ $milestone['project_id'] }}"
                            data-project-name="{{ $milestone['project_name'] }}"
                            data-technician-name="{{ $task['technician']->id }}"
                            data-url="{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}"
                            data-ajax-timesheet-popup="true">

                            <i class="ms-2 me-2 fa-solid fa-hourglass-start fa-xs" style="color:black;"></i>
                            {{ __($task['display_name'] ?? $task['name']) }}

                            <div class="tooltipTaskContent">
                                <strong>{{ $task['technician']->name }}</strong><br />
                                <small>{{ __('Imputed hours') }}: {{ $task['logged_hours'] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted text-center m-2" style="width:80%;">
                    {{ __('No tasks in progress') }}...
                </div>
            @endif
        </div>

        {{-- ================================ --}}
        {{--    FOOTER DEL MILESTONE          --}}
        {{-- ================================ --}}
        <div class="card mb-0">
            <div class="card-body p-2">
                <div class="row">

                    <div class="foot-milestone">
                        {{-- Proyecto --}}
                        <div class="col-6 text-center">
                            <div class="tooltipCus" style="display: flex; flex-direction:column; align-items:center;"
                                data-title="{{ __('Project') }}">
                                <img class="img-fluid p-1 adjustImg"
                                    src="{{ asset('assets/img/' . $milestone['project_type'] . '.png') }}">
                                <b style="font-size:12px;">{{ $milestone['project_name'] }}</b>
                                <span class="text-muted">
                                    <b>{{ $milestone['project_ref'] }}</b>
                                </span>
                                {{-- Workspace --}}
                                @if (strpos(request()->url(), 'my-milestone-board') !== false)
                                    <small class="text-muted d-block mt-1" style="font-size:10px;">
                                        <i class="fa-solid fa-layer-group"></i> {{ $milestone['workspace_name'] }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        {{-- finalization_date (fecha finalización) --}}
                        {{-- planned_end_date (fecha prevista) --}}

                        <div class="fatherDateDivAlign">
                            {{-- Fecha deseada --}}
                            <div class="dateDiv text-center tooltipCus"
                                data-title="{{ __('Desired delivery date') }}">

                                @php
                                    if ($milestone['finalization_date'] == null) {
                                        $currentDate = new DateTime();
                                        $estimatedDate = new DateTime($milestone['end_date']);
                                        $isOverdue = $currentDate > $estimatedDate;
                                    } else {
                                        $estimatedDate = new DateTime($milestone['end_date']);
                                        $finalDate = new DateTime($milestone['finalization_date']);
                                        $isOverdue = $finalDate > $estimatedDate;
                                    }
                                    $statusNumber = (int) $status->id;

                                    if ($statusNumber <= 2) {
                                        $iconColor = $isOverdue ? '#db8d33' : 'black';
                                    } else {
                                        $iconColor = $isOverdue ? 'red' : '#53b446';
                                    }
                                @endphp

                                <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                    style="color:{{ $iconColor }};"></i>

                                <div class="adjustTextCalendar">
                                    <b style="font-size:12px;">
                                        {{ \App\Models\Utility::dateFormat($milestone['end_date']) }}
                                    </b>
                                </div>
                            </div>
                            {{-- Fecha prevista --}}
                            <div class="dateDiv text-center tooltipCus"
                                data-title="{{ __('Planned delivery date') }}">
                                @if ($milestone['planned_end_date'] != null && $milestone['planned_end_date'] != '0000-00-00')
                                    @php
                                        if ($milestone['finalization_date'] == null) {
                                            $currentDate = new DateTime();
                                            $estimatedDate = new DateTime($milestone['planned_end_date']);
                                            $isOverdue = $currentDate > $estimatedDate;
                                        } else {
                                            $estimatedDate = new DateTime($milestone['planned_end_date']);
                                            $finalDate = new DateTime($milestone['finalization_date']);
                                            $isOverdue = $finalDate > $estimatedDate;
                                        }
                                        $statusNumber = (int) $status->id;

                                        if ($statusNumber <= 2) {
                                            $iconColor = $isOverdue ? '#db8d33' : 'black';
                                        } else {
                                            $iconColor = $isOverdue ? 'red' : '#53b446';
                                        }
                                    @endphp

                                    <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                        style="color:{{ $iconColor }};"></i>

                                    <div class="adjustTextCalendar">
                                        <b style="font-size:12px;">
                                            {{ \App\Models\Utility::dateFormat($milestone['planned_end_date']) }}
                                        </b>
                                    </div>
                                @else
                                    <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                        style="color:lightgrey"></i>
                                @endif
                            </div>
                            {{-- Fecha completada --}}
                            <div class="dateDiv text-center tooltipCus" data-title="{{ __('Completed date') }}">
                                @if ($milestone['finalization_date'] != null && $milestone['finalization_date'] != '0000-00-00')
                                    @php
                                        $estimatedDate = new DateTime($milestone['planned_end_date']);
                                        $requestedDate = new DateTime($milestone['end_date']);
                                        $completedDate = new DateTime($milestone['finalization_date']);

                                        if ($estimatedDate < $completedDate && $requestedDate < $completedDate) {
                                            $iconColor = $isOverdue ? '#db8d33' : 'black';
                                        } else {
                                            $iconColor = $isOverdue ? 'red' : '#53b446';
                                        }
                                    @endphp

                                    <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                        style="color:{{ $iconColor }};"></i>

                                    <div class="adjustTextCalendar">
                                        <b style="font-size:12px;">
                                            {{ \App\Models\Utility::dateFormat($milestone['finalization_date']) }}
                                        </b>
                                    </div>
                                @else
                                    <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                        style="color:lightgrey"></i>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    @if ($milestone['priority'] && $milestone['priority'] !== '')
        @php
            $priorityLower = strtolower($milestone['priority']);
            if ($priorityLower === 'baja' || $priorityLower === 'low') {
                $borderLeft = '#0080008a';
                $borderRight = 'green';
                $borderTop = 'green';
                $bgColor = '#008000fa';
                $priorityLabel = __('low');
            } elseif ($priorityLower === 'media' || $priorityLower === 'medium') {
                $borderLeft = '#ff8c008a';
                $borderRight = '#ff8c00';
                $borderTop = '#ff8c00';
                $bgColor = '#ff8c00fa';
                $priorityLabel = __('medium');
            } elseif ($priorityLower === 'alta' || $priorityLower === 'high') {
                $borderLeft = '#aa182c';
                $borderRight = '#aa182c';
                $borderTop = '#aa182c';
                $bgColor = '#aa182c';
                $priorityLabel = __('high');
            } else {
                $borderLeft = '#0080008a';
                $borderRight = 'green';
                $borderTop = 'green';
                $bgColor = '#008000fa';
                $priorityLabel = $milestone['priority'];
            }
        @endphp
        <div style="display: flex; justify-content: center;">
            <span
                style="
                                                        border-left: 2px solid {{ $borderLeft }};
                                                        border-right: 2px solid {{ $borderRight }};
                                                        border-top: 2px solid {{ $borderTop }};
                                                        border-top-left-radius: 10px;
                                                        border-top-right-radius: 10px;
                                                        padding: 5px 10px 5px 10px;
                                                        background-color: {{ $bgColor }};
                                                        color: white;
                                                        font-weight: 600;
                                                    ">{{ __('Priority') }}:
                {{ $priorityLabel }}</span>
        </div>
    @endif
    {{-- @dump($milestone) --}}
</div>

<span class="empty-container" data-placeholder="Empty"></span>
<!-- Modal para pausa de milestone con comentario -->
<div class="modal fade" id="pauseMilestoneModal" tabindex="-1" role="dialog"
    aria-labelledby="pauseMilestoneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pauseMilestoneModalLabel">{{ __('Pause Milestone') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pauseMilestoneForm" method="POST" style="display:none;">
                @csrf
            </form>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pauseComment">{{ __('Pause Reason / Note') }}</label>
                    <textarea class="form-control" id="pauseComment" name="pause_comment" rows="4"
                        placeholder="{{ __('Enter the reason for pausing this milestone...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="submitPauseMilestone()">{{ __('Pause') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openPauseMilestoneModal(milestoneId, slug) {
        // Guardar el ID y slug en el modal para usarlos después
        document.getElementById('pauseMilestoneModal').dataset.milestoneId = milestoneId;
        document.getElementById('pauseMilestoneModal').dataset.slug = slug;

        // Limpiar el textarea
        document.getElementById('pauseComment').value = '';

        // Mostrar el modal
        $('#pauseMilestoneModal').modal('show');
    }

    function submitPauseMilestone() {
        const milestoneId = document.getElementById('pauseMilestoneModal').dataset.milestoneId;
        const slug = document.getElementById('pauseMilestoneModal').dataset.slug;
        const comment = document.getElementById('pauseComment').value;

        // Crear el formulario dinámicamente
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('projects.milestone.wait', [':slug', ':id']) }}'.replace(':slug', slug).replace(':id',
            milestoneId);

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

        // Cerrar el modal
        $('#pauseMilestoneModal').modal('hide');
    }
</script>
