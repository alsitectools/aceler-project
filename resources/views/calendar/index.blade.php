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
        .responsiveDiv{
            width: 32% !important;
        }
        .liStyleTask{
            padding: 10px;
        }
        .titleTask{
            font-size: 13px;
        }
        .subtitleTask{
            font-size: 12px
        }
    }
</style>

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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Calendar') }}</h5>
                </div>
                <div class="card-body">
                    <div id="calendar" class="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 responsiveDiv">
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
            get_data();

            // Agregar evento de cambio para el filtro
            $('#project_id').on('change', function() {
                get_data();
            });
        });

function get_data() {
    var project_id = $('#project_id').val();
    $.ajax({
        url: $("#path_admin").val() + "/calendarr",
        method: "GET",
        data: {
            'project_id': project_id
        },
        success: function(response) {
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
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: "{{ trans('messages.today') }}",
                    timeGridDay: "{{ __('Day') }}",
                    timeGridWeek: "{{ __('Week') }}",
                    dayGridMonth: "{{ __('Month') }}"
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

            // Actualizar la lista de tareas
                var taskList = $('#task-list');
                taskList.empty(); // Limpiar la lista actual

                if (response.tasks.length > 0) {
                    response.tasks.forEach(function(task) {
                        // Agregar la clase 'liStyleTask' al elemento li
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
