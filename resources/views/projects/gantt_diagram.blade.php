@extends('layouts.admin')

@section('page-title')
    {{ __('Gantt Diagram') }}
@endsection

@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Gantt Diagram') }}</li>
@endsection

@section('action-button')
    {{-- View mode selector --}}
    <div class="gantt-segmented mx-1" id="change_view" role="group">
        <button class="gantt-seg-btn" data-view="Day">{{ __('Day') }}</button>
        <button class="gantt-seg-btn active" data-view="Week">{{ __('Week') }}</button>
        <button class="gantt-seg-btn" data-view="Month">{{ __('Month') }}</button>
    </div>

    {{-- Level toggle --}}
    <div class="gantt-segmented mx-1" id="level_toggle" role="group">
        <button class="gantt-seg-btn" data-level="projects">{{ __('Projects') }}</button>
        <button class="gantt-seg-btn active" data-level="milestones">{{ __('+ Order Forms') }}</button>
        <button class="gantt-seg-btn" data-level="tasks">{{ __('+ Tasks') }}</button>
    </div>

    {{-- #15 - Fullscreen button --}}
    <button class="gantt-icon-btn mx-1" id="btnFullscreen" title="{{ __('Fullscreen') }}">
        <i class="fa-solid fa-expand"></i>
    </button>

    {{-- Sidebar toggle button --}}
    <button class="gantt-icon-btn mx-1" id="btnToggleSidebar" title="{{ __('Toggle sidebar') }}">
        <i class="fa-solid fa-table-columns"></i>
    </button>

    {{-- #14 - Export PNG button --}}
    <button class="gantt-icon-btn mx-1" id="btnExport" title="{{ __('Export PNG') }}">
        <i class="fa-solid fa-download"></i>
    </button>
@endsection

@section('content')
    <section class="section" id="ganttSection">
        {{-- Filter row --}}
        <div class="row mb-3 gantt-filters" id="ganttFilters">
            {{-- Project filter --}}
            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                <div class="gantt-multiselect" id="filterProjectWrap">
                    <div class="gantt-ms-trigger">
                        <span class="gantt-ms-label"
                            data-placeholder="{{ __('All Projects') }}">{{ __('All Projects') }}</span>
                        <i class="fa-solid fa-chevron-down gantt-ms-arrow"></i>
                    </div>
                    <div class="gantt-ms-dropdown" id="filterProjectDrop">
                        <div class="gantt-ms-search-wrap">
                            <i class="fa-solid fa-magnifying-glass gantt-ms-search-icon"></i>
                            <input type="text" class="gantt-ms-search" placeholder="{{ __('Search...') }}">
                        </div>
                        <div class="gantt-ms-list">
                            @foreach ($projects as $p)
                                <label class="gantt-ms-option">
                                    <input type="checkbox" value="{{ $p->id }}">
                                    <span class="gantt-ms-check"></span>
                                    <span class="gantt-ms-text">{{ $p->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="gantt-ms-actions">
                            <button type="button" class="gantt-ms-clear">{{ __('Clear') }}</button>
                            <button type="button" class="gantt-ms-apply">{{ __('Apply') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Status filter --}}
            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                <div class="gantt-multiselect" id="filterStatusWrap">
                    <div class="gantt-ms-trigger">
                        <span class="gantt-ms-label"
                            data-placeholder="{{ __('All Statuses') }}">{{ __('All Statuses') }}</span>
                        <i class="fa-solid fa-chevron-down gantt-ms-arrow"></i>
                    </div>
                    <div class="gantt-ms-dropdown" id="filterStatusDrop">
                        <div class="gantt-ms-list" style="padding-top:6px">
                            <label class="gantt-ms-option">
                                <input type="checkbox" value="Ongoing">
                                <span class="gantt-ms-check"></span>
                                <span class="gantt-ms-text">{{ __('Ongoing') }}</span>
                            </label>
                            <label class="gantt-ms-option">
                                <input type="checkbox" value="OnHold">
                                <span class="gantt-ms-check"></span>
                                <span class="gantt-ms-text">{{ __('On Hold') }}</span>
                            </label>
                            <label class="gantt-ms-option">
                                <input type="checkbox" value="Finished">
                                <span class="gantt-ms-check"></span>
                                <span class="gantt-ms-text">{{ __('Finished') }}</span>
                            </label>
                        </div>
                        <div class="gantt-ms-actions">
                            <button type="button" class="gantt-ms-clear">{{ __('Clear') }}</button>
                            <button type="button" class="gantt-ms-apply">{{ __('Apply') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- #1 - User filter --}}
            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                <div class="gantt-multiselect" id="filterUserWrap">
                    <div class="gantt-ms-trigger">
                        <span class="gantt-ms-label" data-placeholder="{{ __('All Users') }}">{{ __('All Users') }}</span>
                        <i class="fa-solid fa-chevron-down gantt-ms-arrow"></i>
                    </div>
                    <div class="gantt-ms-dropdown" id="filterUserDrop">
                        <div class="gantt-ms-search-wrap">
                            <i class="fa-solid fa-magnifying-glass gantt-ms-search-icon"></i>
                            <input type="text" class="gantt-ms-search" placeholder="{{ __('Search...') }}">
                        </div>
                        <div class="gantt-ms-list">
                            @foreach ($users as $u)
                                <label class="gantt-ms-option">
                                    <input type="checkbox" value="{{ $u->id }}">
                                    <span class="gantt-ms-check"></span>
                                    <span class="gantt-ms-text">{{ $u->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="gantt-ms-actions">
                            <button type="button" class="gantt-ms-clear">{{ __('Clear') }}</button>
                            <button type="button" class="gantt-ms-apply">{{ __('Apply') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- #2 - Date range filter --}}
            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                <input type="date" id="filterDateFrom" class="form-control form-control-sm"
                    placeholder="{{ __('From') }}">
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                <input type="date" id="filterDateTo" class="form-control form-control-sm"
                    placeholder="{{ __('To') }}">
            </div>
            <div class="col-auto mb-2 gantt-toggle-col">
                <label class="gantt-switch" title="{{ __('Show labels') }}">
                    <span class="gantt-switch-label">{{ __('Labels') }}</span>
                    <input type="checkbox" id="toggleLabels">
                    <span class="gantt-switch-track"></span>
                </label>
                <label class="gantt-switch" title="{{ __('Show_percentages') }}">
                    <span class="gantt-switch-label">%</span>
                    <input type="checkbox" id="togglePercentages">
                    <span class="gantt-switch-track"></span>
                </label>
            </div>
            <div class="col-auto mb-2" id="exitFullscreenWrapper">
                <button class="btn btn-sm btn-danger" id="btnExitFullscreen" title="{{ __('Exit Fullscreen') }}">
                    <i class="fa-solid fa-compress"></i> {{ __('Exit Fullscreen') }}
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="gantt-legend" id="ganttLegend">
            <span class="gantt-legend-group">
                <span class="gantt-legend-group-label">{{ __('Projects') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#1c49b5;"></span>{{ __('Ongoing') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#10b981;"></span>{{ __('Finished') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#94a3b8;"></span>{{ __('On Hold') }}</span>
            </span>
            <span class="gantt-legend-sep"></span>
            <span class="gantt-legend-group">
                <span class="gantt-legend-group-label">{{ __('Milestones') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#cbd5e1;"></span>{{ __('Todo') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#60a5fa;"></span>{{ __('In_Progress') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#fbbf24;"></span>{{ __('Review') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#34d399;"></span>{{ __('Done') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot" style="background:#f87171;"></span>{{ __('Overdue') }}</span>
            </span>
            <span class="gantt-legend-sep"></span>
            <span class="gantt-legend-group">
                <span class="gantt-legend-group-label">{{ __('Tasks') }}</span>
                <span class="gantt-legend-item"><span class="gantt-dot"
                        style="background:#a5b4fc;"></span>{{ __('Task') }}</span>
            </span>
        </div>

        <div id="gantt-empty" class="gantt-empty-state" style="display:none;">
            <div class="gantt-empty-icon">
                <i class="fa-solid fa-chart-gantt"></i>
            </div>
            <p class="gantt-empty-title">{{ __('No data to display') }}</p>
            <p class="gantt-empty-sub">{{ __('Try adjusting your filters or date range.') }}</p>
        </div>

        <div class="gantt-layout" id="ganttLayout">
            {{-- Skeleton Loading --}}
            <div class="gantt-skeleton" id="ganttSkeleton">
                <div class="gantt-skeleton-sidebar">
                    <div class="gantt-skeleton-header"></div>
                    {{-- Project row --}}
                    <div class="gantt-skeleton-row skel-project">
                        <span class="skel-chevron"></span>
                        <span class="skel-dot" style="background:#1c49b5"></span>
                        <span class="skel-text" style="width:60%"></span>
                    </div>
                    {{-- Milestone rows --}}
                    <div class="gantt-skeleton-row skel-milestone">
                        <span class="skel-chevron"></span>
                        <span class="skel-dot" style="background:#f59e0b"></span>
                        <span class="skel-text" style="width:55%"></span>
                    </div>
                    <div class="gantt-skeleton-row skel-task">
                        <span class="skel-spacer"></span>
                        <span class="skel-dot" style="background:#10b981"></span>
                        <span class="skel-text" style="width:50%"></span>
                    </div>
                    <div class="gantt-skeleton-row skel-task">
                        <span class="skel-spacer"></span>
                        <span class="skel-dot" style="background:#10b981"></span>
                        <span class="skel-text" style="width:38%"></span>
                    </div>
                    <div class="gantt-skeleton-row skel-milestone">
                        <span class="skel-chevron"></span>
                        <span class="skel-dot" style="background:#f59e0b"></span>
                        <span class="skel-text" style="width:48%"></span>
                    </div>
                    <div class="gantt-skeleton-row skel-task">
                        <span class="skel-spacer"></span>
                        <span class="skel-dot" style="background:#10b981"></span>
                        <span class="skel-text" style="width:44%"></span>
                    </div>
                    {{-- Second project --}}
                    <div class="gantt-skeleton-row skel-project" style="border-top:1px solid #e2e8f0">
                        <span class="skel-chevron"></span>
                        <span class="skel-dot" style="background:#1c49b5"></span>
                        <span class="skel-text" style="width:52%"></span>
                    </div>
                    <div class="gantt-skeleton-row skel-milestone">
                        <span class="skel-chevron"></span>
                        <span class="skel-dot" style="background:#f59e0b"></span>
                        <span class="skel-text" style="width:58%"></span>
                    </div>
                    <div class="gantt-skeleton-row skel-task">
                        <span class="skel-spacer"></span>
                        <span class="skel-dot" style="background:#10b981"></span>
                        <span class="skel-text" style="width:42%"></span>
                    </div>
                </div>
                <div class="gantt-skeleton-timeline">
                    <div class="gantt-skeleton-header">
                        <div class="skel-header-cols"></div>
                    </div>
                    {{-- Bars matching each sidebar row --}}
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-project" style="width:45%;margin-left:5%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-milestone" style="width:30%;margin-left:8%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-task" style="width:18%;margin-left:10%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-task" style="width:14%;margin-left:22%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-milestone" style="width:25%;margin-left:15%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-task" style="width:20%;margin-left:12%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-project" style="width:55%;margin-left:3%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-milestone" style="width:35%;margin-left:6%"></span>
                    </div>
                    <div class="gantt-skeleton-bar-row">
                        <span class="skel-bar skel-bar-task" style="width:16%;margin-left:18%"></span>
                    </div>
                </div>
            </div>
            {{-- Sidebar --}}
            <div class="gantt-sidebar" id="ganttSidebar">
                <div class="gantt-sidebar-header"></div>
                <div class="gantt-sidebar-scroll" id="ganttSidebarScroll">
                    <div class="gantt-sidebar-rows" id="ganttSidebarRows"></div>
                </div>
            </div>
            {{-- Timeline --}}
            <div class="gantt-timeline" id="ganttContainer" style="position:relative;">
                <div class="gantt-zoom-indicator" id="zoomIndicator">
                    <i class="fa-solid fa-magnifying-glass zoom-icon"></i>
                    <span id="zoomLabel"></span>
                </div>
                <div class="gantt-target"></div>
            </div>
        </div>
    </section>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/frappe-gantt.css') }}?v={{ time() }}" />
    <style>
        /* ============================================
                                                                                                                                                           GANTT — SaaS Professional Theme
                                                                                                                                                           Scoped to #ganttSection
                                                                                                                                                           Spacing grid: 8px
                                                                                                                                                           ============================================ */

        #ganttSection {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', system-ui, sans-serif;
        }

        /* === A — FILTER CARD === */
        #ganttFilters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 16px 8px;
            margin-bottom: 16px;
            margin-right: 12px;
            margin-left: 12px;
        }

        /* === B — FORM CONTROLS === */
        #ganttSection input.form-control {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            height: 36px;
            color: #334155;
            background-color: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        #ganttSection input.form-control:focus {
            border: 1px solid #aa182c !important;
            box-shadow: 0 0 0 3px rgba(170, 24, 44, .15);
            outline: none;
        }

        /* === B2 — EXCEL-STYLE MULTISELECT DROPDOWN === */
        .gantt-multiselect {
            position: relative;
        }

        .gantt-ms-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 36px;
            padding: 0 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            transition: border-color .15s ease, box-shadow .15s ease;
            user-select: none;
        }

        .gantt-ms-trigger:hover {
            border-color: #cbd5e1;
        }

        .gantt-ms-trigger.open {
            border-color: #aa182c;
            box-shadow: 0 0 0 3px rgba(170, 24, 44, .15);
        }

        .gantt-ms-label {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 34px;
        }

        .gantt-ms-label.has-values {
            color: #1e293b;
            font-weight: 500;
        }

        .gantt-ms-arrow {
            font-size: 10px;
            color: #94a3b8;
            margin-left: 6px;
            transition: transform .2s ease;
        }

        .gantt-ms-trigger.open .gantt-ms-arrow {
            transform: rotate(180deg);
        }

        /* Dropdown panel */
        .gantt-ms-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            min-width: 220px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12), 0 2px 6px rgba(0, 0, 0, .06);
            z-index: 100;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .gantt-ms-dropdown.open {
            display: flex;
            animation: ganttMsSlide .15s ease;
        }

        @keyframes ganttMsSlide {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Search input inside dropdown */
        .gantt-ms-search-wrap {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .gantt-ms-search-icon {
            font-size: 12px;
            color: #94a3b8;
            margin-right: 8px;
        }

        .gantt-ms-search {
            border: none;
            outline: none;
            font-size: 13px;
            color: #334155;
            background: transparent;
            width: 100%;
        }

        .gantt-ms-search::placeholder {
            color: #94a3b8;
        }

        /* Option list */
        .gantt-ms-list {
            max-height: 200px;
            overflow-y: auto;
            padding: 4px 0;
        }

        .gantt-ms-list::-webkit-scrollbar {
            width: 5px;
        }

        .gantt-ms-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* Each option row */
        .gantt-ms-option {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 13px;
            color: #334155;
            transition: background .1s ease;
            margin: 0;
        }

        .gantt-ms-option:hover {
            background: #f1f5f9;
        }

        .gantt-ms-option.hidden {
            display: none;
        }

        /* Custom checkbox */
        .gantt-ms-option input[type="checkbox"] {
            display: none;
        }

        .gantt-ms-check {
            width: 16px;
            height: 16px;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            margin-right: 8px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s ease;
            background: #fff;
        }

        .gantt-ms-check::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 9px;
            color: #fff;
            opacity: 0;
            transform: scale(.5);
            transition: all .15s ease;
        }

        .gantt-ms-option input:checked~.gantt-ms-check {
            background: #aa182c;
            border-color: #aa182c;
        }

        .gantt-ms-option input:checked~.gantt-ms-check::after {
            opacity: 1;
            transform: scale(1);
        }

        .gantt-ms-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Action bar (Clear / Apply) */
        .gantt-ms-actions {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-top: 1px solid #f1f5f9;
        }

        .gantt-ms-clear,
        .gantt-ms-apply {
            border: none;
            background: none;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 6px;
            transition: all .15s ease;
        }

        .gantt-ms-clear {
            color: #94a3b8;
        }

        .gantt-ms-clear:hover {
            color: #64748b;
            background: #f1f5f9;
        }

        .gantt-ms-apply {
            color: #fff;
            background: #ac0d22;
        }

        .gantt-ms-apply:hover {
            background: #8b0b1a;
        }

        /* === E — SEGMENTED CONTROLS (header buttons) === */
        .gantt-segmented {
            display: inline-flex;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 3px;
            gap: 2px;
        }

        .gantt-seg-btn {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
        }

        .gantt-seg-btn:hover {
            color: #334155;
            background: rgba(255, 255, 255, .5);
        }

        .gantt-seg-btn.active {
            background: #fff;
            color: #1e293b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1), 0 1px 2px rgba(0, 0, 0, .06);
            font-weight: 600;
        }

        /* Icon buttons (fullscreen, export) */
        .gantt-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #64748b;
            font-size: 14px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .gantt-icon-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: #f8fafc;
        }

        .gantt-text-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 36px;
            padding: 0 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s ease;
        }

        .gantt-text-toggle-btn:hover {
            border-color: #aa182c;
            color: #aa182c;
            background: #fff5f5;
        }

        .gantt-text-toggle-btn.active {
            border-color: #aa182c;
            color: #fff;
            background: #aa182c;
        }

        .gantt-text-toggle-btn i {
            font-size: 12px;
        }

        /* Compact toggles (switch) */
        .gantt-toggle-col {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .gantt-switch {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            user-select: none;
            white-space: nowrap;
        }

        .gantt-switch input {
            display: none;
        }

        .gantt-switch-track {
            width: 34px;
            height: 20px;
            border-radius: 999px;
            background: #cbd5e1;
            position: relative;
            transition: background .2s ease;
            flex-shrink: 0;
        }

        .gantt-switch-track::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
        }

        .gantt-switch input:checked+.gantt-switch-track {
            background: #aa182c;
        }

        .gantt-switch input:checked+.gantt-switch-track::after {
            transform: translateX(14px);
        }

        .gantt-switch:hover .gantt-switch-label {
            color: #334155;
        }

        /* === C — LEGEND === */
        .gantt-legend {
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 6px;
            padding: 0 0 16px;
            font-size: 12px;
            color: #64748b;
        }

        .gantt-legend-group {
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .gantt-legend-group-label {
            font-weight: 700;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .3px;
            transform: translateY(1px);
        }

        .gantt-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .gantt-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .gantt-legend-sep {
            display: none;
        }

        /* === D — COLOR PALETTE (armonizada) === */

        /* Project bars — thinner with diamond fangs */
        .gantt-project .bar {
            fill: #1c49b5 !important;
            transform: scaleY(0.7);
            transform-box: fill-box;
            transform-origin: center;
        }

        .gantt-project .bar-diamond {
            fill: #1c49b5 !important;
        }

        .gantt-project .handle {
            transform: scaleY(calc(1 / 0.7));
            transform-box: fill-box;
            transform-origin: center;
        }

        .gantt-ms-created .bar-marker  { fill: #94a3b8 !important; }
        .gantt-ms-active .bar-marker   { fill: #3b82f6 !important; }
        .gantt-ms-progress .bar-marker { fill: #f59e0b !important; }
        .gantt-ms-done .bar-marker     { fill: #10b981 !important; }
        .gantt-ms-overdue .bar-marker  { fill: #ef4444 !important; }

        .bar-marker-text {
            fill: #ffffff !important;
            font-size: 9px;
            font-weight: 700;
            pointer-events: none;
        }

        .bar-marker-text-combined {
            font-size: 7px;
        }

        .gantt-project .bar-progress {
            fill: #163790 !important;
        }

        .gantt-project .bar-label {
            fill: #fff !important;
            font-weight: 600 !important;
            font-size: 12px !important;
        }

        .gantt-project .bar-label.big {
            fill: #1c49b5 !important;
        }

        /* Project Finished (emerald) */
        .gantt-project-finished .bar {
            fill: #10b981 !important;
        }

        .gantt-project-finished .bar-progress {
            fill: #059669 !important;
        }

        .gantt-project-finished .bar-label.big {
            fill: #10b981 !important;
        }

        .gantt-project-finished .bar-diamond {
            fill: #10b981 !important;
        }

        /* Project On Hold (slate) */
        .gantt-project-onhold .bar {
            fill: #94a3b8 !important;
        }

        .gantt-project-onhold .bar-diamond {
            fill: #94a3b8 !important;
        }

        .gantt-project-onhold .bar-progress {
            fill: #64748b !important;
        }

        .gantt-project-onhold .bar-label.big {
            fill: #94a3b8 !important;
        }

        /* Milestone To Do (light slate) */
        .gantt-ms-created .bar {
            fill: #cbd5e1 !important;
        }

        .gantt-ms-created .bar-progress {
            fill: #94a3b8 !important;
        }

        /* Milestone In Progress (blue) */
        .gantt-ms-active .bar {
            fill: #60a5fa !important;
        }

        .gantt-ms-active .bar-progress {
            fill: #3b82f6 !important;
        }

        /* Milestone Review (amber) */
        .gantt-ms-progress .bar {
            fill: #fbbf24 !important;
        }

        .gantt-ms-progress .bar-progress {
            fill: #f59e0b !important;
        }

        /* Milestone Done (emerald) */
        .gantt-ms-done .bar {
            fill: #34d399 !important;
        }

        .gantt-ms-done .bar-progress {
            fill: #10b981 !important;
        }

        /* Overdue (red — softer than today line) */
        .gantt-ms-overdue .bar {
            fill: #f87171 !important;
        }

        .gantt-ms-overdue .bar-progress {
            fill: #ef4444 !important;
        }

        .gantt-ms-overdue .bar-label {
            fill: #fff !important;
            font-weight: 600 !important;
        }

        /* Tasks (indigo light) */
        .gantt-task .bar {
            fill: #a5b4fc !important;
        }

        .gantt-task .bar-progress {
            fill: #818cf8 !important;
        }

        .gantt-task .bar-label {
            font-size: 11px !important;
        }

        /* Milestone & task labels inside bars */
        .gantt-ms-created .bar-label,
        .gantt-ms-active .bar-label,
        .gantt-ms-progress .bar-label,
        .gantt-ms-done .bar-label {
            fill: #fff !important;
            font-size: 12px !important;
            font-weight: 500 !important;
        }

        /* Labels outside short bars (.big) — use bar color as text */
        .gantt-ms-created .bar-label.big {
            fill: #94a3b8 !important;
        }

        .gantt-ms-active .bar-label.big {
            fill: #3b82f6 !important;
        }

        .gantt-ms-progress .bar-label.big {
            fill: #f59e0b !important;
        }

        .gantt-ms-done .bar-label.big {
            fill: #10b981 !important;
        }

        .gantt-ms-overdue .bar-label.big {
            fill: #ef4444 !important;
        }

        .gantt-task .bar-label.big {
            fill: #818cf8 !important;
        }

        /* === F — POPUP (SaaS style) === */
        .details-container {
            padding: 12px 16px;
            min-width: 250px;
            max-width: 350px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .12), 0 2px 8px rgba(0, 0, 0, .06);
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', system-ui, sans-serif;
            font-size: 13px;
            color: #334155;
        }

        .details-container .title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            color: #1e293b;
        }

        .details-container .subtitle {
            font-size: 12px;
            line-height: 1.7;
            color: #475569;
        }

        .details-container .date-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 2px;
            color: #64748b;
        }

        .details-container .badge-inline {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        .details-container .hours-row {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
        }

        /* === Today line === */
        .gantt-today-line {
            stroke: #ef4444;
            stroke-width: 2;
            stroke-dasharray: 6 3;
            pointer-events: none;
        }

        /* Today column highlight */
        .gantt .today-highlight {
            fill: #ef4444;
            opacity: 0.06;
        }

/* Project separators */
        .gantt-separator-line {
            stroke: #72c8d4 ;
            stroke-width: 1;
            stroke-dasharray: 4 2;
            pointer-events: none;
        }

        /* === LAYOUT === */
        .gantt-container .gantt {
            min-height: 200px;
        }

        /* Read-only: no drag */
        .gantt .bar-wrapper {
            cursor: pointer !important;
            -webkit-user-select: none;
            user-select: none;
        }

        .gantt .handle-group,
        .gantt .bar-wrapper .handle {
            display: none !important;
        }

        /* Drag-to-pan */
        .gantt-container { cursor: grab; }
        .gantt-container.dragging { cursor: grabbing; user-select: none; }

        /* HTML sticky date header — position:sticky, same mechanism as sidebar header */
        .gantt-sticky-header {
            position: sticky;
            top: 0;
            z-index: 15;
            background: #fff;
            overflow: hidden;
            pointer-events: none;
        }

        .gantt-sticky-header svg {
            display: block;
        }

        .gantt-sticky-header .grid-header {
            fill: #fff;
            stroke: #e0e0e0;
            stroke-width: 1.4;
        }

        /* Hide original header in main SVG (replaced by overlay) */
        .gantt-target .grid-header,
        .gantt-target g.date text {
            visibility: hidden;
        }

        /* "Hoy"/"Today" in today's cell (Day view) */
        .gantt-today-label {
            font-size: 11px;
            fill: #aa182c !important;
        }

        #ganttSection.gantt-hide-bar-labels .gantt .bar-label {
            visibility: hidden !important;
        }

        /* Milestone (encargo) percentage labels at the right of each bar */
        .gantt-percent-label {
            font-size: 11px;
            font-weight: 600;
            fill: #334155;
            pointer-events: none;
        }

        #ganttSection.gantt-hide-bar-percentages .gantt-percent-label {
            display: none;
        }

        /* === G — EMPTY STATE === */
        .gantt-empty-state {
            text-align: center;
            padding: 64px 24px;
        }

        .gantt-empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: #f1f5f9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .gantt-empty-icon i {
            font-size: 28px;
            color: #94a3b8;
        }

        .gantt-empty-title {
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            margin: 0 0 4px;
        }

        .gantt-empty-sub {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
        }

        /* === H — FULLSCREEN === */
        #ganttSection.gantt-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            background: #fff;
            padding: 16px;
            overflow: hidden;
        }

        #ganttSection.gantt-fullscreen #ganttLayout {
            height: calc(100vh - 80px);
        }

        #ganttSection.gantt-fullscreen .gantt-container {
            overflow-x: auto;
            overflow-y: hidden;
        }

        #exitFullscreenWrapper {
            display: none;
        }

        #ganttSection.gantt-fullscreen #exitFullscreenWrapper {
            display: block;
        }

        /* === I — ZOOM INDICATOR === */
        .gantt-zoom-indicator {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            padding: 6px 18px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            pointer-events: none;
            z-index: 100;
            opacity: 0;
            transition: opacity .2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(8px);
        }

        .gantt-zoom-indicator.visible {
            opacity: 1;
        }

        .gantt-zoom-indicator .zoom-icon {
            font-size: 13px;
        }

        /* === J — RESPONSIVE === */
        @media (max-width: 768px) {
            #ganttFilters {
                padding: 12px 12px 4px;
            }

            .gantt-filters .col-lg-2 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .gantt-legend {
                font-size: 11px;
            }

            .gantt-seg-btn {
                font-size: 11px;
                padding: 4px 10px;
            }

            .gantt-text-toggle-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .gantt-filters .col-lg-2 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        /* ============================================
                                                                                                                                                   SIDEBAR
                                                                                                                                                   ============================================ */
        .gantt-layout {
            display: flex;
            align-items: flex-start;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow-y: auto;
            overflow-x: hidden;
            background: #fff;
            box-shadow: 0 4px 20px rgba(15, 23, 42, .05);
            position: relative;
            height: 580px;
        }

        #ganttLayout::-webkit-scrollbar {
            width: 8px;
        }

        #ganttLayout::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        #ganttLayout::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        #ganttLayout::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Sidebar panel */
        .gantt-sidebar {
            width: 240px;
            min-width: 240px;
            flex-shrink: 0;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #fafbfc 0%, #f1f5f9 100%);
            transition: width .2s ease, min-width .2s ease, opacity .2s ease, border-width .2s ease;
        }

        .gantt-sidebar.collapsed {
            width: 0;
            min-width: 0;
            border-right-width: 0;
            opacity: 0;
            pointer-events: none;
        }

        .gantt-sidebar-header {
            height: 60px;
            min-height: 60px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 12px;
            font-size: 11px;
            font-weight: 600;
            color: #000;
            text-transform: uppercase;
            letter-spacing: .5px;
            position: sticky;
            top: 0;
            z-index: 10;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        }

        .gantt-sidebar-scroll {
            flex: 1;
            overflow: hidden;
        }

        /* Hide scrollbar on sidebar (synced with timeline) */
        .gantt-sidebar-scroll::-webkit-scrollbar {
            width: 0;
        }

        .gantt-sidebar-scroll {
            scrollbar-width: none;
        }

        .gantt-sidebar-rows {
            /* Height set dynamically by JS to match SVG */
            transition: min-height .45s cubic-bezier(.4,0,.2,1);
        }

        /* Sidebar row */
        .gantt-sidebar-row {
            height: 38px;
            display: flex;
            align-items: center;
            padding: 0 8px 0 0;
            font-size: 12px;
            color: #334155;
            cursor: pointer;
            border-bottom: 1px solid transparent;
            transition: background .1s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .gantt-sidebar-row:hover {
            background: #f1f5f9;
        }

        .gantt-sidebar-row.highlighted {
            background: #ede9fe;
        }

        /* Indentation by type */
        .gantt-sidebar-row[data-type="project"] {
            padding-left: 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .gantt-sidebar-row[data-type="milestone"] {
            padding-left: 24px;
            font-weight: 500;
        }

        .gantt-sidebar-row[data-type="task"] {
            padding-left: 40px;
            font-weight: 400;
            color: #64748b;
        }

        /* Project group zebra striping - applies to project + all its children (milestones, tasks) */
        .gantt-sidebar-row[data-project-group="even"] { background: #f8fafc; }
        .gantt-sidebar-row[data-project-group="odd"]  { background: #eef2f7; }

        /* Collapse toggle chevron */
        .gantt-collapse-toggle {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 11px;
            cursor: pointer;
            border-radius: 4px;
            transition: transform .55s cubic-bezier(.4,0,.2,1), color .15s ease, background .15s ease;
            margin-right: 4px;
        }

        .gantt-collapse-toggle:hover {
            color: #6366f1;
            background: #e2e8f0;
        }

        .gantt-collapse-toggle.collapsed {
            transform: rotate(-90deg);
        }

        .gantt-collapse-toggle.gantt-spin {
            animation: ganttChevronSpin .55s cubic-bezier(.4,0,.2,1);
        }

        @keyframes ganttChevronSpin {
            from { transform: rotate(-90deg); }
            to   { transform: rotate(0deg); }
        }

        /* Parent rows are collapsible on click */
        .gantt-sidebar-row.has-children {
            cursor: pointer;
        }

        .gantt-sidebar-row.has-children:hover .gantt-collapse-toggle {
            color: #6366f1;
        }

        /* Spacer for rows without chevron */
        .gantt-collapse-spacer {
            width: 24px;
            flex-shrink: 0;
        }

        /* Type indicator icon (projects & milestones) */
        .gantt-sidebar-icon {
            flex-shrink: 0;
            margin-right: 8px;
            font-size: 10px;
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            color: #fff;
        }

        /* Type indicator dot (tasks) */
        .gantt-sidebar-dot {
            font-size: 8px;
            flex-shrink: 0;
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        /* Row label text */
        .gantt-sidebar-label {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Collapse/expand animations for sidebar rows */
        .gantt-sidebar-row {
            transition: opacity .4s cubic-bezier(.4,0,.2,1), transform .4s cubic-bezier(.4,0,.2,1),
                height .45s cubic-bezier(.4,0,.2,1), padding .45s cubic-bezier(.4,0,.2,1),
                border-width .45s cubic-bezier(.4,0,.2,1), background .1s ease;
        }
        .gantt-sidebar-row.gantt-row-hide {
            opacity: 0;
            transform: translateY(-6px);
            height: 0;
            padding-top: 0;
            padding-bottom: 0;
            border-bottom-width: 0;
            pointer-events: none;
        }
        .gantt-sidebar-row.gantt-row-enter {
            animation: ganttRowEnter .55s cubic-bezier(.22,.61,.36,1) backwards;
        }
        @keyframes ganttRowEnter {
            from { height: 0; opacity: 0; transform: translateY(-4px); }
            to   { height: 38px; opacity: 1; transform: none; }
        }

        /* Collapse/expand animations for timeline bars */
        .gantt-target .bar-wrapper.gantt-bar-hide {
            opacity: 0;
            transition: opacity .34s cubic-bezier(.4,0,.2,1);
        }
        .gantt-target .bar-wrapper.gantt-bar-enter {
            animation: ganttBarEnter .4s cubic-bezier(.22,.61,.36,1);
        }
        @keyframes ganttBarEnter {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Fade the whole timeline SVG during re-render */
        .gantt-target.gantt-fade-out {
            opacity: 0;
            transform: translateY(5px);
            transition: opacity .4s cubic-bezier(.4,0,.2,1), transform .4s cubic-bezier(.4,0,.2,1);
            pointer-events: none;
        }
        .gantt-target.gantt-fade-in {
            opacity: 1;
            transform: translateY(0);
            transition: opacity .6s cubic-bezier(.4,0,.2,1), transform .6s cubic-bezier(.4,0,.2,1);
        }

        /* Separator line for projects */
        .gantt-sidebar-row[data-type="project"]+.gantt-sidebar-row[data-type="project"] {
            border-top: 1px solid #e2e8f0;
        }

        /* Timeline takes remaining space */
        .gantt-timeline {
            flex: 1;
            min-width: 0;
        }

        .gantt-target {
        }

        .gantt-target .gantt-container {
            overflow-x: auto;
            overflow-y: hidden;
        }

        /* Responsive — hide sidebar on mobile */
        @media (max-width: 768px) {
            .gantt-sidebar {
                width: 0;
                min-width: 0;
                border-right-width: 0;
                opacity: 0;
                pointer-events: none;
            }
        }

        /* Bar cross-highlight when hovering sidebar */
        .gantt-bar-highlighted .bar {
            filter: brightness(1.15);
            stroke: #6366f1;
            stroke-width: 2;
        }

        /* ============================================
                                                                                                   SKELETON LOADING
                                                                                                   ============================================ */
        .gantt-skeleton {
            display: flex;
            width: 100%;
            min-height: 420px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 5;
            background: #fff;
            transition: opacity .25s ease;
        }

        .gantt-skeleton.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* --- Sidebar skeleton --- */
        .gantt-skeleton-sidebar {
            width: 240px;
            flex-shrink: 0;
            border-right: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0;
            overflow: hidden;
        }

        .gantt-skeleton-timeline {
            flex: 1;
            background: #fff;
            padding: 0;
            overflow: hidden;
            position: relative;
        }

        .gantt-skeleton-header {
            height: 60px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(90deg, #f1f5f9 25%, #e8ecf1 50%, #f1f5f9 75%);
            background-size: 400% 100%;
            animation: skeleton-shimmer 1.5s ease infinite;
            position: relative;
        }

        /* Column lines in timeline header */
        .skel-header-cols {
            position: absolute;
            inset: 0;
            display: flex;
        }

        .skel-header-cols::before,
        .skel-header-cols::after {
            content: '';
            flex: 1;
            border-right: 1px solid rgba(226, 232, 240, .5);
        }

        /* --- Sidebar rows: exact 38px height like real rows --- */
        .gantt-skeleton-row {
            height: 38px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .gantt-skeleton-row.skel-project {
            padding-left: 8px;
        }

        .gantt-skeleton-row.skel-milestone {
            padding-left: 24px;
        }

        .gantt-skeleton-row.skel-task {
            padding-left: 40px;
        }

        /* Chevron placeholder */
        .skel-chevron {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-right: 4px;
            border-radius: 4px;
            background: linear-gradient(90deg, #e2e8f0 25%, #d8dee6 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            animation: skeleton-shimmer 1.5s ease infinite;
        }

        /* Spacer for leaf rows without chevron */
        .skel-spacer {
            width: 24px;
            flex-shrink: 0;
        }

        /* Type dot placeholder — same 6px circle as real */
        .skel-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: 6px;
            opacity: .4;
        }

        /* Text placeholder */
        .skel-text {
            height: 10px;
            border-radius: 4px;
            background: linear-gradient(90deg, #e2e8f0 25%, #d8dee6 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            animation: skeleton-shimmer 1.5s ease infinite;
        }

        /* --- Timeline bar rows: exact 38px height --- */
        .gantt-skeleton-bar-row {
            height: 38px;
            display: flex;
            align-items: center;
        }

        /* Bar placeholder */
        .skel-bar {
            height: 20px;
            border-radius: 4px;
            background: linear-gradient(90deg, #e2e8f0 25%, #d8dee6 50%, #e2e8f0 75%);
            background-size: 400% 100%;
            animation: skeleton-shimmer 1.5s ease infinite;
        }

        /* Subtle color tints matching bar types */
        .skel-bar-project {
            background: linear-gradient(90deg, #c7d2fe 25%, #b4bef0 50%, #c7d2fe 75%);
            background-size: 400% 100%;
            opacity: .5;
        }

        .skel-bar-milestone {
            background: linear-gradient(90deg, #fde68a 25%, #f5d670 50%, #fde68a 75%);
            background-size: 400% 100%;
            opacity: .4;
        }

        .skel-bar-task {
            background: linear-gradient(90deg, #a7f3d0 25%, #86efac 50%, #a7f3d0 75%);
            background-size: 400% 100%;
            opacity: .4;
        }

        @keyframes skeleton-shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        @media (max-width: 768px) {
            .gantt-skeleton-sidebar {
                display: none;
            }
        }
    </style>
@endpush

@push('scripts')
    @php
        $currantLang = basename(App::getLocale());
    @endphp
    <script>
        const month_names = {
            "{{ $currantLang }}": [
                '{{ __('January') }}', '{{ __('February') }}', '{{ __('March') }}',
                '{{ __('April') }}', '{{ __('May') }}', '{{ __('June') }}',
                '{{ __('July') }}', '{{ __('August') }}', '{{ __('September') }}',
                '{{ __('October') }}', '{{ __('November') }}', '{{ __('December') }}'
            ],
            "en": [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
        };
    </script>
    <script src="{{ asset('assets/custom/js/frappe-gantt.js') }}?v={{ time() }}"></script>
    <script>
        (function() {
            let ganttChart = null;
            let currentViewMode = localStorage.getItem('ganttViewMode') || 'Week';
            let currentLevel = localStorage.getItem('ganttLevel') || 'milestones';
            let allData = [];

            let sidebarVisible = window.innerWidth > 768;
            const savedLabelsOverride = localStorage.getItem('ganttLabelsOverride');
            let labelsVisibleOverride = savedLabelsOverride === null ? null : (savedLabelsOverride === 'true');
            let percentagesVisible = localStorage.getItem('ganttPercentagesVisible') !== 'false';
            const collapsedIds = new Set(JSON.parse(sessionStorage.getItem('ganttCollapsed') || '[]'));

            const dataUrl = @json(route('gantt.diagram.data'));

            function getLabelsVisible() {
                return labelsVisibleOverride === null ? !sidebarVisible : labelsVisibleOverride;
            }

            function updateLabelsToggleButton() {
                const cb = document.getElementById('toggleLabels');
                if (!cb) return;
                cb.checked = getLabelsVisible();
            }

            function applyLabelVisibility() {
                const section = document.getElementById('ganttSection');
                if (!section) return;
                section.classList.toggle('gantt-hide-bar-labels', !getLabelsVisible());
                updateLabelsToggleButton();
            }

            function updatePercentagesToggleButton() {
                const cb = document.getElementById('togglePercentages');
                if (!cb) return;
                cb.checked = percentagesVisible;
            }

            function applyPercentagesVisibility() {
                const section = document.getElementById('ganttSection');
                if (!section) return;
                section.classList.toggle('gantt-hide-bar-percentages', !percentagesVisible);
                updatePercentagesToggleButton();
            }

            // # - Draw milestone (encargo) percentage labels at the right of each bar
            function drawPercentages(items) {
                const svg = document.querySelector('.gantt-target svg');
                if (!svg) return;
                svg.querySelectorAll('.gantt-percent-label').forEach(n => n.remove());

                const byId = {};
                (items || []).forEach(i => byId[i.id] = i);

                const bars = svg.querySelectorAll('.bar-wrapper');
                bars.forEach(bw => {
                    const cls = bw.getAttribute('class') || '';
                    if (!/gantt-ms-/.test(cls)) return; // solo encargos (milestones)
                    const item = byId[bw.getAttribute('data-id')];
                    const bar = bw.querySelector('.bar');
                    if (!item || !bar) return;

                    const pct = Math.round(+item.progress || 0);
                    const x = parseFloat(bar.getAttribute('x')) + parseFloat(bar.getAttribute('width'));
                    const y = parseFloat(bar.getAttribute('y')) + parseFloat(bar.getAttribute('height')) / 2;

                    const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    t.setAttribute('x', x + 5);
                    t.setAttribute('y', y);
                    t.setAttribute('dominant-baseline', 'central');
                    t.setAttribute('text-anchor', 'start');
                    t.setAttribute('class', 'gantt-percent-label');
                    t.textContent = pct + '%';
                    svg.appendChild(t);
                });

                applyPercentagesVisibility();
            }

            // === EXCEL-STYLE MULTISELECT DROPDOWNS ===
            function initMultiselect(wrapId, onChange) {
                const wrap = document.getElementById(wrapId);
                if (!wrap) return null;
                const trigger = wrap.querySelector('.gantt-ms-trigger');
                const dropdown = wrap.querySelector('.gantt-ms-dropdown');
                const searchInput = wrap.querySelector('.gantt-ms-search');
                const options = wrap.querySelectorAll('.gantt-ms-option');
                const clearBtn = wrap.querySelector('.gantt-ms-clear');
                const applyBtn = wrap.querySelector('.gantt-ms-apply');
                const label = wrap.querySelector('.gantt-ms-label');
                const placeholder = label.dataset.placeholder;

                function getSelected() {
                    return Array.from(wrap.querySelectorAll('.gantt-ms-option input:checked')).map(cb => cb.value);
                }

                function updateLabel() {
                    const sel = getSelected();
                    if (sel.length === 0) {
                        label.textContent = placeholder;
                        label.classList.remove('has-values');
                    } else {
                        const names = Array.from(wrap.querySelectorAll('.gantt-ms-option input:checked'))
                            .map(cb => cb.closest('.gantt-ms-option').querySelector('.gantt-ms-text').textContent
                                .trim());
                        label.textContent = names.length <= 2 ? names.join(', ') : names.length + ' selected';
                        label.classList.add('has-values');
                    }
                }

                function open() {
                    dropdown.classList.add('open');
                    trigger.classList.add('open');
                    if (searchInput) {
                        searchInput.value = '';
                        filterOptions('');
                        setTimeout(() => searchInput.focus(), 50);
                    }
                }

                function close() {
                    dropdown.classList.remove('open');
                    trigger.classList.remove('open');
                }

                function filterOptions(term) {
                    const t = term.toLowerCase();
                    options.forEach(opt => {
                        const text = opt.querySelector('.gantt-ms-text').textContent.toLowerCase();
                        opt.classList.toggle('hidden', t !== '' && !text.includes(t));
                    });
                }

                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (dropdown.classList.contains('open')) {
                        close();
                    } else {
                        // Close any other open dropdowns first
                        document.querySelectorAll('.gantt-ms-dropdown.open').forEach(d => {
                            d.classList.remove('open');
                            d.closest('.gantt-multiselect').querySelector('.gantt-ms-trigger').classList
                                .remove('open');
                        });
                        open();
                    }
                });

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        filterOptions(this.value);
                    });

                    searchInput.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }

                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                clearBtn.addEventListener('click', function() {
                    wrap.querySelectorAll('.gantt-ms-option input').forEach(cb => cb.checked = false);
                    updateLabel();
                    close();
                    if (onChange) onChange(getSelected());
                });

                applyBtn.addEventListener('click', function() {
                    updateLabel();
                    close();
                    if (onChange) onChange(getSelected());
                });

                // Also apply on individual checkbox change for instant feedback
                options.forEach(opt => {
                    opt.querySelector('input').addEventListener('change', function() {
                        updateLabel();
                    });
                });

                return {
                    getSelected,
                    setSelected(values) {
                        const vals = new Set(values.map(String));
                        wrap.querySelectorAll('.gantt-ms-option input').forEach(cb => {
                            cb.checked = vals.has(cb.value);
                        });
                        updateLabel();
                    },
                    clear() {
                        wrap.querySelectorAll('.gantt-ms-option input').forEach(cb => cb.checked = false);
                        updateLabel();
                    }
                };
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                document.querySelectorAll('.gantt-ms-dropdown.open').forEach(d => {
                    d.classList.remove('open');
                    d.closest('.gantt-multiselect').querySelector('.gantt-ms-trigger').classList.remove(
                        'open');
                });
            });

            const msProject = initMultiselect('filterProjectWrap', () => fetchAndRender());
            const msStatus = initMultiselect('filterStatusWrap', () => fetchAndRender());
            const msUser = initMultiselect('filterUserWrap', () => fetchAndRender());

            // #11 - Localized date formatter
            const dateLocale = '{{ $currantLang }}' || 'en';

            function formatDate(dateStr) {
                if (!dateStr) return '-';
                try {
                    const d = new Date(dateStr + 'T00:00:00');
                    return d.toLocaleDateString(dateLocale === 'es' ? 'es-ES' : dateLocale, {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {
                    return dateStr;
                }
            }

            // #12 - Read filters from URL params on load
            function loadFiltersFromURL() {
                const params = new URLSearchParams(window.location.search);
                if (params.has('project_id') && msProject) msProject.setSelected(params.get('project_id').split(','));
                if (params.has('status') && msStatus) msStatus.setSelected(params.get('status').split(','));
                if (params.has('assigned_to') && msUser) msUser.setSelected(params.get('assigned_to').split(','));
                if (params.has('date_from')) document.getElementById('filterDateFrom').value = params.get('date_from');
                if (params.has('date_to')) document.getElementById('filterDateTo').value = params.get('date_to');
                if (params.has('view')) {
                    currentViewMode = params.get('view');
                    localStorage.setItem('ganttViewMode', currentViewMode);
                    document.querySelectorAll('#change_view .gantt-seg-btn').forEach(b => {
                        b.classList.toggle('active', b.dataset.view === currentViewMode);
                    });
                } else {
                    document.querySelectorAll('#change_view .gantt-seg-btn').forEach(b => {
                        b.classList.toggle('active', b.dataset.view === currentViewMode);
                    });
                }
                if (params.has('level')) {
                    currentLevel = params.get('level');
                    localStorage.setItem('ganttLevel', currentLevel);
                    document.querySelectorAll('#level_toggle .gantt-seg-btn').forEach(b => {
                        b.classList.toggle('active', b.dataset.level === currentLevel);
                    });
                } else {
                    document.querySelectorAll('#level_toggle .gantt-seg-btn').forEach(b => {
                        b.classList.toggle('active', b.dataset.level === currentLevel);
                    });
                }
            }

            // #12 - Save filters to URL
            function syncFiltersToURL() {
                const params = new URLSearchParams();
                const projectIds = msProject ? msProject.getSelected() : [];
                const statuses = msStatus ? msStatus.getSelected() : [];
                const assignedTo = msUser ? msUser.getSelected() : [];
                const dateFrom = document.getElementById('filterDateFrom').value;
                const dateTo = document.getElementById('filterDateTo').value;

                if (projectIds.length) params.set('project_id', projectIds.join(','));
                if (statuses.length) params.set('status', statuses.join(','));
                if (assignedTo.length) params.set('assigned_to', assignedTo.join(','));
                if (dateFrom) params.set('date_from', dateFrom);
                if (dateTo) params.set('date_to', dateTo);
                if (currentViewMode !== 'Week') params.set('view', currentViewMode);
                if (currentLevel !== 'milestones') params.set('level', currentLevel);

                const qs = params.toString();
                const newUrl = window.location.pathname + (qs ? '?' + qs : '');
                window.history.replaceState(null, '', newUrl);
            }

            function showSkeleton() {
                const el = document.getElementById('ganttSkeleton');
                if (el) {
                    el.classList.remove('hidden');
                    el.style.display = '';
                }
            }

            function hideSkeleton() {
                const el = document.getElementById('ganttSkeleton');
                if (el) {
                    el.classList.add('hidden');
                    setTimeout(() => {
                        el.style.display = 'none';
                    }, 250);
                }
            }

            function fetchAndRender() {
                const params = new URLSearchParams();
                const projectIds = msProject ? msProject.getSelected() : [];
                const statuses = msStatus ? msStatus.getSelected() : [];
                const assignedTo = msUser ? msUser.getSelected() : [];
                const dateFrom = document.getElementById('filterDateFrom').value;
                const dateTo = document.getElementById('filterDateTo').value;

                if (projectIds.length) params.set('project_id', projectIds.join(','));
                if (statuses.length) params.set('status', statuses.join(','));
                if (assignedTo.length) params.set('assigned_to', assignedTo.join(','));
                if (dateFrom) params.set('date_from', dateFrom);
                if (dateTo) params.set('date_to', dateTo);
                params.set('show_tasks', '1'); // Always fetch all levels


                // #12
                syncFiltersToURL();
                showSkeleton();

                fetch(dataUrl + '?' + params.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        allData = data;
                        applyLevelCollapses(data);
                        renderGantt(applyClientFilters(data));
                        hideSkeleton();
                    })
                    .catch(err => {
                        console.error('Gantt data error:', err);
                        hideSkeleton();
                    });
            }

            // Apply mass collapse/expand based on current level toggle
            // 'projects' → collapse all projects (hide milestones & tasks)
            // 'milestones' → expand projects, collapse milestones (hide tasks)
            // 'tasks' → expand everything
            function applyLevelCollapses(data) {
                // Clear ALL collapsed state first to avoid stale conflicts
                collapsedIds.clear();

                const projectIds = data.filter(d => d.type === 'project').map(d => d.id);
                const milestoneIds = data.filter(d => d.type === 'milestone').map(d => d.id);

                if (currentLevel === 'projects') {
                    // Collapse all projects → hides milestones and tasks
                    projectIds.forEach(id => collapsedIds.add(id));
                    milestoneIds.forEach(id => collapsedIds.add(id));
                } else if (currentLevel === 'milestones') {
                    // Expand projects, collapse milestones → hides tasks
                    milestoneIds.forEach(id => collapsedIds.add(id));
                }
                // 'tasks' → collapsedIds stays empty (everything expanded)
                saveCollapsedState();
            }

            // Apply client-side filters (collapse)
            function applyClientFilters(data) {
                let filtered = data.slice();
                // Apply collapse filter (hide children of collapsed parents)
                filtered = applyCollapseFilter(filtered);
                return filtered;
            }

            // Collapse filter — hide children whose parent is collapsed
            // Note: Frappe Gantt converts dependencies from string to array, so we handle both
            function getParentId(item) {
                const dep = item.dependencies;
                if (!dep) return '';
                if (Array.isArray(dep)) return dep[0] || '';
                return String(dep).trim();
            }

            function applyCollapseFilter(items) {
                if (collapsedIds.size === 0) return items;
                return items.filter(item => {
                    const parentId = getParentId(item);
                    if (item.type === 'milestone' && parentId && collapsedIds.has(parentId)) return false;
                    if (item.type === 'task') {
                        if (parentId && collapsedIds.has(parentId)) return false;
                        // Also hide if grandparent (project) is collapsed
                        const parentMs = items.find(i => i.id === parentId);
                        if (parentMs) {
                            const grandId = getParentId(parentMs);
                            if (grandId && collapsedIds.has(grandId)) return false;
                        }
                    }
                    return true;
                });
            }

            // Persist collapsed state to sessionStorage
            function saveCollapsedState() {
                sessionStorage.setItem('ganttCollapsed', JSON.stringify([...collapsedIds]));
            }

            // Get dot color for sidebar row based on item data
            function getSidebarDotColor(item) {
                if (item.type === 'project') {
                    if (item.extra && item.extra.status === 'Finished') return '#10b981';
                    if (item.extra && item.extra.status === 'OnHold') return '#94a3b8';
                    return '#1c49b5';
                }
                if (item.type === 'milestone') {
                    if (item.extra && item.extra.is_overdue) return '#ef4444';
                    const s = item.extra ? item.extra.status : '';
                    if (s === '4') return '#34d399';
                    if (s === '3') return '#fbbf24';
                    if (s === '2') return '#60a5fa';
                    return '#cbd5e1';
                }
                return '#a5b4fc'; // task
            }

            // Collapsible only if the item actually has children in allData
            function isCollapsible(item) {
                if (item.type === 'project') return allData.some(i => i.type === 'milestone' && getParentId(i) === item
                    .id);
                if (item.type === 'milestone') return allData.some(i => i.type === 'task' && getParentId(i) === item
                    .id);
                return false;
            }

            // Collect all descendant ids (milestones + tasks) of a project/encargo
            function collectDescendantIds(itemId) {
                const parentMs = allData.find(i => i.id === itemId);
                if (parentMs && parentMs.type === 'milestone') {
                    return allData.filter(i => i.type === 'task' && getParentId(i) === itemId).map(i => i.id);
                }
                const msIds = allData.filter(i => i.type === 'milestone' && getParentId(i) === itemId).map(i => i.id);
                const taskIds = allData.filter(i => i.type === 'task').filter(t => {
                    const p = getParentId(t);
                    return msIds.includes(p) || p === itemId;
                }).map(i => i.id);
                return msIds.concat(taskIds);
            }

            // Fade the timeline SVG out, run a render callback while hidden, then fade it back in
            function fadeThroughSvg(renderFn, contentFn, onDone) {
                const svg = document.querySelector('.gantt-target svg');
                const wrap = document.querySelector('.gantt-target');
                if (!svg || !wrap) {
                    renderFn();
                    if (onDone) onDone();
                    return;
                }
                wrap.classList.add('gantt-fade-out');
                setTimeout(function() {
                    renderFn();
                    contentFn && contentFn();
                    // On collapse, snap sidebar min-height to its exact final value
                    // without animating, so the re-render doesn't cause a residual jump
                    // at the moment the SVG fades back in.
                    if (!contentFn) {
                        const snap = document.getElementById('ganttSidebarRows');
                        if (snap) {
                            snap.style.transition = 'none';
                            syncSidebarHeight();
                            void snap.offsetWidth;
                            snap.style.transition = '';
                        }
                    }
                    // Force reflow then fade back in
                    void wrap.offsetWidth;
                    wrap.classList.remove('gantt-fade-out');
                    wrap.classList.add('gantt-fade-in');
                    setTimeout(function() {
                        wrap.classList.remove('gantt-fade-in');
                        if (onDone) onDone();
                    }, 640);
                }, 500);
            }

            // Animate collapsing: fade child sidebar rows + bars, fade SVG, re-render, fade back in
            function animateCollapse(itemId, descIds, doRender) {
                const rowsEl = document.getElementById('ganttSidebarRows');
                if (!rowsEl || descIds.length === 0) {
                    doRender();
                    return;
                }
                spinChevron(itemId, true);
                rowsEl.querySelectorAll('.gantt-sidebar-row').forEach(r => {
                    const rid = r.getAttribute('data-id');
                    if (descIds.indexOf(rid) !== -1) r.classList.add('gantt-row-hide');
                });
                const curMin = parseFloat(rowsEl.style.minHeight) || 0;
                rowsEl.style.minHeight = Math.max(curMin - descIds.length * 38, 0) + 'px';
                descIds.forEach(id => {
                    const bar = document.querySelector('.gantt-target .bar-wrapper[data-id="' + id + '"]');
                    if (bar) bar.classList.add('gantt-bar-hide');
                });
                fadeThroughSvg(doRender, null, null);
            }

            // Rotate/span the chevron of a sidebar row's collapse toggle
            function spinChevron(itemId, addCollapsed) {
                const row = document.querySelector('.gantt-sidebar-row[data-id="' + itemId + '"]');
                if (!row) return;
                const toggle = row.querySelector('.gantt-collapse-toggle');
                if (!toggle) return;
                if (addCollapsed) {
                    toggle.classList.add('collapsed');
                } else {
                    toggle.classList.remove('collapsed');
                    toggle.classList.add('gantt-spin');
                    toggle.addEventListener('animationend', function() {
                        toggle.classList.remove('gantt-spin');
                    }, { once: true });
                }
            }

            // Animate expanding: fade SVG, re-render, fade back in, then slide in child rows + bars
            function animateExpand(itemId, descIds, doRender) {
                const rowsEl = document.getElementById('ganttSidebarRows');
                if (!rowsEl || descIds.length === 0) {
                    doRender();
                    return;
                }
                fadeThroughSvg(doRender, doExpandBody, null);

                function doExpandBody() {
                    spinChevron(itemId, false);
                    rowsEl.querySelectorAll('.gantt-sidebar-row').forEach(function(r) {
                        const rid = r.getAttribute('data-id');
                        if (descIds.indexOf(rid) === -1) return;
                        r.classList.add('gantt-row-enter');
                    });
                    const curMin = parseFloat(rowsEl.style.minHeight) || 0;
                    rowsEl.style.minHeight = (curMin + descIds.length * 38) + 'px';
                    descIds.forEach(id => {
                        const bar = document.querySelector('.gantt-target .bar-wrapper[data-id="' + id + '"]');
                        if (bar) bar.classList.add('gantt-bar-enter');
                    });
                }
            }

            // Build sidebar rows from visible items
            function buildSidebar(visibleItems) {
                const container = document.getElementById('ganttSidebarRows');
                const headerEl = document.querySelector('.gantt-sidebar-header');
                if (!container) return;

                container.innerHTML = '';
                headerEl.textContent = '{{ __('Structure') }}';

                let currentProjectGroup = 0;

                visibleItems.forEach((item, idx) => {
                    const row = document.createElement('div');
                    row.className = 'gantt-sidebar-row';
                    row.setAttribute('data-type', item.type);
                    row.setAttribute('data-id', item.id);
                    row.setAttribute('data-index', idx);

                    // Assign project group for zebra striping
                    if (item.type === 'project') {
                        currentProjectGroup++;
                    }
                    row.setAttribute('data-project-group', currentProjectGroup % 2 === 0 ? 'even' : 'odd');

                    // Projects and milestones are always collapsible
                    const canCollapse = isCollapsible(item);

                    if (canCollapse) {
                        row.classList.add('has-children');
                        const toggle = document.createElement('span');
                        toggle.className = 'gantt-collapse-toggle' + (collapsedIds.has(item.id) ? ' collapsed' :
                            '');
                        toggle.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
                        row.appendChild(toggle);
                    } else {
                        const spacer = document.createElement('span');
                        spacer.className = 'gantt-collapse-spacer';
                        row.appendChild(spacer);
                    }

                    // Color dot / icon
                    const dot = document.createElement('i');
                    if (item.type === 'project') {
                        dot.className = 'fa-solid fa-diagram-project gantt-sidebar-icon';
                        dot.style.backgroundColor = getSidebarDotColor(item);
                    } else if (item.type === 'milestone') {
                        dot.className = 'fa-solid fa-file-lines gantt-sidebar-icon';
                        dot.style.backgroundColor = getSidebarDotColor(item);
                    } else {
                        dot.className = 'fas fa-circle-dot gantt-sidebar-dot';
                        dot.style.color = getSidebarDotColor(item);
                    }
                    row.appendChild(dot);

                    // Label
                    const label = document.createElement('span');
                    label.className = 'gantt-sidebar-label';
                    label.textContent = item.name.trim();
                    label.title = item.name.trim();
                    row.appendChild(label);

                    // Single click handler on the row
                    row.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!canCollapse) return;
                        const willCollapse = !collapsedIds.has(item.id);

                        const doRender = function() {
                            if (willCollapse) {
                                collapsedIds.add(item.id);
                            } else {
                                collapsedIds.delete(item.id);
                            }
                            saveCollapsedState();
                            renderGantt(applyClientFilters(allData));
                        };

                        const descIds = collectDescendantIds(item.id);
                        if (willCollapse) {
                            animateCollapse(item.id, descIds, doRender);
                        } else {
                            animateExpand(item.id, descIds, doRender);
                        }
                    });

                    // Hover cross-highlight
                    row.addEventListener('mouseenter', function() {
                        this.classList.add('highlighted');
                        const barEl = document.querySelector('.gantt-target .bar-wrapper[data-id="' +
                            item.id + '"]');
                        if (barEl) barEl.classList.add('gantt-bar-highlighted');
                    });
                    row.addEventListener('mouseleave', function() {
                        this.classList.remove('highlighted');
                        const barEl = document.querySelector('.gantt-target .bar-wrapper[data-id="' +
                            item.id + '"]');
                        if (barEl) barEl.classList.remove('gantt-bar-highlighted');
                    });

                    container.appendChild(row);
                });

                // Match sidebar rows height to SVG height
                syncSidebarHeight();
                // Drag-to-pan on Gantt timeline
                setupDragToPan();
                // HTML sticky header overlay + horizontal sync
                buildStickyHeaderOverlay();
                setupStickyHeaderScrollSync();
                // Apply initial sidebar visibility
                applySidebarVisibility();
            }

            // Sync sidebar rows container height to Gantt SVG height
            function syncSidebarHeight() {
                const svg = document.querySelector('.gantt-target svg');
                const rowsEl = document.getElementById('ganttSidebarRows');
                if (svg && rowsEl) {
                    let svgHeight;
                    svgHeight = (svg.height && svg.height.baseVal && svg.height.baseVal.value)
                        || svg.getAttribute('height')
                        || svg.clientHeight
                        || svg.getBoundingClientRect().height
                        || 0;
                    const headerRect = svg.querySelector('.grid-header');
                    const headerH = headerRect
                        ? (parseFloat(headerRect.getAttribute('height')) || 60)
                        : 60;
                    const contentHeight = svgHeight - headerH;
                    if (contentHeight > 0) {
                        rowsEl.style.minHeight = contentHeight + 'px';
                    }
                }
            }

            // Drag-to-pan: click and drag on Gantt timeline to scroll horizontally
            // Bind mousedown on the stable outer #ganttContainer (survives re-renders)
            // Use getScrollContainer() dynamically for scrollLeft (inner div gets recreated)
            let dragToPanSetup = false;

            function setupDragToPan() {
                if (dragToPanSetup) return;
                const outer = document.getElementById('ganttContainer');
                if (!outer) return;

                let isDragging = false, startX, startScrollLeft;

                outer.addEventListener('mousedown', function(e) {
                    if (e.button !== 0) return;
                    if (e.target.closest('.bar-wrapper')) return;
                    const scroller = getScrollContainer();
                    if (!scroller) return;
                    isDragging = true;
                    startX = e.pageX;
                    startScrollLeft = scroller.scrollLeft;
                    scroller.classList.add('dragging');
                    e.preventDefault();
                });

                document.addEventListener('mousemove', function(e) {
                    if (!isDragging) return;
                    const scroller = getScrollContainer();
                    if (!scroller) return;
                    const dx = e.pageX - startX;
                    scroller.scrollLeft = startScrollLeft - dx;
                });

                document.addEventListener('mouseup', function() {
                    if (!isDragging) return;
                    const scroller = getScrollContainer();
                    if (scroller) scroller.classList.remove('dragging');
                    isDragging = false;
                });

                dragToPanSetup = true;
            }

            // Sticky date header: HTML overlay with position:sticky
            function buildStickyHeaderOverlay() {
                var timeline = document.querySelector('.gantt-timeline');
                var svg = document.querySelector('.gantt-target svg');
                if (!timeline || !svg) return;

                var old = document.getElementById('ganttStickyHeader');
                if (old) old.remove();

                var headerRect = svg.querySelector('.grid-header');
                var headerH = headerRect
                    ? (parseFloat(headerRect.getAttribute('height')) || 60)
                    : 60;
                var headerW = headerRect
                    ? (parseFloat(headerRect.getAttribute('width')) || 0)
                    : 0;

                var overlay = document.createElement('div');
                overlay.id = 'ganttStickyHeader';
                overlay.className = 'gantt-sticky-header';
                overlay.style.height = headerH + 'px';

                var svgNS = 'http://www.w3.org/2000/svg';
                var cloneSvg = document.createElementNS(svgNS, 'svg');
                cloneSvg.classList.add('gantt');
                cloneSvg.setAttribute('width', headerW);
                cloneSvg.setAttribute('height', headerH);
                cloneSvg.style.width = headerW + 'px';
                cloneSvg.style.height = headerH + 'px';

                if (headerRect) {
                    var clonedRect = headerRect.cloneNode(true);
                    clonedRect.style.visibility = 'visible';
                    cloneSvg.appendChild(clonedRect);
                }

                var todayHL = svg.querySelector('.today-highlight');
                if (todayHL) {
                    cloneSvg.appendChild(todayHL.cloneNode(true));
                }

                svg.querySelectorAll('g.date text').forEach(function(txt) {
                    var cloned = txt.cloneNode(true);
                    cloned.style.visibility = 'visible';
                    cloneSvg.appendChild(cloned);
                });

                overlay.appendChild(cloneSvg);
                timeline.insertBefore(overlay, timeline.querySelector('.gantt-target'));

                // Compensate: sticky header takes flow space, pull .gantt-target up to re-align bars with sidebar rows
                var targetEl = document.querySelector('.gantt-target');
                if (targetEl) targetEl.style.marginTop = -headerH + 'px';
            }

            // Sync overlay horizontal position with .gantt-container scroll
            function setupStickyHeaderScrollSync() {
                var scroller = getScrollContainer();
                var overlaySvg = document.querySelector('#ganttStickyHeader svg');
                if (!scroller || !overlaySvg) return;
                scroller.addEventListener('scroll', function() {
                    overlaySvg.style.transform = 'translateX(' + (-scroller.scrollLeft) + 'px)';
                }, { passive: true });
            }

            // Apply sidebar visibility state
            function applySidebarVisibility() {
                const sidebar = document.getElementById('ganttSidebar');
                if (!sidebar) return;
                if (sidebarVisible) {
                    sidebar.classList.remove('collapsed');
                } else {
                    sidebar.classList.add('collapsed');
                }
                applyLabelVisibility();
            }

            function renderGantt(items) {
                const emptyEl = document.getElementById('gantt-empty');
                const layoutEl = document.getElementById('ganttLayout');
                const targetEl = document.querySelector('.gantt-target');

                if (!items || items.length === 0) {
                    emptyEl.style.display = 'block';
                    if (layoutEl) layoutEl.style.display = 'none';
                    targetEl.innerHTML = '';
                    ganttChart = null;
                    // Clear sidebar
                    const sidebarRows = document.getElementById('ganttSidebarRows');
                    if (sidebarRows) sidebarRows.innerHTML = '';
                    return;
                }

                emptyEl.style.display = 'none';
                if (layoutEl) layoutEl.style.display = 'flex';
                targetEl.innerHTML = '';
                targetEl.style.marginTop = '';

                ganttChart = new Gantt(".gantt-target", items, {
                    custom_popup_html: function(task) {
                        return buildPopup(task);
                    },
                    popup_trigger: 'mouseover',
                    readonly: true,

                    view_mode: currentViewMode,
                    language: '{{ $currantLang }}'
                });

                // #3 - Draw today line
                drawTodayLine();
                // #17 - Draw project separators
                drawProjectSeparators(items);

                // Build sidebar (uses allData internally for child detection)
                buildSidebar(items);

                applyLabelVisibility();
                drawPercentages(items);

                // #13 - Scroll to today (deferred to ensure SVG layout is complete)
                setTimeout(scrollToToday, 300);
            }

            // #3 - Today vertical line
            function drawTodayLine() {
                if (!ganttChart) return;
                try {
                    const svg = document.querySelector('.gantt-target svg');
                    if (!svg) return;
                    const ganttStart = ganttChart.gantt_start;
                    if (!ganttStart) return;

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const diffDays = (today - ganttStart) / (1000 * 60 * 60 * 24);

                    let pxPerDay = 38;

                    // Get actual column width from rendered ticks
                    const ticks = svg.querySelectorAll('.tick');
                    if (ticks.length >= 2) {
                        const x1 = parseFloat(ticks[0].getAttribute('x1') || ticks[0].getAttribute('x'));
                        const x2 = parseFloat(ticks[1].getAttribute('x1') || ticks[1].getAttribute('x'));
                        if (!isNaN(x1) && !isNaN(x2) && x2 > x1) {
                            pxPerDay = x2 - x1;
                            if (currentViewMode === 'Week') pxPerDay = pxPerDay / 7;
                            else if (currentViewMode === 'Month') pxPerDay = pxPerDay / 30;
                        }
                    }

                    const x = diffDays * pxPerDay;
                    const height = svg.getBBox().height || svg.clientHeight || 500;

                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', x);
                    line.setAttribute('x2', x);
                    line.setAttribute('y1', 0);
                    line.setAttribute('y2', height);
                    line.classList.add('gantt-today-line');
                    svg.appendChild(line);

                    // Small label
                    const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    label.setAttribute('x', x + 4);
                    label.setAttribute('y', 14);
                    label.setAttribute('fill', '#e53935');
                    label.setAttribute('font-size', '10');
                    label.setAttribute('font-weight', '600');
                    label.textContent = '{{ __('Today') }}';
                    svg.appendChild(label);
                } catch (e) {
                    /* ignore render errors */
                }
            }

            // #17 - Draw separator lines between projects (original dashed line)
            function drawProjectSeparators(items) {
                if (!ganttChart) return;
                try {
                    const svg = document.querySelector('.gantt-target svg');
                    if (!svg) return;
                    const bars = svg.querySelectorAll('.bar-wrapper');
                    if (bars.length === 0) return;

                    const svgWidth = svg.getBBox().width || svg.clientWidth || 2000;
                    let barIndex = 0;

                    items.forEach((item, idx) => {
                        if (item.type === 'project' && idx > 0) {
                            const barEl = bars[barIndex];
                            if (barEl) {
                                const barG = barEl.querySelector('.bar');
                                if (barG) {
                                    const y = parseFloat(barG.getAttribute('y')) - 10;
                                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                                    line.setAttribute('x1', 0);
                                    line.setAttribute('x2', svgWidth);
                                    line.setAttribute('y1', y);
                                    line.setAttribute('y2', y);
                                    line.classList.add('gantt-separator-line');
                                    svg.appendChild(line);
                                }
                            }
                        }
                        barIndex++;
                    });
                } catch (e) {
                    /* ignore render errors */
                }
            }

            // Helper to get the actual scrollable container (Frappe Gantt creates its own)
            function getScrollContainer() {
                if (ganttChart && ganttChart.$container) return ganttChart.$container;
                // Fallback: Frappe creates a .gantt-container inside .gantt-target
                return document.querySelector('.gantt-target .gantt-container');
            }

            // #13 - Scroll to today
            function scrollToToday() {
                if (!ganttChart) return;
                const container = getScrollContainer();
                if (!container) return;

                // Compute X from Gantt internals
                const opts = ganttChart.options;
                const ganttStart = ganttChart.gantt_start;
                if (!ganttStart || !opts) return;

                const now = new Date();
                now.setHours(0, 0, 0, 0);
                const diffHours = (now - ganttStart) / (1000 * 60 * 60);
                const x = (diffHours / opts.step) * opts.column_width;

                if (isNaN(x) || x <= 0) return;
                if (container.scrollWidth <= container.clientWidth) return;

                // Center today in the viewport
                const targetScroll = Math.max(0, x - container.clientWidth / 2);
                container.scrollLeft = Math.min(targetScroll, container.scrollWidth - container.clientWidth);
            }

            function buildPopup(task) {
                const extra = task.extra || {};

                if (extra.type === 'project') {
                    return `<div class="details-container">
                        <div class="title">${escHtml(task.name)}</div>
                        <div class="subtitle">
                            <b>{{ __('Status') }}:</b> ${escHtml(extra.status)}<br>
                            <b>{{ __('Order Forms') }}:</b> ${extra.milestones_done} / ${extra.milestones_count}<br>
                            <b>{{ __('Progress') }}:</b> ${task.progress}%
                            <div class="hours-row">
                                <b>{{ __('Total logged') }}:</b> ${extra.logged_hours || '00:00'}
                            </div>
                        </div>
                    </div>`;
                }

                if (extra.type === 'milestone') {
                    const statusLabels = {
                        '1': '{{ __('Todo') }}',
                        '2': '{{ __('In_Progress') }}',
                        '3': '{{ __('Review') }}',
                        '4': '{{ __('Done') }}'
                    };
                    const priorityLabels = {
                        'alta': '{{ __('High') }}',
                        'media': '{{ __('Medium') }}',
                        'baja': '{{ __('Low') }}'
                    };
                    const priorityColors = {
                        'alta': '#ef4444',
                        'media': '#fbbf24',
                        'baja': '#34d399'
                    };

                    let badges = '';
                    // #16 - Priority badge
                    if (extra.priority) {
                        const pColor = priorityColors[extra.priority] || '#999';
                        const pLabel = priorityLabels[extra.priority] || escHtml(extra.priority);
                        badges +=
                            `<span class="badge-inline" style="background:${pColor};color:#fff;">${pLabel}</span> `;
                    }
                    // #18 - Waiting badge
                    if (extra.is_waiting) {
                        badges +=
                            `<span class="badge-inline" style="background:#fff7ed;color:#c2410c;border:1px solid #c2410c;"><i class="fa-solid fa-pause" style="font-size:8px;"></i> {{ __('Waiting') }}</span> `;
                    }
                    // #4 - Overdue badge
                    if (extra.is_overdue) {
                        badges +=
                            `<span class="badge-inline" style="background:#ef4444;color:#fff;">{{ __('Overdue') }}</span>`;
                    }

                    let html = `<div class="details-container">
                        <div class="title">${escHtml(task.name.trim())} ${badges}</div>
                        <div class="subtitle">
                            <b>{{ __('Status') }}:</b> ${statusLabels[extra.status] || escHtml(extra.status)}<br>`;
                    // #11 - Formatted dates
                    if (extra.desired_date) html +=
                        `<div class="date-row"><span>{{ __('Desired delivery') }}:</span><span>${formatDate(extra.desired_date)}</span></div>`;
                    if (extra.planned_date) html +=
                        `<div class="date-row"><span>{{ __('Expected delivery') }}:</span><span>${formatDate(extra.planned_date)}</span></div>`;
                    if (extra.task_start_date) html +=
                        `<div class="date-row"><span>{{ __('Task started') }}:</span><span>${formatDate(extra.task_start_date)}</span></div>`;
                    if (extra.finalization_date) html +=
                        `<div class="date-row"><span>{{ __('Completed') }}:</span><span>${formatDate(extra.finalization_date)}</span></div>`;
                    // #8 - Hours counter
                    html += `<div class="hours-row">
                        <b>{{ __('Logged') }}:</b> ${extra.logged_hours || '00:00'}
                        ${extra.estimated_hours && extra.estimated_hours !== '00:00' ? ' / <b>{{ __('Estimated') }}:</b> ' + extra.estimated_hours : ''}
                    </div>`;
                    html += `<b>{{ __('Progress') }}:</b> ${task.progress}%</div></div>`;
                    return html;
                }

                // task
                return `<div class="details-container">
                    <div class="title">${escHtml(task.name.trim())}</div>
                    <div class="subtitle">
                        <b>{{ __('Logged hours') }}:</b> ${extra.logged_hours || '00:00'}<br>
                        <b>{{ __('Progress') }}:</b> ${task.progress}%
                    </div>
                </div>`;
            }

            function escHtml(str) {
                const div = document.createElement('div');
                div.textContent = str || '';
                return div.innerHTML;
            }

            // === EVENT LISTENERS ===

            // View mode buttons (reuse setViewMode for consistency)
            document.querySelectorAll('#change_view .gantt-seg-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = viewModes.indexOf(this.dataset.view);
                    const curIdx = viewModes.indexOf(currentViewMode);
                    setViewMode(this.dataset.view, idx < curIdx ? 'in' : 'out');
                });
            });

            // Level toggle buttons — control mass collapse/expand
            document.querySelectorAll('#level_toggle .gantt-seg-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('#level_toggle .gantt-seg-btn').forEach(b => b.classList
                        .remove('active'));
                    this.classList.add('active');
                    currentLevel = this.dataset.level;
                    localStorage.setItem('ganttLevel', currentLevel);
                    applyLevelCollapses(allData);
                    renderGantt(applyClientFilters(allData));
                    syncFiltersToURL();
                });
            });

            // Server-side filters
            ['filterDateFrom', 'filterDateTo'].forEach(id => {
                document.getElementById(id).addEventListener('change', fetchAndRender);
            });

            // #15 - Fullscreen toggle
            function exitFullscreen() {
                const section = document.getElementById('ganttSection');
                if (section.classList.contains('gantt-fullscreen')) {
                    section.classList.remove('gantt-fullscreen');
                    document.querySelector('#btnFullscreen i').className = 'fa-solid fa-expand';
                    setTimeout(() => {
                        if (ganttChart) {
                            renderGantt(applyClientFilters(allData));
                        }
                    }, 300);
                }
            }

            document.getElementById('btnFullscreen').addEventListener('click', function() {
                const section = document.getElementById('ganttSection');
                section.classList.toggle('gantt-fullscreen');
                const icon = this.querySelector('i');
                if (section.classList.contains('gantt-fullscreen')) {
                    icon.className = 'fa-solid fa-compress';
                } else {
                    icon.className = 'fa-solid fa-expand';
                }
                // Re-render to fit new size
                setTimeout(() => {
                    if (ganttChart) {
                        renderGantt(applyClientFilters(allData));
                    }
                }, 300);
            });

            document.getElementById('btnExitFullscreen').addEventListener('click', exitFullscreen);

            // Toggle sidebar visibility
            document.getElementById('btnToggleSidebar').addEventListener('click', function() {
                sidebarVisible = !sidebarVisible;
                labelsVisibleOverride = null;
                localStorage.removeItem('ganttLabelsOverride');
                applySidebarVisibility();
            });

            document.getElementById('toggleLabels').addEventListener('change', function() {
                labelsVisibleOverride = this.checked;
                localStorage.setItem('ganttLabelsOverride', String(this.checked));
                applyLabelVisibility();
            });

            document.getElementById('togglePercentages').addEventListener('change', function() {
                percentagesVisible = this.checked;
                localStorage.setItem('ganttPercentagesVisible', String(this.checked));
                applyPercentagesVisibility();
            });

            // Cross-highlight: hovering a Gantt bar highlights the sidebar row
            document.addEventListener('mouseenter', function(e) {
                if (!e.target || typeof e.target.closest !== 'function') return;
                const barWrapper = e.target.closest('.bar-wrapper');
                if (!barWrapper) return;
                const id = barWrapper.getAttribute('data-id');
                if (!id) return;
                const sidebarRow = document.querySelector('.gantt-sidebar-row[data-id="' + id + '"]');
                if (sidebarRow) sidebarRow.classList.add('highlighted');
            }, true);
            document.addEventListener('mouseleave', function(e) {
                if (!e.target || typeof e.target.closest !== 'function') return;
                const barWrapper = e.target.closest('.bar-wrapper');
                if (!barWrapper) return;
                const id = barWrapper.getAttribute('data-id');
                if (!id) return;
                const sidebarRow = document.querySelector('.gantt-sidebar-row[data-id="' + id + '"]');
                if (sidebarRow) sidebarRow.classList.remove('highlighted');
            }, true);

            // ESC to exit fullscreen
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') exitFullscreen();
            });

            // #14 - Export to PNG
            document.getElementById('btnExport').addEventListener('click', function() {
                const svg = document.querySelector('.gantt-target svg');
                if (!svg) return;

                const svgData = new XMLSerializer().serializeToString(svg);
                const svgBlob = new Blob([svgData], {
                    type: 'image/svg+xml;charset=utf-8'
                });
                const url = URL.createObjectURL(svgBlob);

                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const scale = 2; // retina quality
                    canvas.width = img.width * scale;
                    canvas.height = img.height * scale;
                    const ctx = canvas.getContext('2d');
                    ctx.scale(scale, scale);
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0);

                    canvas.toBlob(function(blob) {
                        const a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'gantt-diagram-' + new Date().toISOString().slice(0, 10) +
                            '.png';
                        a.click();
                        URL.revokeObjectURL(a.href);
                    }, 'image/png');

                    URL.revokeObjectURL(url);
                };
                img.src = url;
            });

            // === SCROLL ZOOM ===
            const viewModes = ['Day', 'Week', 'Month'];
            const viewModeLabels = {
                'Day': '{{ __('Day') }}',
                'Week': '{{ __('Week') }}',
                'Month': '{{ __('Month') }}'
            };
            let zoomTimer = null;
            let zoomIndicatorTimer = null;

            function showZoomIndicator(mode, direction) {
                const indicator = document.getElementById('zoomIndicator');
                const label = document.getElementById('zoomLabel');
                const icon = indicator.querySelector('.zoom-icon');
                icon.className = direction === 'in' ?
                    'fa-solid fa-magnifying-glass-plus zoom-icon' :
                    'fa-solid fa-magnifying-glass-minus zoom-icon';
                label.textContent = viewModeLabels[mode] || mode;
                indicator.classList.add('visible');
                clearTimeout(zoomIndicatorTimer);
                zoomIndicatorTimer = setTimeout(() => indicator.classList.remove('visible'), 900);
            }

            function setViewMode(newMode, direction) {
                if (newMode === currentViewMode) return;
                currentViewMode = newMode;
                localStorage.setItem('ganttViewMode', currentViewMode);

                // Sync buttons
                document.querySelectorAll('#change_view .gantt-seg-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.view === currentViewMode);
                });

                showZoomIndicator(currentViewMode, direction);

                renderGantt(applyClientFilters(allData));

                syncFiltersToURL();
            }

            document.getElementById('ganttContainer').addEventListener('wheel', function(e) {
                if (!e.ctrlKey && !e.metaKey) return; // Only zoom with Ctrl/Cmd held
                e.preventDefault();

                clearTimeout(zoomTimer);
                zoomTimer = setTimeout(() => {
                    const currentIdx = viewModes.indexOf(currentViewMode);
                    let newIdx;

                    if (e.deltaY < 0) {
                        // Scroll up / pinch out = zoom IN (more detail)
                        newIdx = Math.max(0, currentIdx - 1);
                    } else {
                        // Scroll down / pinch in = zoom OUT (less detail)
                        newIdx = Math.min(viewModes.length - 1, currentIdx + 1);
                    }

                    if (newIdx !== currentIdx) {
                        setViewMode(viewModes[newIdx], e.deltaY < 0 ? 'in' : 'out');
                    }
                }, 80);
            }, {
                passive: false
            });

            // === INIT ===
            applyLabelVisibility();
            loadFiltersFromURL();
            fetchAndRender();
        })();
    </script>
@endpush
