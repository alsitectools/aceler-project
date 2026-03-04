@extends('layouts.admin')

<style type="text/css">
    /* .fc-h-event .fc-event-title {
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 115% !important;
    } */

    .modal-body {
        background: #ffffff !important;
        padding: 25px !important;
    }

    .liStyleTask {
        background-color: white;
        color: black;
        box-shadow: 0px 0px 15px 0px rgb(0 0 0 / 15%);
        -webkit-box-shadow: 0px 0px 15px 0px rgb(0 0 0 / 15%);
        -moz-box-shadow: 0px 0px 15px 0px rgb(0 0 0 / 15%);
        padding: 15px;
        padding-left: 5%;
        border-radius: 10px;
        margin-bottom: 3%;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
    }

    .milestoneTitle {
        max-width: 95%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .iStyleTask {
        color: white;
        font-size: 30px;
        padding-right: 4%;
    }

    /* Centrar título del calendario */
    .fc-header-toolbar {
        position: relative;
    }

    .fc-toolbar-chunk:nth-child(2) {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .pTotalHours {
        background-color: white;
        color: black;
        padding-top: 2%;
        /* padding-left: 1%; */
        /* border-radius: 10px; */
        width: 100%;
        text-align: center;
        border-top: 3px dashed #aa182C;
    }

    .divIconTask {
        background-color: #aa182C;
        padding: 3%;
        border-radius: 10px;
    }

    .divAlignP {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 89%;
    }

    .titleTask {
        padding-left: 20px;
        font-weight: bold;
    }

    .subtitleTask {
        font-size: 14px;
        color: #0000008a;
        margin-top: -14px;
        padding-left: 20px;
    }

    /* Estilos para acordeón de proyectos */
    .project-accordion {
        list-style: none;
        padding: 0;
        max-height: 82.5vh;
        overflow-y: scroll;
        padding-top: 3px;
    }

    /* el ancho de la barra */
    .project-accordion::-webkit-scrollbar {
        width: 10px;
    }

    /* El "track" (el fondo) */
    .project-accordion::-webkit-scrollbar-track {
        background: transparent;
    }

    /*El "thumb" (la pieza que se mueve) */
    .project-accordion::-webkit-scrollbar-thumb {
        background-color: #ab1126;
        border-radius: 10px;
        border: 3px solid transparent;
        background-clip: content-box;
    }

    .project-item {
        margin-bottom: 12px;
    }

    .project-header {
        background: linear-gradient(135deg, #AA182C 0%, #642e35 100%);
        color: white;
        padding: 12px 15px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .project-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .project-header .icon {
        transition: transform 0.3s ease;
        font-size: 18px;
    }

    .project-header.collapsed .icon {
        transform: rotate(0deg);
    }

    .project-header.expanded .icon {
        transform: rotate(180deg);
    }

    .project-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease, opacity 0.35s ease, padding 0.35s ease;
        opacity: 0;
        padding: 0;
    }

    .project-content.expanded {
        max-height: 1000px;
        opacity: 1;
        padding: 10px 0;
    }

    .task-item {
        background-color: #f8f9fa;
        border-left: 4px solid #AA182C;
        padding: 10px 12px;
        margin: 8px 0;
        border-radius: 4px;
        transition: all 0.3s ease;
        animation: slideIn 0.3s ease forwards;
    }

    .task-item:hover {
        background-color: #e9ecef;
        border-left-color: #642e35;
        transform: translateX(4px);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .task-item-label {
        font-size: 11px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .task-item-text {
        font-weight: 500;
        color: #333;
        margin: 2px 0px 8px 0px;
        font-size: 13px;
        max-width: 100%;
        /* white-space: nowrap; */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .task-item-time {
        font-size: 12px;
        color: #AA182C;
        font-weight: 600;
        margin-top: 4px;
    }

    .calendar-day-add-btn {
        margin-left: 6px;
        width: 18px;
        height: 18px;
        border: 1px solid #AA182C;
        border-radius: 20%;
        background: #fff;
        color: #AA182C;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .calendar-day-add-btn:hover {
        background: #AA182C;
        color: #fff;
    }

    .calendar-day-add-btn:disabled,
    .calendar-day-add-btn.calendar-day-add-btn-disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f1f1f1;
        color: #9e9e9e;
        border-color: #c9c9c9;
    }

    #calendar:not(.calendar-edit-mode) .calendar-day-add-btn {
        display: none;
    }

    .calendar-edit-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-right: 12px;
    }

    .calendar-edit-toggle .form-check-input {
        cursor: pointer;
    }

    .calendar-edit-toggle label {
        margin-bottom: 0;
        font-size: 13px;
        color: #495057;
        cursor: pointer;
    }

    .special-day-hour-wrapper {
        margin-top: 12px;
    }

    .calendar-multi-select-btn {
        margin-right: 8px;
    }

    .calendar-multi-select-btn.hidden {
        display: none;
    }

    .fc-daygrid-day.calendar-multi-selected {
        box-shadow: inset 0 0 0 2px #AA182C;
        background: rgba(170, 24, 44, 0.08);
    }

    @media (max-width: 576px) {
        .header_breadcrumb {
            width: 100% !important;
        }
    }

    @media screen and (min-width:1399px) and (max-width:1600px) {
        /* * {
            border: 1px solid red;
        } */

        .project-accordion {
            max-height: 66vh;
        }
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .adjustWidthCalendar {
            width: 32% !important;
        }

        .responsiveDivCalendarTask {
            width: 99% !important;
        }

        .liStyleTask {
            padding: 10px;
        }

        .titleTask {
            font-size: 15px;
        }

        .subtitleTask {
            font-size: 13px;
        }

        .testCol {
            width: 99% !important;
        }

        .divIconTask {
            height: 20px;
            width: 20px;
            display: flex;
            align-content: center;
            justify-content: center;
            align-items: center;
        }

        .iStyleTask {
            font-size: 30px;
        }
    }
</style>
@include('loader.loader')
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css">
@endpush

@section('page-title')
    {{ __('Calendar') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Calendar') }}</li>
@endsection

@section('multiple-action-button')
    <!-- Filtro para seleccionar el proyecto (comentado para mostrar todos los proyectos) -->
    {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-8 pt-lg-3 pt-xl-2">
        <div class="form-group col-auto">
            <select class="form-select select2" id="project_id" onchange="get_data()">
                <option value="">{{ __('All Projects') }}</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @if ($project_id == $project->id) selected @endif>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div> --}}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 testCol">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h5>{{ __('Calendar') }}</h5>
                    <div style="display: flex; align-items: center;">
                        <button type="button" id="multi-special-day-btn"
                            class="styleAdjust btn btn-sm btn-outline-primary calendar-multi-select-btn hidden">
                            {{ __('Multi-day selection') }}
                        </button>

                        <div class="form-check form-switch calendar-edit-toggle">
                            <input class="form-check-input" type="checkbox" role="switch" id="calendar-edit-mode-toggle">
                            <label class="form-check-label" for="calendar-edit-mode-toggle">{{ __('Edit mode') }}</label>
                        </div>

                        <select id="workspace-select" class="form-select"
                            style="width: auto; display: inline-block; font-size: 0.9rem; padding: 0.25rem 2.5rem 0.25rem 0.75rem; cursor: pointer; font-weight: 500;">
                            <option value="current" selected>{{ __('Current Workspace') }}</option>
                            <option value="all">{{ __('All Workspaces') }}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar" class="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 responsiveDivCalendarTask">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Tasks') }}<span id="selected-date"
                            style="margin-left: 10px; font-size: 0.8em; color: #666;"></span></h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled" id="task-list">
                        <p>{{ __('Select a day to view tasks') }}</p>
                    </ul>
                    <p class="pTotalHours"><strong>{{ __('Total Hours:') }} </strong><span id="total-hours">00:00</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="specialDayModal" tabindex="-1" aria-labelledby="specialDayModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="specialDayModalLabel">{{ __('Set special day') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="margin-bottom: 10px; font-size: 14px; color: #666;">
                        {{ __('Date') }}: <strong id="special-day-selected-date"></strong>
                    </p>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="specialDayType" id="special-day-type-holiday"
                            value="holiday" checked>
                        <label class="form-check-label" for="special-day-type-holiday">
                            {{ __('Holiday') }}
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="specialDayType" id="special-day-type-intensive"
                            value="intensive_work">
                        <label class="form-check-label" for="special-day-type-intensive">
                            {{ __('Intensive workday') }}
                        </label>
                    </div>

                    <div class="special-day-hour-wrapper" id="special-day-hour-wrapper" style="display: none;">
                        <label for="special-day-intensive-hours" class="form-label">{{ __('Hours') }}</label>
                        <input type="time" class="form-control" id="special-day-intensive-hours" value="08:00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="save-special-day-btn">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let calendar;
        const currentWorkspaceId = "{{ $currentWorkspace->id }}";
        let selectedSpecialDate = null;
        let selectedSpecialDates = [];
        let isCalendarEditMode = false;
        let isMultiSelectMode = false;
        let multiSelectedDates = new Set();
        let imputedDatesState = new Set();
        let nonWorkingDatesState = new Set();
        let specialDaysState = {
            holidayRange: [],
            intensiveWorkRange: {}
        };

        $(document).ready(function() {

            // Deshabilitar el scroll
            document.body.style.overflow = 'hidden';

            getCalendarInfo();

            adjustLayout();

            $(window).resize(function() {
                adjustLayout();
            });

            $('input[name="specialDayType"]').on('change', function() {
                const isIntensive = $(this).val() === 'intensive_work';
                $('#special-day-hour-wrapper').toggle(isIntensive);
            });

            $('#save-special-day-btn').on('click', function() {
                saveSpecialDay();
            });

            $('#calendar-edit-mode-toggle').on('change', function() {
                isCalendarEditMode = $(this).is(':checked');
                updateCalendarEditModeUI();
            });

            $('#multi-special-day-btn').on('click', function() {
                handleMultiSelectButtonClick();
            });

            updateCalendarEditModeUI();
            updateMultiSelectButtonUI();
        });

        //adjusting to laptop view
        function adjustLayout() {
            if ($(window).width() <= 1200) {
                $('.testCol').removeClass('col-lg-8').addClass('col-12');
            } else {
                $('.testCol').removeClass('col-12').addClass('col-lg-8');
            }
        }

        function hexToRgba(hex, alpha) {
            hex = hex.replace(/^#/, '');
            let r = parseInt(hex.substring(0, 2), 16);
            let g = parseInt(hex.substring(2, 4), 16);
            let b = parseInt(hex.substring(4, 6), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        function hasImputedHours(hours) {
            if (!hours) {
                return false;
            }

            const [hourPart, minutePart] = String(hours).split(':');
            const parsedHours = Number(hourPart) || 0;
            const parsedMinutes = Number(minutePart) || 0;

            return parsedHours > 0 || parsedMinutes > 0;
        }

        function getCalendarInfo() {
            const operationUrl = '<?php echo url('get-timesheetCalendar'); ?>';
            const mode = $('#workspace-select').val() || 'current';

            $.ajax({
                type: 'GET',
                url: operationUrl,
                data: {
                    workspace_id: currentWorkspaceId,
                    all: mode === 'all'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    // console.log("success", data);

                    const opacity = 0.4;
                    let allEvents = [];
                    specialDaysState = {
                        holidayRange: data?.specialColorData?.holidayRange && Array.isArray(data.specialColorData
                            .holidayRange) ? [...data.specialColorData.holidayRange] : [],
                        intensiveWorkRange: data?.specialColorData?.intensiveWorkRange && typeof data
                            .specialColorData.intensiveWorkRange === 'object' ? {
                                ...data.specialColorData.intensiveWorkRange
                            } : {}
                    };

                    imputedDatesState = new Set(Array.isArray(data?.colorData) ? data.colorData
                        .filter(item => hasImputedHours(item.hours))
                        .map(item => item.date) : []);

                    if (data && data.colorData && Array.isArray(data.colorData) && data.expectedHours) {
                        const nonWorkingDays = Object.keys(data.expectedHours).filter(day => data.expectedHours[
                            day] === null);

                        const nonWorkingEvents = [];
                        const nonWorkingDateValues = [];

                        // Determinar el rango de fechas a partir de colorData
                        let minDate = null;
                        let maxDate = null;

                        data.colorData.forEach(item => {
                            if (!minDate || item.date < minDate) {
                                minDate = item.date;
                            }
                            if (!maxDate || item.date > maxDate) {
                                maxDate = item.date;
                            }
                        });

                        // Si hay colorData, generar non-working days para ese rango
                        if (minDate && maxDate) {
                            const startDate = new Date(minDate + 'T00:00:00Z');
                            const endDate = new Date(maxDate + 'T00:00:00Z');

                            for (let date = new Date(startDate); date <= endDate; date.setUTCDate(date
                                    .getUTCDate() + 1)) {
                                const dayOfWeek = date.toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    timeZone: 'UTC'
                                }).toLowerCase();
                                if (nonWorkingDays.includes(dayOfWeek)) {
                                    const nonWorkingDate = date.toISOString().split('T')[0];
                                    nonWorkingDateValues.push(nonWorkingDate);
                                    nonWorkingEvents.push({
                                        title: '{{ __('Non-working day') }}',
                                        start: nonWorkingDate,
                                        backgroundColor: hexToRgba("#d3d3d3", opacity),
                                        borderColor: '#d3d3d3',
                                        textColor: 'black',
                                        allDay: true
                                    });
                                }
                            }
                        }

                        let events = data.colorData
                            .filter(item => {
                                if (data.specialColorData?.holidayRange && Array.isArray(data
                                        .specialColorData.holidayRange) && data.specialColorData
                                    .holidayRange.includes(item.date)) {
                                    return item.hours !== '00:00';
                                }
                                return true;
                            })
                            .map(item => ({
                                title: item.hours ? item.hours + ' hours' : '',
                                start: item.date,
                                backgroundColor: hexToRgba(item.color, opacity),
                                borderColor: item.color,
                                textColor: 'black',
                                allDay: true,
                                date: item.date
                            }));

                        if (data.specialColorData && data.specialColorData.holidayRange) {
                            data.specialColorData.holidayRange.forEach(day => {
                                events.push({
                                    title: 'Holiday',
                                    start: day,
                                    backgroundColor: hexToRgba(data.specialColorData
                                        .holidayColor, opacity),
                                    borderColor: data.specialColorData.holidayColor,
                                    textColor: 'black',
                                    allDay: true,
                                    id: `holiday_${day}`,
                                    type: 'holiday'
                                });
                            });
                        }

                        if (data.specialColorData && data.specialColorData.intensiveWorkRange) {
                            for (const [hours, days] of Object.entries(data.specialColorData
                                    .intensiveWorkRange)) {
                                days.forEach(day => {
                                    events.push({
                                        title: `${hours} hours`,
                                        start: day,
                                        backgroundColor: hexToRgba(data.specialColorData
                                            .intensiveWorkColor, opacity),
                                        borderColor: data.specialColorData.intensiveWorkColor,
                                        textColor: 'black',
                                        allDay: true,
                                        id: `intensive_${day}`,
                                        type: 'intensive_work'
                                    });
                                });
                            }
                        }

                        allEvents = [...events, ...nonWorkingEvents];
                        nonWorkingDatesState = new Set(nonWorkingDateValues);
                    } else {
                        imputedDatesState = new Set();
                        nonWorkingDatesState = new Set();
                        console.warn("No valid data found. Loading empty calendar.");
                    }

                    // Renderizar el calendario incluso si no hay eventos
                    renderCalendar(allEvents);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    renderCalendar([]); // Renderizar el calendario vacío en caso de error
                }
            });
        }

        function renderCalendar(events) {
            const calendarEl = document.getElementById('calendar');
            const locale = '{{ app()->getLocale() }}';

            if (calendar) {
                calendar.removeAllEvents();
                calendar.addEventSource(events);
                refreshSpecialDayButtonsState();
                document.getElementById('loader-overlay').style.display = 'none';
                return;
            }

            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: locale,
                initialView: 'dayGridMonth',
                firstDay: 1,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                buttonText: {
                    today: "{{ trans('messages.today') }}"
                },
                events: events,
                dateClick: function(info) {
                    if (isMultiSelectMode) {
                        toggleDateInMultiSelection(info.dateStr);
                        return;
                    }

                    // Al hacer clic en un día, obtener las tareas
                    const selectedDate = info.dateStr;

                    // Remover la clase de todos los días
                    document.querySelectorAll('.fc-daygrid-day.fc-day-today').forEach(el => {
                        el.classList.remove('fc-day-today');
                    });

                    // Agregar la clase al día seleccionado
                    info.dayEl.classList.add('fc-day-today');

                    loadTasksByDate(selectedDate);
                },
                dayCellDidMount: function(arg) {
                    const dateNumber = arg.el.querySelector('.fc-daygrid-day-number');
                    if (!dateNumber || dateNumber.querySelector('.calendar-day-add-btn')) {
                        return;
                    }

                    const addBtn = document.createElement('button');
                    addBtn.type = 'button';
                    addBtn.className = 'calendar-day-add-btn';
                    addBtn.textContent = '+';
                    addBtn.setAttribute('aria-label', '{{ __('Add special day') }}');
                    addBtn.setAttribute('title', '{{ __('Add special day') }}');

                    addBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (!isCalendarEditMode) {
                            return;
                        }

                        const clickedDate = arg.el.getAttribute('data-date');

                        if (isDateBlockedForSpecialDay(clickedDate)) {
                            return;
                        }

                        selectedSpecialDates = [];
                        openSpecialDayModal(clickedDate);
                    });

                    dateNumber.appendChild(addBtn);
                },
                eventClick: function(info) {
                    if (info.event.extendedProps.type === 'holiday' || info.event.extendedProps.type ===
                        'intensive_work') {
                        showDeleteModal(info.event);
                    } else {
                        // Cargar tareas para ese día
                        const selectedDate = info.event.startStr;

                        // Remover la clase de todos los días
                        document.querySelectorAll('.fc-daygrid-day.fc-day-today').forEach(el => {
                            el.classList.remove('fc-day-today');
                        });

                        // Buscar y agregar la clase al día padre del evento
                        const dayCell = info.el.closest('.fc-daygrid-day');
                        if (dayCell) {
                            dayCell.classList.add('fc-day-today');
                        }

                        loadTasksByDate(selectedDate);
                    }
                },
                eventDidMount: function(info) {
                    if (info.event.extendedProps.type === 'holiday' || info.event.extendedProps.type ===
                        'intensive_work') {
                        const deleteBtn = document.createElement('span');
                        deleteBtn.innerHTML = '❌';
                        deleteBtn.style.cursor = 'pointer';
                        deleteBtn.style.marginLeft = '10px';
                        deleteBtn.style.color = 'red';
                        deleteBtn.style.fontSize = '14px';

                        deleteBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            showDeleteModal(info.event);
                        });

                        info.el.querySelector('.fc-event-title').appendChild(deleteBtn);
                    }
                }
            });

            calendar.render();
            updateCalendarEditModeUI();
            refreshSpecialDayButtonsState();
            refreshMultiSelectedDayCells();
            window.dispatchEvent(new Event('resize'));
            //reactivar scroll
            document.body.style.overflow = 'auto';
            //esconder el loader
            document.getElementById('loader-overlay').style.display = 'none';

            // Seleccionar el día de hoy por defecto
            const today = new Date().toISOString().split('T')[0];
            const todayElement = document.querySelector(`[data-date="${today}"]`);
            if (todayElement) {
                todayElement.classList.add('fc-day-today');
            }
            loadTasksByDate(today);

            // Add Workspace Select Dropdown event listener
            $('#workspace-select').on('change', function() {
                // Show loader
                document.getElementById('loader-overlay').style.display = 'flex';

                getCalendarInfo();
                const selectedDate = document.querySelector('.fc-day-today') ?
                    document.querySelector('.fc-day-today').getAttribute('data-date') :
                    new Date().toISOString().split('T')[0];
                loadTasksByDate(selectedDate);
            });
        }

        function updateCalendarEditModeUI() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                return;
            }

            const multiSelectButton = $('#multi-special-day-btn');

            if (isCalendarEditMode) {
                calendarEl.classList.add('calendar-edit-mode');
                multiSelectButton.removeClass('hidden');
            } else {
                calendarEl.classList.remove('calendar-edit-mode');
                multiSelectButton.addClass('hidden');
                exitMultiSelectMode();
            }

            refreshSpecialDayButtonsState();
        }

        function getAllSpecialDates() {
            const holidays = Array.isArray(specialDaysState.holidayRange) ? specialDaysState.holidayRange : [];
            const intensiveRanges = specialDaysState.intensiveWorkRange && typeof specialDaysState.intensiveWorkRange ===
                'object' ? specialDaysState.intensiveWorkRange : {};

            const intensiveDates = Object.values(intensiveRanges)
                .flatMap((dates) => Array.isArray(dates) ? dates : []);

            return new Set([...holidays, ...intensiveDates]);
        }

        function isDateSpecialDay(date) {
            if (!date) {
                return false;
            }

            return getAllSpecialDates().has(date);
        }

        function isDateWithImputedHours(date) {
            if (!date) {
                return false;
            }

            return imputedDatesState.has(date);
        }

        function isDateNonWorking(date) {
            if (!date) {
                return false;
            }

            return nonWorkingDatesState.has(date);
        }

        function isDateBlockedForSpecialDay(date) {
            return isDateSpecialDay(date) || isDateWithImputedHours(date) || isDateNonWorking(date);
        }

        function refreshSpecialDayButtonsState() {
            const specialDates = getAllSpecialDates();

            document.querySelectorAll('.fc-daygrid-day').forEach((dayCell) => {
                const date = dayCell.getAttribute('data-date');
                const addBtn = dayCell.querySelector('.calendar-day-add-btn');

                if (!addBtn) {
                    return;
                }

                const isSpecial = specialDates.has(date);
                const hasImputed = isDateWithImputedHours(date);
                const isNonWorking = isDateNonWorking(date);
                const isBlocked = isSpecial || hasImputed || isNonWorking;
                addBtn.disabled = isBlocked;

                if (isSpecial) {
                    addBtn.classList.add('calendar-day-add-btn-disabled');
                    addBtn.setAttribute('title', '{{ __('Special day already exists for this date') }}');
                } else if (hasImputed) {
                    addBtn.classList.add('calendar-day-add-btn-disabled');
                    addBtn.setAttribute('title', '{{ __('This date has imputed hours and cannot be set as special day') }}');
                } else if (isNonWorking) {
                    addBtn.classList.add('calendar-day-add-btn-disabled');
                    addBtn.setAttribute('title', '{{ __('This date is a non-working day and cannot be set as special day') }}');
                } else {
                    addBtn.classList.remove('calendar-day-add-btn-disabled');
                    addBtn.setAttribute('title', '{{ __('Add special day') }}');
                }
            });
        }

        function openSpecialDayModal(date) {
            if (date) {
                selectedSpecialDates = [];
            }

            selectedSpecialDate = date;
            const hasMultiDates = Array.isArray(selectedSpecialDates) && selectedSpecialDates.length > 0;

            if (hasMultiDates) {
                $('#special-day-selected-date').text(`${selectedSpecialDates.length} {{ __('days selected') }}`);
            } else {
                $('#special-day-selected-date').text(date || '');
            }

            $('#special-day-type-holiday').prop('checked', true).trigger('change');
            $('#special-day-intensive-hours').val('08:00');
            $('#specialDayModal').modal('show');
        }

        function saveSpecialDay() {
            const targetDates = (Array.isArray(selectedSpecialDates) && selectedSpecialDates.length > 0) ?
                selectedSpecialDates : (selectedSpecialDate ? [selectedSpecialDate] : []);

            if (!targetDates.length) {
                return;
            }

            const validTargetDates = targetDates.filter((date) => !isDateBlockedForSpecialDay(date));

            if (!validTargetDates.length) {
                alert("{{ __('All selected dates are blocked because they already have a special day, imputed hours, or are non-working days.') }}");
                return;
            }

            const selectedType = $('input[name="specialDayType"]:checked').val();
            const intensiveHours = $('#special-day-intensive-hours').val();
            const postUrl = '<?php echo url('user/specialUpdate-timetable'); ?>';

            let payload = {};

            if (selectedType === 'holiday') {
                const mergedHolidays = Array.from(new Set([...(specialDaysState.holidayRange || []),
                    ...validTargetDates
                ]));

                payload = {
                    rangeDate: JSON.stringify(mergedHolidays)
                };
            } else {
                if (!intensiveHours) {
                    alert("{{ __('Please select hours for intensive workday.') }}");
                    return;
                }

                const existingDates = Array.isArray(specialDaysState.intensiveWorkRange?.[intensiveHours]) ?
                    specialDaysState.intensiveWorkRange[intensiveHours] : [];

                const mergedIntensiveDates = Array.from(new Set([...existingDates, ...validTargetDates]));

                payload = {
                    rangeDate: JSON.stringify(mergedIntensiveDates),
                    intensiveWorkday: intensiveHours
                };
            }

            $.ajax({
                type: 'POST',
                url: postUrl,
                data: {
                    rangeAndInput: JSON.stringify(payload)
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $('#specialDayModal').modal('hide');
                    exitMultiSelectMode();
                    selectedSpecialDate = null;
                    selectedSpecialDates = [];
                    getCalendarInfo();
                    loadTasksByDate(validTargetDates[0]);
                },
                error: function(xhr) {
                    alert("{{ __('An error occurred while saving the special day.') }}");
                    console.error(xhr.responseText);
                }
            });
        }

        function handleMultiSelectButtonClick() {
            if (!isMultiSelectMode) {
                isMultiSelectMode = true;
                selectedSpecialDate = null;
                selectedSpecialDates = [];
                multiSelectedDates = new Set();
                updateMultiSelectButtonUI();
                refreshMultiSelectedDayCells();
                return;
            }

            if (multiSelectedDates.size === 0) {
                exitMultiSelectMode();
                return;
            }

            selectedSpecialDate = null;
            selectedSpecialDates = Array.from(multiSelectedDates);
            openSpecialDayModal(null);
        }

        function toggleDateInMultiSelection(date) {
            if (!date || isDateBlockedForSpecialDay(date)) {
                return;
            }

            if (multiSelectedDates.has(date)) {
                multiSelectedDates.delete(date);
            } else {
                multiSelectedDates.add(date);
            }

            updateMultiSelectButtonUI();
            refreshMultiSelectedDayCells();
        }

        function refreshMultiSelectedDayCells() {
            document.querySelectorAll('.fc-daygrid-day').forEach((dayCell) => {
                const date = dayCell.getAttribute('data-date');
                if (!date) {
                    return;
                }

                if (isMultiSelectMode && multiSelectedDates.has(date)) {
                    dayCell.classList.add('calendar-multi-selected');
                } else {
                    dayCell.classList.remove('calendar-multi-selected');
                }
            });
        }

        function updateMultiSelectButtonUI() {
            const button = $('#multi-special-day-btn');
            if (!button.length) {
                return;
            }

            if (!isMultiSelectMode) {
                button.removeClass('btn-primary').addClass('btn-outline-primary');
                button.text("{{ __('Multi-day selection') }}");
                return;
            }

            button.removeClass('btn-outline-primary').addClass('btn-primary');
            button.text(`{{ __('Apply selection') }} (${multiSelectedDates.size})`);
        }

        function exitMultiSelectMode() {
            isMultiSelectMode = false;
            multiSelectedDates = new Set();
            selectedSpecialDates = [];
            updateMultiSelectButtonUI();
            refreshMultiSelectedDayCells();
        }

        function loadTasksByDate(date) {
            const url = '<?php echo url('get-tasks-by-date'); ?>';
            const mode = $('#workspace-select').val() || 'current';

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    date: date,
                    workspace_id: currentWorkspaceId,
                    all: mode === 'all'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("Tasks loaded:", response);

                    // Actualizar el encabezado con la fecha seleccionada
                    const dateObj = new Date(date + 'T00:00:00');
                    const formattedDate = dateObj.toLocaleDateString('{{ app()->getLocale() }}', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    $('#selected-date').text('(' + formattedDate + ')');

                    // Limpiar lista de tareas
                    const taskList = $('#task-list');
                    taskList.empty();

                    // Filtrar tareas con totalTime diferente de 00:00
                    const filteredTasks = response.tasks ? response.tasks.filter(task => task.totalTime !==
                        '00:00') : [];

                    if (filteredTasks.length > 0) {
                        // Agrupar tareas por proyecto (y workspace si aplica)
                        const projectsMap = {};

                        filteredTasks.forEach(function(task) {
                            // Crear clave única combinando proyecto y workspace para evitar mezclar si hay nombres repetidos
                            const key = mode === 'all' ? `${task.projectName}__${task.workspaceName}` :
                                task.projectName;

                            if (!projectsMap[key]) {
                                projectsMap[key] = {
                                    projectName: task.projectName,
                                    workspaceName: task.workspaceName,
                                    tasks: []
                                };
                            }
                            projectsMap[key].tasks.push(task);
                        });

                        // Crear acordeón por proyectos
                        const accordion = document.createElement('ul');
                        accordion.className = 'project-accordion';

                        Object.values(projectsMap).forEach(function(group, index) {
                            const projectItem = document.createElement('li');
                            projectItem.className = 'project-item';

                            let headerText = group.projectName;

                            const projectHeader = document.createElement('div');
                            projectHeader.className = 'project-header collapsed';
                            projectHeader.innerHTML = `
                                <span>${headerText}</span>
                                <span class="icon"><i class="fa-solid fa-chevron-down"></i></span>
                            `;

                            const projectContent = document.createElement('div');
                            projectContent.className = 'project-content';

                            // Agregar tareas del proyecto
                            group.tasks.forEach(function(task) {
                                // console.log(task);
                                const taskItem = document.createElement('div');
                                taskItem.className = 'task-item';

                                let workspaceHtml = '';
                                if (mode === 'all' && task.workspaceName) {
                                    workspaceHtml = `
                                        <div class="task-item-label">{{ __('Workspace') }}</div>
                                        <div class="task-item-text">${task.workspaceName}</div>
                                    `;
                                }
                                taskItem.innerHTML = `
                                ${workspaceHtml}
                                    <div class="task-item-label">{{ __('Milestone') }}</div>
                                    <div class="task-item-text">${task.milestoneTitle}</div>
                                    <div class="task-item-label">{{ __('Task') }}</div>
                                    <div class="task-item-text">${task.taskTitle}</div>
                                    
                                    <div class="task-item-time">⏱️ ${task.totalTime}</div>
                                `;
                                projectContent.appendChild(taskItem);
                            });

                            // Event listener para expandir/contraer
                            projectHeader.addEventListener('click', function() {
                                projectHeader.classList.toggle('collapsed');
                                projectHeader.classList.toggle('expanded');
                                projectContent.classList.toggle('expanded');
                            });

                            projectItem.appendChild(projectHeader);
                            projectItem.appendChild(projectContent);
                            accordion.appendChild(projectItem);

                        });

                        taskList.append(accordion);

                    } else {
                        taskList.append('<p>{{ __('No tasks available') }}</p>');
                    }

                    // Actualizar las horas totales
                    $('#total-hours').text(response.formattedTotalHours);
                },
                error: function(xhr) {
                    console.error("Error loading tasks:", xhr.responseText);
                    $('#task-list').html('<p>{{ __('Error loading tasks') }}</p>');
                }
            });
        }

        function showDeleteModal(event) {
            console.log(event.id)
            let confirmation = confirm(`Do you want to delete this ${event.extendedProps.type.replace('_', ' ')}?`);
            if (confirmation) {
                deleteEvent(event.id);
            }
        }

        // Función AJAX para eliminar el evento del servidor
        function deleteEvent(eventId) {
            const deleteUrl = '<?php echo url('user/specialDelete-timetable'); ?>';
            $.ajax({
                url: deleteUrl,
                method: 'POST',
                data: {
                    "eventId": eventId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert("Event deleted successfully!");
                    location.reload(); // Refrescar la página para actualizar el calendario
                },
                error: function(xhr) {
                    alert("An error occurred while deleting the event.");
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endpush
