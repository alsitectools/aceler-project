@extends('layouts.admin')

<style type="text/css">
    .modal-body {
        background: #ffffff !important;
        padding: 25px !important;
    }

    .liStyleTask{
        background-color: white;
        color: black;
        box-shadow: 0px 0px 15px 0px rgb(0 0 0 / 15%);
        -webkit-box-shadow:0px 0px 15px 0px rgb(0 0 0 / 15%);
        -moz-box-shadow: 0px 0px 15px 0px rgb(0 0 0 / 15%);
        padding: 15px;
        padding-left: 5%;
        border-radius: 10px;
        margin-bottom: 3%;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
    }
    .iStyleTask{
        color: white;
        font-size: 30px;
        padding-right: 4%;
    }
    .pTotalHours{
        background-color: white;
        color: black;
        padding-top: 2%;
        padding-left: 1%;
        border-radius: 10px;
        width: 40%;
    }
    .divIconTask{
        background-color: #aa182C;
        padding: 3%;
        border-radius: 10px;
    }
    .divAlignP{
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .titleTask{
        padding-left: 20px;
        font-weight: bold;
    }
    .subtitleTask{
        font-size: 14px;
        color: #0000008a;
        margin-top: -14px;
        padding-left: 20px;
    }
    @media (max-width: 576px) {
        .header_breadcrumb {
            width: 100% !important;
        }
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .adjustWidthCalendar {
            width: 32% !important;
        }
        .responsiveDivCalendarTask{
            width: 99% !important;
        }
        .liStyleTask{
            padding: 10px;
        }
        .titleTask{
            font-size: 15px;
        }
        .subtitleTask{
            font-size: 13px;
        }
        .testCol{
            width: 99% !important;
        }
        .divIconTask{
            height: 20px;
            width: 20px;
            display: flex;
            align-content: center;
            justify-content: center;
            align-items: center;
        }
        .iStyleTask{
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
    <!-- Filtro para seleccionar el proyecto -->
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-8 pt-lg-3 pt-xl-2">
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
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 testCol">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Calendar') }}</h5>
                </div>
                <div class="card-body">
                    <div id="calendar" class="calendar"></div>
                </div>
            </div>
        </div>       
        <div class="col-lg-4 responsiveDivCalendarTask">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Tasks') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled" id="task-list">
                        @if(isset($tasks) && count($tasks) > 0)
                            @foreach ($tasks as $task)
                                @php
                                    $milestoneTitle = isset($milestones[$task->milestone_id]) ? $milestones[$task->milestone_id]->title : $task->title;
                                    $taskTitle =  __($task->type_name) ?? 'No Type';
                                    $taskTime = $taskHours[$task->id] ?? '00:00';
                                @endphp
                                <li class="liStyleTask">
                                    <div class="divIconTask">
                                        <i class="fa-solid fa-list-check iStyleTask"></i>
                                    </div>
                                    <div class="divAlignP">
                                        <p class="titleTask">{{ $milestoneTitle }} - {{ $taskTitle }}</p>
                                        <p class="subtitleTask"> ({{ $taskTime }})</p>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <p>{{ __('No tasks available') }}</p>
                        @endif
                    </ul>
                    <p class="pTotalHours"><strong>{{ __('Total Hours:') }} </strong><span id="total-hours">{{ $formattedTotalHours }}</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // Deshabilitar el scroll
            document.body.style.overflow = 'hidden';

            get_data();
            getCalendarInfo();
            // Agregar evento de cambio para el filtro
            $('#project_id').on('change', function() {
                get_data();
            });

            adjustLayout();

            $(window).resize(function () {
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
            const operationUrl = '<?php echo url("get-timesheetCalendar"); ?>';

            $.ajax({
                type: 'GET',
                url: operationUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    console.log("success", data);

                    const opacity = 0.4;
                    let allEvents = [];

                    if (data && data.colorData && Array.isArray(data.colorData) && data.expectedHours) {
                        const nonWorkingDays = Object.keys(data.expectedHours).filter(day => data.expectedHours[day] === null);

                        const currentYear = new Date().getUTCFullYear();
                        const nonWorkingEvents = [];

                        const startOfYear = new Date(Date.UTC(currentYear, 0, 1));
                        const endOfYear = new Date(Date.UTC(currentYear, 11, 31));

                        for (let date = new Date(startOfYear); date <= endOfYear; date.setUTCDate(date.getUTCDate() + 1)) {
                            const dayOfWeek = date.toLocaleDateString('en-US', { weekday: 'long', timeZone: 'UTC' }).toLowerCase();
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

                        let events = data.colorData
                            .filter(item => {
                                if (data.specialColorData?.holidayRange && Array.isArray(data.specialColorData.holidayRange) && data.specialColorData.holidayRange.includes(item.date)) {
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
                                allDay: true
                            }));

                        if (data.specialColorData && data.specialColorData.holidayRange) {
                            data.specialColorData.holidayRange.forEach(day => {
                                events.push({
                                    title: 'Holiday',
                                    start: day,
                                    backgroundColor: hexToRgba(data.specialColorData.holidayColor, opacity),
                                    borderColor: data.specialColorData.holidayColor,
                                    textColor: 'black',
                                    allDay: true,
                                    id: `holiday_${day}`,
                                    type: 'holiday'
                                });
                            });
                        }

                        if (data.specialColorData && data.specialColorData.intensiveWorkRange) {
                            for (const [hours, days] of Object.entries(data.specialColorData.intensiveWorkRange)) {
                                days.forEach(day => {
                                    events.push({
                                        title: `${hours} hours`,
                                        start: day,
                                        backgroundColor: hexToRgba(data.specialColorData.intensiveWorkColor, opacity),
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
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    renderCalendar([]); // Renderizar el calendario vacío en caso de error
                }
            });
        }

        function renderCalendar(events) {
            const calendarEl = document.getElementById('calendar');
            const locale = '{{ app()->getLocale() }}';

            const calendar = new FullCalendar.Calendar(calendarEl, {
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

                eventDidMount: function(info) {
                    if (info.event.extendedProps.type === 'holiday' || info.event.extendedProps.type === 'intensive_work') {
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
            //reactivar scroll
            document.body.style.overflow = 'auto';
            //esconder el loader
            document.getElementById('loader-overlay').style.display = 'none';
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
                const deleteUrl = '<?php echo url("user/specialDelete-timetable"); ?>';
                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        "eventId": eventId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert("Event deleted successfully!");
                        location.reload();  // Refrescar la página para actualizar el calendario
                    },
                    error: function(xhr) {
                        alert("An error occurred while deleting the event.");
                        console.error(xhr.responseText);
                    }
                });
            }
            // Función para verificar si una fecha está en días festivos o de trabajo intensivo
            function isDateRestricted(date) {
                let formattedDate = date.toISOString().split('T')[0];

                // Comprobar si la fecha está en los días festivos
                if (data.specialColorData.holidayRange.includes(formattedDate)) {
                    return {
                        restricted: true,
                        message: 'This is a holiday. No work can be logged.'
                    };
                }

                // Comprobar si la fecha tiene jornada intensiva
                for (const [hours, days] of Object.entries(data.specialColorData.intensiveWorkRange)) {
                    if (days.includes(formattedDate)) {
                        return {
                            restricted: true,
                            message: `Intensive work day (${hours} hours). Adjust your schedule accordingly.`
                        };
                    }
                }

                return { restricted: false };
            }

        function get_data() {
    var project_id = $('#project_id').val();
    $.ajax({
        url: $("#path_admin").val() + "/calendarr",
        method: "GET",
        data: {
            'project_id': project_id
        },
        success: function(response) {
            /*
            var filteredEvents = response.events.filter(event => event.start !== null);

            var milestoneColors = {};
            var predefinedColors = [
                '#A5BFF0', '#8797D9', '#B0A8F5', '#C3B1E1', '#B39DD6',
                '#9FA8DA', '#7986CB', '#8E99F3', '#6D8ACF', '#A59FD8'
            ];

            function getMilestoneColor(id) {
                if (!milestoneColors[id]) {
                    var colorIndex = Object.keys(milestoneColors).length % predefinedColors.length;
                    milestoneColors[id] = predefinedColors[colorIndex];
                }
                return milestoneColors[id];
            }

            // Asignar colores a los eventos
            filteredEvents = filteredEvents.map(event => {
                event.backgroundColor = getMilestoneColor(event.milestone_id);
                event.borderColor = event.backgroundColor;
                event.textColor = 'white';
                return event;
            });

            // Actualizar el calendario
            var calendarEl = document.getElementById('calendar');
            var locale = '{{ app()->getLocale() }}';

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: locale,
                initialView: 'dayGridMonth', // Mostrar solo vista mensual
                firstDay: 1, // Iniciar el calendario en lunes
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: '' // Ocultar otras vistas
                },
                buttonText: {
                    today: "{{ trans('messages.today') }}",
                },
                events: filteredEvents,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    // Obtén la URL actual
                    const currentUrl = window.location.href;

                    // Divide la URL por "/"
                    let splitUrl = currentUrl.split("/");

                    // Reemplaza la última parte del arreglo con "timesheet"
                    splitUrl[splitUrl.length - 1] = "timesheet";

                    // Une la URL de nuevo
                    let newUrl = splitUrl.join("/");

                    // Redirige al usuario a la nueva URL
                    window.location.href = newUrl;
                }

            });
            calendar.render();
*/
            // Actualizar la lista de tareas
            var taskList = $('#task-list');
            taskList.empty(); // Limpiar la lista actual

            if (response.tasks.length > 0) {
                response.tasks.forEach(function(task) {
                    var taskHtml = `<li class="liStyleTask">
                                       <div class="divIconTask">
                                        <i class="fa-solid fa-list-check iStyleTask"></i>
                                        </div>
                                        <div class="divAlignP">
                                            <p class="titleTask"> ${task.milestoneTitle} - ${task.taskTitle}</p>
                                            <p class="subtitleTask">(${task.taskTime})</p>
                                       </div>
                                    </li>`;
                    taskList.append(taskHtml);
                });
            } else {
                taskList.append('<p>{{ __('No tasks available') }}</p>');
            }

            // Actualizar las horas totales
            $('#total-hours').text(response.formattedTotalHours);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
}


    </script>
@endpush
