@php
    // dd($timesheetArray);
@endphp
<style>
    .custom-thead,
    .custom-tfoot {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        padding: 5px;
    }

    .header-cell,
    .footer-cell {
        padding: 10px;
    }

    .total-foot {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }
</style>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/timesheet_week.css') }}">
</head>
<div class="card-body table-border-style">
    <div class="table-responsive">
        <div class="custom-thead d-grid">
            <div class="header-cell">
                <b>{{ isset($allProjects) && $allProjects == true ? __('Projects') : __('Tasks') }}</b>
            </div>
            @foreach ($days['datePeriod'] as $key => $perioddate)
                <div class="header-cell">
                    <b>{{ ucfirst($perioddate->isoFormat('ddd DD MMM')) }}</b>
                </div>
            @endforeach
            <div class="header-cell">
                <b>{{ __('Total') }}</b>
            </div>
        </div>
        <table class="table table-borderless mb-0">
            <tbody>
                @if (isset($allProjects) && $allProjects == true)
                    @foreach ($timesheetArray as $key => $timesheet)
                        <tr>
                            <td colspan="10">
                                <div class="accordion" id="accordion{{ $key }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button mb-1 custom-accordion-button changeBottomRadius"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $key }}" aria-expanded="true"
                                                aria-controls="collapse{{ $key }}">
                                                <div class="project-name pad_row tooltipCus"
                                                    data-title="{{ __('Project') }}">
                                                    {{ $timesheet['project_name'] }}
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $key }}" class="accordion-collapse collapse show"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body mb-0">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        @foreach ($timesheet['milestoneArray'] as $milestoneKey => $milestone)
                                                            <td colspan="10" class="text-center">
                                                                <div class="milestone-name justify-content-center align-items-center"
                                                                    data-milestone-name="{{ $milestone['milestone_name'] }}">
                                                                    <div data-title="{{ __('Milestone') }}"
                                                                        class="tooltipCus text-dark mt-4 divTitleEncargo">
                                                                        {{ $milestone['milestone_name'] }}
                                                                    </div>
                                                                </div>

                                                            </td>
                                                            @foreach ($milestone['taskArray'] as $taskKey => $taskTimesheet)
                                                                <tr class="weekRow">
                                                                    @if (Auth::user()->type != 'admin')
                                                                        <td class="wid-150 firstTdInWeek">
                                                                            <div role="button"
                                                                                data-title="{{ __('Task') }}"
                                                                                data-url="{{ route('show.task', [$currentWorkspace->slug, $taskTimesheet['task_id'], $days['first_day'], $days['seventh_day']]) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="Task Detail"
                                                                                data-task-name="{{ $taskTimesheet['task_name'] }}"
                                                                                data-modal-id="commonModalModified">
                                                                                {{ __($taskTimesheet['task_name']) }}
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                    @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                        @foreach ($dateTimeArray as $dateKey => $dateSubArray)
                                                                            <td>
                                                                                <div class="day-container">
                                                                                    <div class="day-label">
                                                                                        {{ ucfirst($days['datePeriod'][$dateKey]->isoFormat('ddd')) }}
                                                                                    </div>
                                                                                    @if (Auth::user()->id == $dateSubArray['user_id'])
                                                                                        <div role="button"
                                                                                            class="form-control week inputsTask"
                                                                                            title="{{ $dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                                            data-ajax-timesheet-popup="true"
                                                                                            data-type="{{ $dateSubArray['type'] }}"
                                                                                            data-user-id="{{ $dateSubArray['user_id'] }}"
                                                                                            data-project-id="{{ $dateSubArray['project_id'] }}"
                                                                                            data-milestone-id="{{ $dateSubArray['milestone_id'] }}"
                                                                                            data-task-id="{{ $dateSubArray['task_id'] }}"
                                                                                            data-date="{{ $dateSubArray['date'] }}"
                                                                                            data-url="{{ $dateSubArray['url'] }}">
                                                                                            {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="form-control week">
                                                                                            {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    @endforeach
                                                                    <td>
                                                                        <div class="day-label marginForTotalText"
                                                                            style="margin-left: 35px;">
                                                                            Total
                                                                        </div>
                                                                        <div
                                                                            class="total form-control week inputsTaskTotal">
                                                                            {{ $taskTimesheet['totaltime'] }}
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    @foreach ($timesheetArray as $key => $timesheet)
                        <tr>
                            <td colspan="10">
                                <div class="accordion" id="accordionExample{{ $key }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button mb-1 custom-accordion-button changeBottomRadius"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $key }}" aria-expanded="true"
                                                aria-controls="collapse{{ $key }}">
                                                <div class="milestone-name pad_row tooltipCus"
                                                    data-title="{{ __('Milestone') }}">
                                                    {{ $timesheet['milestone_name'] ?? __('Unknown Milestone') }}
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $key }}" class="accordion-collapse collapse show"
                                            data-bs-parent="#accordionExample{{ $key }}">
                                            <div class="accordion-body mb-0">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        @foreach ($timesheet['usersArray'] as $userKey => $user)
                                                            <tr>
                                                                <td colspan="10" class="text-center">
                                                                    <div class="user-name justify-content-center align-items-center"
                                                                        data-user-name="{{ $user['user_name'] }}">
                                                                        <div data-title="{{ __('User') }}"
                                                                            class="tooltipCus text-dark mt-4 divTitleEncargo">
                                                                            {{ $user['user_name'] }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @foreach ($user['taskArray'] as $taskKey => $taskTimesheet)
                                                                <tr class="weekRow">
                                                                    <td class="wid-150 firstTdInWeek">
                                                                        <div role="button"
                                                                            data-title="{{ __('Task') }}"
                                                                            data-url="{{ route('show.task', [$currentWorkspace->slug, $taskTimesheet['task_id'], $days['first_day'], $days['seventh_day']]) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="Task Detail"
                                                                            data-task-name="{{ $taskTimesheet['task_name'] }}"
                                                                            data-modal-id="commonModalModified">
                                                                            {{ __($taskTimesheet['task_name']) }}
                                                                        </div>
                                                                    </td>
                                                                    @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                        @foreach ($dateTimeArray as $dateKey => $dateSubArray)
                                                                            <td>
                                                                                <div class="day-container">
                                                                                    <div class="day-label">
                                                                                        {{ ucfirst($days['datePeriod'][$dateKey]->isoFormat('ddd')) }}
                                                                                    </div>
                                                                                    @if (Auth::user()->id == $dateSubArray['user_id'])
                                                                                        <div role="button"
                                                                                            class="form-control week inputsTask"
                                                                                            title="{{ $dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                                            data-ajax-timesheet-popup="true"
                                                                                            data-type="{{ $dateSubArray['type'] }}"
                                                                                            data-user-id="{{ $dateSubArray['user_id'] }}"
                                                                                            data-project-id="{{ $dateSubArray['project_id'] }}"
                                                                                            data-task-id="{{ $dateSubArray['task_id'] }}"
                                                                                            data-date="{{ $dateSubArray['date'] }}"
                                                                                            data-url="{{ $dateSubArray['url'] }}">
                                                                                            {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                        </div>
                                                                                    @else
                                                                                        <div
                                                                                            class="form-control week inputsTaskTotal">
                                                                                            {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    @endforeach
                                                                    <td>
                                                                        <div class="day-label marginForTotalText"
                                                                            style="margin-left: 35px;">
                                                                            {{ 'Total' }}
                                                                        </div>
                                                                        <div
                                                                            class="total form-control week inputsTaskTotal">
                                                                            {{ $taskTimesheet['totaltime'] }}
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="custom-tfoot d-grid">
            <div class="footer-cell total-foot">
                <div>{{ __('Total') }}</div>
            </div>
            @foreach ($totalDateTimes as $key => $totaldatetime)
                <div class="footer-cell">
                    <div class="greyBackgroundTotalHours">{{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}
                    </div>
                </div>
            @endforeach
            <div class="footer-cell">
                <div class=" greyBackgroundTotalHours">
                    {{ $calculatedTotalTaskTime ? $calculatedTotalTaskTime : 'error' }}</div>
            </div>
        </div>
    </div>
</div>
