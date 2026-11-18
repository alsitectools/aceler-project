@extends('layouts.admin')

@section('page-title')
    {{ __('User Profile') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"> {{ __('User Profile') }}</li>
    @include('users.partials.account_help')
@endsection
@php

    $logo = 'storage/app/public/';
@endphp

@section('content')
    @include('loader.loader')
    @include('saver.saver')

    <style>
        /* styles for searchable workspace list */
        .workspace-search-wrapper .workspace-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .workspace-search-wrapper .workspace-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background-color: #d3d3d378;
            border-radius: 15px;
            font-size: 14px;
            border: 1px solid lightgray;
            font-weight: bold;
        }

        .workspace-search-wrapper .workspace-chip-close {
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }

        .swal-confirm-red {
            background-color: #AA182C !important;
            border-color: #AA182C !important;
        }

        .workspace-search-wrapper .workspace-list {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .workspace-search-wrapper .workspace-item {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .workspace-search-wrapper .workspace-item.selected {
            background-color: #aa182c3b;
            border-color: #aa182c;
        }

        .time-selector {
            position: relative;
            width: 74%;
            margin-top: 5%;
        }

        .time-selector-trigger {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            min-height: 38px;
            padding: 8px 34px 8px 12px;
            border: 1px solid #e5d8db;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(56, 20, 26, 0.06);
            color: #3f2b2f;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.06em;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .time-selector-trigger:hover {
            border-color: #cfb4ba;
            box-shadow: 0 4px 14px rgba(56, 20, 26, 0.08);
        }

        .time-selector.open .time-selector-trigger {
            border-color: #aa182c;
            box-shadow: 0 0 0 3px rgba(170, 24, 44, 0.12);
        }

        .time-selector-trigger:focus-visible {
            outline: none;
            border-color: #aa182c;
            box-shadow: 0 0 0 3px rgba(170, 24, 44, 0.14);
        }

        .time-selector.disabled .time-selector-trigger {
            background: #f1ecec;
            border-color: #e3d9db;
            box-shadow: none;
            color: #998b8e;
            cursor: not-allowed;
        }

        .time-selector-value {
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: none;
        }

        .time-selector-part {
            min-width: 24px;
            text-align: center;
        }

        .time-separator {
            color: #8e5b63;
            font-size: 14px;
        }

        .time-selector-chevron {
            position: absolute;
            right: 13px;
            top: 50%;
            width: 8px;
            height: 8px;
            border-right: 1.5px solid currentColor;
            border-bottom: 1.5px solid currentColor;
            transform: translateY(-65%) rotate(45deg);
            transition: transform 0.2s ease;
            pointer-events: none;
        }

        .time-selector.open .time-selector-chevron {
            transform: translateY(-35%) rotate(-135deg);
        }

        .time-selector-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 50%;
            z-index: 30;
            display: none;
            width: min(228px, calc(100vw - 24px));
            padding: 10px 8px;
            border: 1px solid #eadcdf;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 14px 28px rgba(41, 14, 19, 0.12);
            transform: translateX(-50%);
        }

        .time-selector.open .time-selector-dropdown {
            display: block;
        }

        .time-selector-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .time-selector-column {
            padding: 0 4px;
            border-radius: 0;
            background: transparent;
            border: none;
            min-width: 0;
        }

        .time-selector-heading {
            margin-bottom: 6px;
            color: #86636a;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-align: center;
            padding-left: 0;
        }

        .time-selector-options {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 220px;
            overflow-y: auto;
            padding: 1px 2px 1px 0;
        }

        .time-selector-options::-webkit-scrollbar {
            width: 4px;
        }

        .time-selector-options::-webkit-scrollbar-thumb {
            background: rgba(170, 24, 44, 0.22);
            border-radius: 999px;
        }

        .time-option {
            border: none;
            border-radius: 8px;
            /* background: #f8f5f5; */
            color: #52393d;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            width: 100%;
            min-height: 30px;
            padding: 5px 0px;
            /* box-shadow: inset 0 0 0 1px #efe3e5; */
            transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .time-option:hover {
            background: #f3e6e8;
            color: #7b0f20;
            box-shadow: inset 0 0 0 1px #ddc2c8;
        }

        .time-option.selected {
            background: #aa182c;
            color: white;
            box-shadow: none;
        }

        .time-selector.disabled .time-option {
            pointer-events: none;
        }

        @media screen and (max-width: 1200px) {
            .time-selector {
                width: 84%;
            }

            .time-selector-dropdown {
                width: min(220px, calc(100vw - 20px));
            }
        }

        .inputToggle {
            display: none;
        }
    </style>
    @php
        // Espacios agrupados por país (para el buscador) y los del usuario
        $uaMineIds = $anotherWorkspaces;
        // Una fila por país con varios espacios (los de más espacios primero); los países con un solo
        // espacio (o sin país) van juntos en «Otros países»
        $uaGroups = $workspaces->groupBy(fn($w) => trim((string) ($w->country ?? '')));
        $uaRows = $uaGroups->filter(fn($list, $country) => $country !== '' && $list->count() > 1)
            ->sortBy(fn($list, $country) => sprintf('%03d', 999 - $list->count()) . $country);
        $uaOthers = $uaGroups->reject(fn($list, $country) => $country !== '' && $list->count() > 1)
            ->flatten(1)->sortBy(fn($w) => mb_strtolower($w->display_name))->values();
        if ($uaOthers->isNotEmpty()) {
            $uaRows = $uaRows->put(__('Other countries'), $uaOthers);
        }
        $uaRole = $user->is_exporter ? __('Axapta exporter') : ($user->type === 'admin' ? __('Administrator') : __('User'));
        // Informes de errores («Reportar un problema»): solo soporte (users.is_support)
        $uaIsSupport = (bool) ($user->is_support ?? false);
        $uaReports = $uaIsSupport
            ? \App\Models\ProblemReport::with(['user:id,name,email', 'workspace:id,name', 'handler:id,name'])->orderByDesc('id')->limit(500)->get()
            : collect();
        $uaReportsNew = $uaReports->where('status', 'received')->count();
    @endphp
    <link rel="stylesheet" href="{{ asset('assets/css/user_account.css') }}?v={{ filemtime(public_path('assets/css/user_account.css')) }}">
    <div class="ua">
        {{-- ================= Menú lateral ================= --}}
        <aside class="ua-nav">
            <div class="ua-who">
                @include('projects.partials.pd_avatar', ['user' => $user, 'size' => 38])
                <div class="ua-who-text">
                    <b title="{{ $user->name }}">{{ $user->name }}</b>
                    <small>{{ $uaRole }}</small>
                </div>
            </div>
            <nav class="ua-nav-list" id="uaNav">
                <button type="button" class="ua-nav-item is-active" data-ua-target="workspaces">
                    <i class="fa-solid fa-layer-group"></i><span>{{ __('Workspaces') }}</span>
                    <b class="ua-badge" id="uaMineBadge">{{ count($uaMineIds) }}</b>
                </button>
                <button type="button" class="ua-nav-item" data-ua-target="timetable">
                    <i class="fa-regular fa-clock"></i><span>{{ __('Timetable') }}</span>
                    <b class="ua-badge" id="uaWeeklyBadge">—</b>
                </button>
                @if ($user->is_exporter)
                    <button type="button" class="ua-nav-item" data-ua-target="axapta">
                        <i class="fa-solid fa-file-export"></i><span>{{ __('Export to Axapta') }}</span>
                        <b class="ua-badge" id="uaPendingBadge" hidden></b>
                    </button>
                @endif
                @if ($uaIsSupport)
                    <button type="button" class="ua-nav-item" data-ua-target="reports">
                        <i class="fa-regular fa-flag"></i><span>{{ __('Error reports') }}</span>
                        <b class="ua-badge ua-badge-new" data-reports-new-count title="{{ __('New error reports') }}" @if (!$uaReportsNew) hidden @endif>{{ $uaReportsNew }}</b>
                    </button>
                @endif
            </nav>
        </aside>

        <div class="ua-main">
            {{-- ================= Espacios de trabajo ================= --}}
            <section class="ua-card ua-section is-active" id="v-pills-profile" data-ua-section="workspaces">
                <header class="ua-card-head">
                    <div>
                        <h3>{{ __('Workspaces') }}</h3>
                        <p>{{ __('The ones you can choose in the workspace selector of the side menu.') }}</p>
                    </div>
                </header>
                <div class="ua-card-body" id="workspace-content">
                    <div class="workspace-search-wrapper">
                        <div class="ua-sub">{{ __('My workspaces') }}</div>
                        <div class="workspace-chips ua-mine" id="workspaceChips">
                            @foreach ($workspaces as $workspace)
                                @if (in_array($workspace->id, $uaMineIds))
                                    <div class="workspace-chip" data-workspace-id="{{ $workspace->id }}" title="{{ $workspace->country }}">{{ $workspace->display_name }}@if ((int) $workspace->id === (int) $currentWorkspace->id) <span class="ua-current">{{ __('Current') }}</span>@endif <span class="workspace-chip-close" title="{{ __('Remove') }}">✕</span></div>
                                @endif
                            @endforeach
                        </div>

                        <div class="ua-sub ua-sub-add">{{ __('Add workspace') }}</div>
                        <label class="ua-search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="workspaceSearch" placeholder="{{ __('Search by name or country...') }}" autocomplete="off">
                        </label>
                        <div class="workspace-list ua-groups">
                            @foreach ($uaRows as $country => $list)
                                <div class="ua-group">
                                    <div class="ua-group-title">{{ $country }} <small class="ua-group-count"></small></div>
                                    <div class="ua-group-items">
                                        @foreach ($list as $workspace)
                                            @php
                                                // En «Otros países», el país en pequeño si el nombre del espacio no lo dice ya
                                                $uaHint = $loop->parent->last && $uaOthers->isNotEmpty() && $workspace->country
                                                    && mb_strtolower(trim($workspace->country)) !== mb_strtolower(trim($workspace->display_name))
                                                    ? $workspace->country : '';
                                            @endphp
                                            <div class="workspace-item"
                                                style="{{ in_array($workspace->id, $uaMineIds) ? 'display:none;' : '' }}"
                                                title="{{ $workspace->country }}"
                                                @if ($uaHint) data-hint="{{ $uaHint }}" @endif
                                                data-name="{{ mb_strtolower($workspace->name) }}|{{ mb_strtolower($workspace->display_name) }}|{{ mb_strtolower((string) $workspace->country) }}"
                                                data-workspace-id="{{ $workspace->id }}"><i class="fa-solid fa-plus" aria-hidden="true"></i>{{ $workspace->display_name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <p class="ua-empty" id="uaNoResults" hidden>{{ __('No workspaces match the search.') }}</p>
                        </div>

                        {{-- Guardar a la izquierda (a la derecha lo tapaba el botón de CofrAI); activo solo con cambios --}}
                        <div class="ua-card-foot">
                            <button id="saveWorkspaces" class="ua-btn ua-btn-primary" disabled><i class="fa-solid fa-check"></i>{{ __('Save changes') }}</button>
                            <span class="ua-changes" id="uaWsChanges"></span>
                            <button type="button" class="ua-btn ua-btn-ghost" id="uaWsDiscard" hidden>{{ __('Discard') }}</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ================= Horario ================= --}}
            <section class="ua-card ua-section" id="v-pills-timetable" data-ua-section="timetable">
                <header class="ua-card-head">
                    <div>
                        <h3>{{ __('Weekly timetable') }}</h3>
                        <p>{{ __('Your days in the timesheet are coloured according to it.') }}</p>
                    </div>
                    <div class="ua-card-tools">
                        <button type="button" class="ua-btn" id="uaCopyMonday" title="{{ __('Copy Monday hours to the other active days') }}">
                            <i class="fa-regular fa-copy"></i>{{ __('Copy Monday to active days') }}
                        </button>
                        <button id="saveTimetable" class="ua-btn ua-btn-primary">{{ __('Save') }}</button>
                    </div>
                </header>
                <div class="ua-alert" id="timetableErrorMessage" style="display: none;">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>{{ __('Make sure you have a day selected and the hours imputed correctly.') }}</span>
                </div>
                <div class="ua-card-body">
                    <div class="timetable-content ua-days" id="timetable-content">
                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="dayToggle">
                        <p>{{ __($day) }}</p>
                        <label class="switch">
                            <input type="checkbox" class="dayCheckbox" id="{{ strtolower($day) }}Checkbox">
                            <span class="slider round"></span>
                        </label>
                        <div class="time-selector disabled" data-selected-hour="" data-selected-minute="">
                            <button type="button" class="time-selector-trigger" aria-haspopup="true" aria-expanded="false">
                                <span class="time-selector-value">
                                    <span class="time-selector-part time-display-hour">HH</span>
                                    <span class="time-separator">:</span>
                                    <span class="time-selector-part time-display-minute">MM</span>
                                </span>
                                <span class="time-selector-chevron"></span>
                            </button>
                            <div class="time-selector-dropdown">
                                <div class="time-selector-grid">
                                    <div class="time-selector-column">
                                        <div class="time-selector-heading">{{ __('Hours') }}</div>
                                        <div class="time-selector-options" data-time-type="hour">
                                            @for ($hour = 0; $hour < 24; $hour++)
                                                @php $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-option" data-time-type="hour"
                                                    data-value="{{ $formattedHour }}">
                                                    {{ $formattedHour }}
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="time-selector-column">
                                        <div class="time-selector-heading">{{ __('Minutes') }}</div>
                                        <div class="time-selector-options" data-time-type="minute">
                                            @for ($minute = 0; $minute < 60; $minute++)
                                                @php $formattedMinute = str_pad($minute, 2, '0', STR_PAD_LEFT); @endphp
                                                <button type="button" class="time-option" data-time-type="minute"
                                                    data-value="{{ $formattedMinute }}">
                                                    {{ $formattedMinute }}
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="ua-bar" aria-hidden="true"><span></span></span>
                        <input id="{{ strtolower($day) }}Input" type="hidden" class="inputToggle">
                    </div>
                @endforeach
                    </div>
                    <div class="ua-total">
                        <span>{{ __('Weekly total') }}</span>
                        <b id="uaWeeklyTotal">—</b>
                    </div>

                    <div class="ua-sub ua-sub-add">{{ __('Exceptions') }}</div>
                    <div class="ua-exceptions">
                        @forelse ($intensiveSummary as $period)
                            <span class="ua-chip is-red"><i class="fa-solid fa-sun"></i>{{ __('Intensive workday') }} · {{ \Carbon\Carbon::parse($period['from'])->format('d/m') }}–{{ \Carbon\Carbon::parse($period['to'])->format('d/m') }} · {{ $period['hours'] }} h</span>
                        @empty
                            <span class="ua-chip"><i class="fa-solid fa-sun"></i>{{ __('No intensive workday') }}</span>
                        @endforelse
                        <span class="ua-chip"><i class="fa-solid fa-umbrella-beach"></i>{{ trans_choice(':count holiday|:count holidays', $holidayCount, ['count' => $holidayCount]) }}</span>
                        <a class="ua-chip is-link" href="{{ route('calender.index', $currentWorkspace->slug) }}"><i class="fa-regular fa-calendar"></i>{{ __('Manage in my calendar') }}</a>
                    </div>
                </div>
            </section>

            {{-- ================= Exportar a Axapta ================= --}}
            @if ($user->is_exporter)
                <section class="ua-card ua-section" id="v-pills-axapta" data-ua-section="axapta">
                    <header class="ua-card-head">
                        <div>
                            <h3>{{ __('Export hours to Axapta') }}</h3>
                            <p>{{ __('Finished order forms of Jobsite projects from all delegations.') }}</p>
                        </div>
                        <div class="ua-card-tools">
                            <button id="exportAxapta" class="ua-btn ua-btn-primary">{{ __('Export') }}</button>
                        </div>
                    </header>
                    <div class="ua-card-body" id="axapta-content">
                        <div class="ua-kpis">
                            <div class="ua-kpi">
                                <small>{{ __('Pending to send') }}</small>
                                <div><b id="uaPendingLines">…</b> <span>{{ __('lines') }}</span> · <span id="uaPendingHours">…</span> h</div>
                            </div>
                            <div class="ua-kpi is-warn" id="uaSkippedKpi">
                                <small>{{ __('Cannot be exported') }}</small>
                                <div><b id="uaSkippedCount">…</b> <span>{{ __('order forms') }}</span></div>
                            </div>
                        </div>
                        <div class="ua-issues" id="uaIssues" hidden>
                            <div class="ua-issues-head">
                                <b>{{ __('Check before exporting') }}</b>
                                <button type="button" class="ua-link" id="uaIssuesToggle" data-show="{{ __('See list') }}" data-hide="{{ __('Hide list') }}">{{ __('See list') }}</button>
                            </div>
                            <ul class="ua-issues-list" id="uaIssuesList" hidden></ul>
                        </div>

                        <div class="ua-sub ua-sub-add">{{ __('Latest batches') }}</div>
                        <table class="ua-table">
                            <thead>
                                <tr><th>{{ __('Date') }}</th><th>{{ __('Lines') }}</th><th>{{ __('Hours') }}</th><th>{{ __('Status') }}</th><th class="ua-th-file">{{ __('File') }}</th></tr>
                            </thead>
                            <tbody id="uaBatches">
                                <tr><td colspan="5" class="ua-muted">{{ __('Loading...') }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- ================= Informes de errores (solo soporte) ================= --}}
            @if ($uaIsSupport)
                <section class="ua-card ua-section" id="v-pills-reports" data-ua-section="reports">
                    <header class="ua-card-head">
                        <div>
                            <h3>{{ __('Error reports') }}</h3>
                            <p>{{ __('Problems reported by users with «Report a problem». Open one to see the details, change its status and answer.') }}</p>
                        </div>
                    </header>
                    <div class="ua-card-body">
                        @include('problem_reports.partials.inbox', ['reports' => $uaReports])
                    </div>
                </section>
            @endif
        </div>
    </div>
    @endsection
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript">
            function toggleErrorMessage(show) {
                if (show) {
                    $('#timetableErrorMessage').show();
                } else {
                    $('#timetableErrorMessage').hide();
                }
            }

            function getTimeSelector(dayToggle) {
                return dayToggle.querySelector('.time-selector');
            }

            function getTimeInput(dayToggle) {
                return dayToggle.querySelector('.inputToggle');
            }

            function getTimeTrigger(dayToggle) {
                return dayToggle.querySelector('.time-selector-trigger');
            }

            function getTimeDisplay(dayToggle, type) {
                return dayToggle.querySelector(`.time-display-${type}`);
            }

            function closeAllTimeSelectors(exceptSelector) {
                document.querySelectorAll('.time-selector.open').forEach(function(selector) {
                    if (selector !== exceptSelector) {
                        selector.classList.remove('open');
                        const trigger = selector.querySelector('.time-selector-trigger');
                        if (trigger) {
                            trigger.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
            }

            function updateTimeSelectorDisplay(dayToggle) {
                const timeSelector = getTimeSelector(dayToggle);
                const selectedHour = timeSelector.dataset.selectedHour || '';
                const selectedMinute = timeSelector.dataset.selectedMinute || '';

                getTimeDisplay(dayToggle, 'hour').textContent = selectedHour || 'HH';
                getTimeDisplay(dayToggle, 'minute').textContent = selectedMinute || 'MM';
            }

            function updateTimeOptionSelection(dayToggle) {
                const timeSelector = getTimeSelector(dayToggle);
                const selectedHour = timeSelector.dataset.selectedHour || '';
                const selectedMinute = timeSelector.dataset.selectedMinute || '';

                timeSelector.querySelectorAll('.time-option').forEach(function(option) {
                    const type = option.dataset.timeType;
                    const value = option.dataset.value;
                    const isSelected = (type === 'hour' && value === selectedHour) ||
                        (type === 'minute' && value === selectedMinute);

                    option.classList.toggle('selected', isSelected);
                });
            }

            function scrollSelectedTimeOptionsIntoView(dayToggle) {
                const timeSelector = getTimeSelector(dayToggle);

                timeSelector.querySelectorAll('.time-option.selected').forEach(function(option) {
                    option.scrollIntoView({
                        block: 'center'
                    });
                });
            }

            function normalizeTimeValue(value) {
                if (!value) {
                    return '';
                }

                const parts = value.split(':');
                if (parts.length < 2) {
                    return '';
                }

                return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
            }

            function syncTimeSelectorFromValue(dayToggle, value) {
                const normalizedValue = normalizeTimeValue(value);
                const timeSelector = getTimeSelector(dayToggle);
                const timeInput = getTimeInput(dayToggle);

                if (!normalizedValue) {
                    timeSelector.dataset.selectedHour = '';
                    timeSelector.dataset.selectedMinute = '';
                    timeInput.value = '';
                    updateTimeSelectorDisplay(dayToggle);
                    updateTimeOptionSelection(dayToggle);
                    return;
                }

                const [hour, minute] = normalizedValue.split(':');
                timeSelector.dataset.selectedHour = hour;
                timeSelector.dataset.selectedMinute = minute;
                timeInput.value = normalizedValue;
                updateTimeSelectorDisplay(dayToggle);
                updateTimeOptionSelection(dayToggle);
            }

            function syncTimeInputFromSelector(dayToggle) {
                const timeSelector = getTimeSelector(dayToggle);
                const selectedHour = timeSelector.dataset.selectedHour || '';
                const selectedMinute = timeSelector.dataset.selectedMinute || '';
                const timeInput = getTimeInput(dayToggle);

                if (!selectedHour || !selectedMinute) {
                    timeInput.value = '';
                    updateTimeSelectorDisplay(dayToggle);
                    updateTimeOptionSelection(dayToggle);
                    return;
                }

                timeInput.value = `${selectedHour}:${selectedMinute}`;
                updateTimeSelectorDisplay(dayToggle);
                updateTimeOptionSelection(dayToggle);
            }

            function setTimeSelectorState(dayToggle, enabled) {
                const timeSelector = getTimeSelector(dayToggle);
                const timeInput = getTimeInput(dayToggle);
                const trigger = getTimeTrigger(dayToggle);

                trigger.disabled = !enabled;
                timeInput.disabled = !enabled;
                timeSelector.classList.toggle('disabled', !enabled);

                if (!enabled) {
                    timeSelector.classList.remove('open');
                    trigger.setAttribute('aria-expanded', 'false');
                    syncTimeSelectorFromValue(dayToggle, '');
                }
            }

            let days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            //onload get user timetable info
            window.onload = function() {
                document.body.style.overflow = 'hidden';

                operationUrl = '<?php echo url('user/get-timetable'); ?>';
                $.ajax({
                    type: 'GET',
                    url: operationUrl,
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                    },
                    success: function(data) {
                        console.log("success");

                        // Reactivar scroll
                        document.body.style.overflow = 'auto';
                        // Esconder el loader
                        document.getElementById('loader-overlay').style.display = 'none';

                        // Eliminar id y user_id de los datos recibidos
                        delete data[0].id;
                        delete data[0].user_id;

                        console.log("actual data", data);

                        // Contador de checkboxes activos
                        let checkedCount = 0;

                        // Iterar sobre los días y establecer valores en los inputs
                        days.forEach(day => {
                            let value = data[0][day]; // Obtener el valor del día actual
                            let dayToggle = document.getElementById(`${day}Input`).closest(
                                '.dayToggle');
                            let toggle = dayToggle.querySelector('.dayCheckbox');

                            if (value) {
                                syncTimeSelectorFromValue(dayToggle, value);
                                toggle.checked = true;
                                setTimeSelectorState(dayToggle, true);
                                checkedCount++;
                            } else {
                                setTimeSelectorState(dayToggle, false);
                            }
                        });

                        // Si solo hay un checkbox marcado, deshabilitarlo junto con su inputToggle
                        if (checkedCount < 1) {
                            let lastChecked = $('.dayCheckbox:checked').closest('.dayToggle');
                            lastChecked.find('.dayCheckbox').prop('disabled', true);
                            lastChecked.each(function() {
                                setTimeSelectorState(this, false);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    }
                });
            };

            let saveButton = document.getElementById('saveTimetable');
            if (saveButton) {
                saveButton.addEventListener('click', function() {
                    // Mostrar el loader cuando el usuario guarda
                    document.getElementById('saving-overlay').style.display = 'flex';

                    // Deshabilitar el scroll
                    document.body.style.overflow = 'hidden';

                    // Recoger los valores de los inputs
                    let inputs = document.querySelectorAll(".inputToggle");
                    let schedule = {}; // Diccionario para almacenar los valores de horarios

                    inputs.forEach(input => {
                        let day = input.id.replace("Input", "").toLowerCase();
                        let dayToggle = input.closest('.dayToggle');
                        let checkbox = dayToggle.querySelector('.dayCheckbox');
                        let normalizedValue = normalizeTimeValue(input.value);
                        let value = normalizedValue && normalizedValue !== '00:00' ? normalizedValue : null;

                        if (checkbox.checked && value !== null) {
                            schedule[day] = value;
                        }
                    });


                    if (Object.keys(schedule).length < 1) {

                        toggleErrorMessage(true);

                        document.getElementById('saving-overlay').style.display = 'none';
                        document.body.style.overflow = 'auto';

                        return;
                    }

                    // Llamada AJAX para actualizar el horario
                    operationUrl = '<?php echo url('user/update-timetable'); ?>';
                    $.ajax({
                        type: 'POST',
                        url: operationUrl,
                        data: {
                            "inputHours": JSON.stringify(schedule),
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') // Incluye el token CSRF
                        },
                        success: function(data) {
                            console.log("success");
                            window.location.reload();
                        },
                        fail: function() {
                            console.log("fail");
                            document.getElementById('saving-overlay').style.display = 'none';
                            document.body.style.overflow = 'auto';
                        },
                    });
                });
            }


            $(document).ready(function() {
                function checkAtLeastOneCheckbox() {
                    let isChecked = false;
                    $('.dayCheckbox').each(function() {
                        if ($(this).is(':checked')) {
                            isChecked = true;
                            return false;
                        }
                    });
                    return isChecked;
                }

                $('.dayToggle .switch input[type="checkbox"]').on('change', function() {
                    var dayToggle = $(this).closest('.dayToggle').get(0);

                    // Si el checkbox está marcado
                    if ($(this).is(':checked')) {
                        setTimeSelectorState(dayToggle, true);
                    } else {
                        setTimeSelectorState(dayToggle, false);

                        // Verifica si al menos un checkbox está marcado
                        if ($('.dayToggle .switch input[type="checkbox"]:checked').length < 1) {
                            toggleErrorMessage(true);
                        } else {
                            toggleErrorMessage(false);
                        }
                    }
                });


                $('.toggle-section').on('click', function() {
                    var target = $(this).data('target');
                    var saveButton = $('#saveTimetable');
                    var saveButtonHoliday = $('#saveHoliday');

                    $(target).slideToggle(function() {
                        if (target === "#timetable-content") {
                            if ($(target).is(':visible')) {
                                $(target).css({
                                    display: 'flex',
                                    justifyContent: 'space-evenly'
                                });
                                saveButton.show();
                            } else {
                                $(target).css({
                                    display: 'none'
                                });
                                saveButton.hide();
                            }
                        } else if (target == "#Holiday-content") {
                            if ($(target).is(':visible')) {
                                saveButtonHoliday.show();
                            } else {
                                saveButtonHoliday.hide();
                            }
                        }
                    });

                    var currentSymbol = $(this).text();
                    $(this).text(currentSymbol === '-' ? '+' : '-');
                });

                $('.dayToggle').each(function() {
                    setTimeSelectorState(this, false);
                });

                $('.time-selector-trigger').on('click', function(event) {
                    event.stopPropagation();

                    const dayToggle = $(this).closest('.dayToggle').get(0);
                    const timeSelector = $(this).closest('.time-selector').get(0);
                    if (!timeSelector || timeSelector.classList.contains('disabled')) {
                        return;
                    }

                    const isOpen = timeSelector.classList.contains('open');
                    closeAllTimeSelectors(timeSelector);
                    timeSelector.classList.toggle('open', !isOpen);
                    this.setAttribute('aria-expanded', String(!isOpen));

                    if (!isOpen && dayToggle) {
                        scrollSelectedTimeOptionsIntoView(dayToggle);
                    }
                });

                $('.time-option').on('click', function(event) {
                    event.stopPropagation();

                    const dayToggle = $(this).closest('.dayToggle').get(0);
                    const timeSelector = getTimeSelector(dayToggle);
                    const type = this.dataset.timeType;
                    const value = this.dataset.value;

                    if (type === 'hour') {
                        timeSelector.dataset.selectedHour = value;
                    } else {
                        timeSelector.dataset.selectedMinute = value;
                    }

                    syncTimeInputFromSelector(dayToggle);
                    toggleErrorMessage(false);

                    if (timeSelector.dataset.selectedHour && timeSelector.dataset.selectedMinute) {
                        timeSelector.classList.remove('open');
                        getTimeTrigger(dayToggle).setAttribute('aria-expanded', 'false');
                    }
                });

                $(document).on('click', function() {
                    closeAllTimeSelectors();
                });

                $('.HolidayToggleDiv .switch input[type="checkbox"]').on('change', function() {
                    var intensiveWorkInput = $('#intesiveWordaykInput');

                    if ($(this).is(':checked')) {
                        intensiveWorkInput.prop('disabled', false);
                        intensiveWorkInput.css({
                            'background-color': 'white',
                            '-webkit-box-shadow': 'rgb(0 0 0 / 20%) 0px 4px 10px 0px'
                        });
                    } else {
                        intensiveWorkInput.prop('disabled', true).val('');
                        intensiveWorkInput.css({
                            'background-color': '#E4DEDE',
                            '-webkit-box-shadow': 'rgb(0 0 0 / 10%) 0px 4px 10px 0px'
                        });
                    }
                });
            });

            $('#avatar').change(function() {

                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#myAvatar').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            function workspaceManager(workspaceId) {
                let isChecked = document.getElementById(`workspaceCheckbox-${workspaceId}`).checked;

                let url = isChecked ?
                    '{{ route('addWorkspace', ':id') }}'.replace(':id', workspaceId) :
                    '{{ route('leave-workspace', ':id') }}'.replace(':id', workspaceId);

                let method = isChecked ? 'GET' : 'DELETE';

                $.ajax({
                    url: url,
                    type: method,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response) {
                        location.reload();

                    },
                    error: function(response) {
                        location.reload();

                    }
                });
            }

            // search/filter behavior and chip-based selection for workspace list
            document.addEventListener('DOMContentLoaded', function() {
                const workspaceSection = document.querySelector('#workspace-content .workspace-search-wrapper');
                if (!workspaceSection) {
                    return;
                }

                const searchInput = workspaceSection.querySelector('#workspaceSearch');
                const chipsContainer = workspaceSection.querySelector('#workspaceChips');
                const saveBtn = workspaceSection.querySelector('#saveWorkspaces');
                const workspaceItems = workspaceSection.querySelectorAll('.workspace-item');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                // Initialize selected workspace IDs from existing chips
                let selectedWorkspaceIds = new Set();
                workspaceSection.querySelectorAll('.workspace-chip').forEach(function(chip) {
                    selectedWorkspaceIds.add(parseInt(chip.dataset.workspaceId));
                });
                const initialWorkspaceIds = new Set(selectedWorkspaceIds);

                function renderWorkspaceList() {
                    const filter = (searchInput?.value || '').toLowerCase();

                    workspaceItems.forEach(function(item) {
                        const workspaceId = parseInt(item.dataset.workspaceId);
                        const workspaceName = (item.dataset.name || item.textContent || '').trim()
                            .toLowerCase();
                        const matchesFilter = workspaceName.split('|').some(n => n.includes(filter));
                        const isSelected = selectedWorkspaceIds.has(workspaceId);

                        item.style.display = !isSelected && matchesFilter ? '' : 'none';
                    });
                }

                function createChip(workspaceId, workspaceName) {
                    const chip = document.createElement('div');
                    chip.className = 'workspace-chip';
                    chip.dataset.workspaceId = workspaceId;
                    chip.innerHTML = workspaceName + ' <span class="workspace-chip-close">✕</span>';
                    chipsContainer.appendChild(chip);

                    chip.querySelector('.workspace-chip-close').addEventListener('click', function(e) {
                        e.stopPropagation();
                        removeChip(chip, workspaceId);
                    });
                }

                // Filter list by search input
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        renderWorkspaceList();
                    });
                }

                // Handle workspace item click - add chip and hide item
                workspaceItems.forEach(function(item) {
                    item.addEventListener('click', function() {
                        const workspaceId = parseInt(this.dataset.workspaceId);
                        const workspaceName = this.textContent.trim();

                        if (selectedWorkspaceIds.has(workspaceId)) {
                            return;
                        }

                        selectedWorkspaceIds.add(workspaceId);
                        createChip(workspaceId, workspaceName);
                        renderWorkspaceList();
                    });
                });

                // Handle chip close click
                function removeChip(chipElement, workspaceId) {
                    selectedWorkspaceIds.delete(workspaceId);
                    chipElement.remove();
                    renderWorkspaceList();
                }

                // Handle save button
                if (saveBtn) {
                    saveBtn.addEventListener('click', async function() {
                        let selectedIds = Array.from(selectedWorkspaceIds);
                        const initialIds = Array.from(initialWorkspaceIds);
                        const currentWorkspaceId = {{ (int) $currentWorkspace->id }};

                        if (selectedIds.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __('Cannot delete') }}',
                                text: '{{ __('You must have at least one workspace assigned. Changes will not be saved.') }}',
                                confirmButtonText: '{{ __('Got it') }}',
                                customClass: { confirmButton: 'swal-confirm-red' }
                            }).then(() => {
                                selectedWorkspaceIds = new Set(initialWorkspaceIds);
                                chipsContainer.innerHTML = '';
                                initialWorkspaceIds.forEach(id => {
                                    const item = [...workspaceItems].find(w =>
                                        parseInt(w.dataset.workspaceId) === id
                                    );
                                    if (item) createChip(id, item.textContent.trim());
                                });
                                renderWorkspaceList();
                            });
                            return;
                        }

                        const selectedIdSet = new Set(selectedIds);
                        const idsToAdd = selectedIds.filter(id => !initialWorkspaceIds.has(id));
                        const idsToRemove = initialIds.filter(id => !selectedIdSet.has(id));

                        saveBtn.disabled = true;
                        document.getElementById('saving-overlay').style.display = 'flex';
                        document.body.style.overflow = 'hidden';

                        const doAddWorkspace = function(workspaceId) {
                            return $.ajax({
                                url: '{{ route('addWorkspace', ':id') }}'.replace(':id',
                                    workspaceId),
                                type: 'GET',
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                                }
                            });
                        };

                        const doRemoveWorkspace = function(workspaceId) {
                            return $.ajax({
                                url: '{{ route('leave-workspace', ':id') }}'.replace(':id',
                                    workspaceId),
                                type: 'DELETE',
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                                }
                            });
                        };

                        try {
                            for (const workspaceId of idsToAdd) {
                                await doAddWorkspace(workspaceId);
                            }

                            if (idsToRemove.length > 0) {
                                const resp = await $.ajax({
                                    url: '{{ route('leave-workspace-batch') }}',
                                    type: 'POST',
                                    data: { ids: idsToRemove },
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                                    }
                                });
                                if (resp.error) {
                                    throw new Error(resp.error);
                                }
                            }
                        } catch (error) {
                            console.error('Error saving selected workspaces:', error);
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Error') }}',
                                text: error.message || '{{ __('An error occurred while saving.') }}',
                                confirmButtonText: '{{ __('Got it') }}',
                                customClass: { confirmButton: 'swal-confirm-red' }
                            });
                            saveBtn.disabled = false;
                            document.getElementById('saving-overlay').style.display = 'none';
                            document.body.style.overflow = '';
                            return;
                        } finally {
                            window.location.reload();
                        }
                    });
                }

                // Add event listeners to close buttons on existing chips
                workspaceSection.querySelectorAll('.workspace-chip-close').forEach(function(closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const chip = this.closest('.workspace-chip');
                        const workspaceId = parseInt(chip.dataset.workspaceId);
                        removeChip(chip, workspaceId);
                    });
                });

                renderWorkspaceList();
            });
        </script>
        <script>
            // ===== Perfil: secciones, grupos de espacios, total semanal y resumen de Axapta =====
            (function() {
                // --- Secciones del menú lateral (una visible a la vez; respeta los anclajes antiguos #v-pills-…) ---
                const navItems = document.querySelectorAll('#uaNav .ua-nav-item');
                const sections = document.querySelectorAll('.ua-section');
                const legacyHash = { 'v-pills-profile': 'workspaces', 'v-pills-timetable': 'timetable', 'v-pills-axapta': 'axapta' };

                function show(name) {
                    if (!document.querySelector('.ua-section[data-ua-section="' + name + '"]')) name = 'workspaces';
                    navItems.forEach((b) => b.classList.toggle('is-active', b.dataset.uaTarget === name));
                    sections.forEach((s) => s.classList.toggle('is-active', s.dataset.uaSection === name));
                    try { history.replaceState(null, '', '#' + name); } catch (e) {}
                    if (name === 'axapta' && window.uaLoadAxaptaPreview && !window.__uaAxaptaLoaded) window.uaLoadAxaptaPreview();
                }
                navItems.forEach((b) => b.addEventListener('click', () => show(b.dataset.uaTarget)));
                window.uaShowSection = show; // lo usa la visita guiada (users/partials/account_help)
                const hash = window.location.hash.replace('#', '');
                show(legacyHash[hash] || hash || 'workspaces');

                // --- Espacios: ocultar los países sin resultados y contar cambios sin guardar ---
                const wsWrap = document.querySelector('#workspace-content .workspace-search-wrapper');
                if (wsWrap) {
                    const chips = wsWrap.querySelector('#workspaceChips');
                    const initial = new Set(Array.from(chips.querySelectorAll('.workspace-chip')).map((c) => c.dataset.workspaceId));
                    const changes = document.getElementById('uaWsChanges');
                    const badge = document.getElementById('uaMineBadge');
                    const noResults = document.getElementById('uaNoResults');
                    const saveBtn = document.getElementById('saveWorkspaces');
                    const discard = document.getElementById('uaWsDiscard');
                    const newLabel = @json(__('New'));
                    const refresh = () => {
                        let anyVisible = false;
                        wsWrap.querySelectorAll('.ua-group').forEach((g) => {
                            const visibleCount = Array.from(g.querySelectorAll('.workspace-item')).filter((i) => i.style.display !== 'none').length;
                            g.hidden = visibleCount === 0;
                            const count = g.querySelector('.ua-group-count');
                            if (count) count.textContent = visibleCount;
                            anyVisible = anyVisible || visibleCount > 0;
                        });
                        if (noResults) noResults.hidden = anyVisible;
                        const chipEls = Array.from(chips.querySelectorAll('.workspace-chip'));
                        const now = new Set(chipEls.map((c) => c.dataset.workspaceId));
                        // Etiqueta «Nuevo» en los espacios añadidos que aún no se han guardado
                        chipEls.forEach((c) => {
                            if (initial.has(c.dataset.workspaceId) || c.querySelector('.ua-new')) return;
                            const tag = document.createElement('span');
                            tag.className = 'ua-new';
                            tag.textContent = newLabel;
                            c.insertBefore(tag, c.querySelector('.workspace-chip-close'));
                        });
                        const added = [...now].filter((id) => !initial.has(id)).length;
                        const removed = [...initial].filter((id) => !now.has(id)).length;
                        const dirty = added + removed > 0;
                        if (badge) badge.textContent = now.size;
                        if (changes) {
                            const parts = [];
                            if (added) parts.push(@json(__('Added')) + ': ' + added);
                            if (removed) parts.push(@json(__('Removed')) + ': ' + removed);
                            changes.textContent = parts.length ? parts.join(' · ') + ' — ' + @json(__('unsaved changes')) : '';
                        }
                        if (saveBtn && !document.body.classList.contains('ua-saving')) saveBtn.disabled = !dirty;
                        if (discard) discard.hidden = !dirty;
                    };
                    // Guardar desactiva el botón mientras guarda: que refresh() no lo vuelva a activar
                    if (saveBtn) saveBtn.addEventListener('click', () => document.body.classList.add('ua-saving'), true);
                    // Descartar: volver a lo guardado (la página se recarga con los espacios actuales)
                    if (discard) discard.addEventListener('click', () => window.location.reload());
                    // El script original añade y quita etiquetas y oculta elementos: se observa el resultado
                    // (solo etiquetas y lista: el pie, donde escribe refresh(), queda fuera para no crear un bucle)
                    const mo = new MutationObserver(() => setTimeout(refresh, 0));
                    mo.observe(chips, { childList: true });
                    const list = wsWrap.querySelector('.workspace-list');
                    if (list) mo.observe(list, { subtree: true, attributes: true, attributeFilter: ['style'] });
                    refresh();
                }

                // --- Horario: barras, total semanal y «copiar lunes» ---
                const days = document.getElementById('timetable-content');
                const toHours = (v) => { const m = /^(\d{1,2}):(\d{2})/.exec(v || ''); return m ? (+m[1]) + (+m[2]) / 60 : 0; };
                const fmt = (h) => { const t = Math.round(h * 60); return String(Math.floor(t / 60)).padStart(2, '0') + ':' + String(t % 60).padStart(2, '0'); };
                function refreshTimetable() {
                    if (!days) return;
                    let total = 0;
                    days.querySelectorAll('.dayToggle').forEach((d) => {
                        const on = d.querySelector('.dayCheckbox')?.checked;
                        const h = on ? toHours(d.querySelector('.inputToggle')?.value) : 0;
                        total += h;
                        d.classList.toggle('is-off', !on);
                        const bar = d.querySelector('.ua-bar > span');
                        if (bar) bar.style.width = Math.min(100, h / 12 * 100) + '%';
                    });
                    const label = total > 0 ? fmt(total) + ' h' : '—';
                    const t = document.getElementById('uaWeeklyTotal'); if (t) t.textContent = label;
                    const b = document.getElementById('uaWeeklyBadge'); if (b) b.textContent = total > 0 ? fmt(total).replace(/:00$/, '') + ' h' : '—';
                }
                if (days) {
                    // Los valores los pone el script original (carga por AJAX, selectores): se observa el texto mostrado
                    new MutationObserver(() => setTimeout(refreshTimetable, 0)).observe(days, { subtree: true, childList: true, characterData: true, attributes: true, attributeFilter: ['class'] });
                    days.addEventListener('change', () => setTimeout(refreshTimetable, 0));
                    refreshTimetable();
                }
                const copy = document.getElementById('uaCopyMonday');
                if (copy && days) {
                    copy.addEventListener('click', () => {
                        const monday = document.getElementById('mondayInput');
                        const value = monday && document.getElementById('mondayCheckbox')?.checked ? monday.value : '';
                        if (!value || typeof syncTimeSelectorFromValue !== 'function') return;
                        days.querySelectorAll('.dayToggle').forEach((d) => {
                            if (d.contains(monday) || !d.querySelector('.dayCheckbox')?.checked) return;
                            syncTimeSelectorFromValue(d, value);
                        });
                        refreshTimetable();
                    });
                }

                // --- Axapta: resumen antes de exportar (simulación) e historial de lotes ---
                const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
                window.uaLoadAxaptaPreview = function() {
                    const lines = document.getElementById('uaPendingLines');
                    if (!lines) return;
                    window.__uaAxaptaLoaded = true;
                    $.ajax({
                        url: @json(route('projects.export.axapta.preview')),
                        type: 'GET',
                        success: function(r) {
                            lines.textContent = r.pending_lines;
                            document.getElementById('uaPendingHours').textContent = String(r.pending_hours || '').replace(/:\d{2}$/, ''); // HH:MM
                            const skipped = r.skipped || [], warnings = r.warnings || [];
                            document.getElementById('uaSkippedCount').textContent = skipped.length;
                            document.getElementById('uaSkippedKpi').classList.toggle('is-ok', skipped.length === 0);
                            const badge = document.getElementById('uaPendingBadge');
                            if (badge) { badge.hidden = !r.pending_lines; badge.textContent = r.pending_lines; }
                            const issues = document.getElementById('uaIssues');
                            const list = document.getElementById('uaIssuesList');
                            issues.hidden = !(skipped.length || warnings.length);
                            list.innerHTML = skipped.map((s) => '<li class="is-skip">' + esc(s) + '</li>').join('') +
                                warnings.map((w) => '<li class="is-warn">' + esc(w) + '</li>').join('');
                            const body = document.getElementById('uaBatches');
                            body.innerHTML = (r.batches || []).length ? r.batches.map((b) =>
                                '<tr><td>' + esc(b.date) + '</td><td>' + esc(b.lines) + '</td><td>' + esc(String(b.hours || '').replace(/:\d{2}$/, '')) + '</td><td><span class="ua-state ' + (b.rejected ? 'is-rejected' : 'is-sent') + '"><i></i>' +
                                esc(b.rejected ? @json(__('Rejected')) : @json(__('Sent'))) + '</span></td><td class="ua-td-file">' +
                                (b.download_url
                                    ? '<a class="ua-batch-dl" href="' + esc(b.download_url) + '" title="' + esc(@json(__('Download the file of this batch again'))) + '" aria-label="' + esc(@json(__('Download the file of this batch again'))) + '"><i class="fa-solid fa-download" aria-hidden="true"></i></a>'
                                    : '<span class="ua-muted" title="' + esc(@json(__('Batches exported before this option, or older than the retention period, have no saved file.'))) + '">' + esc(@json(__('Not available'))) + '</span>') +
                                '</td></tr>').join('')
                                : '<tr><td colspan="5" class="ua-muted">' + esc(@json(__('No exports yet.'))) + '</td></tr>';
                        },
                        error: function() {
                            lines.textContent = '—';
                            document.getElementById('uaBatches').innerHTML = '<tr><td colspan="5" class="ua-muted">' + esc(@json(__('The summary could not be loaded.'))) + '</td></tr>';
                        }
                    });
                };

                // Redescargar un lote: es el mismo archivo (mismos RegId). Si se vuelve a importar
                // en Axapta un lote que ya entró, las horas se duplican: se avisa antes.
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('.ua-batch-dl');
                    if (!link) return;
                    if (!window.confirm(@json(__('This batch was already exported. Import it into Axapta only if it was not imported before; otherwise the hours will be duplicated.')))) {
                        e.preventDefault();
                    }
                });
                const toggle = document.getElementById('uaIssuesToggle');
                if (toggle) toggle.addEventListener('click', () => {
                    const list = document.getElementById('uaIssuesList');
                    list.hidden = !list.hidden;
                    toggle.textContent = list.hidden ? toggle.dataset.show : toggle.dataset.hide;
                });
            })();
        </script>

        <script>
            $('#exportAxapta').on('click', function() {
                const $btn = $(this);
                if ($btn.prop('disabled')) return;
                const originalText = $btn.text();
                $btn.prop('disabled', true).text('{{ __("Exporting...") }}');
                operationUrl = '<?php echo url('/projects/export-axapta'); ?>';
                $.ajax({
                    type: 'POST',
                    url: operationUrl,
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Decodificar el contenido del archivo
                            const fileContent = atob(response.fileContent);
                            const blob = new Blob([fileContent], {
                                type: 'text/plain'
                            });

                            // Crear un link de descarga
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = response.fileName;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(link.href);

                            // Encargos o líneas que no se han exportado y avisos
                            if (response.notice) {
                                alert(response.notice);
                            }
                        } else if (response.message) {
                            alert(response.message);
                        } else {
                            console.error("Error en la respuesta:", response.error);
                            alert('Error: ' + (response.error || 'Error desconocido'));
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.message) {
                            alert(xhr.responseJSON.message);
                            return;
                        }
                        console.error("Error AJAX:", error);
                        console.error("Response:", xhr.responseText);
                        alert('Error al descargar el archivo: ' + error);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                        if (window.uaLoadAxaptaPreview) window.uaLoadAxaptaPreview();
                    }
                });
            });
        </script>
    @endpush
    <style>
        .icon {
            width: 25px;
            height: 23px;
            margin-right: 10px;
        }
    </style>
