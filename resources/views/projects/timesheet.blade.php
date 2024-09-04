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
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ __('Project Details') }}</a>
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
                        data-url="{{ route('timesheet.create', $currentWorkspace->slug) }}" title="{{ __('Add Task') }}"><i
                            class="fa-solid fa-thumbtack"></i>
                        {{ __('Add Task on Timesheet') }}</button>
                </div>
            @endif
        @endif
        <div class="col-sm-auto">
            <div class="weekly-dates-div">
                <i role="button" class="fa fa-arrow-left previous"></i>

                <span class="weekly-dates"></span>
                <input type="hidden" id="weeknumber" value="0">
                <input type="hidden" id="selected_dates">

                <i role="button" class="fa fa-arrow-right next"></i>
            </div>
        </div>
        @if ($project_id != '-1')
            <div class="col-auto">
                <a href="{{ route($client_keyword . 'projects.show', [$currentWorkspace->slug, $project_id]) }}"
                    class="btn btn-sm btn-primary">
                    <i class=" ti ti-arrow-back-up"></i>
                </a>
            </div>
        @endif
    </div>
@endsection
@section('content')
    <section class="section">
        @if ($currentWorkspace)
            <div class="row">
                <div class="col-md-12">

                    <div class="card border">
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

            var data = {};
            var url = $(this).data('url');
            var type = $(this).data('type');
            var date = $(this).data('date');
            var task_id = $(this).data('task-id');
            var user_id = $(this).data('user-id');
            var p_id = $(this).data('project-id');

            data.date = date;
            data.task_id = task_id;

            if (user_id != undefined) {
                data.user_id = user_id;
            }

            if (type == 'create') {
                var title = '{{ __('Create Timesheet') }}';
                data.p_id = '{{ $project_id }}';
                data.project_id = data.p_id != '-1' ? data.p_id : p_id;

            } else if (type == 'edit') {
                var title = '{{ __('Edit Timesheet') }}';
            }

            $("#commonModal .modal-title").html(title + ` <small>(` + moment(date).format("ddd DD MMM") +
                `)</small>`);

            $.ajax({
                url: url,
                data: data,
                dataType: 'html',
                success: function(data) {
                    $('#commonModal .body').html(data);
                    // $('#commonModal .modal-body').html(data);
                    $("#commonModal").modal('show');
                    commonLoader();
                    loadConfirm();
                }
            });
        });

        $(document).on('change', '#time_hour, #time_minute', function() {

            var hour = $('#time_hour').children("option:selected").val();
            var minute = $('#time_minute').children("option:selected").val();
            var total = $('#totaltasktime').val().split(':');

            if (hour == '00' && minute == '00') {
                $(this).val('');
                return;
            }

            hour = hour != '' ? hour : 0;
            hour = parseInt(hour) + parseInt(total[0]);

            minute = minute != '' ? minute : 0;
            minute = parseInt(minute) + parseInt(total[1]);

            if (minute > 50) {
                minute = minute - 60;
                hour++;
            }

            hour = hour < 10 ? '0' + hour : hour;
            minute = minute < 10 ? '0' + minute : minute;

            $('.display-total-time span').text('{{ __('Total Time') }} : ' + hour + ' {{ __('Hours') }} ' +
                minute + ' {{ __('Minutes') }}');
        });
    </script>
@endpush
<style type="text/css">
    .weekly-dates-div {
        padding: 8px 12px 8px 5px !important;
    }

    #add_task {
        display: flex !important;
        align-items: center;
        justify-content: space-around !important;
        width: 320px;
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
</style>
