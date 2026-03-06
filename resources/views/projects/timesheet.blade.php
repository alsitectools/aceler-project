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
                href="{{ route('projects.show', [$currentWorkspace->slug, $project_id]) }}">{{ $project_name }}</a>
        </li>
    @endif
    <li class="breadcrumb-item"> {{ __('Timesheet') }}</li>
@endsection
@section('action-button')
    <div class="d-flex justify-content-end align-items-center row1">
        @if ($project_id == -1)
            <div class="col-sm-auto">
                <select id="workspace-filter-select" class="modern-saas-select">
                    <option value="current" selected>{{ __('Current Workspace') }}</option>
                    <option value="all">{{ __('All Workspaces') }}</option>
                </select>
            </div>
        @endif
        @if (isset($currentWorkspace) && $currentWorkspace)
            @if ($project_id == -1)
                <div class="col-sm-auto">
                    <button id="add_task" type="button" class="btn btn-primary add_task" data-ajax-popup="true"
                        data-size="lg" data-title="{{ __('Create New Task') }}"
                        data-url="{{ route('tasks.create', $currentWorkspace->slug) }}" title="{{ __('Add Task') }}"><i
                            class="fa-solid fa-thumbtack"></i>
                        {{ __('Add Task on Timesheet') }}</button>
                </div>
            @else
                <div class="col-sm-auto">
                    <a href="{{ route('timesheet.csv.imputedHours.download', $project_id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-download"></i> {{ __('Download') }}
                    </a>
                </div>
            @endif
        @endif
        <div class="col-sm-auto">
            <div class="weekly-dates-div weekArrowsPadding">
                <div class="btn-group" role="group">
                    <button type="button" id="prevWeekBtn" class="btn btn-primary weekPickerBtn"
                        aria-label="{{ __('Previous week') }}" title="{{ __('Previous week') }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" id="weekRangeDisplay" class="btn btn-primary weekPickerBtn"
                        aria-label="{{ __('Select week') }}" title="{{ __('Select week') }}">
                        <i class="fa-solid fa-calendar-days"></i>
                    </button>
                    <button type="button" id="nextWeekBtn" class="btn btn-primary weekPickerBtn"
                        aria-label="{{ __('Next week') }}" title="{{ __('Next week') }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <input type="date" id="weekPicker" class="weekPickerHidden" tabindex="-1" aria-hidden="true">

                <input type="hidden" id="weeknumber" value="0">
                <input type="hidden" id="selected_dates">
            </div>
        </div>
        @if ($project_id != '-1')
            <!-- <div class="col-auto">
                                                                                                                                                                                    <a href="{{ route($client_keyword . 'projects.show', [$currentWorkspace->slug, $project_id]) }}"
                                                                                                                                                                                        class="btn btn-sm btn-primary">
                                                                                                                                                                                        <i class=" ti ti-arrow-back-up"></i>
                                                                                                                                                                                    </a>
                                                                                                                                                                                </div> -->
        @endif
    </div>
@endsection
@section('content')
    <section class="section">
        @if ($currentWorkspace)
            <div class="row">
                <div class="col-md-12">

                    <div class="card border modifiedWidthTime position-relative mt-4" style="min-height: 180px;">
                        <div class="text-center position-absolute" style="top: -18px; left: 0; right: 0; z-index: 10;">
                            <div class="d-inline-flex align-items-center justify-content-center tag-date-range">
                                <span id="weekRangeSpinner" class="spinner-border spinner-border-sm" role="status"
                                    style="display: none; margin-right: 8px;"></span>
                                <span class="weekRangeText">{{ __('Select week') }}</span>
                            </div>
                        </div>
                        <div id="timesheet-table-view" class="pt-4"></div>

                        <div class="notfound-timesheet text-center w-100" style="display: none; padding-top: 3rem;">
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
            </div>
        @endif
    </section>
@endsection

@push('css-page')
@endpush
@push('scripts')
    {{-- jQuery ya está cargado en el layout principal, no duplicar para evitar perder plugins de Bootstrap --}}

    <script>
        function getTimesheetWeekStorageKey() {
            // Evita colisiones entre workspaces/proyectos
            return 'timesheet:selectedWeekStart:{{ $currentWorkspace->slug }}:{{ $project_id }}';
        }

        function saveSelectedWeekStart(dateStr) {
            try {
                if (dateStr) {
                    localStorage.setItem(getTimesheetWeekStorageKey(), dateStr);
                }
            } catch (e) {
                // noop
            }
        }

        function loadSelectedWeekStart() {
            try {
                return localStorage.getItem(getTimesheetWeekStorageKey());
            } catch (e) {
                return null;
            }
        }

        function formatWeekRange(rangeStr) {
            // Espera: "YYYY-MM-DD - YYYY-MM-DD" => "DD-MM-YYYY → DD-MM-YYYY"
            if (!rangeStr) {
                return '';
            }

            var parts = String(rangeStr).split(' - ');
            if (parts.length !== 2) {
                return String(rangeStr);
            }

            var start = moment(parts[0], 'YYYY-MM-DD', true);
            var end = moment(parts[1], 'YYYY-MM-DD', true);

            if (!start.isValid() || !end.isValid()) {
                return String(rangeStr);
            }

            return start.format('DD/MM/YYYY') +
                ' <i class="fa-solid fa-arrow-right" style="margin: 0 8px; font-size: 12px;"></i> ' + end.format(
                    'DD/MM/YYYY');
        }

        function setWeekFromDate(dateStr) {
            if (!dateStr) {
                return;
            }

            // El backend usa Carbon::now()->addWeeks($week)->startOfWeek() (Lunes)
            // Para alinear, usamos ISO week (lunes -> domingo)
            var currentWeekStart = moment().startOf('isoWeek');
            var targetWeekStart = moment(dateStr, 'YYYY-MM-DD').startOf('isoWeek');

            var weekOffset = targetWeekStart.diff(currentWeekStart, 'weeks');
            $('#weeknumber').val(weekOffset);
        }

        function ajaxFilterTimesheetTableView() {

            var mainEle = $('#timesheet-table-view');
            var notfound = $('.notfound-timesheet');
            var weekRangeSpinner = $('#weekRangeSpinner');
            var weekRangeText = $('.weekRangeText');

            // Mostrar spinner y ocultar texto durante la carga
            weekRangeSpinner.show();
            weekRangeText.hide();

            var week = parseInt($('#weeknumber').val());
            var project_id = '{{ $project_id }}';
            var allWorkspaces = $('#workspace-filter-select').length ? ($('#workspace-filter-select').val() === 'all' ?
                'true' : 'false') : 'false';

            var data = {
                week: week,
                project_id: project_id,
                all: allWorkspaces,
            };

            console.log('Enviando datos:', data); // Debug

            $.ajax({
                url: '{{ route('filter.timesheet.table.view', '__slug') }}'.replace('__slug',
                    '{{ $currentWorkspace->slug }}'),

                data: data,
                success: function(data) {

                    console.log('Respuesta recibida:', data); // Debug

                    // Ocultar spinner y mostrar texto
                    weekRangeSpinner.hide();
                    weekRangeText.show();

                    // Mostrar el rango de semana fuera del "botón"
                    $('.weekRangeText').html(formatWeekRange(data.onewWeekDate));
                    $('.weekly-dates-div #selected_dates').val(data.selectedDate);

                    // Sincroniza el selector para que muestre el inicio de la semana actual cargada
                    if (data.selectedDate) {
                        var parts = String(data.selectedDate).split(' - ');
                        if (parts.length > 0 && parts[0]) {
                            $('#weekPicker').val(parts[0]);
                            saveSelectedWeekStart(parts[0]);
                        }
                    }

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
                },
                error: function() {
                    // Ocultar spinner y mostrar texto en caso de error
                    weekRangeSpinner.hide();
                    weekRangeText.show();
                }
            });
        }

        $(function() {
            // Restaurar semana seleccionada tras recarga (fallback: hoy)
            var savedWeekStart = loadSelectedWeekStart();
            var initialDate = savedWeekStart || moment().format('YYYY-MM-DD');
            $('#weekPicker').val(initialDate);

            // Importantísimo: setea el offset antes del primer AJAX
            setWeekFromDate(initialDate);
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '#weekRangeDisplay', function() {
            var picker = document.getElementById('weekPicker');
            if (!picker) {
                return;
            }

            // Chrome/Edge soportan showPicker()
            if (typeof picker.showPicker === 'function') {
                picker.showPicker();
            } else {
                picker.focus();
                picker.click();
            }
        });

        // Navegación a semana anterior
        $(document).on('click', '#prevWeekBtn', function() {
            var currentWeek = parseInt($('#weeknumber').val());
            var newWeek = currentWeek - 1;

            var currentWeekStart = moment().startOf('isoWeek');
            var targetWeekStart = currentWeekStart.clone().add(newWeek, 'weeks');

            $('#weeknumber').val(newWeek);
            $('#weekPicker').val(targetWeekStart.format('YYYY-MM-DD'));
            saveSelectedWeekStart(targetWeekStart.format('YYYY-MM-DD'));

            ajaxFilterTimesheetTableView();
        });

        // Navegación a semana siguiente
        $(document).on('click', '#nextWeekBtn', function() {
            var currentWeek = parseInt($('#weeknumber').val());
            var newWeek = currentWeek + 1;

            var currentWeekStart = moment().startOf('isoWeek');
            var targetWeekStart = currentWeekStart.clone().add(newWeek, 'weeks');

            $('#weeknumber').val(newWeek);
            $('#weekPicker').val(targetWeekStart.format('YYYY-MM-DD'));
            saveSelectedWeekStart(targetWeekStart.format('YYYY-MM-DD'));

            ajaxFilterTimesheetTableView();
        });

        $(document).on('change', '#weekPicker', function() {
            var picked = $(this).val();
            saveSelectedWeekStart(picked);
            setWeekFromDate(picked);
            ajaxFilterTimesheetTableView();
        });

        $(document).on('change', '#workspace-filter-select', function() {
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '[data-ajax-timesheet-popup="true"]', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var modalId = $(this).data('modal-id') || 'commonModal';
            var data = {};
            var url = $(this).data('url');
            var type = $(this).data('type');
            var date = $(this).data('date');
            var task_id = $(this).data('task-id');
            var user_id = $(this).data('user-id');
            var p_id = $(this).data('project-id');
            var milestone_id = $(this).data('milestone-id');

            data.date = date;
            data.task_id = task_id;
            data.milestone_id = milestone_id;

            if (user_id != undefined) {
                data.user_id = user_id;
            }

            var title;
            if (type == 'create') {
                title = '{{ __('Add record to timesheet') }}';
                data.p_id = '{{ $project_id }}';
                data.project_id = data.p_id != '-1' ? data.p_id : p_id;
                data.milestone_id = milestone_id;
            } else if (type == 'edit') {
                title = '{{ __('Edit timesheet entry') }}';
                data.milestone_id = milestone_id;
            }

            var modalElement = document.getElementById(modalId);

            // Verifica que el modal existe en el DOM
            if (!modalElement) {
                console.error("El modal con ID '" + modalId + "' no existe en el DOM.");
                return false;
            }

            // Restaurar el modal-dialog a su estado original (solo clase 'modal-dialog')
            $("#" + modalId + " .modal-dialog").attr('class', 'modal-dialog');

            $.ajax({
                url: url,
                data: data,
                cache: false,
                success: function(data) {
                    $('#' + modalId + ' .body').html(data);
                    $("#" + modalId + " .modal-title").html(title + ` <small>(` + moment(date).format(
                        "ddd DD MMM") + `)</small>`);

                    // Usar Bootstrap 5 getOrCreateInstance para evitar conflictos
                    var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.show();

                    commonLoader();
                    loadConfirm();
                },
                error: function(data) {
                    data = data.responseJSON;
                    show_toastr('Error', data.error, 'error');
                }
            });

            return false;
        });

        // Evento para mostrar popup al hacer click en las horas totales del día
        $(document).on('click', '.day-total-hours', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var dateFormatted = $(this).data('date-formatted');
            var dateValue = $(this).data('date');
            var hoursValue = $(this).text().trim();
            var tasksData = $(this).data('tasks') || [];

            var modalId = 'dayTotalModal';
            var modalElement = document.getElementById(modalId);


            // Si el modal no existe, crear uno dinámicamente
            if (!modalElement) {
                var modalHTML = `
                    <div class="modal fade" id="dayTotalModal" tabindex="-1" role="dialog" aria-labelledby="dayTotalModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="dayTotalModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="dayTotalContent"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('body').append(modalHTML);
                modalElement = document.getElementById(modalId);
            }

            console.log("he sido clicked dentro del resumen")
            // Construir el contenido del modal
            var contentHTML = '<div class="day-summary">';

            // Encabezado con fecha y total de horas
            contentHTML +=
                '<div class="summary-header mb-3" style="border-bottom: 2px solid #aa182c;padding-bottom: 10px;display: flex;flex-direction: row;align-content: center;justify-content: space-evenly;align-items: center;">';
            contentHTML += '<p style="margin: 0;"><strong>' + dateFormatted + '</strong></p>';
            contentHTML += '<strong>' + hoursValue + '</strong>';
            contentHTML += '</div>';

            // Listar las tareas
            if (tasksData && tasksData.length > 0) {
                contentHTML += '<div class="tasks-list">';
                tasksData.forEach(function(task, index) {
                    contentHTML +=
                        '<div class="task-item mb-2" style="padding: 8px; background-color: #f8f9fa; border-radius: 4px;">';
                    contentHTML +=
                        '<div class="task-name" style="font-weight: 500; margin-bottom: 4px;">• ' + (task
                            .project_name || '') + ' - ' + task.task_name + ' - ' + task.hours + '</div>';
                    contentHTML += '</div>';
                });
                contentHTML += '</div>';
            } else {
                contentHTML += '<p class="text-muted">{{ __('No tasks recorded for this day') }}</p>';
            }

            contentHTML += '</div>';

            // Llenar los datos del modal
            $('#dayTotalModalLabel').text('{{ __('Day Summary') }}');
            $('#dayTotalContent').html(contentHTML);

            // Mostrar el modal
            var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();

            return false;
        });
    </script>
@endpush

<style type="text/css">
    .weekly-dates-div {
        padding: 8px 12px 8px 15px !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .weekly-dates-div {
            padding: 8px 12px 8px 25px !important;
        }

        .modifiedWidthTime {
            width: 99% !important;
        }
    }

    #add_task {
        display: flex !important;
        align-items: center;
        justify-content: space-around !important;
        width: 320px;
    }

    .weekly-dates-div #weekRangeDisplay.weekPickerBtn {
        text-align: left;
        border-color: transparent;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .weekly-dates-div #weekRangeDisplay.weekPickerBtn:hover {
        border-color: transparent;
    }

    .weekly-dates-div #weekRangeDisplay.weekPickerBtn:focus,
    .weekly-dates-div #weekRangeDisplay.weekPickerBtn:active,
    .weekly-dates-div #weekRangeDisplay.weekPickerBtn:focus-visible {
        outline: none;
        border-color: transparent;
    }

    .weekly-dates-div #weekRangeDisplay.weekPickerBtn {
        cursor: pointer;
    }

    /* Input date oculto: se usa solo para abrir el calendario */
    .weekly-dates-div #weekPicker.weekPickerHidden {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    /* Tag de fecha centrado flotante */
    .tag-date-range {
        background-color: #aa182c;
        color: #ffffff;
        font-weight: 700;
        font-size: 16px;
        letter-spacing: 0.5px;
        padding: 10px 33px;
        border-radius: 13px;
        box-shadow: 0 4px 12px rgba(170, 24, 44, 0.2);
        display: inline-flex;
        border: 2px solid #6b101c;
    }

    /* Select moderno estilo Neutro / Soft */
    .modern-saas-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: auto;
        display: inline-block;
        font-size: 0.9rem;
        font-weight: 500;
        color: #495057;
        background-color: #f8f9fa;
        padding: 0.5rem 2.2rem 0.5rem 1rem;
        margin-right: 15px;
        border: 1px solid #aa182c;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        /* Flecha gris neutra */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.8rem center;
        background-size: 1rem;
    }

    .modern-saas-select:hover {
        background-color: #e9ecef;
        border-color: #aa182c;
    }

    .modern-saas-select:focus {
        outline: none;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.1);
    }
</style>
