<div class="card-body table-border-style">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="text-dark m-0" style="padding-left: 80px !important;">
                        {{ isset($allProjects) && $allProjects == true ? __('Projects') : __('dictionary.Tasks') }}
                    </th>
                    @foreach ($days['datePeriod'] as $key => $perioddate)
                        <th class="text-dark m-0">{{ $perioddate->isoFormat('ddd DD MMM') }}</th>
                    @endforeach
                    <th class="th-sm">
                        <span class="pr-1">{{ __('Total') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timesheetArray as $key => $timesheet)
                    <tr>
                        <td colspan="10">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne{{ $key }}" aria-expanded="true"
                                            aria-controls="collapseOne{{ $key }}">
                                            <span data-tooltip="Project" class="project-name pad_row custom-tooltip">
                                                {{ isset($allProjects) && $allProjects == true ? $timesheet['project_name'] : $timesheet['employee'] }}
                                            </span> 
                                        </button>
                                    </h2>
                                    <div id="collapseOne{{ $key }}" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            @if (isset($allProjects) && $allProjects == true)
                                                @foreach ($timesheet['taskArray'] as $taskKey => $taskTimesheet)
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td colspan="9" class="text-center">
                                                                <div data-tooltip="Task"
                                                                    class="task-name ml-3 pad_row custom-tooltip">
                                                                    <b>{{ $taskTimesheet['task_name'] }}</b>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                            <tr>
                                                                <td>
                                                                    <div data-tooltip="User"
                                                                        class="task blue ml-5 custom-tooltip">
                                                                        {{ $dateTimeArray['user_name'] }}
                                                                    </div>
                                                                </td>
                                                                @foreach ($dateTimeArray['week'] as $dateSubArray)
                                                                    <td>
                                                                        @if (
                                                                            (Auth::user()->currant_workspace == $currentWorkspace->id && $currentWorkspace->created_by == Auth::user()->id) ||
                                                                                Auth::user()->id == $dateTimeArray['user_id']
                                                                        )
                                                                            <div role="button"
                                                                                class="form-control border-dark wid-120"
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
                                                                                class="form-control border-dark wid-120">
                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                                <td>
                                                                    <div
                                                                        class="total form-control bg-transparent border-dark wid-120">
                                                                        {{ $dateTimeArray['totaltime'] }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                @endforeach
                                            @else
                                                <table class="table mb-0">
                                                    <tr>
                                                        <td>
                                                            <div class="task-name ml-3">
                                                                {{ $timesheet['task_name'] }}
                                                            </div>
                                                        </td>
                                                        @foreach ($timesheet['dateArray'] as $day => $datetime)
                                                            <td>
                                                                @if (Auth::user()->currant_workspace == $currentWorkspace->id && $currentWorkspace->created_by == Auth::user()->id)
                                                                    <div role="button"
                                                                        class="form-control border-dark wid-120"
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
                                                                        class="form-control border-dark wid-120"
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
                                                                class="total form-control bg-transparent border-dark wid-120">
                                                                {{ $timesheet['totaltime'] }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-primary">
                    <td style="color:white; text-align: center !important; width: 10px;">{{ __('Total') }}</td>
                    @foreach ($totalDateTimes as $key => $totaldatetime)
                        <td style="align-items: center">
                            <div class="form-control border-dark wid-120">
                                {{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}
                            </div>
                        </td>
                    @endforeach
                    <td>
                        <div class="form-control border-dark wid-120">
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

<style type="text/css">
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
        padding-top: 20px;
        padding-left: 8px;
        padding-right: 8px;
        border-bottom: none;
        align-content: center;
        align-items: center;
        text-align: center;
    }

    .table th {}
</style>
