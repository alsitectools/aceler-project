@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
@endphp
@section('page-title')
    {{ __('Order Forms Board') }}
@endsection

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestoneboard.css') }}">
</head>
<style>
    .showCompletedProjectGroup {
        display: flex;
        gap: 17px;
    }

    .showCompletedProjects {
        width: 20px;
        height: 20px;
        margin-top: -6px;
    }

    .showCompletedProjects:hover {
        cursor: pointer;

    }

    .showCompletedProjectsUnabled {
        filter: grayscale(1);
    }

    .modifiedWidth {
        width: 99.9%;
    }

    .adjustImg {
        width: 40px;
    }

    .calendarAlert {
        font-size: 29px;
        padding-top: 12px !important;
        padding-bottom: 0px !important;
    }

    .adjustTextCalendar {
        padding-top: 10% !important;
        font-size: 12px !important;
    }

    .hideUnasignedTasks {
        margin-top: -6px;
        width: 24px;
    }

    .hideUnasignedTasks:hover {
        cursor: pointer;

    }

    .toastNegation {
        z-index: 30;
        position: absolute;
        right: 10px;
        display: flex;
        text-align: center;
        align-content: center;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .fixedHeight {
        max-height: 600px;
        overflow: hidden;
        overflow-y: auto;
        scrollbar-color: #aa182c #ffff0000;
        scrollbar-width: thin;
        /* position: relative; */
    }

    .fixedHeight::-webkit-scrollbar {
        width: 8px;
        height: 10px;
        /* Ancho del scrollbar */
    }

    /* Fondo del scrollbar */
    .fixedHeight::-webkit-scrollbar-track {
        background: #ffffff;
        /* Color del fondo */
        border-radius: 4px;
        /* Bordes redondeados */
    }

    /* Parte deslizable del scrollbar */
    .fixedHeight::-webkit-scrollbar-thumb {
        background: #AA182C;
        /* Color del scrollbar */
        border-radius: 4px;
        height: 10px;
    }

    @media screen and(max-width:1200px) and(min-width:1000px) {
        .adjustImg {
            width: 65%;
        }

        .calendarAlert {
            font-size: 25px !important;
            padding-top: 12px !important;
        }

        .adjustTextCalendar {
            padding-top: 15%;
        }
    }

    .notAsignedMilestone {
        border: 3px solid #a62330 !important;
    }

    .legendIcon {
        width: 20px;
        margin-left: 8px;
        cursor: pointer;
        transition: transform 0.1s ease-in-out;
    }

    .legend {
        opacity: 0;
        background-color: #f9fbfa;
        border: 2px solid transparent;
        border-radius: 15px;
        width: 394px;
        height: 735px;
        position: absolute;
        top: 4.5%;
        left: 21rem;
        display: flex;
        z-index: 3;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(1px 1px 5px #b1b1b1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }

    .legend.visible {
        pointer-events: auto;

    }


    .colorExample {
        width: 20px;
        height: 20px;
        border-radius: 100%;
        position: absolute;
        right: 2rem;
    }

    i.lIcons {
        margin-right: 0.9rem !important;
        color: black;
        margin-top: -6px !important;
    }

    .colorText {
        position: absolute;
        left: 2.5rem;
    }

    .lEntry {
        border-radius: 10px;
        display: flex;
        width: 92%;
        height: 3rem;
        margin-bottom: 10px;
        align-items: center;
        justify-content: space-around;
        align-content: center;
        font-size: 13px;
        font-weight: 600;
    }



    .lDetail {
        border: 2px solid gray;
        gap: 47px;
        text-align: center;
        height: 7rem;
        background-color: #80808030;
        color: #6b6b6b;
    }

    .lTitle {
        position: absolute;
        top: 1rem;
        font-size: 20px;
        font-weight: 600;
        color: black;
    }
</style>
@section('links')
    @if (isset($project_id) && $project_id != -1)
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a>
        </li>

        <li class="breadcrumb-item"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ $project_name }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a
                href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Order Forms Board') }}</li>
    <img class="legendIcon" src="{{ asset('assets/img/questionCircle.svg') }}" />
    <div class="legend">
        <span class="lTitle">{{ __('Color legend') }}</span>
        <hr style="backgroundColor: #e0e1e1; width: 100%; height: 2px;">
        <div class="lEntry" style="border:2px solid #000000 ;height: 5rem !important; text-align: center; "
            style="display: flex; ">
            <span>{{ __('Comparison with the delivery date desired by the manager') }}</span>
            <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert lIcons"></i>
        </div>
        <div class="lEntry" style="border:2px solid #000000 ;height: 5rem !important; text-align: center;">
            <span>{{ __('Comparison with the estimated delivery date') }}</span>
            <i class="ms-2 me-2 fa-solid fa-hourglass-start lIcons" style=" font-size:22px;"></i>
        </div>
        <hr style="backgroundColor: #e0e1e1; width: 100%; height: 2px;">
        <div class="lEntry" style="border:2px solid #000000 ;  border-left: 12px solid black;">
            <span class="colorText">{{ __('Undelivered, still on schedule') }}</span>
            <div class="colorExample" style="background-color: #000000; border:1.5px solid #000000 ;"></div>
        </div>
        <div class="lEntry" style="border:2px solid #db8d33;  border-left: 12px solid #db8d33;">
            <span class="colorText">{{ __('Undelivered,out of date') }}</span>
            <div class="colorExample" style="background-color: #db8d33; border:1.5px solid #db8d33"></div>
        </div>
        <div class="lEntry" style="border:2px solid #53b446;  border-left: 12px solid #53b446;">
            <span class="colorText">{{ __('Delivered on schedule') }}</span>
            <div class="colorExample" style="background-color: #53b446; border:1.5px solid #53b446"></div>
        </div>

        <div class="lEntry" style="border:2px solid #ff0000;  border-left: 12px solid #ff0000;">
            <span class="colorText">{{ __('Delivered, out of date') }}</span>
            <div class="colorExample" style="background-color: #ff0000; border:1.5px solid #ff0000"></div>
        </div>
        <div class="lEntry" style="border:2px solid #a62330; gap:47px; border-left: 12px solid #a62330;">
            <span class="colorText">{{ __('Order pending of assignment') }}</span>
            <div class="colorExample" style="background-color: #a62330; border:1.5px solid #a62330; "></div>
        </div>
        <div class="lEntry lDetail">
            <span>{{ __('In this section you will find the job sheets you have requested, those assigned to you, those on which you have performed tasks and also those not yet assigned.') }}</span>
        </div>
    </div>
@endsection

@section('action-button')
    <div class="d-flex justify-content-end row1">
        <div id="modal-container" class="modal fade" tabindex="-1" role="dialog" data-bs-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="text-align: left; width: 800px;">
                    <!-- El contenido del modal se cargará aquí -->
                </div>
            </div>
        </div>
        @if (isset($currentWorkspace) && $currentWorkspace)
            <div class="col-sm-auto">
                <button style="width: 100%" type="button" class="btn btn-primary addMilestone" data-ajax-popup="true"
                    data-title="{{ __('Milestone order') }}"
                    data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project_id]) }}"
                    data-toggle="popover" title="{{ __('Create') }}"><i class="fa-solid fa-file-lines me-3"
                        style="color: #ffffff;"></i>
                    {{ __('Create Order Form') }}
                </button>
            </div>
        @endif
    </div>
@endsection
@section('content')
    <div class="row modifiedWidth">
        <div class="col-sm-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-toggle="dragula"
                data-containers='{{ json_encode($statusClass) }}' data-handleclass="handleclass">
                @foreach ($stages as $status)
                    <div class="col-3 pe-1" id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}">
                        <div class="card card-list">
                            <div class="card-header">
                                <div class="float-end showCompletedProjectGroup">

                                    @if ($status->name === 'Done')
                                        @if ($project_id == -1)
                                            <img id="toggleCompletedProjectsIcon"
                                                src="{{ asset('assets/img/clipboard-check-solid.svg') }}"
                                                alt="show completed projects" title="{{ __('Show Completed Projects') }}"
                                                class="showCompletedProjects showCompletedProjectsUnabled" />
                                        @endif
                                    @endif
                                    @if ($status->name === 'To Do')
                                        <img id="hideUnassignedMilstoneIcon"
                                            src="{{ asset('assets/img/address-card-regular.svg') }}"
                                            alt="show completed projects" title="{{ __('Hide Unasigned Order Forms') }}"
                                            class="hideUnasignedTasks" />
                                    @endif
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0 "
                                        style="height: 19.7px;">
                                        <span class="badge badge-secondary rounded-pill count">
                                            {{ isset($milestones[$status->id]) ? count($milestones[$status->id]) : 0 }}
                                        </span>
                                    </button>
                                </div>
                                <h4 class="mb-0">
                                    {{ __($status->name) }}
                                </h4>
                            </div>
                            <div id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}"
                                data-status="{{ $status->id }}" class="card-body kanban-box fixedHeight">

                                @if (isset($milestones[$status->id]))
                                    @foreach ($milestones[$status->id] as $milestone)
                                        <div class="card {{ empty($milestone['assined_to_user']) ? 'notAsignedMilestone' : '' }}"
                                            id="{{ $milestone['id'] }}" data-status="{{ $status->id }}"
                                            data-project-id="{{ $milestone['project_id'] }}">

                                            <div class="card-header border-0 pb-0 col-sm-12">
                                                <div class="d-flex">
                                                    <div class="col-sm-9 text-center tooltipCus"
                                                        data-title="{{ __('Milestone') }}">
                                                        <b class="mileTitle cursor-pointer"
                                                            id="milestoneTitleForNotification"
                                                            data-header="{{ $milestone['title'] }}"
                                                            data-milestone-id="{{ $milestone['id'] }}"
                                                            data-project-slug="{{ $currentWorkspace->slug }}">

                                                            {{ $milestone['title'] }}
                                                        </b>
                                                    </div>
                                                    <div class="col-sm-2 pt-1 text-center">
                                                        <a href="#" {{-- class="tooltipCus"  --}} id="milestoneReqName"
                                                            data-milestone-id={{ $milestone['id'] }}
                                                            data-technician-id={{ $milestone['assign_to'] }}
                                                            data-project-name={{ $milestone['project_name'] }}
                                                            {{-- data-title="{{ $milestone['sales']->name ?? 'Nombre no disponible' }}" --}}>

                                                            <img alt="image" class="user-groupTasks tooltipCus"
                                                                title="{{ __('Requested by') }} {{ $milestone['sales']->name ?? 'Nombre no disponible' }}"
                                                                style="margin-top: -10;"
                                                                @if ($milestone['sales']->avatar) src="{{ asset($milestone['sales']->avatar) }}"
            @else
                avatar="{{ $milestone['sales']->avatar ?? $milestone['sales']->name }}" @endif>
                                                            {{-- assigned to user avatar --}}
                                                            @if (isset($milestone['asiggned_user_data']) && $milestone['asiggned_user_data']->avatar)
                                                                <img alt="image" class="user-groupTasks tooltipCus"
                                                                    style="margin-top: -10;"
                                                                    src="{{ asset($milestone['asiggned_user_data']->avatar) }}"
                                                                    title="{{ __('Assigned to') }} {{ $milestone['asiggned_user_data']->name }}">
                                                            @endif
                                                        </a>

                                                    </div>
                                                </div>
                                                <hr class="border border-2 opacity-50">
                                                <div class="card-header-right col-sm-1 text-end">
                                                    <div class="btn-group card-option">
                                                        @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')
                                                            <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="feather icon-more-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="#" class="dropdown-item"
                                                                    data-ajax-popup="true" title="{{ __('View') }}"
                                                                    data-title="{{ __('Order form details') }}"
                                                                    data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                    <i class="ti ti-eye pr-1"></i>
                                                                    {{ __('View') }}
                                                                </a>
                                                                <a href="#" class="dropdown-item milestone-action"
                                                                    data-ajax-popup="true" title="assign_to_someone"
                                                                    data-title="{{ __('Assign Milestone') }}"
                                                                    data-url="{{ route('projects.milestone.assign', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                    <i class="fa-solid fa-user-plus"></i>
                                                                    {{ __('Assign Milestone') }}
                                                                </a>
                                                                <a href="#" class="dropdown-item milestone-action"
                                                                    data-ajax-popup="true" title="{{ __('Add Task') }}"
                                                                    data-title="{{ __('Add Task') }}"
                                                                    data-url="{{ route('tasks.create', [$currentWorkspace->slug, 'project_id' => $milestone['project_id'], 'milestoneTitle' => $milestone['title'], 'milestone_id' => $milestone['id']]) }}">
                                                                    <i class="fas fa-tasks pr-1"></i> {{ __('Add Task') }}
                                                                </a>
                                                                @if (
                                                                    $currentWorkspace->permission == 'Owner' ||
                                                                        ($currentWorkspace->permission == 'Member' && Auth::user()->type == 'user'))
                                                                    <a href="#"
                                                                        class="dropdown-item milestone-action"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('Edit Milestone') }}"
                                                                        data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                        <i class="ti ti-edit pr-1"></i>{{ __('Edit') }}
                                                                    </a>
                                                                    @if (empty($milestone['tasks']))
                                                                        <a href="#"
                                                                            class="dropdown-item bs-pass-para"
                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                            data-confirm-yes="delete-form-{{ $milestone['id'] }}">
                                                                            <i class="ti ti-trash"></i>
                                                                            {{ __('Delete') }}
                                                                        </a>
                                                                        <form id="delete-form-{{ $milestone['id'] }}"
                                                                            action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone['id']]) }}"
                                                                            method="POST" style="display: none;">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    @else
                                                                        <a href="#"
                                                                            class="dropdown-item milestone-action bs-pass-para"
                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                            data-confirm-yes="delete-form-{{ $milestone['id'] }}">
                                                                            <i class="ti ti-trash"></i>
                                                                            {{ __('Delete') }}
                                                                        </a>
                                                                        <form id="delete-form-{{ $milestone['id'] }}"
                                                                            action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone['id']]) }}"
                                                                            method="POST" style="display: none;">
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
                                            <div class="card-body pt-1">
                                                <div class="row">
                                                    @if ($milestone['tasks'])
                                                        <div class="col-sm-12 p-3">
                                                            @foreach ($milestone['tasks'] as $task)
                                                                <div class="taskList tooltipCus p-target mb-2 col-sm-12 marginText"
                                                                    role="button" data-task-id="{{ $task['id'] }}"
                                                                    data-task-name="{{ $task['name'] }}"
                                                                    data-milestone-id="{{ $milestone['id'] }}"
                                                                    data-project-id="{{ $milestone['project_id'] }}"
                                                                    data-project-name="{{ $milestone['project_name'] }}"
                                                                    data-technician-name="{{ $task['technician']->id }}"
                                                                    data-url="{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}"
                                                                    data-ajax-timesheet-popup="true"
                                                                    data-title="{{ $task['technician']->name . '  (Fecha de entrega estimada: ' . \Carbon\Carbon::parse($task['estimated_date'])->format('d/m/Y') }})">
                                                                    @php

                                                                        // Get the current milestone status from the container it's in
$milestoneStatus = (int) $status->id;
$isAfterEstimatedDate =
    strtotime($task['estimated_date']) <
    strtotime(date('Y-m-d'));

// Determine icon color based on milestone status and estimated date
if ($milestoneStatus <= 2) {
    // Status 1 or 2 (To Do or In Progress)
    if ($isAfterEstimatedDate) {
        $iconColor = '#db8d33'; // Yellow for overdue tasks
    } else {
        $iconColor = 'black'; // Black for on-time tasks
    }
} else {
    // Status 3 or 4 (In Review or Done)
    if ($isAfterEstimatedDate) {
        $iconColor = 'red'; // Red for overdue tasks
    } else {
        $iconColor = '#53b446'; // Green for on-time tasks
    }
}

$icon =
    '<i class="ms-2 me-2 fa-solid fa-hourglass-' .
    ($isAfterEstimatedDate ? 'end' : 'start') .
    ' fa-xs" style="color: ' .
    $iconColor .
    '"></i>';
                                                                    @endphp
                                                                    {!! $icon !!}{{ __($task['name']) }}
                                                                </div>
                                                                @if ($project_id != -1)
                                                                    <div class="taskList tooltipCus col-sm-12 text-end"
                                                                        data-title="{{ $task['technician']->name }}">
                                                                        <a href="#"></a>
                                                                    </div>
                                                                @endif
                                                            @endforeach

                                                            @if ($project_id == -1)
                                                                <div class="col-sm-11 text-end"
                                                                    data-title="{{ $task['technician']->name }}">
                                                                    <a href="#"></a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-muted text-center m-2" style="width: 80%">
                                                            {{ __('No tasks in progress') }}...
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card mb-0">
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="foot-milestone">
                                                                <div class="col-6 text-center">
                                                                    <div class="text-center tooltipCus"
                                                                        data-title="{{ __('Project') }}">
                                                                        <div>
                                                                            <img class="img-fluid p-1 adjustImg"
                                                                                src="{{ asset('assets/img/' . $milestone['project_type'] . '.png') }}"
                                                                                alt="Project type">
                                                                        </div>
                                                                        <b
                                                                            style="font-size: 12px">{{ $milestone['project_name'] }}</b>
                                                                        <span class="text-muted"
                                                                            data-title="{{ __('Ref. M.O') }}"><b>{{ $milestone['project_ref'] }}</b></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 text-center tooltipCus"
                                                                    data-title="{{ __('Desired delivery date') }}">
                                                                    @php
                                                                        if ($milestone['finalization_date'] == null) {
                                                                            $currentDate = new DateTime();
                                                                            $latestStatus = (int) $status->id; // Get current column status
                                                                            $estimatedDate = new DateTime(
                                                                                $milestone['end_date'],
                                                                            );
                                                                            $isOverdue = $currentDate > $estimatedDate;
                                                                        } else {
                                                                            $latestStatus = (int) $status->id;
                                                                            $estimatedDate = new DateTime(
                                                                                $milestone['end_date'],
                                                                            );
                                                                            $finalizationDate = new DateTime(
                                                                                $milestone['finalization_date'],
                                                                            );
                                                                            $isOverdue =
                                                                                $finalizationDate > $estimatedDate;
                                                                        }

                                                                        // Determine icon color and animation based on status and date
                                                                        $iconColor = 'black'; // Default
                                                                        $iconAnimation = '';

                                                                        if ($latestStatus <= 2) {
                                                                            // To Do or In Progress
                                                                            if ($isOverdue) {
                                                                                $iconColor = '#db8d33'; // Yellow for overdue tasks in status 1-2
                                                                            } else {
                                                                                $iconColor = 'black'; // Black for on-time tasks in status 1-2
                                                                            }
                                                                        } else {
                                                                            // In Review or Done
                                                                            if ($isOverdue) {
                                                                                $iconColor = 'red'; // Red for overdue tasks in status 3-4
                                                                            } else {
                                                                                $iconColor = '#53b446'; // Green for on-time tasks in status 3-4
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert "
                                                                        style="color: {{ $iconColor }};"></i>
                                                                    <div class="text-center adjustTextCalendar">
                                                                        <b style="font-size: 12px">
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
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
    @if ($milestones != null)
        @push('scripts')
            <!-- <script>
                ! function(a) {
                    "use strict";

                    var t = function() {
                        this.$body = a("body");
                    };

                    t.prototype.init = function() {
                        a('[data-toggle="dragula"]').each(function() {
                            var containers = a(this).data("containers");
                            var containersArray = [];

                            if (containers && containers.length) {
                                for (var i = 0; i < containers.length; i++) {
                                    var container = a("#" + containers[i] + " .kanban-box")[0];
                                    if (container) {
                                        containersArray.push(container);
                                    } else {
                                        console.error('Contenedor no encontrado:', containers[i]);
                                    }
                                }
                            } else {
                                containersArray = [a(this)[0]];
                            }
                            var handleClass = a(this).data("handleclass");
                            dragula(containersArray, {
                                moves: function(el, container, handle) {

                                    return el.classList.contains('card');
                                }
                            }).on('drop', handleDrop);
                        });
                    };

                    function handleDrop(el, target, source, sibling) {
                        var sort = [];
                        a(target).find(".card").each(function(key) {
                            var cardId = a(this).attr('id');
                            if (cardId) {
                                console.log('Card ID at index', key, ':', cardId);
                                sort.push(cardId);
                            } else {
                                console.warn('Card at index', key, 'does not have an ID');
                            }
                        });

                        // Obtenemos el cardId del elemento que se acaba de mover
                        var cardId = a(el).attr('id');
                        var oldStatus = a(source).data('status');
                        var newStatus = a(target).data('status');
                        var project_id = a(el).data('project-id');
                        var milestoneTitle = a(el).find('#mileTitle').text(); // Obtenemos el título del milestone

                        if (oldStatus == 1 && newStatus == 2) {
                            console.log('De por hacer a in progress');
                            console.log('Card ID:', cardId, 'Old status:', oldStatus, 'New status:', newStatus, 'Project ID:',
                                project_id, 'Milestone Title:', milestoneTitle);

                            // Se dispara la misma acción que al hacer clic en "Add Task on Timesheet"
                            var url = '{{ route('tasks.create', $currentWorkspace->slug) }}' + '?project_id=' + project_id +
                                '&milestoneTitle=' + milestoneTitle + '&milestone_id=' + cardId;
                            var title = '{{ __('Create New Task') }}';
                            var modalId = 'commonModal';

                            $("#" + modalId + " .modal-title").html(title);
                            $.ajax({
                                url: url,
                                dataType: 'html',
                                success: function(data) {
                                    $('#' + modalId + ' .body').html(data);
                                    $("#" + modalId).modal('show');
                                    commonLoader();
                                    loadConfirm();
                                }
                            });
                        }
                        updateTaskCount(source);
                        updateTaskCount(target);

                        a.ajax({
                            url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                            type: 'POST',
                            data: {
                                id: cardId, // Se envía el cardId obtenido
                                sort: sort,
                                new_status: newStatus,
                                old_status: oldStatus,
                                project_id: project_id
                            },
                            success: function(data) {
                                console.log('AJAX success');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al actualizar el orden:', error);
                            }
                        });
                    }

                    function updateTaskCount(container) {
                        var parentCardList = a(container).parents('.card-list');
                        var count = a(container).children('.card').length;
                        parentCardList.find('.count').text(count);
                    }

                    a.Dragula = new t;
                    a.Dragula.Constructor = t;

                }(window.jQuery);

                ! function(a) {
                    "use strict";
                    a.Dragula.init();
                }(window.jQuery);
            </script> -->


            <script>
                ! function(a) {
                    "use strict";

                    var t = function() {
                        this.$body = a("body");
                    };

                    t.prototype.init = function() {
                        a('[data-toggle="dragula"]').each(function() {
                            var containers = a(this).data("containers");
                            var containersArray = [];

                            if (containers && containers.length) {
                                for (var i = 0; i < containers.length; i++) {
                                    var container = a("#" + containers[i] + " .kanban-box")[0];
                                    if (container) {
                                        containersArray.push(container);
                                    } else {
                                        console.error('Contenedor no encontrado:', containers[i]);
                                    }
                                }
                            } else {
                                containersArray = [a(this)[0]];
                            }
                            var handleClass = a(this).data("handleclass");

                            // Inicializamos dragula y agregamos eventos 'drag' y 'drop'
                            dragula(containersArray, {
                                    moves: function(el, container, handle) {
                                        return el.classList.contains('card');
                                    }
                                })
                                // Al iniciar el drag, almacenamos el contenedor de origen y el índice original
                                .on('drag', function(el, source) {
                                    a(el).data('originContainer', source);
                                    a(el).data('originalIndex', a(source).children('.card').index(a(el)));
                                })
                                // Al soltar el elemento se ejecuta el handleDrop
                                .on('drop', handleDrop);
                        });
                    };

                    function handleDrop(el, target, source, sibling) {
                        // Obtenemos el nuevo orden de los elementos en el contenedor destino
                        var sort = [];
                        a(target).find(".card").each(function(key) {
                            var cardId = a(this).attr('id');
                            if (cardId) {
                                console.log('Card ID at index', key, ':', cardId);
                                sort.push(cardId);
                            } else {
                                console.warn('Card at index', key, 'does not have an ID');
                            }
                        });

                        // Obtenemos información necesaria
                        var cardId = a(el).attr('id');
                        // Utilizamos .data() para obtener el status; sin embargo, al mover la tarjeta actualizaremos el atributo
                        var oldStatus = a(source).data('status');
                        var newStatus = a(target).data('status');
                        var project_id = a(el).data('project-id');
                        // var milestoneTitle = a(el).find('mileTitle').text(); // Título del milestone
                        var milestoneTitle = a(el).find('.mileTitle').attr('data-header');
                        console.log("el completo ");
                        console.log(el)
                        console.log("longitud " + a(el).find('#milestoneTitleForNotification').length);

                        // Definir las transiciones permitidas
                        const allowedTransitions = {
                            4: [3], // oldStatus 4 solo puede ir a 3
                            3: [2, 4], // oldStatus 3 solo puede ir a 2 o 4
                            2: [3], // oldStatus 2 solo puede ir a 3
                            1: [2] // oldStatus 1 solo puede ir a 2
                        };

                        // Verificar si el movimiento es válido
                        if (!(allowedTransitions[oldStatus] && allowedTransitions[oldStatus].includes(newStatus))) {
                            console.log(
                                `Movimiento no permitido: No se puede mover un elemento de status ${oldStatus} a status ${newStatus}.`
                            );

                            // Se recupera el contenedor de origen y la posición original que se almacenaron en el evento "drag"
                            var originContainer = a(el).data('originContainer');
                            var originalIndex = a(el).data('originalIndex');

                            // Asegurarse de que el contenedor de origen aún existe
                            var $origin = a(originContainer);
                            if ($origin.length === 0) {
                                console.error("El contenedor de origen ya no existe en el DOM.");
                                return;
                            }

                            // Remover el elemento del contenedor destino correctamente
                            a(el).detach(); // Mejor que .remove(), evita errores de referencia

                            // Obtener todas las tarjetas dentro del contenedor original
                            var $cards = $origin.children('.card');

                            // Reinsertamos en la posición original
                            if ($cards.length > 0 && originalIndex < $cards.length) {
                                a(el).insertBefore($cards.eq(originalIndex));
                            } else {
                                $origin.append(el);
                            }

                            // Limpiar referencias de origen para evitar anidamientos
                            a(el).data('originContainer', null);
                            a(el).data('originalIndex', null);

                            // Actualizamos el contador de tareas de ambos contenedores
                            updateTaskCount(source);
                            updateTaskCount(target);

                            // No se ejecuta la llamada AJAX ya que se ha cancelado el cambio
                            return;
                        }


                        // Si se permite el movimiento y es de status 1 a 2, se dispara primero el popup de asignación
                        if (oldStatus == 1 && newStatus >= 2) {
                            console.log('De por hacer a in progress');

                            // Modificamos la construcción de la URL para asegurar la ruta correcta
                            var assignUrl = '{{ route('projects.milestone.assign', [$currentWorkspace->slug, ':id']) }}'.replace(
                                ':id', cardId);
                            var assignTitle = '{{ __('Assign Milestone') }}';
                            var modalId = 'commonModal';

                            $("#" + modalId + " .modal-title").html(assignTitle);
                            $.ajax({
                                url: assignUrl,
                                dataType: 'html',
                                success: function(assignData) {
                                    $('#' + modalId + ' .body').html(assignData);
                                    // Marcamos el formulario para saber que viene del cambio de estado
                                    $('#asignMilestoneForm').attr('data-from-status-change', 'true');
                                    $("#" + modalId).modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    });
                                    $("#" + modalId).modal('show');


                                    // Escuchar el evento solo si se disparó desde el form
                                    document.addEventListener('milestoneAssigned', function showTaskModal() {
                                        setTimeout(() => {


                                            document.removeEventListener('milestoneAssigned',
                                                showTaskModal);

                                            var createTaskUrl =
                                                '{{ route('tasks.create', $currentWorkspace->slug) }}' +
                                                '?project_id=' + project_id +
                                                '&milestoneTitle=' + encodeURIComponent(
                                                    milestoneTitle) +
                                                '&milestone_id=' + cardId +
                                                '&fromMilestoneBoard=true';
                                            var createTaskTitle = '{{ __('Create New Task') }}';

                                            $("#" + modalId + " .modal-title").html(createTaskTitle);
                                            $.ajax({
                                                url: createTaskUrl,
                                                dataType: 'html',
                                                success: function(taskData) {
                                                    $('#' + modalId + ' .body').html(
                                                        taskData);
                                                    $("#" + modalId).modal({
                                                        backdrop: 'static',
                                                        keyboard: false
                                                    });
                                                    $("#" + modalId).modal('show');
                                                    commonLoader();
                                                    loadConfirm();
                                                }
                                            });
                                        }, 2000);
                                    }, {
                                        once: true
                                    });

                                    commonLoader();
                                    loadConfirm();
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al cargar el modal de asignación:', error);
                                }
                            });
                        }

                        // Si se permite el movimiento y es de status 3 a 4, se genera una notificación

                        if (oldStatus == 3 && newStatus == 4) {
                            console.log('Generando notificacion de milestone completado');
                            let msg = milestoneTitle;
                            let ntipe = 3;
                            if (!msg) return;

                            fetch("{{ route('notifications.add') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        workspace_id: {{ $currentWorkspace->id }},
                                        msg: msg,
                                        ntipe: ntipe,
                                        milestoneAssignedTo: -2
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        let notificationList = document.querySelector('.limited');
                                        let newNotification = document.createElement('div');
                                        newNotification.classList.add('notificationSTL');
                                        newNotification.innerHTML = `
                    <span class="textRepo">${data.data.msg}</span>
                    <span class="textRepo">${data.data.type}</span>
                    <button type="button" class="btn-close repoIcon" aria-label="Close"></button>
                `;
                                        notificationList.prepend(newNotification);
                                    }
                                })
                                .catch(error => console.error("Error al agregar notificación:", error));
                        }
                        if (oldStatus == 2 && newStatus == 3) {
                            /////////////INICIO status 2 a 3///////////////////////////
                            var milestoneRequBy = a(el).find('#milestoneReqName').attr('data-technician-id');
                            var projectName = a(el).find('#milestoneReqName').attr('data-project-name');
                            var milestonetId = a(el).find('#milestoneReqName').attr('data-milestone-id');
                            console.log("El id del milestone es")
                            console.log(milestonetId);
                            $.ajax({
                                url: '{{ route('projects.milestone.checkTaskHours', [$currentWorkspace->slug, $milestone['id']]) }}',
                                type: 'GET',
                                data: {
                                    id: milestonetId
                                },
                                success: function(data) {
                                    if (data.all_exist) {
                                        console.log('Todas las tareas tienen timesheets.');
                                    } else {
                                        console.log('No todas las tareas tienen timesheets.');

                                        $.ajax({
                                            url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                                            type: 'POST',
                                            data: {
                                                id: milestonetId, // Se envía el milestone ID
                                                sort: sort,
                                                new_status: 2,
                                                old_status: 300, // Estado temporal
                                                project_id: project_id
                                            },
                                            success: function(response) {
                                                console.log('Cambio aplicado correctamente');

                                                // Actualizar el DOM: mover el elemento al contenedor correspondiente
                                                const milestoneCard = document.querySelector(
                                                    `.card[id='${milestonetId}']`);
                                                if (milestoneCard) {
                                                    // Actualizar el atributo data-status (si ya lo tienes definido en el HTML)
                                                    milestoneCard.setAttribute('data-status', 2);

                                                    // Mover el elemento al contenedor correspondiente (buscando por data-status)
                                                    const newContainer = document.querySelector(
                                                        `.kanban-box[data-status='2']`);
                                                    if (newContainer) {
                                                        newContainer.appendChild(milestoneCard);
                                                    }

                                                    // Actualizar contadores de tareas
                                                    updateTaskCount(source);
                                                    updateTaskCount(newContainer);
                                                }

                                                // Crear el toast dinámicamente
                                                const toastHTML = `
                        <div aria-live="polite" aria-atomic="true"
                             class="toast align-items-center text-white bg-primary border-0 toastNegation"
                             role="alert" id="successToast" data-bs-autohide="true" data-bs-delay="2000">
                            <div class="d-flex">
                                <div class="toast-body">
                                    {{ __('Todas las tareas tienen que tener horas inputadas') }}
                                </div>
                            </div>
                        </div>
                    `;

                                                // Buscar el elemento con data-title="Hoja de encargo" y añadir el toast encima
                                                const targetElement = document.querySelector(
                                                    '[data-title="Hoja de encargo"]');
                                                if (targetElement) {
                                                    // Inserta el toast justo antes del targetElement
                                                    $(targetElement).before(toastHTML);

                                                    // Inicializa y muestra el toast con Bootstrap
                                                    const toastElement = document.getElementById(
                                                        'successToast');
                                                    const toast = new bootstrap.Toast(toastElement);
                                                    toast.show();
                                                } else {
                                                    console.warn(
                                                        'No se encontró el elemento con data-title="Hoja de encargo".'
                                                    );
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error('Error al actualizar el orden:', error);
                                            }
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al verificar las horas de tarea:', error);
                                }
                            });








                            console.log("lo ha solicitado:");
                            console.log(milestoneRequBy)
                            console.log('Generando notificacion de milestone completado');
                            console.log('Titulo: ' +
                                milestoneTitle)
                            let msg = milestoneTitle + ' en el proyecto ' + projectName;
                            let ntipe = 5;
                            if (!msg) return;

                            fetch("{{ route('notifications.add') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        workspace_id: {{ $currentWorkspace->id }},
                                        msg: msg,
                                        ntipe: ntipe,
                                        milestoneAssignedTo: milestoneRequBy
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        let notificationList = document.querySelector('.limited');
                                        let newNotification = document.createElement('div');
                                        newNotification.classList.add('notificationSTL');
                                        newNotification.innerHTML = `
                <span class="textRepo">${data.data.msg}</span>
                <span class="textRepo">${data.data.type}</span>
                <button type="button" class="btn-close repoIcon" aria-label="Close"></button>
            `;
                                        notificationList.prepend(newNotification);
                                    }
                                })
                                .catch(error => console.error("Error al agregar notificación:", error));
                            /////////////FINAL status 2 a 3///////////////////////////
                        }

                        // Actualizamos los contadores de tareas en los contenedores de origen y destino
                        updateTaskCount(source);
                        updateTaskCount(target);

                        // *** Actualización dinámica del status en el elemento ***
                        // Esto asegura que, si se mueve la tarjeta y cambia su status,
                        // el atributo data-status se actualiza y el toggle lo detecta correctamente.
                        a(el).attr('data-status', newStatus);

                        // Se realiza la llamada AJAX para actualizar el orden y el estado en el servidor
                        a.ajax({
                            url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                            type: 'POST',
                            data: {
                                id: cardId, // Se envía el cardId obtenido
                                sort: sort,
                                new_status: newStatus,
                                old_status: oldStatus,
                                project_id: project_id
                            },
                            success: function(data) {
                                console.log('AJAX success');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al actualizar el orden:', error);
                            }
                        });
                    }

                    function updateTaskCount(container) {
                        var parentCardList = a(container).parents('.card-list');
                        var count = a(container).children('.card').length;
                        parentCardList.find('.count').text(count);
                    }

                    a.Dragula = new t;
                    a.Dragula.Constructor = t;

                }(window.jQuery);

                ! function(a) {
                    "use strict";
                    a.Dragula.init();
                }(window.jQuery);
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Delegación de eventos para manejar clicks dinámicos
                    document.body.addEventListener('click', function(e) {
                        const mileTitle = e.target.closest('.mileTitle');
                        if (!mileTitle) return;

                        const slug = mileTitle.dataset.projectSlug;
                        const milestoneId = mileTitle.dataset.milestoneId;
                        const viewLink = document.querySelector(`a[data-url*="/milestone/${milestoneId}/show"]`);

                        if (viewLink) {
                            // Simular click en el enlace "View" real
                            viewLink.click();
                        } else {
                            // Fallback manual
                            const url = `${window.location.origin}/projects/${slug}/milestone/${milestoneId}/show`;
                            const modal = new bootstrap.Modal(document.getElementById('commonModal'));

                            fetch(url)
                                .then(response => response.text())
                                .then(data => {
                                    document.getElementById('commonModal').querySelector('.modal-body')
                                        .innerHTML = data;
                                    modal.show();
                                });
                        }
                    });
                });
            </script>
            @if ($project_id == -1)
                <!-- Script encargado de mostrar/ocultar los proyectos completado -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let showCompleted = false; // Variable global para rastrear la visibilidad de proyectos completados

                        // Inicializa: Oculta grupos de milestones cuyo TODOS elementos tengan status 4
                        initializeCompletedProjects();

                        // Configura el listener para el toggle
                        const toggleIcon = document.getElementById('toggleCompletedProjectsIcon');
                        if (toggleIcon) {
                            toggleIcon.addEventListener('click', function() {
                                showCompleted = !showCompleted;
                                toggleCompletedProjects(showCompleted);
                                this.classList.toggle('showCompletedProjectsUnabled', !showCompleted);
                                this.title = showCompleted ? "{{ __('Hide Completed Projects') }}" :
                                    "{{ __('Show Completed Projects') }}";
                            });
                        }

                        function initializeCompletedProjects() {
                            const milestones = document.querySelectorAll('.card[data-project-id]');
                            const projectMap = new Map();

                            milestones.forEach(milestone => {
                                const projectId = milestone.dataset.projectId;
                                if (!projectMap.has(projectId)) {
                                    projectMap.set(projectId, []);
                                }
                                projectMap.get(projectId).push(milestone);
                            });

                            projectMap.forEach((milestones, projectId) => {
                                const allInStatus4 = milestones.every(m => parseInt(m.dataset.status) === 4);
                                if (allInStatus4) {
                                    milestones.forEach(m => m.style.display = 'none');
                                }
                            });
                        }

                        function toggleCompletedProjects(shouldShow) {
                            const milestones = document.querySelectorAll('.card[data-project-id]');
                            const projectMap = new Map();

                            milestones.forEach(milestone => {
                                const projectId = milestone.dataset.projectId;
                                if (!projectMap.has(projectId)) {
                                    projectMap.set(projectId, []);
                                }
                                projectMap.get(projectId).push(milestone);
                            });

                            projectMap.forEach((milestones, projectId) => {
                                const allInStatus4 = milestones.every(m => parseInt(m.dataset.status) === 4);
                                milestones.forEach(m => {
                                    m.style.display = (allInStatus4 && !shouldShow) ? 'none' : 'block';
                                    m.style.border = (allInStatus4 && shouldShow) ? '3px solid #15b500' :
                                        'none';
                                });
                            });
                        }

                        function checkAndUpdateProjectVisibility(el) {
                            const projectId = el.dataset.projectId;
                            const projectMilestones = document.querySelectorAll(`.card[data-project-id='${projectId}']`);
                            const allInStatus4 = Array.from(projectMilestones).every(m => parseInt(m.dataset.status) === 4);

                            if (allInStatus4 && !showCompleted) {
                                projectMilestones.forEach(m => m.style.display = 'none');
                            }
                        }

                        // Agrega un MutationObserver para detectar cambios en data-status y actualizar dinámicamente
                        function observeMilestoneStatusChanges() {
                            const milestoneCards = document.querySelectorAll('.card[data-project-id]');
                            milestoneCards.forEach(card => {
                                const observer = new MutationObserver(mutations => {
                                    mutations.forEach(mutation => {
                                        if (mutation.type === 'attributes' && mutation.attributeName ===
                                            'data-status') {
                                            checkAndUpdateProjectVisibility(card);
                                        }
                                    });
                                });
                                observer.observe(card, {
                                    attributes: true,
                                    attributeFilter: ['data-status']
                                });
                            });
                        }

                        observeMilestoneStatusChanges();
                    });
                </script>
            @endif
            <!-- Script encargado de la acción de "Add Task on Timesheet" al hacer clic en una tarea (se desactiva si el milestone está en status 4) -->
            <script>
                // Espera a que el DOM esté completamente cargado
                document.addEventListener('DOMContentLoaded', function() {

                    // Selecciona todos los elementos con la clase .taskList
                    const tasks = document.querySelectorAll('.taskList');

                    tasks.forEach(task => {
                        // Verifica si el técnico asignado es el usuario actual
                        const technicianId = task.getAttribute('data-technician-name');
                        const currentUserId = "{{ Auth::id() }}";

                        if (technicianId === currentUserId) {
                            task.addEventListener('click', function() {
                                // El resto del código del evento click se mantiene igual
                                const milestone = this.closest('.card');
                                const milestoneStatus = milestone.getAttribute('data-status');

                                if (milestoneStatus === '4' || milestoneStatus === '3') {
                                    console.log(
                                        'El milestone está en status 3 o 4, no se ejecutará la acción.');
                                    return;
                                }

                                const taskData = {
                                    task_id: this.getAttribute('data-task-id'),
                                    milestone_id: this.getAttribute('data-milestone-id'),
                                    project_id: this.getAttribute('data-project-id'),
                                    user_id: this.getAttribute('data-technician-name'),
                                    date: new Date().toISOString().split('T')[0],
                                };

                                $.ajax({
                                    url: '{{ route('create.timesheet.from.orders', [$currentWorkspace->slug, $project_id]) }}',
                                    type: 'GET',
                                    data: taskData,
                                    success: function(data) {
                                        $('#modal-container .modal-content').html(data);
                                        var myModal = new bootstrap.Modal(document
                                            .getElementById('modal-container'));
                                        myModal.show();
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error al actualizar el orden:', error);
                                    }
                                });
                            });
                        } else {
                            // Desactiva el evento click si el técnico asignado no es el usuario actual
                            task.addEventListener('click', function(event) {
                                event.stopPropagation();
                                event.preventDefault();
                            });
                            // Añade el estilo de cursor not-allowed
                            task.style.cursor = 'not-allowed';
                        }
                    });
                });
            </script>
            <!-- Script encargado de mostrar/ocultar las opciones cuando el estado del proyecto esta en 4 (en Hecho) -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Actualiza la visibilidad de las opciones de acción según el status
                    updateMilestoneActions();

                    // Toggle visibility of not assigned milestones
                    const hideUnassignedIcon = document.getElementById('hideUnassignedMilstoneIcon');
                    let hideUnassigned = false;

                    if (hideUnassignedIcon) {
                        hideUnassignedIcon.addEventListener('click', function() {
                            hideUnassigned = !hideUnassigned;
                            const notAssignedMilestones = document.querySelectorAll('.notAsignedMilestone');
                            notAssignedMilestones.forEach(milestone => {
                                milestone.style.display = hideUnassigned ? 'none' : 'block';
                            });

                            // Change icon color and hover text
                            this.style.filter = hideUnassigned ? 'grayscale(1)' : 'none';
                            this.title = hideUnassigned ? "{{ __('Show Unassigned Order Forms') }}" :
                                "{{ __('Hide Unasigned Order Forms') }}";
                        });
                    }

                    // Agrega un MutationObserver para detectar cambios en data-status y actualizar dinámicamente
                    function observeMilestoneStatusChanges() {
                        const milestoneCards = document.querySelectorAll('.card[data-project-id]');
                        milestoneCards.forEach(card => {
                            const observer = new MutationObserver(mutations => {
                                mutations.forEach(mutation => {
                                    if (mutation.type === 'attributes' && mutation.attributeName ===
                                        'data-status') {
                                        updateMilestoneActions();
                                    }
                                });
                            });
                            observer.observe(card, {
                                attributes: true,
                                attributeFilter: ['data-status']
                            });
                        });
                    }

                    function updateMilestoneActions() {
                        const milestoneCards = document.querySelectorAll('.card[data-project-id]');
                        milestoneCards.forEach(card => {
                            const status = parseInt(card.dataset.status);
                            const actionItems = card.querySelectorAll('.milestone-action');
                            if (status === 4 || status === 3) {
                                actionItems.forEach(item => item.style.display = 'none');
                            } else {
                                actionItems.forEach(item => item.style.display = '');
                            }
                        });
                    }

                    observeMilestoneStatusChanges();
                });
            </script>
            <!-- Script encargado de mostrar/ocultar la leyenda de colores -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const legendIcon = document.querySelector('.legendIcon');
                    const legend = document.querySelector('.legend');

                    // Configuración inicial
                    legend.style.opacity = '0';
                    legend.style.transform = 'translateY(-10px)';
                    legend.style.visibility = 'hidden';

                    legendIcon.addEventListener('click', function() {
                        const isVisible = legend.style.visibility === 'visible';

                        if (isVisible) {
                            legend.style.opacity = '0';
                            legend.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                legend.style.visibility = 'hidden';
                            }, 300); // Coincide con la duración de la transición
                        } else {
                            legend.style.visibility = 'visible';
                            legend.style.opacity = '1';
                            legend.style.transform = 'translateY(0)';
                        }

                        // Agregar animación de click al ícono
                        this.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            this.style.transform = 'scale(1)';
                        }, 100);
                    });
                });
            </script>
        @endpush
    @endif
