@php
    use Carbon\Carbon;
    use App\Models\CustomTasks;
    $holidayDates = $holidayDates ?? [];
    $intensiveHoursByDate = $intensiveHoursByDate ?? [];
    // dd($timesheetArray);
    //print_r($workHoursWeek);
@endphp
<style>
    .custom-thead {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(7rem, 1fr));
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        padding: 5px;
        align-content: center;
    }

    .header-cell {
        padding: 10px;
    }

    .footer-cell {
        display: flex;
        width: 7rem !important;
        flex-direction: column;
        align-content: center;
        align-items: center;
    }

    .total-foot {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .hiddenPositioner {
        width: 9rem !important;
        margin-left: 5% !important;
        opacity: 0;
    }

    .custom-tfoot {
        gap: 3rem;
        display: flex !important;
        flex-direction: row;
        align-content: center;
        margin-bottom: 5px;

    }

    .greyBackgroundTotalHours {
        width: 100%;
    }

    .lastCell {
        margin-left: 1%;
    }

    .delegationTag {
        margin-left: 10px;
        background-color: black;
        padding: 5px 10px 5px 10px;
        border-radius: 15px;
        font-weight: 100;
    }

    @media screen and (max-width:1600px) and (min-width:1000px) {
        .taskName {
            font-size: 13px;
        }

        .footer-cell {
            width: 7rem !important;
        }

        .greyBackgroundTotalHours {
            width: 100%;
        }

        .custom-tfoot {
            gap: 1rem;
        }

        .hiddenPositioner {
            width: 8rem !important;
            margin-left: 6% !important;

        }

        .lastCell {
            margin-left: 2%;
        }
    }

    .day-label {
        color: #000000 !important;
    }

    .delimitatorContainer {
        background-color: #af9b9c17;
        border-radius: 8px;
        padding: 5px;
        margin-top: 16px;
        width: 98%;
        border: 1px solid #af9b9c5c
    }

    .delimitatorContainer .divTitleEncargo,
    .delimitatorContainer .taskName,
    .delimitatorContainer [data-task-name],
    .delimitatorContainer [data-milestone-name] {
        color: #000000 !important;
    }

    .divTitleEncargo {
        margin-bottom: 33px;
    }

    .titleMilestone {
        background-color: white;
    }

    .weekRow {
        border: none !important;
    }

    .containerColum {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/timesheet_week.css') }}">
</head>
<div class="card-body table-border-style">
    <div class="table-responsive">
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
                                                @if (isset($timesheet['ref_delegation']) && $timesheet['ref_delegation'] != '')
                                                    <div>
                                                        <span class="tooltipCus delegationTag"
                                                            data-title="{{ __('Delegation ID') }}">
                                                            {{ $timesheet['ref_delegation'] }}</span>
                                                    </div>
                                                @endif
                                                {{-- @dump($timesheet) --}}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $key }}" class="accordion-collapse collapse show"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body mb-0">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        @foreach ($timesheet['milestoneArray'] as $milestoneKey => $milestone)
                                                            <tr>
                                                                <td colspan="10" class="p-0 containerColum">
                                                                    <div class="delimitatorContainer">
                                                                        <div class="milestone-name justify-content-center align-items-center d-flex"
                                                                            data-milestone-name="{{ $milestone['milestone_name'] }}">
                                                                            <div data-title="{{ __('Milestone') }}"
                                                                                class="tooltipCus mt-0 divTitleEncargo titleMilestone">
                                                                                {{ $milestone['milestone_name'] }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table class="table mb-0">
                                                                                @foreach ($milestone['taskArray'] as $taskKey => $taskTimesheet)
                                                                                    <tr class="weekRow">
                                                                                        @if (Auth::user()->type != 'admin')
                                                                                            @php
                                                                                                $displayTaskName =
                                                                                                    $taskTimesheet[
                                                                                                        'task_name'
                                                                                                    ];
                                                                                                if (
                                                                                                    strtolower(
                                                                                                        $taskTimesheet[
                                                                                                            'task_name'
                                                                                                        ],
                                                                                                    ) === 'custom'
                                                                                                ) {
                                                                                                    try {
                                                                                                        $customTask = CustomTasks::where(
                                                                                                            'id_task',
                                                                                                            $taskTimesheet[
                                                                                                                'task_id'
                                                                                                            ],
                                                                                                        )->first();
                                                                                                        $displayTaskName = $customTask
                                                                                                            ? $customTask->name
                                                                                                            : 'custom';
                                                                                                    } catch (\Exception $e) {
                                                                                                        $displayTaskName =
                                                                                                            'custom';
                                                                                                    }
                                                                                                }
                                                                                            @endphp
                                                                                            <td
                                                                                                class="wid-150 firstTdInWeek text-start">
                                                                                                <div role="button"
                                                                                                    data-title="{{ __('Task') }}"
                                                                                                    data-url="{{ route('show.task', [$currentWorkspace->slug, $taskTimesheet['task_id'], $days['first_day'], $days['seventh_day']]) }}"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-title="Task Detail"
                                                                                                    class="taskName"
                                                                                                    data-task-name="{{ $displayTaskName }}"
                                                                                                    data-modal-id="commonModalModified">

                                                                                                    {{ __($displayTaskName) }}

                                                                                                </div>
                                                                                            </td>
                                                                                        @endif
                                                                                        @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                                            @foreach ($dateTimeArray as $dateKey => $dateSubArray)
                                                                                                <td>
                                                                                                    <div
                                                                                                        class="day-container">
                                                                                                        @php
                                                                                                            // Convertir la fecha a un objeto Carbon
                                                                                                            $date = Carbon::parse(
                                                                                                                $dateSubArray[
                                                                                                                    'date'
                                                                                                                ],
                                                                                                            );

                                                                                                            // Obtener el día de hoy
                                                                                                            $today = Carbon::today();

                                                                                                            // Determinar si el día es futuro
                                                                                                            $isFuture = $date->greaterThan(
                                                                                                                $today,
                                                                                                            );

                                                                                                            // Obtener el nombre del día en minúsculas (ejemplo: "monday", "tuesday", etc.)
                                                                                                            $dayName = strtolower(
                                                                                                                $date->format(
                                                                                                                    'l',
                                                                                                                ),
                                                                                                            );

                                                                                                            // Verificar festivo y disponibilidad de jornada
                                                                                                            $isHoliday = in_array(
                                                                                                                $dateSubArray[
                                                                                                                    'date'
                                                                                                                ],
                                                                                                                $holidayDates,
                                                                                                                true,
                                                                                                            );
                                                                                                            $isAllowed =
                                                                                                                isset(
                                                                                                                    $workHoursWeek[
                                                                                                                        $dayName
                                                                                                                    ],
                                                                                                                ) &&
                                                                                                                !$isFuture &&
                                                                                                                !$isHoliday;
                                                                                                        @endphp

                                                                                                        <div
                                                                                                            class="day-label">
                                                                                                            {{ ucfirst($date->isoFormat('dddd')) }}
                                                                                                            <!-- Muestra el día de la semana -->
                                                                                                        </div>

                                                                                                        @if (Auth::user()->id == $dateSubArray['user_id'])
                                                                                                            <div role="button"
                                                                                                                class="form-control week inputsTask {{ $isAllowed ? '' : 'disabled-day' }}"
                                                                                                                title="{{ $isHoliday ? __('This day is marked as holiday and cannot be edited') : ($isFuture ? __('This day is in the future and cannot be edited') : __('Click to Add/Edit Timesheet')) }}"
                                                                                                                data-ajax-timesheet-popup="{{ $isAllowed ? 'true' : 'false' }}"
                                                                                                                data-type="{{ $dateSubArray['type'] }}"
                                                                                                                data-user-id="{{ $dateSubArray['user_id'] }}"
                                                                                                                data-project-id="{{ $dateSubArray['project_id'] }}"
                                                                                                                data-task-id="{{ $dateSubArray['task_id'] }}"
                                                                                                                data-date="{{ $dateSubArray['date'] }}"
                                                                                                                data-url="{{ $dateSubArray['url'] }}"
                                                                                                                style="{{ $isAllowed ? '' : 'background-color: #a293933d; cursor: not-allowed; border: 2px solid #ced4da; color:black' }}">
                                                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                                            </div>
                                                                                                        @else
                                                                                                            <div
                                                                                                                class="form-control week">
                                                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                                            </div>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </td>
                                                                                            @endforeach
                                                                                        @endforeach

                                                                                        <td>
                                                                                            <div class="day-label marginForTotalText"
                                                                                                {{-- style="margin-left: 35px;" --}}>
                                                                                                Total
                                                                                            </div>
                                                                                            <div
                                                                                                class="total form-control week inputsTaskTotal">
                                                                                                {{ $taskTimesheet['totaltime'] }}
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
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
                                                                <td colspan="10" class="p-0 containerColum">
                                                                    <div class="delimitatorContainer">
                                                                        <div class="user-name justify-content-center align-items-center d-flex"
                                                                            data-user-name="{{ $user['user_name'] }}">
                                                                            <div data-title="{{ __('User') }}"
                                                                                class="tooltipCus text-dark mt-0 divTitleEncargo titleMilestone">
                                                                                {{ $user['user_name'] }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="table-responsive">
                                                                            <table class="table mb-0">
                                                                                @foreach ($user['taskArray'] as $taskKey => $taskTimesheet)
                                                                                    @php
                                                                                        $displayTaskName2 =
                                                                                            $taskTimesheet['task_name'];
                                                                                        if (
                                                                                            strtolower(
                                                                                                $taskTimesheet[
                                                                                                    'task_name'
                                                                                                ],
                                                                                            ) === 'custom'
                                                                                        ) {
                                                                                            try {
                                                                                                $customTask2 = CustomTasks::where(
                                                                                                    'id_task',
                                                                                                    $taskTimesheet[
                                                                                                        'task_id'
                                                                                                    ],
                                                                                                )->first();
                                                                                                $displayTaskName2 = $customTask2
                                                                                                    ? $customTask2->name
                                                                                                    : 'custom';
                                                                                            } catch (\Exception $e) {
                                                                                                $displayTaskName2 =
                                                                                                    'custom';
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                    <tr class="weekRow">
                                                                                        <td
                                                                                            class="wid-150 firstTdInWeek">
                                                                                            <div role="button"
                                                                                                data-title="{{ __('Task') }}"
                                                                                                data-url="{{ route('show.task', [$currentWorkspace->slug, $taskTimesheet['task_id'], $days['first_day'], $days['seventh_day']]) }}"
                                                                                                data-ajax-popup="true"
                                                                                                data-title="Task Detail"
                                                                                                data-task-name="{{ $displayTaskName2 }}"
                                                                                                data-modal-id="commonModalModified">
                                                                                                {{ __($displayTaskName2) }}
                                                                                            </div>
                                                                                        </td>
                                                                                        @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                                                                            @foreach ($dateTimeArray as $dateKey => $dateSubArray)
                                                                                                <td>
                                                                                                    <div
                                                                                                        class="day-container">
                                                                                                        @php

                                                                                                            // Convertir la fecha a un objeto Carbon
                                                                                                            $date = Carbon::parse(
                                                                                                                $dateSubArray[
                                                                                                                    'date'
                                                                                                                ],
                                                                                                            );

                                                                                                            // Obtener el día de hoy
                                                                                                            $today = Carbon::today();

                                                                                                            // Determinar si el día es futuro
                                                                                                            $isFuture = $date->greaterThan(
                                                                                                                $today,
                                                                                                            );

                                                                                                            // Obtener el nombre del día en minúsculas (ejemplo: "monday", "tuesday", etc.)
                                                                                                            $dayName = strtolower(
                                                                                                                $date->format(
                                                                                                                    'l',
                                                                                                                ),
                                                                                                            );

                                                                                                            // Verificar si el día está permitido en workHoursWeek y si no es futuro
                                                                                                            $isHoliday = in_array(
                                                                                                                $dateSubArray[
                                                                                                                    'date'
                                                                                                                ],
                                                                                                                $holidayDates,
                                                                                                                true,
                                                                                                            );
                                                                                                            $isAllowed =
                                                                                                                isset(
                                                                                                                    $workHoursWeek[
                                                                                                                        $dayName
                                                                                                                    ],
                                                                                                                ) &&
                                                                                                                !$isFuture &&
                                                                                                                !$isHoliday;
                                                                                                        @endphp

                                                                                                        <div
                                                                                                            class="day-label">
                                                                                                            {{ ucfirst($date->isoFormat('dddd')) }}
                                                                                                            <!-- Muestra el día de la semana -->
                                                                                                        </div>

                                                                                                        @if (Auth::user()->id == $dateSubArray['user_id'])
                                                                                                            <div role="button"
                                                                                                                class="form-control week inputsTask {{ $isAllowed ? '' : 'disabled' }}"
                                                                                                                title="{{ $isHoliday ? __('This day is marked as holiday and cannot be edited') : ($isAllowed ? __('Click to Add/Edit Timesheet') : __('This day is not available or is in the future')) }}"
                                                                                                                data-ajax-timesheet-popup="{{ $isAllowed ? 'true' : 'false' }}"
                                                                                                                data-type="{{ $dateSubArray['type'] }}"
                                                                                                                data-user-id="{{ $dateSubArray['user_id'] }}"
                                                                                                                data-project-id="{{ $dateSubArray['project_id'] }}"
                                                                                                                data-task-id="{{ $dateSubArray['task_id'] }}"
                                                                                                                data-date="{{ $dateSubArray['date'] }}"
                                                                                                                data-url="{{ $dateSubArray['url'] }}"
                                                                                                                data-milestone-id="{{ $timesheet['milestone_id'] }}"
                                                                                                                style="{{ $isAllowed ? '' : 'background-color: #a293933d; cursor: not-allowed;border: 2px solid #ced4da; color:black' }}">
                                                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                                            </div>
                                                                                                        @else
                                                                                                            <div
                                                                                                                class="form-control week">
                                                                                                                {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                                                                            </div>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                </td>
                                                                                            @endforeach
                                                                                        @endforeach

                                                                                        <td>
                                                                                            <div class="day-label marginForTotalText"
                                                                                                {{-- style="margin-left: 35px;
                                                                                                " --}}>
                                                                                                {{ 'Total' }}
                                                                                            </div>
                                                                                            <div
                                                                                                class="total form-control week inputsTaskTotal">
                                                                                                {{ $taskTimesheet['totaltime'] }}
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
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
            @php
                $combinedData = [];
                foreach ($days['datePeriod'] as $index => $datePeriod) {
                    $dateKey = Carbon::parse($datePeriod)->toDateString();
                    $combinedData[$dateKey] = $totalDateTimes[$index] ?? '00:00';
                }
                $popupTasksByDate = $popupTasksByDate ?? [];
            @endphp
            <div class="footer-cell hiddenPositioner">
                <p><b>{{ __('Total') }}</b></p>
                <div class="greyBackgroundTotalHours">
                    {{ $calculatedTotalTaskTime ? $calculatedTotalTaskTime : 'error' }}
                </div>
            </div>
            @foreach ($combinedData as $perioddate => $totaldatetime)
                @php
                    // Convertir la fecha en un objeto Carbon
                    $dateObj = Carbon::parse($perioddate);
                    $dateFormatted = ucfirst($dateObj->isoFormat('ddd DD MMM')); // Día con formato

                    // Obtener el día de hoy para comparación
                    $today = Carbon::today();

                    // Determinar si el día es futuro
                    $isFuture = $dateObj->greaterThan($today);

                    // Obtener el nombre del día en minúsculas (ejemplo: "monday", "tuesday", etc.)
                    $dayName = strtolower($dateObj->format('l'));

                    // Verificar si el día está en workHoursWeek (es laborable)
                    $isWorkday = isset($workHoursWeek[$dayName]);
                    $isHoliday = in_array($perioddate, $holidayDates, true);

                    // Convertir horas trabajadas y esperadas a formato decimal para comparación
                    $workedHoursFormatted =
                        $totaldatetime !== '00:00' ? floatval(str_replace(':', '.', $totaldatetime)) : 0;
                    $expectedHourByDate = $intensiveHoursByDate[$perioddate] ?? null;
                    $expectedHourRaw = $expectedHourByDate ?? ($isWorkday ? $workHoursWeek[$dayName] : '00:00');
                    $expectedHour = floatval(str_replace(':', '.', $expectedHourRaw));

                    // Determinar color según la lógica proporcionada
                    if ($isHoliday) {
                        $dayColor = 'rgba(145, 221, 207, 0.4)'; // Festivo
                    } elseif (!$isWorkday || $isFuture) {
                        $dayColor = '#d3d3d3'; // Gris para días no laborables o futuros
                    } elseif ($workedHoursFormatted == 0) {
                        $dayColor = '#e06c71'; // Rojo (sin horas)
                    } elseif ($workedHoursFormatted < $expectedHour) {
                        $dayColor = '#fcf75e'; // Amarillo (horas parciales)
                    } elseif ($workedHoursFormatted == $expectedHour) {
                        $dayColor = '#89e186'; // Verde (horas completas)
                    } elseif ($workedHoursFormatted > $expectedHour) {
                        $dayColor = '#b2e2f2'; // Azul (horas extras)
                    }
                @endphp

                <div class="footer-cell" data-date="{{ $perioddate }}"
                    data-date-formatted="{{ $dateFormatted }}">
                    <p><b>{{ $dateFormatted }}</b></p>
                    <div class="greyBackgroundTotalHours day-total-hours"
                        style="background-color: {{ $dayColor }} !important; padding: 5px; border-radius: 5px; cursor: pointer;"
                        role="button" data-date="{{ $perioddate }}" data-date-formatted="{{ $dateFormatted }}"
                        data-is-holiday="{{ $isHoliday ? '1' : '0' }}"
                        data-tasks="{{ json_encode($popupTasksByDate[$perioddate] ?? []) }}"
                        title="{{ __('Click to view day details') }}">
                        {{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}
                    </div>
                </div>
            @endforeach

            <div class="footer-cell lastCell">
                <p><b>{{ __('Total') }}</b></p>
                <div class="greyBackgroundTotalHours">
                    {{ $calculatedTotalTaskTime ? $calculatedTotalTaskTime : 'error' }}
                </div>
            </div>

        </div>
    </div>
</div>
