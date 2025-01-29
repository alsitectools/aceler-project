@extends('layouts.admin')

@section('page-title')
    {{ __('Project Detail') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a>
    </li>
    <li class="breadcrumb-item">{{ $project->name }}</li>
@endsection
@php
    $objUser = Auth::user();
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
@endphp
<style>
    .btn-task-milestone {
        display: flex !important;
        justify-content: space-evenly !important;
        align-content: center;
        width: 180px;
        text-align: center;
        align-items: center;

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
                class="fa-solid fa-file-lines"></i>{{ __('Milestones') }}</a>
    </div>
    <div class="col-lg-auto pb-3">
        <a href="{{ route('projects.timesheet.index', [$currentWorkspace->slug, $project->id]) }}"
            class="btn btn-primary btn-task-milestone" title="{{ __('Tasks') }}"><i
                class="fas fa-tasks text-white"></i>{{ __('Tasks') }}</a>
    </div>
@endsection

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
                                <h3 class="text-white pb-3"> {{ $project->name }}</h3>
                            </div>
                            <div class="d-block d-sm-flex">
                                <div style="font-size: 14px;" class="col-sm-12 d-flex  align-items-center row1 text-white">
                                    <div class="col-3" data-toggle="tooltip" data-placement="top"
                                        title="{{ __('Company') }}">
                                        <i class="fa-regular fa-building fa-xl me-2"></i>
                                        {{ $currentWorkspace->country }}
                                    </div>
                                    <div class="col-3" data-toggle="tooltip" data-placement="top"
                                        title="{{ __('Branch') }}">
                                        <i class="fa-solid fa-location-dot fa-xl me-2"></i>
                                        {{ $currentWorkspace->name }}
                                    </div>
                                    <div class="col-3 d-inline">
                                        <i class="fas fa-users fa-xl me-2"></i>
                                        {{ (int) $project->technicians->count() + (int) $project->salesManager->count() }}
                                    </div>
                                    <div class="col-3">
                                        @if ($project->status == 'Finished')
                                            <div class="badge bg-success rounded"> {{ __('Finished') }}
                                            </div>
                                        @elseif($project->status == 'Ongoing')
                                            <div class="badge bg-secondary rounded buttonCenterText"
                                                style="width: 75px !important; height: 25px !important;">
                                                {{ __('Ongoing') }}
                                            </div>
                                        @else
                                            <div class="badge bg-warning rounded">{{ __('OnHold') }}</div>
                                        @endif
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
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-primary">
                                            <i class="fas fas fa-calendar-day"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Created date') }}</h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 ">{{ App\Models\Utility::dateFormat($project->start_date) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-tasks text-white"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Tasks') }}</h6>
                                            <span class="h6 font-weight-bold mb-0 ">{{ $project->countTask() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar" style="background-color: #B197FC;">
                                            <i class="fa-solid fa-file-lines fa-lg text-white"
                                                style="color: #B197FC;"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Milestones') }}</h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 ">{{ $project->milestonescount() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-success">
                                            <i class="fa-solid fa-diagram-project bg-success text-white"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">
                                                {{ $project->type != 1 ? __('Project type') : __('MO') }}</h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 ">{{ $project->ref_mo != '' ? $project->ref_mo : __($project->typeName()) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 widthAdjustDiv">
                        @if ($currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner')
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Milestones') }} ({{ count($project->milestones) }})
                                            </h5>
                                        </div>
                                        <div class="float-end">
                                            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true"
                                                data-title="{{ __('Milestone order') }}"
                                                data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project->id]) }}"
                                                data-toggle="popover" title="{{ __('Create') }}"><i
                                                    class="ti ti-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Created date') }}</th>
                                                    <th>{{ __('Desired delivery date') }}</th>
                                                    <th>{{ __('Task started') }}</th>
                                                    <th>{{ __('Last update') }}</th>
                                                    @if ($objUser->type == 'client' || $currentWorkspace->permission == 'Owner')
                                                        <th>{{ __('Action') }}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($project->milestones as $key => $milestone)
                                                    <tr>
                                                        <td><a href="#" class="d-block font-weight-500 mb-0"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Milestone Details') }}"
                                                                data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone->id]) }}">
                                                                <h5 class="m-0"> {{ $milestone->title }} </h5>
                                                            </a>
                                                        </td>
                                                        <td>{{ $milestone->start_date }}</td>
                                                        <td>{{ $milestone->end_date }}</td>
                                                        <td>{{ $milestone->taskStart() }}</td>
                                                        <td>{{ $milestone->taskEnd() }}</td>
                                                        <td class="text-right">
                                                            <div class="col-auto">
                                                                @if ($milestone->assign_to == $objUser->id)
                                                                    <a href="#"
                                                                        class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('Edit Milestone') }}"
                                                                        data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                            class="ti ti-edit"></i></a>
                                                                @elseif($currentWorkspace->permission == 'Owner')
                                                                    <a href="#"
                                                                        class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="{{ __('Edit Milestone') }}"
                                                                        data-toggle="popover" title="{{ __('Edit') }}"
                                                                        data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                            class="ti ti-edit"></i></a>

                                                                    @if ($currentWorkspace->permission == 'Owner')
                                                                        <a href="#"
                                                                            class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                            data-toggle="popover"
                                                                            title="{{ __('Delete') }}"
                                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                            data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                                class="ti ti-trash"></i></a>
                                                                        <form id="delete-form1-{{ $milestone->id }}"
                                                                            action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                            method="POST" style="display: none;">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    @endif
                                                                @endif
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
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{-- team usuario/tecnicos --}}
                                            <h5 class="mb-0">{{ __('Technicians') }}
                                                ({{ count($project->technicians) }})
                                            </h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-sm-flex align-items-center mb-0">
                                                @if (\Auth::user()->type == 'admin')
                                                    <a href="#" class="btn btn-sm btn-primary "
                                                        data-ajax-popup="true" data-title="{{ __('Invite') }}"
                                                        data-toggle="popover" title="{{ __('Invite') }}"
                                                        data-url="{{ route('projects.invite.popup', [$currentWorkspace->slug, $project->id]) }}"><i
                                                            class="ti ti-brand-telegram"></i></a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pb-1">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($project->technicians as $user)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        @if ($user->avatar) src="{{ asset($user->avatar) }}" @else avatar="{{ $user->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $user->name }}</h5>
                                                                    <small class="text-muted">{{ $user->email }}<span
                                                                            class="text-primary "> -
                                                                            {{ (int) count($project->user_done_tasks($user->id)) }}/{{ (int) count($project->user_tasks($user->id)) }}</span></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                {{-- modificado --}}
                                                                @if (\Auth::user()->type == 'admin')
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Permission') }}"
                                                                        data-title="{{ __('Edit Permission') }}"
                                                                        data-url="{{ route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id]) }}"><i
                                                                            class="ti ti-lock"></i></a>
                                                                    <a href="#"
                                                                        class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover" title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-user-{{ $user->id }}"><i
                                                                            class="ti ti-trash ml-1"></i></a>
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
                        <div class="col-md-6 widthAdjustMediumDiv">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Sales manager') }}
                                                ({{ count($project->salesManager) }})
                                            </h5>
                                        </div>
                                        <div class="float-end">
                                            <p class="text-muted d-none d-sm-flex align-items-center mb-0">
                                                {{-- modificado --}}
                                                @if (\Auth::user()->type == 'admin')
                                                    <a href="#" class="btn btn-sm btn-primary"
                                                        data-ajax-popup="true" data-title="{{ __('Share to Client') }}"
                                                        data-toggle="popover" title="{{ __('Share to Client') }}"
                                                        data-url="{{ route('projects.share.popup', [$currentWorkspace->slug, $project->id]) }}"><i
                                                            class="ti ti-share"></i></a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pb-1">
                                    <div class=" px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($project->salesManager as $client)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        @if ($client->avatar) src="{{ asset($logo . $client->avatar) }}" @else avatar="{{ $client->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $client->name }}</h5>
                                                                    <small
                                                                        class="text-muted">{{ $client->email }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                {{-- modificado --}}
                                                                @if (\Auth::user()->type == 'admin')
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-toggle="popover" title="{{ __('Permission') }}"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="{{ __('Edit Permission') }}"
                                                                        data-url="{{ route('projects.client.permission', [$currentWorkspace->slug, $project->id, $client->id]) }}"><i
                                                                            class="ti ti-lock"></i></a>

                                                                    <a href="#"
                                                                        class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover" title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-client-{{ $client->id }}"><i
                                                                            class="ti ti-trash"></i></a>

                                                                    <form id="delete-client-{{ $client->id }}"
                                                                        action="{{ route('projects.client.delete', [$currentWorkspace->slug, $project->id, $client->id]) }}"
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
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card min-end">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"> {{ __('Files') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="author-box-name form-control-label mb-4"></div>
                                    <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                        <div class="dz-message" data-dz-message>
                                            <span> {{ __('Drop files here to upload') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card min-end">
                                <div class="card-header" style="padding: 25px 35px !important;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="row">
                                            <h5 class="mb-0">{{ __('Progress') }}<span
                                                    class="text-end">({{ __('Last week tasks') }}) </span></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                    </div>
                                </div>
                                <div id="task-chart"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card min-end">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Activity') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="timeline timeline-one-side top-10-scroll" data-timeline-content="axis"
                                        data-timeline-axis-style="dashed">
                                        @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')
                                            @foreach ($project->activities as $activity)
                                                <div class="timeline-block px-2 pt-3">
                                                    @if ($activity->log_type == 'Upload File')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-primary text-white">
                                                            <i class="fas fa-file"></i></span>
                                                    @elseif($activity->log_type == 'Create Milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'Create Task')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-tasks"></i></span>
                                                    @elseif($activity->log_type == 'Create Timesheet')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-clock-o"></i></span>
                                                    @endif
                                                    <div class="last_notification_text">
                                                        <p class="m-0">
                                                            <span>{{ $activity->logType($activity->log_type) }}
                                                            </span>
                                                        </p> <br>
                                                        <p> {!! $activity->getRemark() !!} </p>
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
            if ($(".top-10-scroll").length) {
                $(".top-10-scroll").css({
                    "max-height": 300
                }).niceScroll();
            }
        });
    </script>
    <script src="{{ asset('assets/custom/js/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 209715200,
            parallelUploads: 1,
            //acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg,.pdf,.txt,.doc,.docx,.zip,.rar",
            url: "{{ route('projects.file.upload', [$currentWorkspace->slug, $project->id]) }}",
            success: function(file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    show_toastr('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                } else {
                    myDropzone.removeFile(file);
                    // show_toastr('error', 'File type must be match with Storage setting.');
                    show_toastr('{{ __('Error') }}',
                        'File type and size must be match with Storage setting.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{ __('Error') }}',
                        'File type and size must be match with Storage setting.', 'error');
                } else {
                    show_toastr('{{ __('Error') }}',
                        'File type and size must be match with Storage setting.', 'error');
                }
            }
        });

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
@endpush
