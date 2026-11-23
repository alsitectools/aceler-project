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

    /* =============================================
       CALENDARIO · horas / jornada en cada día, total semanal, resumen del mes y detalle del día
       ============================================= */
    .cal-kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }

    .cal-kpi {
        border: 1px solid #eef0f3;
        border-radius: 10px;
        padding: 8px 12px;
    }

    .cal-kpi small {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .cal-kpi b {
        font-size: 19px;
        color: #1f2937;
    }

    .cal-kpi span {
        font-size: 12px;
        color: #6b7280;
    }

    .cal-kpi.is-warn b {
        color: #c0392b;
    }

    #calendar .fc-toolbar-title {
        font-size: 20px;
    }

    #calendar .fc-toolbar-title::first-letter {
        text-transform: uppercase;
    }

    /* eventos convertidos en contenido del día (sin la pastilla de color) */
    #calendar .fc-daygrid-event.cal-ev {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        margin: 0 4px 2px;
        padding: 0;
        white-space: normal;
        cursor: pointer;
    }

    #calendar .fc-daygrid-event.cal-ev:hover {
        background: transparent !important;
    }

    .cal-h {
        padding: 2px 4px;
        color: #374151;
    }

    .cal-h-val {
        font-size: 11.5px;
        color: #6b7280;
        white-space: nowrap;
    }

    .cal-h-val b {
        font-size: 15px;
        color: #1f2937;
    }

    .cal-st-text--none b {
        color: #c0392b;
    }

    .cal-st-text--overtime b {
        color: #1d6fa5;
    }

    .cal-h-warn {
        margin-left: 4px;
        font-size: 11px;
        color: #d08700;
    }

    .cal-h-bar {
        height: 5px;
        margin-top: 4px;
        border-radius: 3px;
        background: #f1f2f4;
        overflow: hidden;
    }

    .cal-h-bar i {
        display: block;
        height: 100%;
    }

    .cal-bg--none { background: #e06c71; }
    .cal-bg--partial { background: #e8c800; }
    .cal-bg--complete { background: #5cc45a; }
    .cal-bg--overtime { background: #63b6dc; }

    .cal-off {
        padding: 2px 4px;
        font-size: 11px;
        color: #b6bbc4;
    }

    .cal-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        max-width: 100%;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
    }

    .cal-chip--holiday {
        background: rgba(145, 221, 207, .35);
        color: #1f6f63;
    }

    .cal-chip--intensive {
        background: rgba(137, 168, 178, .25);
        color: #3f5a63;
    }

    .cal-ev-del {
        margin-left: 2px;
        font-size: 13px;
        line-height: 1;
        opacity: .6;
    }

    .cal-ev-del:hover {
        opacity: 1;
        color: #c0392b;
    }

    /* tono del día según su estado */
    #calendar .fc-daygrid-day.cal-st--none {
        background: #fdf3f4;
    }

    #calendar .fc-daygrid-day.cal-st--off {
        background: #fafbfc;
    }

    #calendar .fc-daygrid-day.cal-st--holiday {
        background: rgba(145, 221, 207, .12);
    }

    #calendar .fc-day-other {
        opacity: .55;
    }

    /* hoy: número en círculo; seleccionado: borde corporativo */
    #calendar .fc .fc-daygrid-day.fc-day-today,
    #calendar .fc-daygrid-day.fc-day-today {
        background-color: inherit;
    }

    #calendar .fc-day-today .fc-daygrid-day-number {
        color: #fff;
        background: #AA182C;
        border-radius: 999px;
        padding: 1px 8px;
        margin: 3px;
    }

    #calendar .fc-daygrid-day.cal-selected {
        box-shadow: inset 0 0 0 2px #AA182C;
        background: #fff7f8;
    }

    /* total de la semana en el hueco del número de semana */
    #calendar .fc-daygrid-week-number {
        background: transparent;
        padding: 0;
        top: 5px;
        left: 4px;
        pointer-events: none;
    }

    /* compacto: el mes entero cabe en la ventana (alto calculado en fitCalendarHeight) */
    #calendar .fc-header-toolbar.fc-toolbar {
        margin-bottom: 10px !important;
    }

    #calendar .fc-button {
        padding: 4px 10px;
    }

    #calendar .fc-daygrid-day-number {
        padding: 3px 6px;
        font-size: 12.5px;
    }

    #calendar .fc-daygrid-day-frame {
        min-height: 0;
    }

    #calendar .fc-daygrid-day-events {
        margin-top: 0;
    }

    .testCol > .card > .card-body {
        padding-top: 14px;
        padding-bottom: 12px;
    }

    .testCol > .card > .card-header {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .cal-kpis {
        margin-bottom: 10px;
    }

    .cal-legend {
        margin-top: 8px;
    }

    .cal-side .card-body {
        overflow-y: auto;
    }

    /* filas bajas: una línea por día (horas + barra fina), sin el texto de no laborable */
    #calendar.cal-compact .fc-daygrid-day-number {
        padding: 1px 5px;
        font-size: 12px;
    }

    #calendar.cal-compact .cal-h {
        padding: 0 2px;
    }

    #calendar.cal-compact .cal-h-val b {
        font-size: 12.5px;
    }

    #calendar.cal-compact .cal-h-val {
        font-size: 10.5px;
    }

    #calendar.cal-compact .cal-h-bar {
        height: 3px;
        margin-top: 2px;
    }

    #calendar.cal-compact .cal-off {
        display: none;
    }

    #calendar.cal-compact .fc-daygrid-week-number {
        top: 3px;
    }

    #calendar.cal-compact .cal-wk {
        padding: 0 5px;
        font-size: 9.5px;
    }

    #calendar.cal-compact .cal-wk-exp {
        display: none;
    }

    #calendar.cal-compact .cal-chip {
        padding: 0 6px;
        font-size: 10px;
    }

    @media (max-height: 820px) {
        .cal-kpi {
            padding: 5px 10px;
        }

        .cal-kpi b {
            font-size: 16px;
        }

        .cal-h-val b {
            font-size: 13px;
        }
    }

    .cal-wk {
        display: inline-block;
        padding: 1px 7px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 10.5px;
        white-space: nowrap;
    }

    .cal-wk b {
        color: #1f2937;
    }

    /* panel lateral: detalle del día */
    .cal-side .card-header small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .cal-side .card-header h5 {
        margin: 2px 0 0;
    }

    .cal-day {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-bottom: 14px;
        margin-bottom: 8px;
        border-bottom: 1px solid #eef0f3;
    }

    .cal-day-num {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        white-space: nowrap;
    }

    .cal-day svg {
        flex: none;
    }

    .cal-day-num span {
        font-size: 13px;
        font-weight: 600;
        color: #9ca3af;
    }

    .cal-day-num #total-hours {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
    }

    .cal-day-state {
        font-size: 12px;
        color: #6b7280;
    }

    .cal-day .btn {
        margin-left: auto;
        white-space: nowrap;
    }

    .cal-proj {
        margin: 10px 0 2px;
        font-size: 11px;
        font-weight: 700;
        color: #AA182C;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .cal-proj small {
        margin-left: 6px;
        color: #9ca3af;
        font-weight: 600;
        text-transform: none;
    }

    .cal-task {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f4f5f7;
    }

    .cal-task-ms {
        font-size: 11px;
        color: #9ca3af;
    }

    .cal-task-name {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }

    .cal-task-time {
        grid-row: 1 / 3;
        grid-column: 2;
        align-self: center;
        font-weight: 700;
    }

    .cal-empty {
        padding: 10px 0;
        font-size: 13px;
        color: #9ca3af;
    }

    .cal-pending {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #eef0f3;
    }

    .cal-pending h6 {
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .cal-pd {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 5px 0;
        border: 0;
        background: none;
        font-size: 12.5px;
        color: #374151;
        text-align: left;
    }

    .cal-pd:hover {
        color: #AA182C;
    }

    .cal-pd i {
        width: 8px;
        height: 8px;
        border-radius: 2px;
        background: #e06c71;
        flex: none;
    }

    .cal-pd span {
        margin-left: auto;
        color: #AA182C;
        font-weight: 600;
        font-size: 12px;
        text-transform: none;
    }

    .cal-pd-more {
        font-size: 12px;
        color: #9ca3af;
    }

    .cal-legend {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px 16px;
        margin-top: 10px;
        font-size: 12px;
        color: #6b7280;
    }

    .cal-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .cal-legend i {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        display: inline-block;
    }

    @media (max-width: 768px) {
        .cal-kpis {
            grid-template-columns: repeat(2, 1fr);
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
    @include('calendar.partials.calendar_help')
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
                    {{-- Resumen del mes visible (se rellena en updateMonthSummary) --}}
                    <div class="cal-kpis" id="cal-kpis">
                        <div class="cal-kpi"><small>{{ __('Logged this month') }}</small><b data-kpi="logged">—</b> <span data-kpi="expected"></span></div>
                        <div class="cal-kpi" data-kpi-box="none"><small>{{ __('Days without hours') }}</small><b data-kpi="none">—</b> <span data-kpi="workdays"></span></div>
                        <div class="cal-kpi"><small>{{ __('Overtime') }}</small><b data-kpi="overtime">—</b> <span data-kpi="overtimeDays"></span></div>
                        <div class="cal-kpi"><small>{{ __('Holidays') }}</small><b data-kpi="holidays">—</b> <span>{{ __('this month') }}</span></div>
                    </div>
                    <div id="calendar" class="calendar"></div>
                    <div class="cal-legend">
                        <span><i class="cal-bg--none"></i>{{ __('No hours') }}</span>
                        <span><i class="cal-bg--partial"></i>{{ __('Partial') }}</span>
                        <span><i class="cal-bg--complete"></i>{{ __('Complete') }}</span>
                        <span><i class="cal-bg--overtime"></i>{{ __('Overtime') }}</span>
                        <span><i style="background: rgba(145, 221, 207, .6)"></i>{{ __('Holiday') }}</span>
                        <span><i style="background: rgba(137, 168, 178, .5)"></i>{{ __('Intensive workday') }}</span>
                        <span><i style="background: #eef0f3"></i>{{ __('Non-working day') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 responsiveDivCalendarTask">
            <div class="card cal-side">
                <div class="card-header">
                    <small>{{ __('Day detail') }}</small>
                    <h5 id="selected-date">{{ __('Select a day to view tasks') }}</h5>
                </div>
                <div class="card-body">
                    <div class="cal-day" id="cal-day" hidden>
                        <svg width="64" height="64" viewBox="0 0 36 36" aria-hidden="true">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f2f4" stroke-width="3.5" />
                            <circle id="cal-day-ring" cx="18" cy="18" r="15.9" fill="none" stroke="#e8c800" stroke-width="3.5"
                                stroke-linecap="round" stroke-dasharray="0 100" transform="rotate(-90 18 18)" />
                        </svg>
                        <div>
                            <div class="cal-day-num"><span id="total-hours">0:00</span> <span id="cal-day-expected"></span></div>
                            <div class="cal-day-state" id="cal-day-state"></div>
                        </div>
                        @unless (\Auth::guard('client')->check())
                            <a class="btn btn-sm btn-primary" id="cal-day-log" href="#" hidden>
                                <i class="fa-solid fa-plus"></i> {{ __('Log hours') }}
                            </a>
                        @endunless
                    </div>
                    <div id="task-list"></div>
                    <div class="cal-pending">
                        <h6>{{ __('Pending days this month') }}</h6>
                        <div id="cal-pending-list"></div>
                    </div>
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
    @php
        $calTexts = [
            'weekShort' => __('W'),
            'nonWorking' => __('Non-working day'),
            'holiday' => __('Holiday'),
            'intensive' => __('Intensive'),
            'remove' => __('Delete'),
            'tooMany' => __('Check: more than 12 h in one day'),
            'ofWorkdays' => __('of :n working days'),
            'inDays' => __('in :n days'),
            'noPending' => __('No pending days this month'),
            'noHours' => __('No hours'),
            'see' => __('View'),
            'moreDays' => __('+ :n more days'),
            'future' => __('Future day'),
            'missing' => __('Missing :h'),
            'partial' => __('Partial'),
            'complete' => __('Complete'),
            'overtimeLower' => __('overtime'),
            'noTasks' => __('No tasks available'),
        ];
    @endphp
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

        // --- horas / jornada por día (para pintar cada día, el total semanal y el resumen del mes) ---
        let expectedHoursState = null; // jornada por día de la semana (monday: '08:00:00' | null)
        let dayInfo = {}; // 'YYYY-MM-DD' => minutos imputados
        const timesheetUrl = "{{ \Auth::guard('client')->check() ? '' : route('timesheet.index', $currentWorkspace->slug) }}";
        const calT = @json($calTexts);

        const pad2 = (n) => String(n).padStart(2, '0');
        const isoLocal = (d) => d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
        const todayIso = () => isoLocal(new Date());
        const toMin = (t) => {
            if (!t) return 0;
            const p = String(t).split(':');
            return (Number(p[0]) || 0) * 60 + (Number(p[1]) || 0);
        };
        const fmtMin = (m) => Math.floor(m / 60) + ':' + pad2(Math.round(m) % 60);
        const calEsc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
        const weekdayKey = (iso) => new Date(iso + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();

        function isHolidayDate(iso) {
            return Array.isArray(specialDaysState.holidayRange) && specialDaysState.holidayRange.includes(iso);
        }

        function intensiveHoursFor(iso) {
            const ranges = specialDaysState.intensiveWorkRange || {};
            for (const [hours, dates] of Object.entries(ranges)) {
                if (Array.isArray(dates) && dates.includes(iso)) return hours;
            }
            return null;
        }

        // Jornada esperada del día en minutos (0 = no laborable o festivo)
        function expectedMinFor(iso) {
            if (isHolidayDate(iso)) return 0;
            const intensive = intensiveHoursFor(iso);
            if (intensive) return toMin(intensive);
            const val = expectedHoursState ? expectedHoursState[weekdayKey(iso)] : null;
            return val ? toMin(val) : 0;
        }

        // Estado del día calculado en minutos (el servidor compara texto y marca 8:00 como parcial)
        function dayState(iso) {
            if (isHolidayDate(iso)) return 'holiday';
            const exp = expectedMinFor(iso);
            if (!exp || iso > todayIso()) return 'off';
            const min = dayInfo[iso] || 0;
            if (min === 0) return 'none';
            if (min < exp) return 'partial';
            if (min === exp) return 'complete';
            return 'overtime';
        }

        function selectDay(iso) {
            document.querySelectorAll('#calendar .fc-daygrid-day.cal-selected').forEach((el) => el.classList.remove('cal-selected'));
            const cell = document.querySelector(`#calendar .fc-daygrid-day[data-date="${iso}"]`);
            if (cell) cell.classList.add('cal-selected');
        }

        const capFirst = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : s;

        function formatLongDate(iso) {
            return capFirst(new Date(iso + 'T00:00:00').toLocaleDateString('{{ app()->getLocale() }}', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            }));
        }

        // Total de la semana en el hueco del número de semana (lunes a domingo)
        function weekNumberContent(arg) {
            const start = new Date(arg.date);
            let min = 0, exp = 0, any = false;
            for (let i = 0; i < 7; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                const iso = isoLocal(d);
                min += dayInfo[iso] || 0;
                exp += expectedMinFor(iso);
                if (iso <= todayIso()) any = true;
            }
            const label = calT.weekShort + arg.num;
            if (!any && !min) {
                return { html: `<span class="cal-wk">${label}</span>` };
            }
            return { html: `<span class="cal-wk" title="${fmtMin(min)} / ${fmtMin(exp)}">${label} · <b>${fmtMin(min)}</b><span class="cal-wk-exp"> / ${fmtMin(exp)}</span></span>` };
        }

        // Resumen del mes visible y lista de días pendientes
        function updateMonthSummary() {
            if (!calendar) return;
            const view = calendar.view;
            const start = new Date(view.currentStart);
            const end = new Date(view.currentEnd);
            const today = todayIso();
            let logged = 0, expected = 0, workdays = 0, overtime = 0, overtimeDays = 0, holidays = 0;
            const pending = [];

            for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
                const iso = isoLocal(d);
                const min = dayInfo[iso] || 0;
                if (isHolidayDate(iso)) holidays++;
                if (iso > today) continue;
                logged += min;
                const exp = expectedMinFor(iso);
                if (!exp) continue;
                workdays++;
                expected += exp;
                if (min === 0) pending.push(iso);
                if (min > exp) {
                    overtime += min - exp;
                    overtimeDays++;
                }
            }

            const set = (k, v) => { const el = document.querySelector(`#cal-kpis [data-kpi="${k}"]`); if (el) el.textContent = v; };
            set('logged', fmtMin(logged));
            set('expected', '/ ' + fmtMin(expected));
            set('none', pending.length);
            set('workdays', calT.ofWorkdays.replace(':n', workdays));
            set('overtime', overtime ? '+' + fmtMin(overtime) : '0:00');
            set('overtimeDays', overtimeDays ? calT.inDays.replace(':n', overtimeDays) : '');
            set('holidays', holidays);
            const noneBox = document.querySelector('#cal-kpis [data-kpi-box="none"]');
            if (noneBox) noneBox.classList.toggle('is-warn', pending.length > 0);

            const list = document.getElementById('cal-pending-list');
            if (!list) return;
            if (!pending.length) {
                list.innerHTML = `<div class="cal-empty">${calT.noPending}</div>`;
                return;
            }
            pending.reverse();
            const shown = pending.slice(0, 4).map((iso) => {
                const label = capFirst(new Date(iso + 'T00:00:00').toLocaleDateString('{{ app()->getLocale() }}', { weekday: 'long', day: 'numeric' }));
                return `<button type="button" class="cal-pd" data-cal-day="${iso}"><i></i>${calEsc(label)} · ${calT.noHours.toLowerCase()}<span>${calT.see} →</span></button>`;
            }).join('');
            const more = pending.length > 4 ? `<div class="cal-pd-more">${calT.moreDays.replace(':n', pending.length - 4)}</div>` : '';
            list.innerHTML = shown + more;
        }

        $(document).on('click', '[data-cal-day]', function() {
            const iso = $(this).data('cal-day');
            selectDay(iso);
            loadTasksByDate(iso);
        });

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
            fitCalendarHeight();
        }

        // El mes entero cabe en la ventana sin scroll: alto del calendario = lo que queda hasta abajo
        // (menos la leyenda y el margen de la tarjeta). Por debajo de 460 px se deja scroll para que se pueda leer.
        function calendarHeight() {
            const el = document.getElementById('calendar');
            if (!el) return 600;
            const top = el.getBoundingClientRect().top + window.scrollY;
            const legend = document.querySelector('.cal-legend');
            const below = (legend ? legend.offsetHeight + 10 : 0) + 34;
            return Math.max(360, Math.floor(window.innerHeight - top - below));
        }

        function fitCalendarHeight() {
            // Panel lateral primero: su lista lleva scroll propio para no alargar la página
            const side = document.querySelector('.cal-side .card-body');
            if (side) {
                const sideTop = side.getBoundingClientRect().top + window.scrollY;
                side.style.maxHeight = Math.max(260, window.innerHeight - sideTop - 48) + 'px';
            }
            if (!calendar) return;
            let h = calendarHeight();
            calendar.setOption('height', h);
            // Lo que aún sobresale de la ventana (márgenes del layout) se descuenta del calendario
            const over = document.documentElement.scrollHeight - window.innerHeight;
            if (over > 0 && h > 360) {
                h = Math.max(360, h - over - 2);
                calendar.setOption('height', h);
            }
            // Filas bajas (portátiles): versión compacta del contenido del día para que no salga «+1 más»
            const calEl = document.getElementById('calendar');
            if (calEl) calEl.classList.toggle('cal-compact', (h - 90) / 6 < 88);
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

        function parseIsoDateToUtc(dateValue) {
            return new Date(`${dateValue}T00:00:00Z`);
        }

        function formatUtcDateToIso(dateObj) {
            return dateObj.toISOString().split('T')[0];
        }

        function resolveCalendarRange(colorData, specialColorData) {
            const today = new Date();
            const todayUtc = new Date(Date.UTC(today.getUTCFullYear(), today.getUTCMonth(), today.getUTCDate()));
            const dates = [];

            if (Array.isArray(colorData)) {
                colorData.forEach((item) => {
                    if (item?.date) {
                        dates.push(item.date);
                    }
                });
            }

            if (Array.isArray(specialColorData?.holidayRange)) {
                specialColorData.holidayRange.forEach((date) => {
                    if (date) {
                        dates.push(date);
                    }
                });
            }

            if (specialColorData?.intensiveWorkRange && typeof specialColorData.intensiveWorkRange === 'object') {
                Object.values(specialColorData.intensiveWorkRange).forEach((rangeDates) => {
                    if (!Array.isArray(rangeDates)) {
                        return;
                    }

                    rangeDates.forEach((date) => {
                        if (date) {
                            dates.push(date);
                        }
                    });
                });
            }

            if (!dates.length) {
                const firstDay = new Date(Date.UTC(todayUtc.getUTCFullYear(), todayUtc.getUTCMonth(), 1));
                const lastDay = new Date(Date.UTC(todayUtc.getUTCFullYear(), todayUtc.getUTCMonth() + 1, 0));
                return {
                    minDate: formatUtcDateToIso(firstDay),
                    maxDate: formatUtcDateToIso(lastDay)
                };
            }

            const sortedDates = [...dates].sort();
            return {
                minDate: sortedDates[0],
                maxDate: sortedDates[sortedDates.length - 1]
            };
        }

        function buildZeroHourColorData(expectedHours, minDate, maxDate) {
            if (!expectedHours || !minDate || !maxDate) {
                return [];
            }

            const startDate = parseIsoDateToUtc(minDate);
            const endDate = parseIsoDateToUtc(maxDate);
            const generatedData = [];

            for (let date = new Date(startDate); date <= endDate; date.setUTCDate(date.getUTCDate() + 1)) {
                const dayOfWeek = date.toLocaleDateString('en-US', {
                    weekday: 'long',
                    timeZone: 'UTC'
                }).toLowerCase();

                if (expectedHours[dayOfWeek] === null || expectedHours[dayOfWeek] === undefined) {
                    continue;
                }

                generatedData.push({
                    dayOfWeek: dayOfWeek.charAt(0).toUpperCase() + dayOfWeek.slice(1),
                    date: formatUtcDateToIso(date),
                    hours: '00:00',
                    color: '#e06c71'
                });
            }

            return generatedData;
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
                    const expectedHours = data?.expectedHours && typeof data.expectedHours === 'object' ? data
                        .expectedHours : null;
                    const sourceColorData = Array.isArray(data?.colorData) ? data.colorData : [];

                    specialDaysState = {
                        holidayRange: data?.specialColorData?.holidayRange && Array.isArray(data.specialColorData
                            .holidayRange) ? [...data.specialColorData.holidayRange] : [],
                        intensiveWorkRange: data?.specialColorData?.intensiveWorkRange && typeof data
                            .specialColorData.intensiveWorkRange === 'object' ? {
                                ...data.specialColorData.intensiveWorkRange
                            } : {}
                    };

                    const calendarRange = resolveCalendarRange(sourceColorData, specialDaysState);
                    const effectiveColorData = sourceColorData.length ? sourceColorData : buildZeroHourColorData(
                        expectedHours,
                        calendarRange.minDate,
                        calendarRange.maxDate
                    );

                    expectedHoursState = expectedHours;
                    dayInfo = {};
                    sourceColorData.forEach((item) => {
                        if (item?.date) dayInfo[item.date] = toMin(item.hours);
                    });

                    imputedDatesState = new Set(Array.isArray(effectiveColorData) ? effectiveColorData
                        .filter(item => hasImputedHours(item.hours))
                        .map(item => item.date) : []);

                    if (expectedHours) {
                        const nonWorkingDays = Object.keys(expectedHours).filter(day => expectedHours[
                            day] === null);

                        const nonWorkingEvents = [];
                        const nonWorkingDateValues = [];

                        // Generar non-working days en el rango efectivo del calendario.
                        if (calendarRange.minDate && calendarRange.maxDate) {
                            const startDate = parseIsoDateToUtc(calendarRange.minDate);
                            const endDate = parseIsoDateToUtc(calendarRange.maxDate);

                            for (let date = new Date(startDate); date <= endDate; date.setUTCDate(date
                                    .getUTCDate() + 1)) {
                                const dayOfWeek = date.toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    timeZone: 'UTC'
                                }).toLowerCase();
                                if (nonWorkingDays.includes(dayOfWeek)) {
                                    const nonWorkingDate = formatUtcDateToIso(date);
                                    nonWorkingDateValues.push(nonWorkingDate);
                                    nonWorkingEvents.push({
                                        title: '{{ __('Non-working day') }}',
                                        start: nonWorkingDate,
                                        allDay: true,
                                        classNames: ['cal-ev'],
                                        kind: 'off'
                                    });
                                }
                            }
                        }

                        let events = effectiveColorData
                            .filter(item => {
                                if (data.specialColorData?.holidayRange && Array.isArray(data
                                        .specialColorData.holidayRange) && data.specialColorData
                                    .holidayRange.includes(item.date)) {
                                    return item.hours !== '00:00';
                                }
                                return true;
                            })
                            .map(item => ({
                                title: item.hours || '',
                                start: item.date,
                                allDay: true,
                                classNames: ['cal-ev'],
                                kind: 'hours',
                                date: item.date
                            }));

                        if (data.specialColorData && data.specialColorData.holidayRange) {
                            data.specialColorData.holidayRange.forEach(day => {
                                events.push({
                                    title: '{{ __('Holiday') }}',
                                    start: day,
                                    allDay: true,
                                    classNames: ['cal-ev'],
                                    kind: 'holiday',
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
                                        title: `${hours}`,
                                        start: day,
                                        allDay: true,
                                        classNames: ['cal-ev'],
                                        kind: 'intensive',
                                        hours: hours,
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
                document.querySelectorAll('#calendar .fc-daygrid-day').forEach((el) => {
                    el.classList.remove('cal-st--none', 'cal-st--off', 'cal-st--holiday');
                });
                calendar.removeAllEvents();
                calendar.addEventSource(events);
                // Función nueva para que FullCalendar vuelva a pintar los totales semanales con los datos nuevos
                calendar.setOption('weekNumberContent', (arg) => weekNumberContent(arg));
                updateMonthSummary();
                refreshSpecialDayButtonsState();
                document.getElementById('loader-overlay').style.display = 'none';
                return;
            }

            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: locale,
                initialView: 'dayGridMonth',
                firstDay: 1,
                height: calendarHeight(),
                // Si algún día no cabe (p. ej. festivo + intensiva), se resume en «+1» en vez de crecer la fila
                dayMaxEvents: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                buttonText: {
                    today: "{{ trans('messages.today') }}"
                },
                events: events,
                weekNumbers: true,
                weekNumberContent: weekNumberContent,
                datesSet: function() {
                    updateMonthSummary();
                },
                // Cada día: horas / jornada con barra; no laborable, festivo e intensiva como etiqueta
                eventContent: function(arg) {
                    const p = arg.event.extendedProps;
                    const iso = arg.event.startStr;
                    if (p.kind === 'off') {
                        return { html: `<div class="cal-off">${calT.nonWorking}</div>` };
                    }
                    if (p.type === 'holiday') {
                        return { html: `<span class="cal-chip cal-chip--holiday"><i class="fa-solid fa-umbrella-beach"></i>${calT.holiday}<span class="cal-ev-del" title="${calT.remove}">&times;</span></span>` };
                    }
                    if (p.type === 'intensive_work') {
                        return { html: `<span class="cal-chip cal-chip--intensive"><i class="fa-regular fa-clock"></i>${calT.intensive} ${calEsc(String(p.hours || '').slice(0, 5))}<span class="cal-ev-del" title="${calT.remove}">&times;</span></span>` };
                    }
                    const min = dayInfo[iso] || 0;
                    const exp = expectedMinFor(iso);
                    const state = dayState(iso);
                    const pct = state === 'none' ? 100 : (exp ? Math.min(100, Math.round(min * 100 / exp)) : 0);
                    const warn = min > 12 * 60 ? `<i class="fa-solid fa-triangle-exclamation cal-h-warn" title="${calT.tooMany}"></i>` : '';
                    return {
                        html: `<div class="cal-h"><span class="cal-h-val cal-st-text--${state}"><b>${fmtMin(min)}</b>${exp ? ' / ' + fmtMin(exp) : ''}</span>${warn}` +
                            `<div class="cal-h-bar"><i class="cal-bg--${state}" style="width:${pct}%"></i></div></div>`
                    };
                },
                dateClick: function(info) {
                    if (isMultiSelectMode) {
                        toggleDateInMultiSelection(info.dateStr);
                        return;
                    }

                    // Al hacer clic en un día: marcarlo y cargar su detalle
                    selectDay(info.dateStr);
                    loadTasksByDate(info.dateStr);
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
                        if (isMultiSelectMode) {
                            toggleDateInMultiSelection(info.event.startStr);
                            return;
                        }
                        // Cargar el detalle de ese día
                        selectDay(info.event.startStr);
                        loadTasksByDate(info.event.startStr);
                    }
                },
                eventDidMount: function(info) {
                    const p = info.event.extendedProps;
                    const cell = info.el.closest('.fc-daygrid-day');

                    // Tono de la celda según el estado del día
                    if (cell) {
                        if (p.kind === 'off') cell.classList.add('cal-st--off');
                        else if (p.type === 'holiday') cell.classList.add('cal-st--holiday');
                        else if (p.kind === 'hours' && dayState(info.event.startStr) === 'none') cell.classList.add('cal-st--none');
                    }

                    if (p.type === 'holiday' || p.type === 'intensive_work') {
                        const deleteBtn = info.el.querySelector('.cal-ev-del');
                        if (deleteBtn) {
                            deleteBtn.addEventListener('click', function(e) {
                                e.stopPropagation();
                                showDeleteModal(info.event);
                            });
                        }
                    }
                }
            });

            calendar.render();
            fitCalendarHeight();
            updateCalendarEditModeUI();
            refreshSpecialDayButtonsState();
            refreshMultiSelectedDayCells();
            window.dispatchEvent(new Event('resize'));
            //reactivar scroll
            document.body.style.overflow = 'auto';
            //esconder el loader
            document.getElementById('loader-overlay').style.display = 'none';

            // Seleccionar el día de hoy por defecto
            const today = todayIso();
            selectDay(today);
            loadTasksByDate(today);
            updateMonthSummary();

            // Add Workspace Select Dropdown event listener
            $('#workspace-select').on('change', function() {
                // Show loader
                document.getElementById('loader-overlay').style.display = 'flex';

                getCalendarInfo();
                const selectedCell = document.querySelector('#calendar .cal-selected');
                const selectedDate = selectedCell ? selectedCell.getAttribute('data-date') : todayIso();
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
                    // --- cabecera: fecha, anillo con horas / jornada y estado del día ---
                    $('#selected-date').text(formatLongDate(date));
                    const worked = toMin(response.formattedTotalHours);
                    const exp = expectedMinFor(date);
                    const state = dayState(date);
                    const ringColors = { none: '#e06c71', partial: '#e8c800', complete: '#5cc45a', overtime: '#63b6dc', holiday: '#91DDCF', off: '#d1d5db' };
                    const pct = exp ? Math.min(100, Math.round(worked * 100 / exp)) : (worked ? 100 : 0);
                    $('#cal-day').prop('hidden', false);
                    $('#cal-day-ring').attr('stroke', ringColors[state] || '#d1d5db').attr('stroke-dasharray', pct + ' 100');
                    $('#total-hours').text(fmtMin(worked));
                    $('#cal-day-expected').text(exp ? '/ ' + fmtMin(exp) : '');
                    let stateText = '';
                    if (state === 'holiday') stateText = calT.holiday;
                    else if (state === 'off') stateText = date > todayIso() ? calT.future : calT.nonWorking;
                    else if (state === 'none') stateText = calT.noHours;
                    else if (state === 'partial') stateText = calT.missing.replace(':h', fmtMin(exp - worked)) + ' · ' + calT.partial;
                    else if (state === 'complete') stateText = calT.complete;
                    else stateText = '+' + fmtMin(worked - exp) + ' ' + calT.overtimeLower;
                    $('#cal-day-state').text(stateText);
                    const logBtn = $('#cal-day-log');
                    if (logBtn.length) {
                        const canLog = timesheetUrl && date <= todayIso() && state !== 'holiday';
                        logBtn.prop('hidden', !canLog).attr('href', canLog ? timesheetUrl + '?date=' + date : '#');
                    }

                    // --- tareas del día agrupadas por proyecto ---
                    const taskList = $('#task-list');
                    taskList.empty();
                    const filteredTasks = response.tasks ? response.tasks.filter(task => task.totalTime !== '00:00') : [];

                    if (!filteredTasks.length) {
                        taskList.html(`<div class="cal-empty">${calT.noTasks}</div>`);
                        return;
                    }

                    const projectsMap = {};
                    filteredTasks.forEach(function(task) {
                        // Clave con el espacio de trabajo para no mezclar proyectos con el mismo nombre
                        const key = mode === 'all' ? `${task.projectName}__${task.workspaceName}` : task.projectName;
                        if (!projectsMap[key]) {
                            projectsMap[key] = { projectName: task.projectName, workspaceName: task.workspaceName, tasks: [] };
                        }
                        projectsMap[key].tasks.push(task);
                    });

                    let html = '';
                    Object.values(projectsMap).forEach(function(group) {
                        html += `<div class="cal-proj">${calEsc(group.projectName)}` +
                            (mode === 'all' && group.workspaceName ? `<small>${calEsc(group.workspaceName)}</small>` : '') + `</div>`;
                        group.tasks.forEach(function(task) {
                            html += `<div class="cal-task"><span class="cal-task-ms">${calEsc(task.milestoneTitle)}</span>` +
                                `<span class="cal-task-time">${fmtMin(toMin(task.totalTime))}</span>` +
                                `<span class="cal-task-name">${calEsc(task.taskTitle)}</span></div>`;
                        });
                    });
                    taskList.html(html);
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
