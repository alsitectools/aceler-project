@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
@endphp
@section('page-title')
    {{ __('Milestoneboard') }}
@endsection

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestoneboard.css') }}">
</head>
<style>
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
</style>
@section('links')
    @if (isset($project_id) && $project_id != -1)
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a></li>
    
        <li class="breadcrumb-item"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{$project_name}}</a>
        </li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Milestoneboard') }}</li>
@endsection

@section('action-button')
    <div class="d-flex justify-content-end row1">
        @if (isset($currentWorkspace) && $currentWorkspace)
            <div class="col-sm-auto">
                <button style="width: 100%" type="button" class="btn btn-primary addMilestone" data-ajax-popup="true"
                    data-title="{{ __('Milestone order') }}"
                    data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project_id]) }}"
                    data-toggle="popover" title="{{ __('Create') }}"><i class="fa-solid fa-file-lines me-3"
                        style="color: #ffffff;"></i>
                    {{ __('Create Milestone') }}
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
                                <div class="float-end">
                                    <button class="btn-submit btn btn-md btn-primary btn-icon px-1 py-0">
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
                                data-status="{{ $status->id }}" class="card-body kanban-box">
                                @if (isset($milestones[$status->id]))
                                    @foreach ($milestones[$status->id] as $milestone)
                                        <div class="card" id="{{ $milestone['id'] }}" data-status="{{ $status->id }}"
                                            data-project-id="{{ $milestone['project_id'] }}">
                                            <div class="card-header border-0 pb-0 col-sm-12">
                                                <div class="d-flex">
                                                    <div class="col-sm-9 text-center tooltipCus"
                                                        data-title="{{ __('Milestone') }}">
                                                        <b id="mileTitle">{{ $milestone['title'] }}</b>
                                                    </div>
                                                    <div class="col-sm-2 pt-1 text-center">
                                                        <a href="#" class=" tooltipCus"
                                                            data-title="{{ $milestone['sales']->name }}">
                                                            <img class="img-fluid"
                                                                src="{{ asset('assets/img/salesManager.png') }}"
                                                                alt="logo" />
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
                                                                    data-title="{{ __('Milestone Details') }}"
                                                                    data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                    <i class="ti ti-eye pr-1"></i>
                                                                    {{ __('View') }}
                                                                </a>
                                                                <a href="#" class="dropdown-item"  data-ajax-popup="true" title="{{ __('Add Task') }}" data-title="{{ __('Add Task') }}" data-url="{{ route('tasks.create', [$currentWorkspace->slug, 'project_id'=> $milestone['project_id'], 'milestoneTitle' => $milestone['title'], 'milestone_id' =>$milestone['id']]) }}"><i class="fas fa-tasks pr-1"></i> {{ __('Add Task') }}</a>
                                                                @if (
                                                                    $currentWorkspace->permission == 'Owner' ||
                                                                        ($currentWorkspace->permission == 'Member' && Auth::user()->type == 'user'))
                                                                    <a href="#" class="dropdown-item"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('Edit Milestone') }}"
                                                                        data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone['id']]) }}">
                                                                        <i class="ti ti-edit pr-1"></i>{{ __('Edit') }}
                                                                    </a>
                                                                    @if (empty($milestone['tasks']))
                                                                        <a href="#" class="dropdown-item bs-pass-para"
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
                                                                    <a href="#" class="dropdown-item bs-pass-para"
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
                                                        <div class="col-sm-12 tooltipCus p-3"
                                                            data-title="{{ __('Tasks') }}">
                                                            @foreach ($milestone['tasks'] as $task)
                                                                <div class="taskList p-target mb-2 col-sm-12 marginText">
                                                                    @php
                                                                        $isLate =
                                                                            strtotime($task['estimated_date']) <
                                                                            strtotime(date('Y-m-d'));
                                                                        $dateClass = $isLate ? 'danger' : 'success';
                                                                        $icon =
                                                                            $dateClass == 'danger'
                                                                                ? '<i class="ms-2 me-2 fa-solid fa-hourglass-end fa-xs text-' .
                                                                                    $dateClass .
                                                                                    '"></i>'
                                                                                : '<i class="ms-2 me-2 fa-solid fa-hourglass-start fa-xs p-0 m-0 text-' .
                                                                                    $dateClass .
                                                                                    '"></i>';
                                                                    @endphp
                                                                    {!! $icon !!}{{ __($task['name']) }}
                                                                </div>
                                                                @if ($project_id != -1)
                                                                    <div class="tooltipCus col-sm-12 text-end"
                                                                        data-title="{{ $task['technician']->name }}">
                                                                        <a href="#">

                                                                            <img alt="image"
                                                                                class="tooltipCus user-groupTasks"
                                                                                data-title="{{ $task['technician']->name }}"
                                                                                @if ($task['technician']->avatar) src="{{ asset($task['technician']->avatar) }}" @else avatar="{{ $task['technician']->name }}" @endif>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                            @if ($project_id == -1)
                                                                <div class="col-sm-11 text-end">
                                                                    <a href="#">
                                                                        <img alt="image" class="user-groupTasks"
                                                                            @if ($task['technician']->avatar) src="{{ asset($task['technician']->avatar) }}" @else avatar="{{ $task['technician']->name }}" @endif>
                                                                    </a>
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
                                                                    data-title="{{ __('End Date') }}">
                                                                    @if ($milestone['daysleft'] < 1)
                                                                        <i class="fa-solid fa-calendar-check fa-beat-fade m-1 pb-1 fa-2xl calendarAlert"
                                                                            style="color: red;"></i>
                                                                    @elseif($milestone['daysleft'] < 3)
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                                                            style="color: #db8d33;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1 calendarAlert"
                                                                            style="color: #53b446;"></i>
                                                                    @endif
                                                                    <div class="text-center adjustTextCalendar">
                                                                        <b style="font-size: 12px">
                                                                            {{ \App\Models\Utility::dateFormat($milestone['end_date']) }}</b>
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
        console.log('Card ID:', cardId, 'Old status:', oldStatus, 'New status:', newStatus, 'Project ID:', project_id, 'Milestone Title:', milestoneTitle);

        // Se dispara la misma acción que al hacer clic en "Add Task on Timesheet"
        var url = '{{ route('tasks.create', $currentWorkspace->slug) }}' + '?project_id=' + project_id + '&milestoneTitle=' + milestoneTitle  + '&milestone_id=' + cardId;
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
            id: cardId,  // Se envía el cardId obtenido
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
        @endpush
    @endif
