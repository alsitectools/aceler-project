<head>
    <link rel="stylesheet" href="{{ asset('assets/css/timesheet_week.css') }}">
</head>
<div class="card-body table-border-style">
    <div class="table-responsive">
        <table class="table table-borderless mb-0">
            <thead>
                <tr colspan="9" class="d-flex text-center padding-r mt-2">
                    <td class="week wid">
                        <b>{{ isset($allProjects) && $allProjects == true ? __('Projects') : __('Tasks') }}</b>
                    </td>
                    @foreach ($days['datePeriod'] as $key => $perioddate)
                        <td class="wid-100 header-days" style="background-color: transparent !important;">
                            <b>{{ ucfirst($perioddate->isoFormat('ddd DD MMM')) }}</b>
                        </td>
                    @endforeach
                    <td class="wid-100 header-days" style="background-color: transparent !important;">
                        <b>{{ __('Total') }}</b>
                    </td>
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
                                            <button
                                                class="accordion-button mb-1 custom-accordion-button changeBottomRadius"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne{{ $key }}" aria-expanded="true"
                                                aria-controls="collapseOne{{ $key }}">
                                                <div class="project-name pad_row tooltipCus"
                                                    data-title="{{ __('Project') }}">
                                                    {{ $timesheet['project_name'] }}
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $key }}"
                                            class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
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
                                                                            data-url="{{ route('show.task', [$currentWorkspace->slug, $taskTimesheet['task_id']]) }}"
                                                                            data-ajax-popup="true"
                                                                            data-title="Task Detail"
                                                                            data-task-name="{{ $taskTimesheet['task_name'] }}"
                                                                            data-modal-id="commonModalModified"> <!-- Nuevo atributo añadido aquí -->
                                                                            {{ __($taskTimesheet['task_name']) }}
                                                                        </div>
                                                                    </td>
                                                                    @endif
                                                                    @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                        @foreach ($dateTimeArray['week'] as $dateKey => $dateSubArray)
                                                                            <td>
                                                                                <div class="day-container">
                                                                                    <!-- Mostrar el día encima de las horas -->
                                                                                    <div class="day-label">
                                                                                        {{ ucfirst($days['datePeriod'][$dateKey]->isoFormat('ddd')) }}
                                                                                    </div>
                                                                                    @if (Auth::user()->id == $taskTimesheet['user_id'])
                                                                                        <div role="button"
                                                                                            class="form-control week inputsTask"
                                                                                            title="{{ $dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                                            data-ajax-timesheet-popup="true"
                                                                                            data-type="{{ $dateSubArray['type'] }}"
                                                                                            data-user-id="{{ $taskTimesheet['user_id'] }}"
                                                                                            data-project-id="{{ $timesheet['project_id'] }}"
                                                                                            data-task-id="{{ $taskTimesheet['task_id'] }}"
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
                                                                        <td>
                                                                            <div class="day-label marginForTotalText"
                                                                                style="margin-left: 35px;">
                                                                                Total
                                                                            </div>
                                                                            <div
                                                                                class="total form-control week inputsTaskTotal">
                                                                                {{ $dateTimeArray['totaltime'] }}
                                                                            </div>
                                                                        </td>
                                                                    @endforeach
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
                    @else
                        <tr>
                            <td colspan="10">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button mb-1 custom-accordion-button changeBottomRadius"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne{{ $key }}" aria-expanded="true"
                                                aria-controls="collapseOne{{ $key }}"
                                                style=" background-color: #F8F9FD; font-weight: bold;">
                                                <span data-title="{{ __('Milestone') }}"
                                                    style="width: 200px !important"
                                                    class="milestone-name pad_row tooltipCus">
                                                    {{ $timesheet['milestone_name'] }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $key }}"
                                            class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body mb-0">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        @foreach ($timesheet['usersArray'] as $userKey => $user)
                                                            <td colspan="10" class="text-center">
                                                                <div class="tooltipCus text-dark mt-4 divTitleEncargo"
                                                                    data-title="{{ $user['user_name'] }}">
                                                                    <div class="text-dark">
                                                                        {{ $user['user_name'] }}
                                                                    </div>
                                                                </div>
                                                                <hr class="border border-2 opacity-50 ">
                                                            </td>
                                                            @foreach ($user['taskArray'] as $taskKey => $taskTimesheet)
                                                                <tr>
                                                                    @if (Auth::user()->type != 'admin')
                                                                        <td style="padding-left: 5px;">
                                                                            <div role="button"
                                                                                data-title="{{ __('Task') }}"
                                                                                data-url="{{ route('show.task', [$currentWorkspace->slug, $taskTimesheet['task_id']]) }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="Task Detail"
                                                                                data-task-name="{{ $taskTimesheet['task_name'] }}">
                                                                                {{ __($taskTimesheet['task_name']) }}
                                                                            </div>
                                                                        </td>
                                                                    @endif
                                                                    @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                        @if (Auth::user()->type == 'admin')
                                                                            <td style="padding-left: 5px;">
                                                                                <div class="tooltipCus text-center"
                                                                                    data-title="{{ $taskTimesheet['user_name'] }}">
                                                                                    {{ $dateTimeArray['user_name'] }}
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                        @foreach ($dateTimeArray['week'] as $dateKey => $dateSubArray)
                                                                            <td>
                                                                                <div class="day-container">
                                                                                    <!-- Muestra el día encima de las horas -->
                                                                                    <div class="day-label">
                                                                                        {{ ucfirst($days['datePeriod'][$dateKey]->isoFormat('ddd')) }}
                                                                                    </div>
                                                                                    @if (Auth::user()->id == $user['user_id'])
                                                                                        <div role="button"
                                                                                            class="form-control wid-100 week inputsTaskModify"
                                                                                            title="{{ $dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                                                            data-ajax-timesheet-popup="true"
                                                                                            data-type="{{ $dateSubArray['type'] }}"
                                                                                            data-user-id="{{ $user['user_id'] }}"
                                                                                            data-project-id="{{ $timesheet['project_id'] }}"
                                                                                            data-task-id="{{ $taskTimesheet['task_id'] }}"
                                                                                            data-date="{{ $dateSubArray['date'] }}"
                                                                                            data-url="{{ $dateSubArray['url'] }}">
                                                                                            {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                        </div>
                                                                                    @else
                                                                                        <div
                                                                                            class="form-control wid-100 week inputsTaskModify">
                                                                                            {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                        <td>
                                                                            <div class="day-label">
                                                                                Total
                                                                            </div>
                                                                            <div
                                                                                class="total form-control wid-100 week inputsTaskTotalModify">
                                                                                {{ $dateTimeArray['totaltime'] }}
                                                                            </div>
                                                                        </td>
                                                                    @endforeach
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
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr colspan="9" class="d-flex text-center padding-r mt-2">
                    @if (isset($allProjects) && $allProjects == true)
                        <td class="week wid totalChangeSpace">
                            <b> {{ __('Total') }}</b>
                        </td>
                    @else
                        <td class="week wid totalChangeSpaceVariant">
                            <b> {{ __('Total') }}</b>
                        </td>
                    @endif
                    @foreach ($totalDateTimes as $key => $totaldatetime)
                        <td class="wid-100 header-days greyBackgroundTotalHours">
                            <b>{{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}</b>
                        </td>
                    @endforeach
                    <td class="wid-100 header-days greyBackgroundTotalHours">
                        <b> {{ $calculatedtotaltaskdatetime }}</b>

                    </td>
                </tr>
                <tr>
                    <td class="text-center d-flex align-items-center justify-content-center">
                        <div class="summary">
                            <svg xmlns="http://www.w3.org/2000/svg" height="50" width="45"
                                viewBox="0 0 512 512" class="mt-2">
                                <path fill="#63E6BE"
                                    d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9V168c0 13.3 10.7 24 24 24H134.1c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.4-6.6-44.6 7.9s-6.6 34.4 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75zm181 53c-13.3 0-24 10.7-24 24V256c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65V152c0-13.3-10.7-24-24-24z" />
                            </svg>
                            <h5 class="mt-2">{{ __('Total hours') }}</h5>
                            <span>
                                <b> {{ $calculatedtotaltaskdatetime }}</b>
                            </span>
                        </div>
                        <div class="summary">
                            <svg xmlns="http://www.w3.org/2000/svg" height="50" width="45"
                                viewBox="0 0 448 512">
                                <path fill="#B197FC"
                                    d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64C28.7 64 0 92.7 0 128v16 48V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V192 144 128c0-35.3-28.7-64-64-64H344V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192h80v56H48V192zm0 104h80v64H48V296zm128 0h96v64H176V296zm144 0h80v64H320V296zm80-48H320V192h80v56zm0 160v40c0 8.8-7.2 16-16 16H320V408h80zm-128 0v56H176V408h96zm-144 0v56H64c-8.8 0-16-7.2-16-16V408h80zM272 248H176V192h96v56z" />
                            </svg>
                            <h5 class="mt-2">{{ __('Período') }}</h5>
                            <span>
                                <b>{{ ucfirst($days['first_day']->isoFormat('ddd DD MMM')) }} a
                                    {{ ucfirst($days['seventh_day']->isoFormat('ddd DD MMM')) }}</b>
                            </span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
