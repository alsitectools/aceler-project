@extends('layouts.admin')
@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp
@section('page-title')
    {{ __('Timesheet') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>

    @if ($project_id != -1)
        <li class="breadcrumb-item"><a
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ $project_name }}</a>
        </li>
    @endif
    <li class="breadcrumb-item"> {{ __('Timesheet') }}</li>
@endsection
@section('action-button')
    <div class="d-flex justify-content-end row1">
        @if (isset($currentWorkspace) && $currentWorkspace)
            @if ($project_id == -1)
                <div class="col-sm-auto">
                    <button id="add_task" type="button" class="btn btn-primary add_task" data-ajax-popup="true"
                        data-size="lg" data-title="{{ __('Create New Task') }}"
                        data-url="{{ route('tasks.create', $currentWorkspace->slug) }}" title="{{ __('Add Task') }}"><i
                            class="fa-solid fa-thumbtack"></i>
                        {{ __('Add Task on Timesheet') }}</button>
                </div>
            @else
                <div class="col-sm-auto">
                    <a href="{{ route('timesheet.csv.imputedHours.download', $project_id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-download"></i> {{ __('Download') }}
                    </a>
                </div>
            @endif
        @endif
        <div class="col-sm-auto">
            <div class="weekly-dates-div weekArrowsPadding">
                <i role="button" class="fa fa-arrow-left previous"></i>

                <span class="weekly-dates"></span>
                <input type="hidden" id="weeknumber" value="0">
                <input type="hidden" id="selected_dates">

                <i role="button" class="fa fa-arrow-right next"></i>
            </div>
        </div>
        @if ($project_id != '-1')
            <!-- <div class="col-auto">
                        <a href="{{ route($client_keyword . 'projects.show', [$currentWorkspace->slug, $project_id]) }}"
                            class="btn btn-sm btn-primary">
                            <i class=" ti ti-arrow-back-up"></i>
                        </a>
                    </div> -->
        @endif
    </div>
@endsection
@section('content')
    <section class="section">
        @if ($currentWorkspace)
            <div class="row">
                <div class="col-md-12">

                    <div class="card border modifiedWidthTime">
                        <div id="timesheet-table-view"></div>
                    </div>
                    <div class="card notfound-timesheet text-center">
                        <div class="card-body p-3">
                            <div class="page-error">
                                <div class="page-inner">
                                    <div class="page-description">
                                        {{ __("We couldn't find any data") }}
                                    </div>
                                    <div class="page-search">
                                        <p class="text-muted mt-3">
                                            {{ __("Sorry we can't find any timesheet records on this week") }}
                                            <br>
                                            @if ($project_id != '-1')
                                                {{ __('To add timesheet record go to') }}
                                                <b>{{ __('Add Task on Timesheet.') }}</b>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@push('css-page')
@endpush
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function ajaxFilterTimesheetTableView() {

            var mainEle = $('#timesheet-table-view');
            var notfound = $('.notfound-timesheet');

            var week = parseInt($('#weeknumber').val());
            var project_id = '{{ $project_id }}';

            var data = {
                week: week,
                project_id: project_id,
            };

            $.ajax({
                url: '{{ route('filter.timesheet.table.view', '__slug') }}'.replace('__slug',
                    '{{ $currentWorkspace->slug }}'),

                data: data,
                success: function(data) {

                    $('.weekly-dates-div .weekly-dates').text(data.onewWeekDate);
                    $('.weekly-dates-div #selected_dates').val(data.selectedDate);

                    $.each(data.tasks, function(i, item) {
                        $('#project_tasks').append($("<option></option>")
                            .attr("value", i)
                            .text(item));
                    });

                    if (data.totalrecords == 0) {
                        mainEle.hide();
                        notfound.css('display', 'block');
                    } else {
                        notfound.hide();
                        mainEle.show();
                    }

                    mainEle.html(data.html);
                }
            });
        }

        $(function() {
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '.weekly-dates-div i', function() {

            var weeknumber = parseInt($('#weeknumber').val());

            if ($(this).hasClass('previous')) {
                weeknumber--;
                $('#weeknumber').val(weeknumber);

            } else if ($(this).hasClass('next')) {
                weeknumber++;
                $('#weeknumber').val(weeknumber);
            }

            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '[data-ajax-timesheet-popup="true"]', function(e) {
            e.preventDefault();

            var modalId = $(this).data('modal-id') || 'commonModal';
            var data = {};
            var url = $(this).data('url');
            var type = $(this).data('type');
            var date = $(this).data('date');
            var task_id = $(this).data('task-id');
            var user_id = $(this).data('user-id');
            var p_id = $(this).data('project-id');
            var milestone_id = $(this).data('milestone-id');

            data.date = date;
            data.task_id = task_id;
            data.milestone_id = milestone_id;

            if (user_id != undefined) {
                data.user_id = user_id;
            }

            var title;
            if (type == 'create') {
                title = '{{ __('Add record to timesheet') }}';
                data.p_id = '{{ $project_id }}';
                data.project_id = data.p_id != '-1' ? data.p_id : p_id;
                data.milestone_id = milestone_id;
            } else if (type == 'edit') {
                title = '{{ __('Edit timesheet entry') }}';
                data.milestone_id = milestone_id;
            }

            // Verifica que el modal existe en el DOM
            if ($("#" + modalId).length) {
                $("#" + modalId + " .modal-title").html(title + ` <small>(` + moment(date).format("ddd DD MMM") +
                    `)</small>`);
            } else {
                console.error("El modal con ID '" + modalId + "' no existe en el DOM.");
            }

            $.ajax({
                url: url,
                data: data,
                dataType: 'html',
                success: function(data) {
                    $('#' + modalId + ' .body').html(data);

                    // Si estás usando Bootstrap 5
                    var modal = new bootstrap.Modal(document.getElementById(modalId));
                    modal.show(); // Muestra el modal

                    // O si estás usando la versión anterior de Bootstrap, usa:
                    // $("#" + modalId).modal('show');

                    commonLoader();
                    loadConfirm();
                }
            });

        });
    </script>
@endpush

<style type="text/css">
    .weekly-dates-div {
        padding: 8px 12px 8px 15px !important;
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .weekly-dates-div {
            padding: 8px 12px 8px 25px !important;
        }

        .modifiedWidthTime {
            width: 99% !important;
        }
    }

    #add_task {
        display: flex !important;
        align-items: center;
        justify-content: space-around !important;
        width: 320px;
    }
</style>
