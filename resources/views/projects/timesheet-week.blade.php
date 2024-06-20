<div class="card-body table-border-style">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="head-table">
                <tr>
                    <th class="nom-project">
                        {{ isset($allProjects) && $allProjects == true ? __('Projects') : __('dictionary.Tasks') }}
                    </th>
                    @foreach ($days['datePeriod'] as $key => $perioddate)
                        <th>{{ $perioddate->isoFormat('ddd DD MMM') }}</th>
                    @endforeach
                    <th class="text-start">
                        {{ __('Total') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timesheetArray as $key => $timesheet)
                    @if (isset($allProjects) && $allProjects == true)
                        <tr>
                            <td colspan="10">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne{{ $key }}" aria-expanded="true"
                                                aria-controls="collapseOne{{ $key }}">
                                                <span data-tooltip="Project"
                                                    class="project-name pad_row custom-tooltip">
                                                    {{ $timesheet['project_name'] }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $key }}"
                                            class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">

                                                @foreach ($timesheet['taskArray'] as $taskKey => $taskTimesheet)
                                                    <table class="table mb-0">
                                                        <td colspan="9" class="text-center">
                                                            <div data-tooltip="Milestone"
                                                                class="ml-3 pad_row custom-tooltip milestone-name"
                                                                data-milestone-name="{{ $taskTimesheet['milestone'] }}">
                                                                <b>{{ $taskTimesheet['milestone'] }}</b>
                                                            </div>
                                                        </td>
                                                        <tr>
                                                            @if (Auth::user()->type != 'admin')
                                                                <td>
                                                                    <div data-tooltip="Task"
                                                                        class="ml-3 pad_row custom-tooltip task-name"
                                                                        data-task-name="{{ $taskTimesheet['task_name'] }}">
                                                                        <b>{{ $taskTimesheet['task_name'] }}</b>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                            @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                @if (Auth::user()->type == 'admin')
                                                                    <td>
                                                                        <div data-tooltip="{{ $taskTimesheet['task_name'] }}"
                                                                            class="blue ml-5 custom-tooltip task-name">
                                                                            {{ $dateTimeArray['user_name'] }}
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                                @foreach ($dateTimeArray['week'] as $dateSubArray)
                                                                    <td>
                                                                        @if (Auth::user()->currant_workspace == $currentWorkspace->id && Auth::user()->id == $dateTimeArray['user_id'])
                                                                            <div role="button"
                                                                                class="form-control border-dark wid-110"
                                                                                title="{{ $dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                                data-ajax-timesheet-popup="true"
                                                                                data-type="{{ $dateSubArray['type'] }}"
                                                                                data-user-id="{{ $dateTimeArray['user_id'] }}"
                                                                                data-project-id="{{ $timesheet['project_id'] }}"
                                                                                data-task-id="{{ $taskTimesheet['task_id'] }}"
                                                                                data-date="{{ $dateSubArray['date'] }}"
                                                                                data-url="{{ $dateSubArray['url'] }}">
                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                            </div>
                                                                        @else
                                                                            <div
                                                                                class="form-control border-dark wid-110">
                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                                <td>
                                                                    <div
                                                                        class="total form-control bg-transparent border-dark wid-100">
                                                                        {{ $dateTimeArray['totaltime'] }}
                                                                    </div>
                                                                </td>
                                                            @endforeach
                                                        </tr>

                                                    </table>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="10">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne{{ $key }}" aria-expanded="true"
                                                aria-controls="collapseOne{{ $key }}">
                                                <span data-tooltip="Milestone"
                                                    class="milestone-name pad_row custom-tooltip">
                                                    {{ $timesheet['milestone'] }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $key }}"
                                            class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">

                                                @foreach ($timesheetArray as $timesheet)
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td>
                                                                <div class="ml-3 task-name">
                                                                    {{ $timesheet['task_name'] }}
                                                                </div>
                                                            </td>

                                                            @foreach ($timesheet['dateArray'] as $datetime)
                                                                <td>
                                                                    @if (Auth::user()->id == $timesheet['employee_id'])
                                                                        <div role="button"
                                                                            class="form-control border-dark wid-110"
                                                                            title="{{ $datetime['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                            data-ajax-timesheet-popup="true"
                                                                            data-type="{{ $datetime['type'] }}"
                                                                            data-task-id="{{ $timesheet['task_id'] }}"
                                                                            data-date="{{ $datetime['date'] }}"
                                                                            data-url="{{ $datetime['url'] }}">
                                                                            {{ isset($datetime['total_task_time']) ? $datetime['total_task_time'] : '00:00' }}
                                                                        </div>
                                                                    @else
                                                                        <div role="button"
                                                                            class="form-control border-dark wid-110"
                                                                            title="{{ $datetime['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                            data-ajax-timesheet-popup="true"
                                                                            data-type="{{ $datetime['type'] }}"
                                                                            data-task-id="{{ $timesheet['task_id'] }}"
                                                                            data-date="{{ $datetime['date'] }}"
                                                                            data-url="{{ $datetime['url'] }}">
                                                                            {{ $datetime['time'] != '00:00' ? $datetime['time'] : '00:00' }}
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                            <td>
                                                                <div
                                                                    class="total form-control bg-transparent border-dark wid-110">
                                                                    {{ $timesheet['totaltime'] ?? '00:00' }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-primary">
                    <td style="color:white;" class="text-center">{{ __('Total') }}</td>
                    @foreach ($totalDateTimes as $key => $totaldatetime)
                        <td class="footer-item">
                            <div class="form-control border-dark footer-item wid-110">
                                {{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}
                            </div>
                        </td>
                    @endforeach
                    <td>
                        <div class="form-control border-dark footer-item wid-110">
                            {{ $calculatedtotaltaskdatetime }}
                        </div>
                    </td>
                </tr>
            </tfoot>

        </table>
    </div>
</div>
<div class="text-center d-flex align-items-center justify-content-center mt-4 mb-5">
    <h5 class="f-w-900 me-2 mb-0">{{ __('Time Logged:') }}</h5>
    <span class="p-2 f-w-900 rounded bg-primary d-inline-block border border-dark span">
        {{ $calculatedtotaltaskdatetime }}
    </span>
</div>
<script>
    $(document).ready(function() {
        var previousTaskName = null;

        $('.task-name[data-task-name]').each(function() {
            var currentTaskName = $(this).data('task-name');
            if (currentTaskName === previousTaskName) {
                $(this).closest('tr')
                    .remove(); // Remove the row if the task name is the same as the previous one
            } else {
                previousTaskName = currentTaskName;
            }
        });
        $('.milestone-name[data-milestone-name]').each(function() {
            var currentTaskName = $(this).data('milestone-name');
            if (currentTaskName === previousTaskName) {
                $(this).closest('tr')
                    .remove(); // Remove the row if the task name is the same as the previous one
            } else {
                previousTaskName = currentTaskName;
            }
        });
    });
</script>
<style type="text/css">
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .nom-project {
        text-align: left !important;
        padding-left: 100px !important;
        padding-right: 50px !important;
    }

    .head-table th {
        padding-top: 10px;
        min-width: 90px;
        margin: auto;
        justify-content: center;
        align-content: center;
    }

    .table thead {
        line-height: 30px !important;
        align-content: center;
        align-items: center;
        text-align: center;
    }

    .accordion-button:not(.collapsed) {
        background-color: #AA182C;
        color: #fff;
    }

    .span {
        color: white !important;
    }

    .accordion-header {
        line-height: 50px !important;
    }

    .table td {
        padding-left: 0%;
        padding-right: 0%;
        border-bottom: none;
        align-content: center;
        align-items: center;

    }

    .task-name {
        padding-right: 0%;
        /* text-align: left; */
        max-width: 145px;
        min-width: 145px;
    }

    /* Asegúrate de tener un ancho mínimo y que los elementos sean flexibles */
    .footer-item {
        padding-right: 1px !important;
    }

    /* #footer{
        padding-left: 20px !important;
        text-align: center;
        width: 110px;
         padding-right: 15px;
    } */
    .table th {}
</style>
