<style>
    .waitingMilestone {
        border: 2px dashed #bdbdbd !important;
        background-color: #f0f0f0 !important;
        opacity: 0.65;
        filter: grayscale(100%) brightness(0.9);
        position: relative;
    }
</style>
<div class="card 
        {{ empty($milestone['assined_to_user']) ? 'notAsignedMilestone' : '' }} 
        {{ !empty($milestone['is_waiting']) && $milestone['is_waiting'] == 1 ? 'waitingMilestone' : '' }}
        {{ $extraClass ?? '' }}"
    id="{{ $milestone['id'] }}" data-status="{{ $status->id }}" data-project-id="{{ $milestone['project_id'] }}"
    data-assign-to="{{ $milestone['assign_to'] }}" data-is-waiting="{{ $milestone['is_waiting'] }}"
    style="{{ $inlineStyle ?? '' }}">


    {{-- ========================= --}}
    {{--   HEADER DEL MILESTONE   --}}
    {{-- ========================= --}}
    <div class="card-header border-0 pb-0 col-sm-12">
        <div class="d-flex">
            <div class="col-sm-9 text-center tooltipCus" data-title="{{ __('Milestone') }}">
                <b class="mileTitle cursor-pointer" id="milestoneTitleForNotification"
                    data-header="{{ $milestone['title'] }}" data-milestone-id="{{ $milestone['id'] }}"
                    data-is-waiting="{{ $milestone['is_waiting'] }}"
                    data-project-slug="{{ $currentWorkspace->slug }}">
                    {{ $milestone['title'] }}
                </b>
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
                    @if (isset($milestone['asiggned_user_data']) && $milestone['asiggned_user_data']->avatar)
                        <img alt="image" class="user-groupTasks tooltipCus" style="margin-top:-10px;"
                            src="{{ asset($milestone['asiggned_user_data']->avatar) }}"
                            title="{{ __('Assigned to') }} {{ $milestone['asiggned_user_data']->name }}">
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
                            data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $project_id, $milestone['id']]) }}">
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
                            <a href="#" class="dropdown-item" data-ajax-popup="true"
                                data-title="{{ __('Add Task') }}"
                                data-url="{{ route('tasks.create', [
                                    $currentWorkspace->slug,
                                    'project_id' => $milestone['project_id'],
                                    'milestoneTitle' => $milestone['title'],
                                    'milestone_id' => $milestone['id'],
                                ]) }}">
                                <i class="fas fa-tasks"></i>
                                {{ __('Add Task') }}
                            </a>

                            {{-- Pausar --}}
                            <a href="#" class="dropdown-item"
                                onclick="event.preventDefault(); document.getElementById('wait-milestone-{{ $milestone['id'] }}').submit();">
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
                        <div class="taskList tooltipCus p-target mb-2 col-sm-12 marginText" role="button"
                            data-task-id="{{ $task['id'] }}" data-task-name="{{ $task['name'] }}"
                            data-milestone-id="{{ $milestone['id'] }}"
                            data-project-id="{{ $milestone['project_id'] }}"
                            data-project-name="{{ $milestone['project_name'] }}"
                            data-technician-name="{{ $task['technician']->id }}"
                            data-url="{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}"
                            data-ajax-timesheet-popup="true" data-title="{{ $task['technician']->name }}">

                            <i class="ms-2 me-2 fa-solid fa-hourglass-start fa-xs" style="color:black;"></i>
                            {{ __($task['name']) }}
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
                            <div class="tooltipCus" data-title="{{ __('Project') }}">
                                <img class="img-fluid p-1 adjustImg"
                                    src="{{ asset('assets/img/' . $milestone['project_type'] . '.png') }}">
                                <b style="font-size:12px;">{{ $milestone['project_name'] }}</b>
                                <span class="text-muted">
                                    <b>{{ $milestone['project_ref'] }}</b>
                                </span>
                            </div>
                        </div>

                        {{-- Fecha deseada --}}
                        <div class="col-6 text-center tooltipCus" data-title="{{ __('Desired delivery date') }}">

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
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<span class="empty-container" data-placeholder="Empty"></span>
