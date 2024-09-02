@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->type == 'client' ? 'client' : '';
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    use App\Models\User;
    use App\Models\Milestone;
    use App\Models\Task;
    //  dd($milestones);
@endphp
@section('page-title')
    {{ __('Milestoneboard') }}
@endsection
<style>
    .hight_img {
        max-width: 30px !important;
        max-height: 30px !important;
    }

    .tooltipCus {
        position: relative;
        cursor: pointer;
    }

    .tooltipCus::after {
        content: attr(data-title);
        visibility: hidden;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 10px;
        padding: 10px;
        position: absolute;
        z-index: 1;
        bottom: 100%;
        left: 60%;
        transform: translateX(-5%);
        opacity: 0;
        transition: opacity 0.3s;
        white-space: nowrap;
    }

    .tooltipCus:hover::after {
        visibility: visible;
        opacity: 1;

    }

    .foot-milestone {
        display: flex !important;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
    }

    .taskList {
        height: 20px;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: baseline;
        justify-content: space-evenly;

    }

    .statusDate {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        flex-direction: column;
        margin: 5%;
    }

    /* .p-target {
        padding-left: 10%;

    } */

    .addMilestone {
        display: flex !important;
        justify-content: space-evenly !important;
        width: 200px;
        text-align: center;
        padding-right: 1%;
        align-items: center;

    }
</style>
@section('links')
    @if (isset($project_id) && $project_id != -1)
        <li class="breadcrumb-item"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ __('Project Details') }}</a>
        </li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Milestoneboard') }}</li>
@endsection

@section('action-button')
    <div class="d-flex justify-content-end row1">
        @if (isset($currentWorkspace) && $currentWorkspace)
            <div class="col-sm-auto">
                <button type="button" class="btn btn-primary addMilestone" data-ajax-popup="true"
                    data-title="{{ __('Milestone order') }}"
                    data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project_id]) }}" data-toggle="popover"
                    title="{{ __('Create') }}"><i class="fa-solid fa-file-lines" style="color: #ffffff;"></i>
                    {{ __('Create milestone') }}
                </button>
            </div>
        @endif
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row kanban-wrapper horizontal-scroll-cards" data-toggle="dragula"
                data-containers='{{ json_encode($statusClass) }}' data-handleclass="handleclass">
                @foreach ($stages as $status)
                    <div class="col-3" id="{{ 'milestone-list-' . str_replace(' ', '_', $status->id) }}">
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
                                                        <b>{{ $milestone['title'] }}</b>
                                                    </div>
                                                    <div class="col-sm-2 pt-1 text-center">
                                                        <a href="#" class=" tooltipCus"
                                                            data-title="{{ $milestone['sales']->name }}">
                                                            <i class="fa-solid fa-user-tie fa-xl"
                                                                style="color: #0000009a; padding-top: 25%;"></i>
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
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-1">
                                                <div class="row">
                                                    @if ($milestone['tasks'])
                                                        <div class="col-sm-12  tooltipCus"
                                                            data-title="{{ __('Tasks') }}">
                                                            @foreach ($milestone['tasks'] as $task)
                                                                <div class="taskList p-target col-sm-12">
                                                                    @php
                                                                        $isLate =
                                                                            strtotime($task['estimated_date']) <
                                                                            strtotime(date('Y-m-d'));
                                                                        $dateClass = $isLate ? 'danger' : 'success';
                                                                        $icon =
                                                                            $dateClass == 'danger'
                                                                                ? '<i class="fa-solid fa-hourglass-end fa-xs text-' .
                                                                                    $dateClass .
                                                                                    '"></i>'
                                                                                : '<i class="fa-solid fa-hourglass-start fa-xs p-0 m-0 text-' .
                                                                                    $dateClass .
                                                                                    '"></i>';
                                                                    @endphp

                                                                    {!! $icon !!} <p class="mb-1 col-md-8">
                                                                        {{ $task['name'] }} </p>

                                                                    @if ($project_id != -1)
                                                                        <div class="user-group col-sm-2 tooltipCus"
                                                                            data-title="{{ $task['technician']->name }}">
                                                                            <a href="#">
                                                                                <img alt="image" class="tooltipCus"
                                                                                    data-title="{{ $task['technician']->name }}"
                                                                                    @if ($task['technician']->avatar) src="{{ asset($logo . $task['technician']->avatar) }}" @else avatar="{{ $task['technician']->name }}" @endif>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                            @if ($project_id == -1)
                                                                <div class="user-group col-sm-11 tooltipCus text-end"
                                                                    data-title="{{ $task['technician']->name }}">
                                                                    <a href="#">
                                                                        <img alt="image" class="tooltipCus"
                                                                            data-title="{{ $task['technician']->name }}"
                                                                            @if ($task['technician']->avatar) src="{{ asset($logo . $task['technician']->avatar) }}" @else avatar="{{ $task['technician']->name }}" @endif>
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-muted text-center">
                                                            {{ __('No tasks in progress') . '...' }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card mb-0 mt-3">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="foot-milestone">
                                                                <div class="col-6 text-center">
                                                                    <div class="text-center tooltipCus"
                                                                        data-title="{{ __('Project') }}">
                                                                        <div>
                                                                            <img class="img-fluid p-1" width="40px"
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
                                                                        <i class="fa-solid fa-calendar-check fa-beat-fade m-1 pb-1 fa-2xl"
                                                                            style="color: red;"></i>
                                                                    @elseif($milestone['daysleft'] < 3)
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1"
                                                                            style="color: #db8d33;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-calendar-check fa-2xl m-1"
                                                                            style="color: #53b446;"></i>
                                                                    @endif
                                                                    <div class="text-center"
                                                                        style="padding-top: 15%; font-size: 12px;">
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
                        var id = el.id;
                        var oldStatus = a(source).data('status');
                        var newStatus = a(target).data('status');
                        var project_id = a(el).data('project-id');

                        updateTaskCount(source);
                        updateTaskCount(target);

                        a.ajax({
                            url: '{{ route('milestone.update.order', [$currentWorkspace->slug, $milestone['project_id']]) }}',
                            type: 'POST',
                            data: {
                                id: id,
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
