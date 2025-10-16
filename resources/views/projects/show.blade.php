@extends('layouts.admin')

@section('page-title')
    {{ __('Project Detail') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a>
    </li>
    <li class="breadcrumb-item">{{ $project->name }}</li>
@endsection
@php
    use Carbon\Carbon;

    $objUser = Auth::user();
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
@endphp

<style type="text/css">
    .fix_img {
        width: 40px !important;
        border-radius: 50%;
    }

    .buttonCenterText {
        padding-top: 7px !important;
    }

    .min-h {
        min-height: 180px;
    }

    .min-end {
        min-height: 300px;
    }

    .projectTitleH3 {
        text-align: center;
        font-size: 28px;
    }

    .projectDivSubtitle {
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        color: white;
        margin-bottom: 10px;
    }

    .uploaded-files-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .uploaded-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        min-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 95%
    }

    .uploaded-file p {
        margin: 0;
        font-size: 14px;
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .uploaded-file-buttons {
        display: flex;
        gap: 3px;
        align-items: center;
        padding-right: 5px;
    }

    .buttonFiles {
        background-color: #aa182c !important;
        width: 25px !important;
        height: 25px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 5px !important;
    }

    .buttonFiles:hover {
        background-color: #b9515f;
        color: white;
        text-decoration: none;
        border-color: #b9515f;
    }

    .plusIcon {
        margin-left: 10px;
    }

    .fatherMilestoneDiv {
        /* max-height: 140px; */
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
    }

    .custom-file-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
    }

    .custom-file {
        display: flex;
        justify-content: space-between;
        background: #f4f5ff;
        padding: 0.5%;
        margin: 5px;
        -moz-box-shadow: 10px 10px 5px 0px rgba(237, 237, 237, 1);
        box-shadow: -3px 3px 0px 0px rgb(239 239 239);
        border-radius: 6px;
    }

    .milestoneGridDisplay {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .styleIconFiles {
        width: 28px;
        height: 21px;
        padding-left: 5px;
        padding-right: 5px;
    }

    .size40AndBold {
        font-weight: bold;
        font-size: 40px;
    }

    .titleFiles {
        display: flex !important;
        align-items: baseline;
    }

    @media (max-width: 1300px) {
        .header_breadcrumb {
            width: 100% !important;
        }

        .row1 {
            display: flex;
            flex-wrap: wrap;
        }
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .widthAdjustDiv {
            width: 99% !important;
        }

        .widthAdjustMediumDiv {
            width: 49%;
        }

        .uploaded-files-container {
            gap: 5px;
        }

        .uploaded-file {
            width: 92%;
        }

        .last_notification_text {
            padding: 0 0 0 5px !important;
        }

        .last_notification_text p {
            margin-right: 10px !important;
            font-size: 9px !important;
        }
    }
</style>
@section('multiple-action-button')
    @if (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12" data-toggle="popover"
                title="{{ trans('messages.Shared_Project_Settings') }}" data-ajax-popup="true" data-size="md"
                data-title="{{ trans('messages.Shared_Project_Settings') }}"
                data-url="{{ route('projects.copylink.setting.create', [$currentWorkspace->slug, $project->id]) }}"
                data-toggle="tooltip" title="{{ __('Add Project') }}">
                <i class="ti ti-settings"></i>
            </a>
        </div>
    @endif
    <div class="col-lg-auto pb-3">
        <a href="{{ route('projects.milestone.board', [$currentWorkspace->slug, $project->id]) }}"
            class="btn btn-primary btn-task-milestone" title="{{ __('Milestones') }}"><i
                class="fa-solid fa-file-lines fileIcon me-3"></i>{{ __('Order forms') }}</a>
    </div>
    <div class="col-lg-auto pb-3">
        <a href="{{ route('projects.timesheet.index', [$currentWorkspace->slug, $project->id]) }}"
            class="btn btn-primary btn-task-milestone" title="{{ __('Tasks') }}"><i
                class="fas fa-tasks text-white me-3"></i>{{ __('Timesheet') }}</a>
    </div>
@endsection

<style type="text/css">
    .reqByImgContainer {
        display: flex;
        justify-content: center;
        align-items: center;
        align-content: center;
    }

    .assignedToImgContainer {
        text-align: center
    }

    .fix_img {
        width: 40px !important;
        border-radius: 50%;
    }

    .buttonCenterText {
        padding-top: 7px !important;
    }

    .min-h {
        min-height: 180px;
    }

    .min-end {
        min-height: 300px;
    }

    .projectTitleH3 {
        text-align: center;
        font-size: 28px;
    }

    .projectDivSubtitle {
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        color: white;
        margin-bottom: 10px;
    }

    .uploaded-files-container {
        display: grid;
        /* Cambia a un diseño de cuadrícula */
        grid-template-columns: repeat(3, 1fr);
        /* Limita a 3 elementos por fila */
        gap: 10px;
        /* Espaciado entre los archivos */
        /* max-height: 140px; */
        /* Limita la altura del contenedor */
        overflow-y: auto;
        /* Permite el desplazamiento vertical si hay demasiados archivos */
        overflow-x: hidden;
        /* Evita el desplazamiento horizontal */
    }

    .uploaded-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        min-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 95%
    }

    .uploaded-file p {
        margin: 0;
        font-size: 14px;
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    .buttonFiles {
        background-color: #aa182c;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
    }

    .buttonFiles:hover {
        background-color: #b9515f;
        color: white;
        text-decoration: none;
        border-color: #b9515f;
    }

    .plusIcon {
        margin-left: 10px;
    }

    .alignMiddle {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .activityContainer {
        max-height: 374px;
        overflow-y: scroll;
    }

    /* Scrollbar en navegadores WebKit */
    .activityContainer::-webkit-scrollbar {
        width: 8px;
        height: 10px;
        /* Ancho del scrollbar */
    }

    /* Fondo del scrollbar */
    .activityContainer::-webkit-scrollbar-track {
        background: #ffffff;
        /* Color del fondo */
        border-radius: 4px;
        /* Bordes redondeados */
    }

    /* Parte deslizable del scrollbar */
    .activityContainer::-webkit-scrollbar-thumb {
        background: #AA182C;
        /* Color del scrollbar */
        border-radius: 4px;
        height: 10px;
    }



    @media (max-width: 1300px) {
        .header_breadcrumb {
            width: 100% !important;
        }

        .row1 {
            display: flex;
            flex-wrap: wrap;
        }
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .widthAdjustDiv {
            width: 99% !important;
        }

        .widthAdjustMediumDiv {
            width: 49%;
        }

        .uploaded-files-container {
            gap: 5px;
        }

        .uploaded-file {
            width: 92%;
        }

        .last_notification_text {
            padding: 0 0 0 5px !important;
        }

        .last_notification_text p {
            margin-right: 10px !important;
            font-size: 9px !important;
        }
    }
</style>
<style>
    .sortable-header {
        cursor: pointer;
        position: relative;
    }

    .sortable-header:hover {
        background-color: #f8f9fa;
    }

    .sort-indicator {
        margin-left: 5px;
        font-size: 12px;
    }
</style>
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card bg-primary widthAdjustDiv">
                        <div class="card-body pb-2">
                            <div>
                                <h3 class="text-white projectTitleH3"> {{ $project->name }}</h3>
                            </div>
                            <div>
                                <div class="projectDivSubtitle">
                                    <div data-toggle="tooltip" data-placement="top" title="{{ __('Company') }}">
                                        <i class="fa-regular fa-building fa-xl me-2"></i>
                                        {{ $currentWorkspace->country }} / {{ $currentWorkspace->name }}
                                    </div>
                                    <div>
                                        <i class="fas fa-users fa-xl me-2"></i>
                                        {{ (int) $project->technicians->count() + (int) $project->salesManager->count() }}
                                    </div>

                                    <div>
                                        <i class="fas fas fa-calendar-day"></i>
                                        {{ App\Models\Utility::dateFormat($project->start_date) }}
                                    </div>

                                    <div>
                                        <i class="fa-solid fa-diagram-project  text-white"></i>
                                        {{ $project->ref_mo != '' ? $project->ref_mo : __($project->typeName()) }}
                                    </div>

                                    <div>
                                        @if ($project->status == 'Finished')
                                            <div class="badge bg-success p-2 px-3 rounded"> {{ __('Finished') }}
                                            </div>
                                        @elseif($project->status == 'Ongoing')
                                            <div class="badge bg-secondary p-2 px-3 rounded">
                                                {{ __('Ongoing') }}
                                            </div>
                                        @else
                                            <div class="badge bg-warning p-2 px-3 rounded">{{ __('OnHold') }}</div>
                                        @endif
                                    </div>

                                    <div>
                                        {{ __('Hours charged') }}: {{ $totalHours ? $totalHours : '00:00' }}h
                                    </div>

                                    <div>
                                        {{ __('Order forms createds') }}: {{ $totalMilestones ? $totalMilestones : '0' }}
                                    </div>
                                </div>

                                @if (!$project->is_active)
                                    <button class="btn btn-light d"> <a href="#" class=""
                                            title="{{ __('Locked') }}">
                                            <i data-feather="lock"> </i>
                                        </a></button>
                                @else
                                    @auth('web')
                                        @if ($objUser->type == 'admin')
                                            <div class="d-flex align-items-center ">

                                                <a href="#" class=""
                                                    data-url="{{ route('projects.edit', [$currentWorkspace->slug, $project->id]) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Edit Project') }}"
                                                    data-toggle="popover" title="{{ __('Edit') }}">
                                                    <button class="btn btn-light d-flex align-items-between me-3">
                                                        <i class="ti ti-edit"> </i>
                                                    </button>
                                                </a>
                                                <a href="#" class="bs-pass-para" data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $project->id }}" data-toggle="popover"
                                                    title="{{ __('Delete') }}">
                                                    <button class="btn btn-light d">
                                                        <i class="ti ti-trash"> </i>
                                                    </button>
                                                </a>
                                            </div>
                                            <form id="delete-form-{{ $project->id }}"
                                                action="{{ route('projects.destroy', [$currentWorkspace->slug, $project->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <form id="leave-form-{{ $project->id }}"
                                                action="{{ route('projects.leave', [$currentWorkspace->slug, $project->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 widthAdjustDiv">
                        @if ($currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner')
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Order forms') }}
                                                ({{ count($project->milestones) }})
                                            </h5>
                                        </div>
                                        <div class="float-end">
                                            <a href="#" class="btn btn-primary addMilestone" data-ajax-popup="true"
                                                data-title="{{ __('Milestone order') }}"
                                                data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project->id]) }}"
                                                data-toggle="popover"><i class="fa-solid fa-file-lines me-3"
                                                    style="color: #ffffff;"></i> {{ __('Create Order Form') }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 41vh;">
                                        <table id="" class="table table-bordered" style="text-align: center;">
                                            <thead>
                                                {{-- <tr>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Requested by') }}</th>
                                                    <th>{{ __('Assigned to') }}</th>
                                                    <th>{{ __('Status') }}</th> --}}


                                                {{-- <th>{{ __('Created date') }}</th>
                                                    <th>{{ __('Desired delivery date') }}</th>
                                                    <th>{{ __('Expected delivery date') }}</th>
                                                    <th>{{ __('Task started date') }}</th>
                                                    <th>{{ __('Completion date') }}</th> --}}


                                                {{-- <th>{{ __('Created') }}</th>
                                                    <th>{{ __('Desired delivery') }}</th>
                                                    <th>{{ __('Expected delivery') }}</th>
                                                    <th>{{ __('Task started') }}</th>
                                                    <th>{{ __('Completion') }}</th>

                                                    <th>{{ __('Action') }}</th>
                                                </tr> --}}
                                                <tr>
                                                    <th class="sortable-header" data-sort="title" data-type="text">
                                                        {{ __('Name') }}<span class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="requested_by"
                                                        data-type="text">{{ __('Requested by') }}<span
                                                            class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="assigned_to" data-type="text">
                                                        {{ __('Assigned to') }}<span class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="status" data-type="status">
                                                        {{ __('Status') }}<span class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="start_date" data-type="date">
                                                        {{ __('Created') }}<span class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="end_date" data-type="date">
                                                        {{ __('Desired delivery') }}<span class="sort-indicator"></span>
                                                    </th>
                                                    <th class="sortable-header" data-sort="planned_end_date"
                                                        data-type="date">{{ __('Expected delivery') }}<span
                                                            class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="task_start_date"
                                                        data-type="date">{{ __('Task started') }}<span
                                                            class="sort-indicator"></span></th>
                                                    <th class="sortable-header" data-sort="finalization_date"
                                                        data-type="date">{{ __('Completion') }}<span
                                                            class="sort-indicator"></span></th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($project->milestones as $key => $milestone)
                                                    <tr>
                                                        <td><a href="#" class="d-block font-weight-500 mb-0"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Order form details') }}"
                                                                data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone->id]) }}">
                                                                <h5 class="m-0"> {{ $milestone->title }} </h5>
                                                            </a>
                                                        </td>
                                                        <td class="reqByImgContainer">
                                                            @if ($milestone->getRequestedBy() != null)
                                                                <img class="fix_img"
                                                                    title="{{ $milestone->getRequestedBy()->name }}"
                                                                    @if ($milestone->getRequestedBy()->avatar) src="{{ asset($milestone->getRequestedBy()->avatar) }}" @else avatar="{{ $milestone->getRequestedBy()->name }}" @endif>
                                                            @endif
                                                        </td>
                                                        <td class="assignedToImgContainer">
                                                            @if ($milestone->getAssignedToUser() != null)
                                                                <img class="fix_img"
                                                                    title="{{ $milestone->getAssignedToUser()->name }}"
                                                                    @if ($milestone->getAssignedToUser()->avatar) src="{{ asset($milestone->getAssignedToUser()->avatar) }}" @else avatar="{{ $milestone->getAssignedToUser()->name }}" @endif>
                                                            @else
                                                                ...
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if ($milestone->status == 3)
                                                                <label
                                                                    class="badge bg-warning p-2 px-3 rounded">{{ __('For Review') }}</label>
                                                            @elseif ($milestone->status == 4)
                                                                <label
                                                                    class="badge bg-success p-2 px-3 rounded">{{ __('Finished') }}</label>
                                                            @else
                                                                <label
                                                                    class="badge p-2 px-3 rounded {{ $milestone->status == 1 ? 'bg-info' : 'bg-secondary' }}">
                                                                    {{ $milestone->status == 1 ? __('To Do') : __('Ongoing') }}
                                                                </label>
                                                            @endif
                                                        </td>
                                                        <td>{{ $milestone->start_date ? Carbon::parse($milestone->start_date)->format('d-m-Y') : '...' }}
                                                        </td>
                                                        <td>{{ $milestone->end_date ? Carbon::parse($milestone->end_date)->format('d-m-Y') : '...' }}
                                                        </td>
                                                        {{-- <td>{{ $milestone->planned_end_date ? Carbon::parse($milestone->planned_end_date)->format('d-m-Y') : '...' }}
                                                        </td> --}}
                                                        <td>
                                                            {{ $milestone->planned_end_date && $milestone->planned_end_date !== '0000-00-00'
                                                                ? \Carbon\Carbon::parse($milestone->planned_end_date)->format('d-m-Y')
                                                                : '...' }}
                                                        </td>
                                                        {{-- <td>{{ $milestone->planned_end_date }}</td> --}}
                                                        {{-- Task started date con lógica de color --}}
                                                        @php
                                                            // Determinar fecha de referencia (prevista o deseada)
                                                            $expectedDate = null;
                                                            if (
                                                                !empty($milestone->planned_end_date) &&
                                                                $milestone->planned_end_date !== '0000-00-00'
                                                            ) {
                                                                $expectedDate = \Carbon\Carbon::parse(
                                                                    $milestone->planned_end_date,
                                                                );
                                                            } elseif (
                                                                !empty($milestone->end_date) &&
                                                                $milestone->end_date !== '0000-00-00'
                                                            ) {
                                                                $expectedDate = \Carbon\Carbon::parse(
                                                                    $milestone->end_date,
                                                                );
                                                            }

                                                            $taskStartDate =
                                                                !empty($milestone->task_start_date) &&
                                                                $milestone->task_start_date !== '0000-00-00'
                                                                    ? \Carbon\Carbon::parse($milestone->task_start_date)
                                                                    : null;

                                                            $startColor = '';
                                                            if (
                                                                $taskStartDate &&
                                                                $expectedDate &&
                                                                $taskStartDate->gt($expectedDate)
                                                            ) {
                                                                // Si la tarea comenzó después de la entrega prevista/deseada
                                                                $startColor = '#db8d33';
                                                            }
                                                        @endphp
                                                        <td style="color: {{ $startColor }}">
                                                            {{ $taskStartDate ? $taskStartDate->format('d-m-Y') : '...' }}
                                                        </td>

                                                        {{-- Completion date con lógica de color --}}
                                                        @php
                                                            $completionDate =
                                                                !empty($milestone->finalization_date) &&
                                                                $milestone->finalization_date !== '0000-00-00'
                                                                    ? \Carbon\Carbon::parse(
                                                                        $milestone->finalization_date,
                                                                    )
                                                                    : null;

                                                            $completionColor = '';
                                                            if ($completionDate && $expectedDate) {
                                                                if ($completionDate->lte($expectedDate)) {
                                                                    // Completado a tiempo o antes → verde
                                                                    $completionColor = '#53b446';
                                                                } else {
                                                                    // Completado después → rojo
                                                                    $completionColor = '#ff0000';
                                                                }
                                                            }
                                                        @endphp
                                                        <td style="color: {{ $completionColor }}">
                                                            {{ $completionDate ? $completionDate->format('d-m-Y') : '...' }}
                                                        </td>

                                                        </td>
                                                        <td class="text-right">
                                                            <div class="col-auto">
                                                                <a href="#"
                                                                    class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                    data-ajax-popup="true" data-size="lg"
                                                                    data-toggle="popover" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Milestone') }}"
                                                                    data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                        class="ti ti-edit"></i></a>
                                                                <a href="#"
                                                                    class="action-btn bg-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-toggle="popover" title="{{ __('Delete') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                        class="ti ti-trash"></i></a>
                                                                <form id="delete-form1-{{ $milestone->id }}"
                                                                    action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>
                    {{-- ======================================================================================== --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">
                                                {{ __('Average delivery time') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body alignMiddle">
                                    <span class="size40AndBold">
                                        {{ $averageDelivery }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">
                                                {{ __('Average working time') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body alignMiddle">
                                    <span class="size40AndBold">
                                        {{ $averageWorkingTime }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">
                                                {{ __('Average start-up time') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body alignMiddle">
                                    <span class="size40AndBold">
                                        {{ $averageStartUpTime }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">
                                                {{ __('Average delay time') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body alignMiddle">
                                    <span class="size40AndBold">
                                        {{ $averageDelayTime }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- Usuarios que han creado un encargo --}}
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{-- Usuarios que han creado milestones --}}
                                            <h5 class="mb-0">{{ __('Users who created Order forms') }}
                                                ({{ count($milestoneCreators) }})
                                            </h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-sm-flex align-items-center mb-0">
                                                @if (\Auth::user()->type == 'admin')
                                                    <a href="#" class="btn btn-sm btn-primary "
                                                        data-ajax-popup="true" data-title="{{ __('Invite') }}"
                                                        data-toggle="popover" title="{{ __('Invite') }}"
                                                        data-url="{{ route('projects.invite.popup', [$currentWorkspace->slug, $project->id]) }}">
                                                        <i class="ti ti-brand-telegram"></i>
                                                    </a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body pb-1">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($milestoneCreators as $user)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        @if ($user->avatar) src="{{ asset($user->avatar) }}" 
                                                @else avatar="{{ $user->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $user->name }}</h5>
                                                                    <small class="text-muted">
                                                                        {{ $user->email }}
                                                                        <span class="text-primary">
                                                                            - {{ $user->milestones_count }}
                                                                            {{ __('Order forms') }}
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                @if (\Auth::user()->type == 'admin')
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Permission') }}"
                                                                        data-title="{{ __('Edit Permission') }}"
                                                                        data-url="{{ route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id]) }}">
                                                                        <i class="ti ti-lock"></i>
                                                                    </a>
                                                                    <a href="#"
                                                                        class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover" title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-user-{{ $user->id }}">
                                                                        <i class="ti ti-trash ml-1"></i>
                                                                    </a>
                                                                    <form id="delete-user-{{ $user->id }}"
                                                                        action="{{ route('projects.user.delete', [$currentWorkspace->slug, $project->id, $user->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Usuarios que han imputado horas --}}
                        <div class="col-md-6 widthAdjustMediumDiv">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Users with imputed hours') }}
                                                ({{ count($usersWithHours) }})
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pb-1">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($usersWithHours as $user)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class="text-start">
                                                                    <img class="fix_img"
                                                                        @if ($user->avatar) src="{{ asset($user->avatar) }}"
                                                @else avatar="{{ $user->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $user->name }}</h5>
                                                                    <small class="text-muted">
                                                                        {{ $user->email }}
                                                                        <span class="text-primary">
                                                                            -
                                                                            {{ $user->total_time ? substr($user->total_time, 0, 5) : '00:00' }}h
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Si quieres añadir acciones de admin como antes --}}
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                @if (\Auth::user()->type == 'admin')
                                                                    {{-- Aquí podrías añadir botones de acciones si quieres --}}
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"> {{ __('Files') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 col-md-12" style="min-height: 374px;">
                                    <div class="author-box-name form-control-label mb-4"></div>
                                    <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                        <div class="dz-message" data-dz-message>
                                            <span> {{ __('Drop files here to upload') }}</span>
                                            <p>
                                                {{ __('You can Also hold click + Control + V to paste the content of the clipboard') }}
                                            </p>
                                            <p class="text-muted" style="font-size:15px; margin:5px;">200MB</p>
                                            <small class="text-muted">.png .gif .pdf .txt .doc .docx .zip .rar .dwg
                                                .dxf</small>
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md-12">
                                        <div class="titleFiles">
                                            <i class="fa-regular fa-folder-open d-inline me-2 fa-xl"></i>
                                            <h5>{{ __('Project files') }}</h5>
                                        </div>
                                        <div class="custom-file-container ms-4">
                                            @if (!empty($projectFiles) && count($projectFiles) > 0)
                                                @foreach ($projectFiles as $file)
                                                    <div class="custom-file">
                                                        <img src="{{ asset('assets/iconFilesTypes/' . $file->extension . '.png') }}"
                                                            alt="{{ $file->extension }} icon"
                                                            class="styleIconFiles mt-2">
                                                        <p class="m-2"
                                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $file->file_name }}
                                                        </p>
                                                        <div class="uploaded-file-buttons">
                                                            <a onclick="downloadFile({{ $project->id }}, '', '{{ $file->file_path }}')"
                                                                class="buttonFiles btn btn-sm">
                                                                <i class="ti ti-download" style="color:white"></i>
                                                            </a>
                                                            <a class="bs-pass-para buttonFiles btn btn-sm"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-toggle="popover" title="{{ __('Delete File') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-file-{{ $file->id }}">
                                                                <i class="fa-solid fa-trash" style="color:white"></i>
                                                            </a>
                                                            <form id="delete-file-{{ $file->id }}"
                                                                action="{{ route('project.deleteFile', ['idProject' => $project->id, 'milestoneTitle' => '', 'fileID' => $file->id]) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">{{ __('No project files uploaded yet.') }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-4 titleFiles">
                                            <i class="fa-regular fa-folder-open d-inline me-2 fa-xl"></i>
                                            <h6>{{ __('Milestone files') }}</h6>
                                        </div>
                                        <div class="col-md-12">
                                            <!-- Sección de archivos de Milestones -->
                                            @if (!empty($milestoneFiles) && count($milestoneFiles) > 0)
                                                @foreach ($milestoneFiles as $milestone)
                                                    <div class="milestone-files mb-4">
                                                        <div class="ms-4 mt-2">
                                                            <div class="titleFiles">
                                                                <i class="fa-solid fa-file-lines me-2 text-dark"></i>
                                                                <h6>{{ $milestone['title'] }}</h6>
                                                            </div>
                                                            <hr class="mt-0" style="border: 1px solid #eeeeee;">
                                                        </div>

                                                        <div class="custom-file-container ms-4">
                                                            @if (!empty($milestone['files']) && count($milestone['files']) > 0)
                                                                @foreach ($milestone['files'] as $file)
                                                                    <div class="custom-file">
                                                                        <img src="{{ asset('assets/iconFilesTypes/' . $file->extension . '.png') }}"
                                                                            alt="{{ $file->extension }} icon"
                                                                            class="styleIconFiles mt-2">
                                                                        <p class="m-2"
                                                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                            {{ $file->name }}
                                                                        </p>
                                                                        <div class="uploaded-file-buttons">
                                                                            <a onclick="downloadFile({{ $project->id }}, '{{ $milestone['title'] }}', '{{ $file->file }}')"
                                                                                class="buttonFiles btn btn-sm">
                                                                                <i class="ti ti-download"
                                                                                    style="color:white"></i>
                                                                            </a>
                                                                            <a class="bs-pass-para buttonFiles btn btn-sm"
                                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                                data-toggle="popover"
                                                                                title="{{ __('Delete File') }}"
                                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                data-confirm-yes="delete-file-{{ $file->id }}">
                                                                                <i class="fa-solid fa-trash"
                                                                                    style="color:white"></i>
                                                                            </a>
                                                                            <form id="delete-file-{{ $file->id }}"
                                                                                action="{{ route('project.deleteFile', ['idProject' => $project->id, 'milestoneTitle' => $milestone['title'], 'fileID' => $file->id]) }}"
                                                                                method="POST" style="display: none;">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p class="text-muted">
                                                                    {{ __('No files uploaded for this milestone.') }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Activity') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 activityContainer">
                                    <div class="timeline timeline-one-side" data-timeline-content="axis"
                                        data-timeline-axis-style="dashed">
                                        @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')
                                            @foreach ($project->activities as $activity)
                                                <div class="timeline-block px-2 pt-3">
                                                    @if ($activity->log_type == 'Upload File')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-file"></i></span>
                                                    @elseif($activity->log_type == 'Create Milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'Create Timesheet')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-clock-o"></i></span>
                                                    @elseif($activity->log_type == 'has delete a file')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-primary text-white"
                                                            style="border-color: #aa182c !important;">
                                                            <i class="fas fa-file"></i></span>
                                                    @elseif($activity->log_type == 'has created a new project')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fa-solid fa-diagram-project"></i></span>
                                                    @elseif($activity->log_type == 'has updated a milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'has updated the milestone status to')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'has deleted a milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-primary text-white"
                                                            style="border-color: #aa182c !important;">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @endif
                                                    <div class="last_notification_text">
                                                        <!-- Person who did the notification -->
                                                        <p> {!! $activity->getRemark() !!} : </p>
                                                        <br>
                                                        <div class="notification_time_main">
                                                            <p>{{ $activity->created_at->diffForHumans() }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        function downloadFile(idProject, titleMilestone, file) {

            const downloadUrl = "{{ route('project.downloadFile') }}";
            $.ajax({
                url: downloadUrl,
                method: 'POST',
                data: {
                    "idProject": idProject,
                    "milestoneTitle": titleMilestone,
                    "fileName": file,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        console.log("Download URL: ", response.file_url);

                        // Crear un enlace temporal para descargar el archivo
                        let downloadLink = document.createElement("a");
                        downloadLink.href = response.file_url;
                        downloadLink.target = "_blank";
                        downloadLink.download = file; // Nombre del archivo
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    } else {
                        alert("Error: File not found.");
                    }
                },
                error: function(xhr) {
                    alert("An error occurred while downloading the file.");
                    console.error(xhr.responseText);
                }
            });
        }

        (function() {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: {!! json_encode($chartData['color']) !!},
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [
                    @foreach ($chartData['stages'] as $id => $name)
                        {
                            name: "{{ __($name) }}",
                            // data: 
                            data: {!! json_encode($chartData[$id]) !!},
                        },
                    @endforeach
                ],
                xaxis: {
                    type: "category",
                    categories: {!! json_encode($chartData['label']) !!},
                    title: {
                        text: '{{ __('Days') }}'
                    },
                    tooltip: {
                        enabled: false,
                    }
                },
                yaxis: {
                    show: true,
                    position: "left",
                    title: {
                        text: '{{ __('Tasks') }}'
                    },
                },
                grid: {
                    show: true,
                    borderColor: "#EBEBEB",
                    strokeDashArray: 0,
                    position: "back",
                    xaxis: {
                        show: true,
                        lines: {
                            show: true,
                        },
                    },
                    yaxis: {
                        show: false,
                        lines: {
                            show: false,
                        },
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5,
                    },
                    column: {
                        position: "back",
                        colors: undefined,
                        opacity: 0.5,
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0,
                    },
                },
                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },

                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#task-chart"), options);
            chart.render();
        })();
    </script>
    <script src="{{ asset('assets/custom/libs/nicescroll/jquery.nicescroll.min.js') }} "></script>
    <script>
        $(document).ready(function() {

            $(".uploaded-files-container").niceScroll();

            if ($(".top-10-scroll").length) {
                $(".top-10-scroll").niceScroll();
            }

        });
    </script>
    <script src="{{ asset('assets/custom/js/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            maxFilesize: 209715200, // Tamaño máximo = 200MB
            parallelUploads: 1,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg,.pdf,.txt,.doc,.docx,.zip,.rar,.dwg,.dxf",
            url: "{{ route('projects.file.upload', [$currentWorkspace->slug, $project->id]) }}",

            success: function(file, response) {
                if (response.is_success) {
                    show_toastr('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('{{ __('Error') }}', 'Error while storing the document.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                show_toastr('{{ __('Error') }}', 'Error while storing the document.', 'error');
            },

            complete: function(file) {
                // Verifica si Dropzone ha terminado con todos los archivos
                if (this.getQueuedFiles().length === 0 && this.getUploadingFiles().length === 0) {
                    // Recarga la página después de que se suban todos los archivos
                    setTimeout(function() {
                            location.reload(); // Recarga la página actual
                        },
                        1000
                    ); // Opcional: Agrega un pequeño delay para asegurarte de que el backend procese todo.
                }
            }
        });
        // Función para agregar un botón de eliminación al archivo en Dropzone
        function addDeleteButton(file, filePath) {
            const deleteButton = Dropzone.createElement(`
                <button class="btn btn-sm btn-danger ml-2">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `);

            // Agregar evento al botón de eliminación
            deleteButton.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (confirm("Are you sure you want to delete this file?")) {
                    deleteFile({{ $project->id }}, filePath); // Usar la lógica de eliminación existente
                    myDropzone.removeFile(file);
                }
            });

            // Agregar el botón de eliminación al contenedor del archivo
            file.previewElement.appendChild(deleteButton);
        }

        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("project_id", {{ $project->id }});
        });

        @if (isset($permisions) && in_array('show uploading', $permisions))
            $(".dz-hidden-input").prop("disabled", true);
            myDropzone.removeEventListeners();
        @endif

        function dropzoneBtn(file, response) {

            var html = document.createElement('span');
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "popover");
            download.setAttribute('download', "");
            download.setAttribute('title', "{{ __('Download') }}");
            // download.innerHTML = "<i class='fas fa-download mt-2'></i>";
            download.innerHTML = "<i class='ti ti-download'> </i>";
            html.appendChild(download);

            @if (isset($permisions) && in_array('show uploading', $permisions))
            @else
                var del = document.createElement('a');
                del.setAttribute('href', response.delete);
                del.setAttribute('class', "action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center");
                del.setAttribute('data-toggle', "popover");
                del.setAttribute('title', "{{ __('Delete') }}");
                del.innerHTML = "<i class='ti ti-trash '></i>";

                del.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm("Are you sure ?")) {
                        var btn = $(this);
                        $.ajax({
                            url: btn.attr('href'),
                            type: 'DELETE',
                            success: function(response) {
                                if (response.is_success) {
                                    btn.closest('.dz-image-preview').remove();
                                    show_toastr('{{ __('Success') }}', 'File Successfully Deleted',
                                        'success');
                                } else {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                }
                            },
                            error: function(response) {
                                response = response.responseJSON;
                                if (response.is_success) {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                } else {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                }
                            }
                        })
                    }
                });
                html.appendChild(del);
            @endif

            file.previewTemplate.appendChild(html);
        }
        @php($setting = App\Models\Utility::getAdminPaymentSettings())

        @php($files = $project->files)
        @foreach ($files as $file)
            @php($storage_file = asset($logo_project_files . $file->file_path))

            // Create the mock file:
            @if (Storage::disk($setting['storage_setting'])->exists('/project_files/' . $file->file_path))

                var mockFile = {
                    name: "{{ $file->file_name }}",
                    size: {{ filesize('storage/project_files/' . $file->file_path) }}
                };
            @endif
            // Call the default addedfile event handler
            myDropzone.emit("addedfile", mockFile);
            // And optionally show the thumbnail of the file:
            myDropzone.emit("thumbnail", mockFile, "{{ asset($logo_project_files . $file->file_path) }}");
            myDropzone.emit("complete", mockFile);

            dropzoneBtn(mockFile, {
                download: "{{ route('projects.file.download', [$currentWorkspace->slug, $project->id, $file->id]) }}",
                delete: "{{ route('projects.file.delete', [$currentWorkspace->slug, $project->id, $file->id]) }}"
            });
        @endforeach
    </script>
    {{-- Sorting  table script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortableHeaders = document.querySelectorAll('.sortable-header');
            let currentSort = {
                key: null,
                direction: 'asc' // 'asc' o 'desc'
            };

            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const sortKey = this.dataset.sort;
                    const sortType = this.dataset.type;

                    // Determinar dirección
                    if (currentSort.key === sortKey) {
                        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.key = sortKey;
                        currentSort.direction = 'asc';
                    }

                    sortTable(sortKey, sortType, currentSort.direction);
                    updateSortIndicators(this);
                });
            });

            function sortTable(sortKey, sortType, direction) {
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                rows.sort((a, b) => {
                    const aValue = getCellValue(a, sortKey);
                    const bValue = getCellValue(b, sortKey);

                    return compareValues(aValue, bValue, sortType, direction);
                });

                // Limpiar y reinsertar filas ordenadas
                tbody.innerHTML = '';
                rows.forEach(row => tbody.appendChild(row));
            }

            function getCellValue(row, sortKey) {
                const cells = row.querySelectorAll('td');
                switch (sortKey) {
                    case 'title':
                        return row.querySelector('td:nth-child(1) h5').textContent.trim();

                    case 'requested_by':
                        return row.querySelector('td:nth-child(2) img')?.title?.trim() || '';

                    case 'assigned_to':
                        return row.querySelector('td:nth-child(3) img')?.title?.trim() || '';

                    case 'status':
                        return row.querySelector('td:nth-child(4) label').textContent.trim();

                    case 'start_date':
                    case 'end_date':
                    case 'planned_end_date':
                    case 'task_start_date':
                    case 'finalization_date':
                        const idx = Array.from(sortableHeaders).findIndex(h => h.dataset.sort === sortKey);
                        const dateStr = cells[idx].textContent.trim();
                        return parseDate(dateStr);

                    default:
                        return '';
                }
            }

            function compareValues(a, b, type, direction) {
                const modifier = direction === 'asc' ? 1 : -1;

                if (type === 'text' || type === 'status') {
                    // Sort alphabetically, case-insensitive (español)
                    return a.localeCompare(b, 'es', {
                        sensitivity: 'base'
                    }) * modifier;
                } else if (type === 'date') {
                    return (a - b) * modifier;
                }

                return 0;
            }

            function parseDate(dateStr) {
                if (dateStr === '...') return 0;
                const [day, month, year] = dateStr.split('-');
                return new Date(year, month - 1, day);
            }

            function updateSortIndicators(activeHeader) {
                sortableHeaders.forEach(header => {
                    header.querySelector('.sort-indicator').textContent = '';
                    if (header === activeHeader) {
                        header.querySelector('.sort-indicator').textContent =
                            currentSort.direction === 'asc' ? ' ↑' : ' ↓';
                    }
                });
            }
        });
    </script>
    <script>
        /**
         * Integración de subida de archivos por Ctrl+V, drag & drop y selección manual
         * - El <div class="dropzone" tabindex="0"> permite foco y captura de “paste”
         * - El <input type="file" id="fileInput" multiple style="display: none;"> permite selección manual
         * - El CSS ya define borde punteado y efecto dragover
         * - La función handleFiles(files) es central y única para todos los métodos
         */

        // --- Selección de elementos ---
        const dropzone = document.querySelector('.dropzone');
        let fileInput = document.getElementById('fileInput');
        if (!fileInput) {
            fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.id = 'fileInput';
            fileInput.multiple = true;
            fileInput.style.display = 'none';
            dropzone.parentNode.insertBefore(fileInput, dropzone.nextSibling);
        }

        // --- Extensiones y tipos MIME permitidos ---
        const allowed = [{
                ext: 'png',
                mime: 'image/png'
            },
            {
                ext: 'gif',
                mime: 'image/gif'
            },
            {
                ext: 'pdf',
                mime: 'application/pdf'
            },
            {
                ext: 'txt',
                mime: 'text/plain'
            },
            {
                ext: 'doc',
                mime: 'application/msword'
            },
            {
                ext: 'docx',
                mime: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            },
            {
                ext: 'zip',
                mime: 'application/zip'
            },
            {
                ext: 'rar',
                mime: 'application/vnd.rar'
            },
            {
                ext: 'rar',
                mime: 'application/x-rar-compressed'
            },
            {
                ext: 'dwg',
                mime: 'application/acad'
            },
            {
                ext: 'dwg',
                mime: 'application/autocad_dwg'
            },
            {
                ext: 'dxf',
                mime: 'application/dxf'
            }
        ];
        const allowedExts = allowed.map(a => a.ext);
        const allowedMimes = allowed.map(a => a.mime);

        // --- Drag & Drop visual feedback ---
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        dropzone.addEventListener('dragleave', function(e) {
            dropzone.classList.remove('dragover');
        });
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                handleFiles(Array.from(e.dataTransfer.files));
                // Al acabar de procesar, refocuseamos
                dropzone.focus();
            }
        });

        // --- Selección manual desde input file ---
        dropzone.addEventListener('dblclick', function() {
            fileInput.value = '';
            fileInput.click();
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files.length) {
                handleFiles(Array.from(fileInput.files));
                dropzone.focus();
            }
        });

        // --- Permitimos que la dropzone reciba foco y capture paste ---
        dropzone.setAttribute('tabindex', '0'); // hace que se pueda enfocar

        // Si el usuario hace clic en la dropzone (cualquier parte), la enfocamos
        dropzone.addEventListener('click', () => {
            dropzone.focus();
        });

        // --- Capturar paste a nivel de document, pero sólo procesar si foco está dentro de dropzone ---
        document.addEventListener('paste', function(e) {
            // Si el elemento actualmente enfocado NO es la dropzone ni ninguno de sus hijos, salimos
            const focused = document.activeElement;
            if (focused !== dropzone && !dropzone.contains(focused)) {
                return;
            }

            e.preventDefault(); // Evitamos comportamiento nativo no deseado

            if (!e.clipboardData || !e.clipboardData.items) {
                return;
            }

            const items = Array.from(e.clipboardData.items);
            const conversionPromises = items.map(item => {
                if (item.kind !== 'file') {
                    return Promise.resolve(null);
                }
                const file = item.getAsFile();
                if (!file) {
                    return Promise.resolve(null);
                }
                const ext = file.name.split('.').pop().toLowerCase();
                const mime = file.type;

                // Si es PNG o GIF → convertir a JPG
                if (
                    mime === 'image/png' || mime === 'image/gif' ||
                    ext === 'png' || ext === 'gif'
                ) {
                    return new Promise(resolve => {
                        convertImageToJPG(file, function(jpgFile) {
                            resolve(jpgFile);
                        });
                    });
                }

                // Si es otro formato permitido, devolvemos el File tal cual
                if (
                    allowedExts.includes(ext) ||
                    allowedMimes.includes(mime)
                ) {
                    return Promise.resolve(file);
                }

                // De lo contrario, no lo tomamos
                return Promise.resolve(null);
            });

            Promise.all(conversionPromises).then(results => {
                const archivosValidos = results.filter(f => f instanceof File);
                if (archivosValidos.length > 0) {
                    handleFiles(archivosValidos);
                } else {
                    alert('El portapapeles no contiene un archivo válido');
                }
                // Refocuseamos la dropzone para seguir recibiendo Ctrl+V indefinidamente
                dropzone.focus();
            });
        });

        // --- Función ÚNICA para procesar archivos subidos (pegados, arrastrados o seleccionados) ---
        function handleFiles(files) {
            files.forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase();
                const mime = file.type;

                // 1) Si el archivo ya es un JPEG (resultado de la conversión), lo subimos directamente
                if (mime === 'image/jpeg') {
                    myDropzone.addFile(file);
                    return;
                }

                // 2) Si es PNG o GIF (arrastrado o seleccionado manualmente), convertimos a JPG
                if (
                    mime === 'image/png' || mime === 'image/gif' ||
                    ext === 'png' || ext === 'gif'
                ) {
                    convertImageToJPG(file, function(jpgFile) {
                        myDropzone.addFile(jpgFile);
                    });
                    return;
                }

                // 3) Si es cualquier otro formato permitido, lo subimos tal cual
                if (
                    allowedExts.includes(ext) ||
                    allowedMimes.includes(mime)
                ) {
                    myDropzone.addFile(file);
                    return;
                }

                // 4) Cualquier otro, ignorar completamente
            });
        }

        // --- Conversión de imagen PNG/GIF a JPG usando canvas ---
        function convertImageToJPG(blobOrFile, callback) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    canvas.toBlob(function(jpgBlob) {
                        // Conservamos el nombre y cambiamos extensión a .jpg
                        const nuevoNombre = (blobOrFile.name || 'clipboard').replace(/\.(png|gif)$/i,
                            '.jpg');
                        const jpgFile = new File([jpgBlob], nuevoNombre, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });
                        callback(jpgFile);
                    }, 'image/jpeg', 0.92);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(blobOrFile);
        }

        // --- Evento para asegurar que Dropzone vuelve a enfocar tras cada archivo añadido ---
        // Suponiendo que ya inicializaste `myDropzone = new Dropzone(...)` en algún punto anterior:
        // Así te aseguras de que, aunque Dropzone injecte previews u otros elementos que roben foco,
        // la dropzone recupere inmediatamente el foco.
        if (window.myDropzone) {
            myDropzone.on('addedfile', function() {
                // Tras cada archivo agregado, refocuseamos
                dropzone.focus();
            });
        }
    </script>
@endpush
