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
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .task-item-time {
        font-size: 12px;
        color: #AA182C;
        font-weight: 600;
        margin-top: 4px;
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
                    <select id="workspace-select" class="form-select"
                        style="width: auto; display: inline-block; font-size: 0.9rem; padding: 0.25rem 2.5rem 0.25rem 0.75rem; cursor: pointer; font-weight: 500;">
                        <option value="current" selected>{{ __('Current Workspace') }}</option>
                        <option value="all">{{ __('All Workspaces') }}</option>
                    </select>
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
@endsection

@push('scripts')
    <script>
        let calendar;
        const currentWorkspaceId = "{{ $currentWorkspace->id }}";

        $(document).ready(function() {

            // Deshabilitar el scroll
            document.body.style.overflow = 'hidden';

            getCalendarInfo();

            adjustLayout();

            $(window).resize(function() {
                adjustLayout();
            });
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

                    if (data && data.colorData && Array.isArray(data.colorData) && data.expectedHours) {
                        const nonWorkingDays = Object.keys(data.expectedHours).filter(day => data.expectedHours[
                            day] === null);

                        const nonWorkingEvents = [];

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
                                    nonWorkingEvents.push({
                                        title: 'Non-working day',
                                        start: date.toISOString().split('T')[0],
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
                    } else {
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

                    if (response.tasks && response.tasks.length > 0) {
                        // Agrupar tareas por proyecto (y workspace si aplica)
                        const projectsMap = {};

                        response.tasks.forEach(function(task) {
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
