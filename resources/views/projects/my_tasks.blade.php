@extends('layouts.admin')
@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
    $currentUser = Auth::user();
@endphp
@section('page-title')
    {{ __('My Tasks') }}
@endsection

@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('My Tasks') }}</li>
@endsection

@push('css-page')
    <style>
        .my-tasks-wrap {
            --mt-accent: #b6122e;
            --mt-accent-soft: #ffe7ec;
            --mt-bg: #fff7f9;
            --mt-border: #efd5dc;
            --mt-text-strong: #4a1421;
            --mt-text-muted: #7c5a63;
            --mt-danger: #b42318;
            --mt-warning: #b54708;
        }

        .my-tasks-hero {
            border-radius: 16px;
            padding: 18px;
            background: #ffffff;
            box-shadow: 0 4px 14px rgba(74, 20, 33, 0.06);
            margin-bottom: 16px;
        }

        .my-tasks-hero h4 {
            color: var(--mt-text-strong);
            margin-bottom: 4px;
            font-weight: 700;
        }

        .my-tasks-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .my-tasks-help-icon {
            width: 20px;
            cursor: pointer;
            margin-top: -1px;
            transition: transform 0.1s ease-in-out;
        }

        .my-tasks-help-popup {
            opacity: 0;
            background-color: #f9fbfa;
            border: 2px solid transparent;
            border-radius: 15px;
            width: 410px;
            max-width: calc(100vw - 40px);
            position: absolute;
            top: 34px;
            left: 0;
            z-index: 6;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 14px 16px;
            filter: drop-shadow(1px 1px 5px #b1b1b1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            visibility: hidden;
        }

        .my-tasks-help-popup strong {
            color: #2c1a1f;
        }

        .my-tasks-help-popup p {
            margin: 0;
            color: #4b4244;
            font-size: 13px;
            line-height: 1.4;
        }

        .my-tasks-hero p {
            margin: 0;
            color: var(--mt-text-muted);
        }

        .my-tasks-kpi {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .my-tasks-controls {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .my-tasks-segmented {
            display: inline-flex;
            gap: 6px;
            border: 1px solid var(--mt-border);
            border-radius: 999px;
            padding: 4px;
            background: #fff;
        }

        .my-tasks-segmented-btn {
            border: 0;
            background: transparent;
            color: #496264;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
            transition: all .18s ease;
        }

        .my-tasks-segmented-btn:hover {
            background: #f2f7f7;
            color: var(--mt-text-strong);
        }

        .backgroundWhite{
            background-color: #ffffff !important;
        }
        .my-tasks-segmented-btn.is-active {
            background: var(--mt-accent);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(182, 18, 46, 0.22);
        }

        .my-tasks-period {
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
        }

        .my-tasks-period-btn {
            border: 1px solid #bf1835;
            background: #ffffff;
            color: #7b1528;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
            transition: all .18s ease;
        }

        .my-tasks-period-btn:hover {
            border-color: #bf1835;
            background: #fff0f4;
            color: #7b1528;
        }

        .my-tasks-period-btn.is-active {
            border-color: #b6122e;
            background: #b6122e;
            color: #fff;
            box-shadow: 0 2px 8px rgba(182, 18, 46, 0.22);
        }

        .my-tasks-kpi-card {
            border: 1px solid lightgray;
            border-radius: 12px;
            background: #fff;
            padding: 12px;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .my-tasks-kpi-card[data-logged-range] {
            cursor: pointer;
        }

        .my-tasks-kpi-card[data-logged-range]:hover {
            border-color: #e7c6cf;
            box-shadow: 0 4px 12px rgba(74, 20, 33, 0.08);
            transform: translateY(-1px);
        }

        /* .my-tasks-kpi-card:hover {
            border-color: #e7c6cf;
            box-shadow: 0 4px 12px rgba(74, 20, 33, 0.08);
        } */

        .my-tasks-kpi-label {
            color: var(--mt-text-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .my-tasks-kpi-value {
            font-size: 24px;
            line-height: 1.1;
            font-weight: 700;
            color: var(--mt-text-strong);
        }

        .my-tasks-card {
            border-radius: 16px;
            border: 1px solid var(--mt-border);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(74, 20, 33, 0.05);
        }

        .my-tasks-card .card-body {
            padding: 12px 0 48px;
        }

        .my-tasks-table-shell {
            border-radius: 14px;
            padding: 0 36px 4px;
            width: 100%;
        }

        .my-tasks-table-scroll {
            max-height: 350px;
            overflow: auto;
            scrollbar-gutter: stable;
        }

        .my-tasks-table-inner {
            min-width: 100%;
            width: max-content;
            zoom: 0.9;
        }

        @media (max-width: 1920px) {
            .my-tasks-table-inner { zoom: .80; }
        }
        @media (max-width: 1600px) {
            .my-tasks-table-inner { zoom: .75; }
        }
        @media (max-width: 1440px) {
            .my-tasks-table-inner { zoom: .70; }
        }
        @media (max-width: 1366px) {
            .my-tasks-table-inner { zoom: .70; }
        }
        @media (max-width: 1280px) {
            .my-tasks-table-inner { zoom: .65; }
        }

        .my-tasks-header-row {
            display: flex;
            position: sticky;
            z-index: 2;
            background: #f8f9fd;
            border-bottom: 2px solid #f8f9fd;
            top: 0;
            width: max-content;
            min-width: 100%;
            gap: 8px;
        }

        .my-tasks-th {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 16px 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #000000;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .my-tasks-th-content {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .my-tasks-body-rows {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 12px;
            width: 100%;
        }

        .my-tasks-body-row {
            display: flex;
            border: 1px solid #f0dde2;
            border-radius: 12px;
            background: #ffffff;
            transition: all .18s ease;
            gap: 8px;
            cursor: pointer;
            width: max-content;
            min-width: 100%;
        }

        .my-tasks-body-row:hover {
            border-color: #b6122e;
            background: #fff0f4;
            color: #7b1528;
            transform: translateY(-1px);
        }

        .my-tasks-body-row:focus-visible {
            outline: 0;
            box-shadow: inset 0 0 0 2px rgba(182, 18, 46, 0.18);
        }

        .my-tasks-td {
            padding: 12px 8px;
            flex-shrink: 0;
        }

        .my-tasks-th[data-col-key="project"],
        .my-tasks-td[data-col-key="project"] { flex: 3; min-width: 150px; overflow: hidden; display: flex; align-items: center; }

        .my-tasks-td[data-col-key="project"] .my-tasks-main,
        .my-tasks-td[data-col-key="milestone"] .my-tasks-main {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .my-tasks-th[data-col-key="milestone"],
        .my-tasks-td[data-col-key="milestone"] { flex: 3; min-width: 150px; overflow: hidden; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="stage"],
        .my-tasks-td[data-col-key="stage"] { flex: 1.5; min-width: 95px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="phase"],
        .my-tasks-td[data-col-key="phase"] { flex: 1.5; min-width: 95px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="task"],
        .my-tasks-td[data-col-key="task"] { flex: none; width: 100px; min-width: 100px; overflow: hidden; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="start_date"],
        .my-tasks-td[data-col-key="start_date"] { flex: 1.5; min-width: 95px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="estimated_date"],
        .my-tasks-td[data-col-key="estimated_date"] { flex: 1.5; min-width: 95px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="finalization_date"],
        .my-tasks-td[data-col-key="finalization_date"] { flex: 1.5; min-width: 95px; display: flex; align-items: center; }

        .my-tasks-filter-btn {
            width: 24px;
            height: 24px;
            border: 1px solid transparent;
            border-radius: 8px;
            background: transparent;
            color: #8f6a73;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .18s ease;
            padding: 0;
            flex-shrink: 0;
        }

        .my-tasks-filter-btn:hover {
            background: #ffe8ee;
            border-color: #efc6d1;
            color: #7b1528;
        }

        .my-tasks-filter-btn.is-active {
            background: #b6122e;
            border-color: #b6122e;
            color: #fff;
        }

        .my-tasks-header-tools {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .my-tasks-column-toggle-btn {
            min-height: 36px;
            border: 1px solid #efc6d1;
            border-radius: 10px;
            background: linear-gradient(180deg, #fff 0%, #fff7f9 100%);
            color: #6f1830;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .18s ease;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .my-tasks-column-toggle-btn:hover {
            border-color: #b6122e;
            background: #fff0f4;
            color: #7b1528;
            transform: translateY(-1px);
        }

        .my-tasks-column-toggle-btn:focus-visible {
            outline: 0;
            border-color: #b6122e;
            box-shadow: 0 0 0 3px rgba(182, 18, 46, 0.15);
        }

        .my-tasks-column-toggle-btn.is-active {
            border-color: #b6122e;
            background: #b6122e;
            color: #fff;
            box-shadow: 0 2px 8px rgba(182, 18, 46, 0.22);
        }

        .my-tasks-column-toggle-icon {
            width: 15px;
            height: 15px;
            fill: currentColor;
        }

        .my-tasks-column-toggle-label {
            white-space: nowrap;
            line-height: 1;
        }

        .my-tasks-column-toggle-count {
            min-width: 28px;
            height: 20px;
            border-radius: 999px;
            border: 1px solid #efc6d1;
            background: #fff;
            color: #8f6a73;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            padding: 0 6px;
            line-height: 1;
        }

        .my-tasks-column-toggle-btn.is-active .my-tasks-column-toggle-count {
            border-color: rgba(255, 255, 255, 0.42);
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
        }

        .my-tasks-column-menu {
            position: fixed;
            z-index: 1200;
            width: 260px;
            max-width: calc(100vw - 24px);
            background: #fff;
            border: 1px solid #efc6d1;
            border-radius: 14px;
            box-shadow: 0 18px 40px rgba(64, 24, 33, 0.16);
            padding: 14px;
        }

        .my-tasks-column-menu[hidden] {
            display: none;
        }

        .my-tasks-column-menu-title {
            margin: 0;
            color: #4a1421;
            font-size: 13px;
            font-weight: 700;
        }

        .my-tasks-filter-icon {
            width: 14px;
            height: 14px;
            display: block;
            fill: currentColor;
        }

        .my-tasks-filter-menu {
            position: fixed;
            z-index: 1200;
            width: 260px;
            max-width: calc(100vw - 24px);
            background: #fff;
            border: 1px solid #efc6d1;
            border-radius: 14px;
            box-shadow: 0 18px 40px rgba(64, 24, 33, 0.16);
            padding: 14px;
        }

        .my-tasks-filter-menu[hidden] {
            display: none;
        }

        .my-tasks-filter-menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }

        .my-tasks-filter-menu-title {
            margin: 0;
            color: #4a1421;
            font-size: 13px;
            font-weight: 700;
        }

        .my-tasks-filter-menu-actions {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .my-tasks-filter-link {
            border: 0;
            background: transparent;
            color: #8a1b32;
            font-size: 12px;
            font-weight: 600;
            padding: 0;
        }

        .my-tasks-filter-link:hover {
            color: #b6122e;
        }

        .my-tasks-filter-search {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 10px;
            padding: 8px 10px;
            color: #2c1a1f;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .my-tasks-filter-search:focus {
            outline: 0;
            border-color: #b6122e;
            box-shadow: 0 0 0 3px rgba(182, 18, 46, 0.12);
        }

        .my-tasks-filter-options {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 280px;
            overflow: auto;
        }

        .my-tasks-filter-option {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #4d4d4d;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .my-tasks-filter-option:hover {
            background: #fff5f7;
        }

        .my-tasks-filter-option input[type='checkbox'] {
            accent-color: #aa182c;
            cursor: pointer;
        }

        .my-tasks-filter-option span:first-of-type {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .my-tasks-filter-option-count {
            margin-left: auto;
            color: #8f6a73;
            font-size: 11px;
        }

        .my-tasks-filter-empty {
            margin: 0;
            color: #8f6a73;
            font-size: 12px;
            text-align: center;
            padding: 8px 0;
        }

        .my-tasks-filtered-empty-state {
            display: none;
            border: 1px dashed var(--mt-border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            color: var(--mt-text-muted);
            background: #fbfefe;
            margin-top: 10px;
        }

        .my-tasks-filtered-empty-state.is-visible {
            display: block;
        }

        .task-id-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
            background: var(--mt-accent-soft);
            color: #7b1528;
        }

        .my-tasks-main {
            font-weight: 600;
            color: #2c1a1f;
            line-height: 1.2;
        }

        .my-tasks-truncate-project,
        .my-tasks-truncate-milestone,
        .my-tasks-truncate-task {
            display: block;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1400px) {
            .my-tasks-truncate-project,
            .my-tasks-truncate-milestone,
            .my-tasks-truncate-task {
                max-width: 90px;
            }
        }

        .my-tasks-caption {
            font-size: 12px;
            color: #8b6a73;
            margin-top: 2px;
        }

        .task-date {
            font-weight: 600;
            color: #2f3f40;
            white-space: nowrap;
        }

        .task-date.on-time,
        .project-task-meta-value.on-time {
            color: #067647;
        }

        .task-date.overdue,
        .project-task-meta-value.overdue {
            color: var(--mt-danger);
        }

        .task-date.soon {
            color: var(--mt-warning);
        }

        .task-secondary {
            font-size: 12px;
            color: var(--mt-text-muted);
        }

        .my-tasks-placeholder {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px dashed #d5dbe4;
            background: #f6f8fb;
            color: #6b7280 !important;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
        }

        .my-tasks-placeholder--not-required {
            border-color: #e7c6cf;
            background: #fff2f6;
            color: #9d2f46 !important;
        }

        .my-tasks-chart-wrap {
            position: relative;
            min-height: 360px;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 8px;
        }

        .my-tasks-chart-wrap canvas {
            min-height: 320px;
        }

        .my-tasks-chart-wrap canvas.is-interactive {
            cursor: pointer;
        }

        .my-tasks-visual-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0dde2;
        }

        .my-tasks-visual-eyebrow {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #8f6a73;
            margin-bottom: 2px;
            font-weight: 700;
        }

        .my-tasks-visual-title {
            margin: 0;
            color: #4a1421;
            font-size: 34px;
            font-weight: 700;
            line-height: 1;
        }

        .my-tasks-visual-total {
            text-align: right;
        }

        .my-tasks-visual-total .label {
            display: block;
            font-size: 11px;
            color: #8f6a73;
            margin-bottom: 1px;
        }

        .my-tasks-visual-total .value {
            font-size: 44px;
            line-height: 1;
            font-weight: 800;
            color: #b6122e;
        }

        #diagramViewCard {
            background: #faf7f8;
            border-color: #ead6dc;
        }

        .my-tasks-chart-empty {
            min-height: 280px;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            border: 1px dashed var(--mt-border);
            border-radius: 12px;
            background: #fbfefe;
            color: var(--mt-text-muted);
            padding: 16px;
        }

        .my-tasks-chart-empty.is-visible {
            display: flex;
        }

        .project-task-modal .modal-content {
            border-radius: 14px;
            border: 1px solid var(--mt-border);
        }

        .project-task-modal .modal-header {
            background: #ffffff;
            border-bottom: 1px solid lightgray;
        }

        .project-task-list {
            display: grid;
            gap: 10px;
            max-height: 62vh;
            overflow: auto;
            padding-right: 2px;
        }

        .project-task-item {
            border: 1px solid lightgray;
            border-radius: 12px;
            padding: 10px 12px;
            background: #fff;
        }

        .project-task-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .project-task-item-title {
            color: var(--mt-text-strong);
            font-weight: 700;
            margin: 0;
            font-size: 14px;
        }

        .project-task-item-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .project-task-item-grid.is-logged-summary {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .project-task-meta-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #8f6a73;
            margin-bottom: 2px;
        }

        .project-task-meta-value {
            color: #2f3f40;
            font-size: 13px;
            font-weight: 600;
        }

        .project-task-empty {
            border: 1px dashed var(--mt-border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            color: var(--mt-text-muted);
            background: #fbfefe;
        }

        @media (max-width: 991px) {
            .my-tasks-kpi {
                grid-template-columns: 1fr;
            }

            .my-tasks-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .my-tasks-segmented,
            .my-tasks-period {
                width: 100%;
            }

            .my-tasks-segmented-btn,
            .my-tasks-period-btn {
                flex: 1;
                text-align: center;
            }

            .project-task-item-grid {
                grid-template-columns: 1fr;
            }

            .my-tasks-body-rows {
                gap: 6px;
                padding-top: 8px;
            }

            .my-tasks-th,
            .my-tasks-td {
                padding: 10px 6px;
            }

            .my-tasks-column-toggle-btn {
                padding: 0 10px;
            }

            .my-tasks-column-toggle-label {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $taskCollection = $tasks ?? collect();
        $today = \Carbon\Carbon::today();
        $weekEnd = \Carbon\Carbon::today()->copy()->addDays(7);
        $projectTypesWithStageAndPhase = [3, 5];
        $notApplicableStagePhaseText = __('Not required');
        $naText = __('N/A');
        $showStageColumn = $taskCollection->contains(function ($task) use ($projectTypesWithStageAndPhase, $notApplicableStagePhaseText, $naText) {
            $supportsStageAndPhase = in_array((int) optional($task->project)->type, $projectTypesWithStageAndPhase, true);
            $resolvedStageName = optional($task->milestone)->resolved_stage_name;
            $stageName = $supportsStageAndPhase
                ? ($resolvedStageName ?: $naText)
                : $notApplicableStagePhaseText;

            return $stageName !== $notApplicableStagePhaseText;
        });
        $showPhaseColumn = $taskCollection->contains(function ($task) use ($projectTypesWithStageAndPhase, $notApplicableStagePhaseText, $naText) {
            $supportsStageAndPhase = in_array((int) optional($task->project)->type, $projectTypesWithStageAndPhase, true);
            $phaseValue = optional(optional($task->milestone)->phase)->phases;
            $phaseName = $supportsStageAndPhase
                ? ($phaseValue ? __(\App\Models\MilestonePhases::translationKey($phaseValue)) : $naText)
                : $notApplicableStagePhaseText;

            return $phaseName !== $notApplicableStagePhaseText;
        });
        $visibleOptionalColumns = (int) $showStageColumn + (int) $showPhaseColumn;
        $stageColumnIndex = $showStageColumn ? 2 : null;
        $phaseColumnIndex = $showPhaseColumn ? 2 + ((int) $showStageColumn) : null;
        $taskColumnIndex = 2 + $visibleOptionalColumns;
        $startDateColumnIndex = 3 + $visibleOptionalColumns;
        $estimatedDateColumnIndex = 4 + $visibleOptionalColumns;
        $finalizationDateColumnIndex = 5 + $visibleOptionalColumns;
    @endphp

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="my-tasks-wrap mt-3">
                    <div class="my-tasks-hero">
                        <div class="my-tasks-title-row">
                            <h4 class="mb-0">{{ __('Task Overview') }}</h4>
                            <img class="my-tasks-help-icon" id="taskOverviewHelpIcon" src="{{ asset('assets/img/questionCircle.svg') }}"
                                alt="{{ __('Task Overview info') }}" title="{{ __('Task Overview info') }}" />
                            <div class="my-tasks-help-popup" id="taskOverviewHelpPopup">
                                <p>{!! __('In <strong>Diagram View</strong>, the tasks shown respect the task\'s creation date.') !!}</p>
                                <p>{!! __('<strong>N/A</strong> means there is no value for that field.') !!}</p>
                            </div>
                        </div>
                        <p>{{ __('Quick view of your assigned work and upcoming delivery dates.') }}</p>

                        <div class="my-tasks-kpi">
                            <div class="my-tasks-kpi-card">
                                <div class="my-tasks-kpi-label">{{ __('Total assigned') }}</div>
                                <div class="my-tasks-kpi-value">{{ $taskCollection->count() }}</div>
                            </div>
                            <div class="my-tasks-kpi-card" data-logged-range="weekly" role="button" tabindex="0">
                                <div class="my-tasks-kpi-label">{{ __('Logged this week') }}</div>
                                <div class="my-tasks-kpi-value">{{ $loggedTasksThisWeekCount ?? 0 }}</div>
                            </div>
                            <div class="my-tasks-kpi-card" data-logged-range="thirty_days" role="button" tabindex="0">
                                <div class="my-tasks-kpi-label">{{ __('Logged in 30 days') }}</div>
                                <div class="my-tasks-kpi-value">{{ $loggedTasksLastThirtyDaysCount ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="my-tasks-controls">
                            <div class="my-tasks-segmented" id="overviewViewMode">
                                <button type="button" class="my-tasks-segmented-btn is-active" data-view-mode="table">
                                    {{ __('Table view') }}
                                </button>
                                <button type="button" class="my-tasks-segmented-btn" data-view-mode="diagram">
                                    {{ __('Diagram view') }}
                                </button>
                            </div>

                            <div class="my-tasks-period" id="diagramPeriod" style="display: none;">
                                <button type="button" class="my-tasks-period-btn is-active" data-period="weekly">{{ __('Last week') }}</button>
                                <button type="button" class="my-tasks-period-btn" data-period="monthly">{{ __('Last month') }}</button>
                                <button type="button" class="my-tasks-period-btn" data-period="quarterly">{{ __('Last quarter') }}</button>
                                <button type="button" class="my-tasks-period-btn" data-period="yearly">{{ __('Last year') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="card my-tasks-card" id="tableViewCard">
                        <div class="card-header d-flex  align-items-center flex-wrap gap-2">
                            <h5 class="mb-0">{{ __('Assigned tasks') }}</h5>
                            <div class="my-tasks-header-tools">
                                <button type="button" id="myTasksColumnsToggleBtn" class="my-tasks-column-toggle-btn" aria-label="{{ __('Show or hide table columns') }}" title="{{ __('Show or hide table columns') }}" aria-expanded="false">
                                    <svg class="my-tasks-column-toggle-icon" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M8 3.2c3.3 0 5.8 2.3 6.9 4.8-1.1 2.5-3.6 4.8-6.9 4.8S2.2 10.5 1.1 8C2.2 5.5 4.7 3.2 8 3.2Zm0 1.2c-2.6 0-4.7 1.7-5.8 3.6 1.1 1.9 3.2 3.6 5.8 3.6s4.7-1.7 5.8-3.6c-1.1-1.9-3.2-3.6-5.8-3.6Zm0 1.4a2.2 2.2 0 1 1 0 4.4 2.2 2.2 0 0 1 0-4.4Zm0 1.2a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z"></path>
                                    </svg>
                                    <span class="my-tasks-column-toggle-count" id="myTasksColumnsToggleCount">0/0</span>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            @if ($taskCollection->isEmpty())
                            <div class="text-center py-4">
                                <h6 class="mb-2">{{ __('No tasks assigned') }}</h6>
                                <p class="text-muted mb-0">{{ __('You currently do not have assigned tasks.') }}</p>
                            </div>
                        @else
                            <div class="my-tasks-table-shell">
                                <div class="my-tasks-table-scroll">
                                <div class="my-tasks-table-inner">
                                    <div class="my-tasks-header-row">
                                        <div class="my-tasks-th" data-col-key="project">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Project') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="project" data-filter-label="{{ __('Project') }}" data-column-index="0" aria-label="{{ __('Filter Project') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="milestone">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Milestone') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="milestone" data-filter-label="{{ __('Milestone') }}" data-column-index="1" aria-label="{{ __('Filter Milestone') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @if ($showStageColumn)
                                        <div class="my-tasks-th" data-col-key="stage">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Stage') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="stage" data-filter-label="{{ __('Stage') }}" data-column-index="{{ $stageColumnIndex }}" aria-label="{{ __('Filter Stage') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($showPhaseColumn)
                                        <div class="my-tasks-th" data-col-key="phase">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Phase') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="phase" data-filter-label="{{ __('Phase') }}" data-column-index="{{ $phaseColumnIndex }}" aria-label="{{ __('Filter Phase') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="my-tasks-th" data-col-key="task">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Task') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="task" data-filter-label="{{ __('Task') }}" data-column-index="{{ $taskColumnIndex }}" aria-label="{{ __('Filter Task') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="start_date">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Start date') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="start_date" data-filter-label="{{ __('Start date') }}" data-column-index="{{ $startDateColumnIndex }}" aria-label="{{ __('Filter Start date') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="estimated_date">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Estimated date') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="estimated_date" data-filter-label="{{ __('Estimated date') }}" data-column-index="{{ $estimatedDateColumnIndex }}" aria-label="{{ __('Filter Estimated date') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="finalization_date">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Finalization date') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="finalization_date" data-filter-label="{{ __('Finalization date') }}" data-column-index="{{ $finalizationDateColumnIndex }}" aria-label="{{ __('Filter Finalization date') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-tasks-body-rows">
                                        @foreach ($tasks as $task)
                                            @php
                                                $taskTypeName = optional($task->type)->name;
                                                $isCustomType = strtolower(trim((string) $taskTypeName)) === 'custom';
                                                $displayTypeName = $isCustomType
                                                    ? (optional($task->customTask)->name ?: __('Custom'))
                                                    : ($taskTypeName ? __($taskTypeName) : __('N/A'));

                                                $estimatedDate = !empty($task->estimated_date)
                                                    ? \Carbon\Carbon::parse($task->estimated_date)
                                                    : null;

                                                $finalizationDate = !empty($task->end_date)
                                                    ? \Carbon\Carbon::parse($task->end_date)
                                                    : null;

                                                $finalizationDateClass = 'task-date';
                                                if ($estimatedDate && $finalizationDate) {
                                                    $finalizationDateClass .= $finalizationDate->lte($estimatedDate)
                                                        ? ' on-time'
                                                        : ' overdue';
                                                }

                                                $projectName = optional($task->project)->name ?: $naText;
                                                $milestoneTitle = optional($task->milestone)->title ?: $naText;
                                                $startDateText = $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') : $naText;
                                                $estimatedDateText = $task->estimated_date ? \Carbon\Carbon::parse($task->estimated_date)->format('d/m/Y') : $naText;
                                                $finalizationDateText = $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') : $naText;

                                                $placeholderClass = 'my-tasks-placeholder';

                                                $projectTypeId = (int) optional($task->project)->type;
                                                $supportsStageAndPhase = in_array($projectTypeId, $projectTypesWithStageAndPhase, true);
                                                $resolvedStageName = optional($task->milestone)->resolved_stage_name;
                                                $stageName = $supportsStageAndPhase
                                                    ? ($resolvedStageName ?: __('N/A'))
                                                    : $notApplicableStagePhaseText;
                                                $phaseValue = optional(optional($task->milestone)->phase)->phases;
                                                $phaseName = $supportsStageAndPhase
                                                    ? ($phaseValue ? __(\App\Models\MilestonePhases::translationKey($phaseValue)) : __('N/A'))
                                                    : $notApplicableStagePhaseText;

                                                $projectClass = $projectName === $naText ? $placeholderClass : '';
                                                $milestoneClass = $milestoneTitle === $naText ? $placeholderClass : '';
                                                $taskTypeClass = $displayTypeName === $naText ? $placeholderClass : 'my-tasks-main';
                                                $notRequiredClass = 'my-tasks-placeholder my-tasks-placeholder--not-required';
                                                $stageClass = $stageName === $notApplicableStagePhaseText
                                                    ? $notRequiredClass
                                                    : ($stageName === $naText ? $placeholderClass : 'my-tasks-main');
                                                $phaseClass = $phaseName === $notApplicableStagePhaseText
                                                    ? $notRequiredClass
                                                    : ($phaseName === $naText ? $placeholderClass : 'my-tasks-main');
                                                $startDateClass = $startDateText === $naText ? $placeholderClass : 'task-date';
                                                $estimatedDateClass = $estimatedDateText === $naText ? $placeholderClass : 'task-date';
                                                $finalizationRenderClass = $finalizationDateText === $naText ? $placeholderClass : $finalizationDateClass;
                                                $hasTimesheetAction = !empty($task->timesheet_edit_url) && !empty($task->timesheet_edit_date);
                                            @endphp
                                            <div class="my-tasks-body-row"
                                                @if ($hasTimesheetAction)
                                                    data-timesheet-edit-url="{{ $task->timesheet_edit_url }}"
                                                    data-timesheet-edit-date="{{ $task->timesheet_edit_date }}"
                                                    data-timesheet-task-id="{{ $task->id }}"
                                                    data-timesheet-mode="{{ $task->timesheet_action_mode }}"
                                                    data-timesheet-title="{{ $task->timesheet_action_title }}"
                                                    tabindex="0"
                                                    role="button"
                                                    aria-label="{{ $task->timesheet_action_title }}"
                                                @endif
                                            >
                                                <div class="my-tasks-td" data-col-key="project">
                                                    <div class="my-tasks-main my-tasks-truncate-project {{ $projectClass }}" title="{{ $projectName }}">{{ $projectName }}</div>
                                                </div>
                                                <div class="my-tasks-td" data-col-key="milestone">
                                                    <div class="my-tasks-main my-tasks-truncate-milestone {{ $milestoneClass }}" title="{{ $milestoneTitle }}">{{ $milestoneTitle }}</div>
                                                </div>
                                                @if ($showStageColumn)
                                                <div class="my-tasks-td" data-col-key="stage">
                                                    <span class="{{ $stageClass }}">{{ $stageName }}</span>
                                                </div>
                                                @endif
                                                @if ($showPhaseColumn)
                                                <div class="my-tasks-td" data-col-key="phase">
                                                    <span class="{{ $phaseClass }}">{{ $phaseName }}</span>
                                                </div>
                                                @endif
                                                <div class="my-tasks-td" data-col-key="task">
                                                    <div class="my-tasks-truncate-task" title="{{ $displayTypeName }}">
                                                        <span class="{{ $taskTypeClass }}">{{ $displayTypeName }}</span>
                                                    </div>
                                                </div>
                                                <div class="my-tasks-td" data-col-key="start_date">
                                                    <span class="{{ $startDateClass }}">{{ $startDateText }}</span>
                                                </div>
                                                <div class="my-tasks-td" data-col-key="estimated_date">
                                                    <span class="{{ $estimatedDateClass }}">{{ $estimatedDateText }}</span>
                                                </div>
                                                <div class="my-tasks-td" data-col-key="finalization_date">
                                                    <span class="{{ $finalizationRenderClass }}">{{ $finalizationDateText }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="myTasksFilteredEmptyState" class="my-tasks-filtered-empty-state">
                                        <h6 class="mb-2">{{ __('No tasks match the selected filters') }}</h6>
                                        <p class="mb-0">{{ __('Adjust or clear filters to see more results.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        </div>
                    </div>

                    <div class="card my-tasks-card mt-3" id="diagramViewCard" style="display: none;">
                        <div class="backgroundWhite card-body">
                            <div class="my-tasks-visual-header">
                                <div>
                                    <h5 class="my-tasks-visual-title">{{ __('Tasks By Project') }}</h5>
                                </div>
                                <div class="my-tasks-visual-total">
                                    <span id="diagramRangeLabel" class="label">{{ __('Last year') }}</span>
                                    <span id="diagramTotalValue" class="value">0</span>
                                </div>
                            </div>
                            <div class="my-tasks-chart-wrap">
                                <canvas id="myTasksProjectChart"></canvas>
                                <div id="myTasksChartEmpty" class="my-tasks-chart-empty">
                                    <h6 class="mb-2">{{ __('No tasks created in this period') }}</h6>
                                    <p class="mb-0">{{ __('Try another period to view projects with recently created tasks.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade project-task-modal" id="projectTasksModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title mb-0" id="projectTasksModalTitle">{{ __('Project tasks') }}</h5>
                                        <small class="text-muted" id="projectTasksModalPeriod">{{ __('Range') }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="projectTasksModalContent" class="project-task-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const viewModeContainer = document.getElementById('overviewViewMode');
            const viewModeButtons = Array.from(viewModeContainer.querySelectorAll('[data-view-mode]'));
            const periodContainer = document.getElementById('diagramPeriod');
            const periodButtons = Array.from(periodContainer.querySelectorAll('[data-period]'));
            const loggedTaskCards = Array.from(document.querySelectorAll('[data-logged-range]'));
            const tableViewCard = document.getElementById('tableViewCard');
            const diagramViewCard = document.getElementById('diagramViewCard');
            const diagramRangeLabel = document.getElementById('diagramRangeLabel');
            const diagramTotalValue = document.getElementById('diagramTotalValue');
            const chartEmptyState = document.getElementById('myTasksChartEmpty');
            const chartCanvas = document.getElementById('myTasksProjectChart');
            const projectTasksModalElement = document.getElementById('projectTasksModal');
            const taskOverviewHelpIcon = document.getElementById('taskOverviewHelpIcon');
            const taskOverviewHelpPopup = document.getElementById('taskOverviewHelpPopup');
            const projectTasksModalTitle = document.getElementById('projectTasksModalTitle');
            const projectTasksModalPeriod = document.getElementById('projectTasksModalPeriod');
            const projectTasksModalContent = document.getElementById('projectTasksModalContent');
            const columnToggleButton = document.getElementById('myTasksColumnsToggleBtn');
            const taskTable = document.querySelector('.my-tasks-table-inner');
            const taskTableRows = taskTable ? Array.from(taskTable.querySelectorAll('.my-tasks-body-row')) : [];
            const timesheetTableRows = taskTable ? Array.from(taskTable.querySelectorAll('.my-tasks-body-row[data-timesheet-edit-url]')) : [];
            const taskTableHeaders = taskTable ? Array.from(taskTable.querySelectorAll('.my-tasks-header-row .my-tasks-th')) : [];
            const tableFilterButtons = taskTable ? Array.from(taskTable.querySelectorAll('.my-tasks-filter-btn')) : [];
            const filteredEmptyState = document.getElementById('myTasksFilteredEmptyState');
            const projectTasksModal = (window.bootstrap && projectTasksModalElement)
                ? new window.bootstrap.Modal(projectTasksModalElement)
                : null;

            const diagramDataByPeriod = @json($diagramDataByPeriod ?? []);
            const loggedTaskDetailsByRange = @json($loggedTaskDetailsByRange ?? []);
            const defaultDiagramPeriod = @json($defaultDiagramPeriod ?? 'weekly');
            const naLabel = "{{ __('N/A') }}";
            const notRequiredLabel = "{{ __('Not required') }}";

            let myTasksChart = null;
            let selectedMode = 'table';
            let selectedPeriod = defaultDiagramPeriod;
            const tableFilterState = {};
            let activeFilterButton = null;
            const columnVisibilityState = new Map();
            const filterMenu = document.createElement('div');
            const columnMenu = document.createElement('div');

            filterMenu.className = 'my-tasks-filter-menu';
            filterMenu.hidden = true;
            document.body.appendChild(filterMenu);

            columnMenu.className = 'my-tasks-column-menu';
            columnMenu.hidden = true;
            document.body.appendChild(columnMenu);

            function setActiveButton(buttons, activeValue, attrName) {
                buttons.forEach(function(btn) {
                    const isActive = btn.getAttribute(attrName) === activeValue;
                    btn.classList.toggle('is-active', isActive);
                });
            }

            function getPeriodLabel(period) {
                const labels = {
                    weekly: "{{ __('Last week') }}",
                    monthly: "{{ __('Last month') }}",
                    quarterly: "{{ __('Last quarter') }}",
                    yearly: "{{ __('Last year') }}",
                };
                return labels[period] || "{{ __('Range') }}";
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderDisplayValue(value, baseClass) {
                const text = (value === null || value === undefined || value === '') ? naLabel : String(value);
                const cssClasses = [baseClass || ''];

                if (text === notRequiredLabel) {
                    cssClasses.push('my-tasks-placeholder', 'my-tasks-placeholder--not-required');
                } else if (text === naLabel) {
                    cssClasses.push('my-tasks-placeholder');
                }

                return '<span class="' + cssClasses.filter(Boolean).join(' ') + '">' + escapeHtml(text) + '</span>';
            }

            function getTableCellValue(row, columnIndex) {
                const cell = row.children[columnIndex];
                const value = cell ? cell.textContent.replace(/\s+/g, ' ').trim() : '';
                return value || "{{ __('N/A') }}";
            }

            function isDateFilterKey(filterKey) {
                return ['start_date', 'estimated_date', 'finalization_date'].includes(filterKey);
            }

            function formatFilterMonth(value) {
                const parsedDate = parseTaskDate(value);

                if (!parsedDate) {
                    return "{{ __('N/A') }}";
                }

                return parsedDate.toLocaleDateString('es-ES', {
                    month: 'long',
                    year: 'numeric'
                });
            }

            function getFilterMatchValue(filterKey, rawValue) {
                if (isDateFilterKey(filterKey)) {
                    return formatFilterMonth(rawValue);
                }

                return rawValue;
            }

            function getColumnOptions(filterKey, columnIndex) {
                const counts = new Map();

                taskTableRows.forEach(function(row) {
                    const value = getFilterMatchValue(filterKey, getTableCellValue(row, columnIndex));
                    counts.set(value, (counts.get(value) || 0) + 1);
                });

                return Array.from(counts.entries())
                    .sort(function(left, right) {
                        if (left[0] === "{{ __('N/A') }}") {
                            return 1;
                        }

                        if (right[0] === "{{ __('N/A') }}") {
                            return -1;
                        }

                        return left[0].localeCompare(right[0], undefined, {
                            numeric: true,
                            sensitivity: 'base'
                        });
                    })
                    .map(function(entry) {
                        return {
                            value: entry[0],
                            count: entry[1]
                        };
                    });
            }

            function isColumnFiltered(filterKey) {
                return tableFilterState[filterKey] instanceof Set;
            }

            function updateFilterButtonStates() {
                tableFilterButtons.forEach(function(button) {
                    button.classList.toggle('is-active', isColumnFiltered(button.dataset.filterKey));
                });
            }

            function applyTableFilters() {
                if (!taskTableRows.length) {
                    return;
                }

                let visibleRows = 0;

                taskTableRows.forEach(function(row) {
                    const isVisible = tableFilterButtons.every(function(button) {
                        const filterKey = button.dataset.filterKey;
                        const activeValues = tableFilterState[filterKey];

                        if (!(activeValues instanceof Set)) {
                            return true;
                        }

                        return activeValues.has(getFilterMatchValue(filterKey, getTableCellValue(row, Number(button.dataset.columnIndex))));
                    });

                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        visibleRows++;
                    }
                });

                if (filteredEmptyState) {
                    filteredEmptyState.classList.toggle('is-visible', visibleRows === 0);
                }

                updateFilterButtonStates();
            }

            function closeFilterMenu() {
                filterMenu.hidden = true;
                activeFilterButton = null;
            }

            function closeColumnMenu() {
                columnMenu.hidden = true;
                if (columnToggleButton) {
                    columnToggleButton.classList.remove('is-active');
                    columnToggleButton.setAttribute('aria-expanded', 'false');
                }
            }

            function updateColumnToggleSummary() {
                if (!columnToggleButton || !taskTableHeaders.length) {
                    return;
                }

                const visibleCount = taskTableHeaders.reduce(function(total, _, columnIndex) {
                    return total + (columnVisibilityState.get(columnIndex) !== false ? 1 : 0);
                }, 0);
                const totalCount = taskTableHeaders.length;
                const toggleCount = document.getElementById('myTasksColumnsToggleCount');

                if (toggleCount) {
                    toggleCount.textContent = visibleCount + '/' + totalCount;
                }

                const actionText = visibleCount === totalCount
                    ? "{{ __('Hide or show table columns') }}"
                    : "{{ __('Show hidden table columns') }}";

                columnToggleButton.setAttribute('aria-label', actionText);
                columnToggleButton.setAttribute('title', actionText);
            }

            function positionFilterMenu(button) {
                const rect = button.getBoundingClientRect();
                const menuWidth = 260;
                const viewportWidth = window.innerWidth;
                const left = Math.max(12, Math.min(rect.right - menuWidth, viewportWidth - menuWidth - 12));

                filterMenu.style.top = (rect.bottom + 8) + 'px';
                filterMenu.style.left = left + 'px';
            }

            function positionColumnMenu(button) {
                const rect = button.getBoundingClientRect();
                const menuWidth = 260;
                const viewportWidth = window.innerWidth;
                const left = Math.max(12, Math.min(rect.right - menuWidth, viewportWidth - menuWidth - 12));

                columnMenu.style.top = (rect.bottom + 8) + 'px';
                columnMenu.style.left = left + 'px';
            }

            function getHeaderLabelByIndex(index) {
                const header = taskTableHeaders[index];
                if (!header) {
                    return '';
                }

                const label = header.querySelector('.my-tasks-th-content span');
                return (label ? label.textContent : header.textContent).replace(/\s+/g, ' ').trim();
            }

            function applyColumnVisibility() {
                taskTableHeaders.forEach(function(header, columnIndex) {
                    const isVisible = columnVisibilityState.get(columnIndex) !== false;
                    header.style.display = isVisible ? '' : 'none';
                });

                taskTableRows.forEach(function(row) {
                    taskTableHeaders.forEach(function(_, columnIndex) {
                        const cell = row.children[columnIndex];
                        if (!cell) {
                            return;
                        }

                        const isVisible = columnVisibilityState.get(columnIndex) !== false;
                        cell.style.display = isVisible ? '' : 'none';
                    });
                });

                updateColumnToggleSummary();
            }

            function renderColumnMenu() {
                if (!taskTableHeaders.length) {
                    return;
                }

                columnMenu.innerHTML = [
                    '<div class="my-tasks-filter-menu-header">',
                    '<h6 class="my-tasks-column-menu-title">{{ __('Visible columns') }}</h6>',
                    '<button type="button" class="my-tasks-filter-link" data-column-close="1">{{ __('Close') }}</button>',
                    '</div>',
                    '<div class="my-tasks-filter-menu-actions">',
                    '<button type="button" class="my-tasks-filter-link" data-column-reset="1">{{ __('Hide all') }}</button>',
                    '<button type="button" class="my-tasks-filter-link" data-column-select-all="1">{{ __('Show all') }}</button>',
                    '</div>',
                    '<div class="my-tasks-filter-options">',
                    taskTableHeaders.map(function(_, columnIndex) {
                        const label = getHeaderLabelByIndex(columnIndex);
                        const isChecked = columnVisibilityState.get(columnIndex) !== false;

                        return [
                            '<label class="my-tasks-filter-option" data-column-option="1">',
                            '<input type="checkbox" data-column-index="' + columnIndex + '" ' + (isChecked ? 'checked' : '') + '>',
                            '<span>' + escapeHtml(label) + '</span>',
                            '</label>'
                        ].join('');
                    }).join(''),
                    '</div>'
                ].join('');

                if (columnToggleButton) {
                    positionColumnMenu(columnToggleButton);
                    columnToggleButton.classList.add('is-active');
                    columnToggleButton.setAttribute('aria-expanded', 'true');
                }

                columnMenu.hidden = false;
            }

            function renderFilterMenu(button) {
                const filterKey = button.dataset.filterKey;
                const filterLabel = button.dataset.filterLabel;
                const columnIndex = Number(button.dataset.columnIndex);
                const options = getColumnOptions(filterKey, columnIndex);
                const activeValues = tableFilterState[filterKey];

                filterMenu.innerHTML = [
                    '<div class="my-tasks-filter-menu-header">',
                    '<h6 class="my-tasks-filter-menu-title">' + escapeHtml(filterLabel) + '</h6>',
                    '<button type="button" class="my-tasks-filter-link" data-filter-close="1">{{ __('Close') }}</button>',
                    '</div>',
                    '<div class="my-tasks-filter-menu-actions">',
                    '<button type="button" class="my-tasks-filter-link" data-filter-reset="1">{{ __('Clear') }}</button>',
                    '<button type="button" class="my-tasks-filter-link" data-filter-select-all="1">{{ __('Select all') }}</button>',
                    '</div>',
                    '<input type="search" class="my-tasks-filter-search" placeholder="{{ __('Search') }}..." />',
                    '<div class="my-tasks-filter-options">',
                    options.map(function(option) {
                        const isChecked = !(activeValues instanceof Set) || activeValues.has(option.value);

                        return [
                            '<label class="my-tasks-filter-option" data-filter-option="1">',
                            '<input type="checkbox" value="' + escapeHtml(option.value) + '" ' + (isChecked ? 'checked' : '') + '>',
                            '<span>' + escapeHtml(option.value) + '</span>',
                            '<span class="my-tasks-filter-option-count">' + option.count + '</span>',
                            '</label>'
                        ].join('');
                    }).join(''),
                    '</div>'
                ].join('');

                filterMenu.dataset.filterKey = filterKey;
                filterMenu.dataset.columnIndex = String(columnIndex);
                filterMenu.hidden = false;
                activeFilterButton = button;
                positionFilterMenu(button);
            }

            function syncFilterStateFromMenu(filterKey, columnIndex) {
                const options = getColumnOptions(filterKey, columnIndex);
                const checkedValues = Array.from(filterMenu.querySelectorAll('input[type="checkbox"]:checked')).map(function(input) {
                    return input.value;
                });

                if (checkedValues.length === options.length) {
                    tableFilterState[filterKey] = null;
                } else {
                    tableFilterState[filterKey] = new Set(checkedValues);
                }

                applyTableFilters();
            }

            function parseTaskDate(value) {
                if (!value || value === "{{ __('N/A') }}") {
                    return null;
                }

                if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                    const parts = value.split('/');
                    return new Date(Number(parts[2]), Number(parts[1]) - 1, Number(parts[0]));
                }

                if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                    const parts = value.split('-');
                    return new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
                }

                const parsedDate = new Date(value);
                return Number.isNaN(parsedDate.getTime()) ? null : parsedDate;
            }

            function getComparedDateClass(finalizationDate, estimatedDate, baseClass) {
                const parsedFinalizationDate = parseTaskDate(finalizationDate);
                const parsedEstimatedDate = parseTaskDate(estimatedDate);

                if (!parsedFinalizationDate || !parsedEstimatedDate) {
                    return baseClass;
                }

                return baseClass + (parsedFinalizationDate <= parsedEstimatedDate ? ' on-time' : ' overdue');
            }

            function openTimesheetEditModal(row) {
                const url = row.dataset.timesheetEditUrl;
                const taskId = row.dataset.timesheetTaskId;
                const date = row.dataset.timesheetEditDate;
                const mode = row.dataset.timesheetMode || 'edit';
                const modalElement = document.getElementById('commonModal');

                if (!url || !taskId || !date || !modalElement || !window.jQuery || !window.bootstrap) {
                    return;
                }

                const $modal = window.jQuery(modalElement);
                const $dialog = $modal.find('.modal-dialog');
                const title = row.dataset.timesheetTitle || "{{ __('Edit Timesheet') }}";

                $dialog.removeClass('modal-sm modal-md modal-lg modal-xl modal-fullscreen').addClass('modal-lg');

                window.jQuery.ajax({
                    url: url,
                    cache: false,
                    data: {
                        task_id: taskId,
                        date: date,
                        from_my_tasks: 1,
                    },
                    success: function(html) {
                        $modal.find('.body').html(html);
                        $modal.find('.modal-title').html(title + ' <small>(' + escapeHtml(date) + ')</small>');
                        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();

                        if (typeof commonLoader === 'function') {
                            commonLoader();
                        }
                    },
                    error: function(xhr) {
                        const response = xhr && xhr.responseJSON ? xhr.responseJSON : {};

                        if (typeof show_toastr === 'function') {
                            show_toastr('Error', response.error || "{{ __('Unable to load timesheet') }}", 'error');
                        }
                    }
                });
            }

            filterMenu.addEventListener('click', function(event) {
                const resetButton = event.target.closest('[data-filter-reset]');
                const selectAllButton = event.target.closest('[data-filter-select-all]');
                const closeButton = event.target.closest('[data-filter-close]');

                if (closeButton) {
                    closeFilterMenu();
                    return;
                }

                if (!resetButton && !selectAllButton) {
                    return;
                }

                const checkboxes = Array.from(filterMenu.querySelectorAll('input[type="checkbox"]'));
                if (resetButton) {
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });

                    tableFilterState[filterMenu.dataset.filterKey] = new Set();
                    applyTableFilters();
                    return;
                }

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                });

                tableFilterState[filterMenu.dataset.filterKey] = null;
                applyTableFilters();
            });

            filterMenu.addEventListener('change', function(event) {
                if (event.target.matches('input[type="checkbox"]')) {
                    syncFilterStateFromMenu(filterMenu.dataset.filterKey, Number(filterMenu.dataset.columnIndex));
                }
            });

            filterMenu.addEventListener('input', function(event) {
                if (!event.target.matches('.my-tasks-filter-search')) {
                    return;
                }

                const query = event.target.value.trim().toLowerCase();
                Array.from(filterMenu.querySelectorAll('[data-filter-option]')).forEach(function(option) {
                    const optionText = option.textContent.toLowerCase();
                    option.style.display = optionText.includes(query) ? '' : 'none';
                });
            });

            tableFilterButtons.forEach(function(button) {
                tableFilterState[button.dataset.filterKey] = null;

                button.addEventListener('click', function(event) {
                    event.stopPropagation();

                    if (activeFilterButton === button && !filterMenu.hidden) {
                        closeFilterMenu();
                        return;
                    }

                    renderFilterMenu(button);
                });
            });

            if (columnToggleButton && taskTableHeaders.length) {
                taskTableHeaders.forEach(function(_, columnIndex) {
                    columnVisibilityState.set(columnIndex, true);
                });

                columnToggleButton.addEventListener('click', function(event) {
                    event.stopPropagation();

                    if (!columnMenu.hidden) {
                        closeColumnMenu();
                        return;
                    }

                    renderColumnMenu();
                });
            }

            columnMenu.addEventListener('click', function(event) {
                const closeButton = event.target.closest('[data-column-close]');
                const resetButton = event.target.closest('[data-column-reset]');
                const selectAllButton = event.target.closest('[data-column-select-all]');

                if (closeButton) {
                    closeColumnMenu();
                    return;
                }

                if (resetButton) {
                    taskTableHeaders.forEach(function(_, columnIndex) {
                        columnVisibilityState.set(columnIndex, false);
                    });

                    applyColumnVisibility();
                    renderColumnMenu();
                    return;
                }

                if (selectAllButton) {
                    taskTableHeaders.forEach(function(_, columnIndex) {
                        columnVisibilityState.set(columnIndex, true);
                    });

                    applyColumnVisibility();
                    renderColumnMenu();
                }
            });

            columnMenu.addEventListener('change', function(event) {
                if (!event.target.matches('input[type="checkbox"][data-column-index]')) {
                    return;
                }

                const columnIndex = Number(event.target.dataset.columnIndex);
                columnVisibilityState.set(columnIndex, event.target.checked);
                applyColumnVisibility();
            });

            function openProjectTasksModal(projectName, period, tasks) {
                if (!projectTasksModal || !projectTasksModalContent) {
                    return;
                }

                projectTasksModalTitle.textContent = "{{ __('Tasks in project') }}" + ': ' + (projectName || "{{ __('N/A') }}");
                projectTasksModalPeriod.textContent = getPeriodLabel(period);

                if (!Array.isArray(tasks) || !tasks.length) {
                    projectTasksModalContent.innerHTML =
                        '<div class="project-task-empty">' +
                        '<h6 class="mb-1">{{ __('No tasks for this project in the selected period') }}</h6>' +
                        '<p class="mb-0">{{ __('Try another period to view other records.') }}</p>' +
                        '</div>';
                    projectTasksModal.show();
                    return;
                }

                projectTasksModalContent.innerHTML = tasks.map(function(task) {
                    return [
                        '<div class="project-task-item">',
                        '<div class="project-task-item-header">',
                        '<h6 class="project-task-item-title mb-0">' +
                        renderDisplayValue(task.name || naLabel, '') +
                        ' - ' +
                        renderDisplayValue(task.milestone || naLabel, '') +
                        '</h6>',
                        '</div>',
                        '<div class="project-task-item-grid">',
                        '<div>',
                        '<span class="project-task-meta-label">{{ __('Start date') }}</span>',
                        renderDisplayValue(task.start_date || naLabel, 'project-task-meta-value'),
                        '</div>',
                        '<div>',
                        '<span class="project-task-meta-label">{{ __('Estimated date') }}</span>',
                        renderDisplayValue(task.estimated_date || naLabel, 'project-task-meta-value'),
                        '</div>',
                        '<div>',
                        '<span class="project-task-meta-label">{{ __('Finalization date') }}</span>',
                        renderDisplayValue(task.finalization_date || naLabel, getComparedDateClass(task.finalization_date, task.estimated_date, 'project-task-meta-value')),
                        '</div>',
                        '</div>',
                        '</div>'
                    ].join('');
                }).join('');

                projectTasksModal.show();
            }

            function openLoggedTasksModal(rangeKey) {
                if (!projectTasksModal || !projectTasksModalContent) {
                    return;
                }

                const source = loggedTaskDetailsByRange[rangeKey] || {
                    label: "{{ __('Logged tasks') }}",
                    range_label: "{{ __('Range') }}",
                    tasks: []
                };
                const tasks = Array.isArray(source.tasks) ? source.tasks : [];

                projectTasksModalTitle.textContent = source.label || "{{ __('Logged tasks') }}";
                projectTasksModalPeriod.textContent = source.range_label || "{{ __('Range') }}";

                if (!tasks.length) {
                    projectTasksModalContent.innerHTML =
                        '<div class="project-task-empty">' +
                        '<h6 class="mb-1">{{ __('No logged tasks in this period') }}</h6>' +
                        '<p class="mb-0">{{ __('Register timesheets in this range to see the detail here.') }}</p>' +
                        '</div>';
                    projectTasksModal.show();
                    return;
                }

                projectTasksModalContent.innerHTML = tasks.map(function(task) {
                    return [
                        '<div class="project-task-item">',
                        '<div class="project-task-item-header">',
                        '<h6 class="project-task-item-title mb-0">' + renderDisplayValue(task.name || naLabel, '') + '</h6>',
                        '</div>',
                        '<div class="project-task-item-grid is-logged-summary">',
                        '<div>',
                        '<span class="project-task-meta-label">{{ __('Project') }}</span>',
                        renderDisplayValue(task.project || naLabel, 'project-task-meta-value'),
                        '</div>',
                        '<div>',
                        '<span class="project-task-meta-label">{{ __('Milestone') }}</span>',
                        renderDisplayValue(task.milestone || naLabel, 'project-task-meta-value'),
                        '</div>',
                        '<div>',
                        '<span class="project-task-meta-label">{{ __('Hours') }}</span>',
                        renderDisplayValue(task.hours || '00:00', 'project-task-meta-value'),
                        '</div>',
                        '</div>',
                        '</div>'
                    ].join('');
                }).join('');

                projectTasksModal.show();
            }

            function toggleViewMode() {
                const isDiagramMode = selectedMode === 'diagram';

                tableViewCard.style.display = isDiagramMode ? 'none' : 'block';
                diagramViewCard.style.display = isDiagramMode ? 'block' : 'none';
                periodContainer.style.display = isDiagramMode ? 'inline-flex' : 'none';

                if (isDiagramMode) {
                    renderDiagram(selectedPeriod);
                }
            }

            function renderDiagram(period) {
                const source = diagramDataByPeriod[period] || {
                    project_ids: [],
                    labels: [],
                    series: [],
                    project_tasks: {},
                    total_formatted: '0'
                };

                const projectIds = Array.isArray(source.project_ids) ? source.project_ids : [];
                const labels = Array.isArray(source.labels) ? source.labels : [];
                const series = Array.isArray(source.series) ? source.series : [];
                const projectTasks = source.project_tasks && typeof source.project_tasks === 'object' ? source.project_tasks : {};
                const total = series.reduce((acc, value) => acc + Number(value || 0), 0);

                diagramRangeLabel.textContent = getPeriodLabel(period);
                diagramTotalValue.textContent = source.total_formatted || String(total);

                const minimumCanvasWidth = Math.max(760, labels.length * 78);
                chartCanvas.width = minimumCanvasWidth;

                if (myTasksChart) {
                    myTasksChart.destroy();
                    myTasksChart = null;
                }

                if (!labels.length) {
                    chartCanvas.style.display = 'none';
                    chartCanvas.classList.remove('is-interactive');
                    chartEmptyState.classList.add('is-visible');
                    return;
                }

                chartCanvas.style.display = 'block';
                chartCanvas.classList.remove('is-interactive');
                chartEmptyState.classList.remove('is-visible');

                const ctx = chartCanvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, '#d84b62');
                gradient.addColorStop(1, '#ad0f2a');

                const maxValue = Math.max(...series, 0);
                const roundedMax = Math.ceil(maxValue / 7) * 7;

                myTasksChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "{{ __('Tasks') }}",
                            data: series,
                            borderWidth: 0,
                            borderRadius: 7,
                            borderSkipped: false,
                            barPercentage: 0.72,
                            categoryPercentage: 0.82,
                            backgroundColor: gradient
                        }]
                    },
                    options: {
                        indexAxis: 'x',
                        maintainAspectRatio: false,
                        responsive: true,
                        onHover: function(event, elements) {
                            const hasActiveBar = Array.isArray(elements) && elements.length > 0;
                            chartCanvas.classList.toggle('is-interactive', hasActiveBar);
                        },
                        onClick: function(event, elements) {
                            if (!elements || !elements.length) {
                                return;
                            }

                            const barIndex = elements[0].index;
                            const projectId = projectIds[barIndex];
                            const projectLabel = labels[barIndex] || "{{ __('N/A') }}";
                            const tasks = projectTasks[String(projectId)] || projectTasks[projectId] || [];

                            openProjectTasksModal(projectLabel, period, tasks);
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: roundedMax > 0 ? roundedMax : 7,
                                ticks: {
                                    callback: function(value) {
                                        return value;
                                    }
                                },
                                grid: {
                                    color: 'rgba(160, 139, 147, 0.20)',
                                    borderDash: [3, 3]
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 0,
                                    minRotation: 0,
                                    callback: function(value) {
                                        const label = this.getLabelForValue(value) || '';
                                        return label.length > 16 ? label.slice(0, 16) + '...' : label;
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return "{{ __('Tasks') }}: " + Number(context.parsed.y || 0);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            viewModeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    selectedMode = this.getAttribute('data-view-mode');
                    setActiveButton(viewModeButtons, selectedMode, 'data-view-mode');
                    toggleViewMode();
                });
            });

            periodButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    selectedPeriod = this.getAttribute('data-period');
                    setActiveButton(periodButtons, selectedPeriod, 'data-period');
                    renderDiagram(selectedPeriod);
                });
            });

            timesheetTableRows.forEach(function(row) {
                row.addEventListener('click', function(event) {
                    if (event.target.closest('button, a, input, label')) {
                        return;
                    }

                    openTimesheetEditModal(row);
                });

                row.addEventListener('keydown', function(event) {
                    if (event.key !== 'Enter' && event.key !== ' ') {
                        return;
                    }

                    event.preventDefault();
                    openTimesheetEditModal(row);
                });
            });

            loggedTaskCards.forEach(function(card) {
                const openModal = function() {
                    openLoggedTasksModal(card.getAttribute('data-logged-range'));
                };

                card.addEventListener('click', openModal);
                card.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openModal();
                    }
                });
            });

            if (taskOverviewHelpIcon && taskOverviewHelpPopup) {
                taskOverviewHelpPopup.style.opacity = '0';
                taskOverviewHelpPopup.style.transform = 'translateY(-10px)';
                taskOverviewHelpPopup.style.visibility = 'hidden';

                taskOverviewHelpIcon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const isVisible = taskOverviewHelpPopup.style.visibility === 'visible';

                    if (isVisible) {
                        taskOverviewHelpPopup.style.opacity = '0';
                        taskOverviewHelpPopup.style.transform = 'translateY(-10px)';
                        setTimeout(function() {
                            taskOverviewHelpPopup.style.visibility = 'hidden';
                        }, 300);
                    } else {
                        taskOverviewHelpPopup.style.visibility = 'visible';
                        taskOverviewHelpPopup.style.opacity = '1';
                        taskOverviewHelpPopup.style.transform = 'translateY(0)';
                    }

                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });

                document.addEventListener('click', function(event) {
                    if (!taskOverviewHelpPopup.contains(event.target) && event.target !== taskOverviewHelpIcon) {
                        if (taskOverviewHelpPopup.style.visibility === 'visible') {
                            taskOverviewHelpPopup.style.opacity = '0';
                            taskOverviewHelpPopup.style.transform = 'translateY(-10px)';
                            setTimeout(function() {
                                taskOverviewHelpPopup.style.visibility = 'hidden';
                            }, 300);
                        }
                    }
                });

                taskOverviewHelpPopup.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }

            document.addEventListener('click', function(event) {
                if (!filterMenu.hidden && !filterMenu.contains(event.target) && !event.target.closest('.my-tasks-filter-btn')) {
                    closeFilterMenu();
                }

                if (!columnMenu.hidden && !columnMenu.contains(event.target) && !event.target.closest('#myTasksColumnsToggleBtn')) {
                    closeColumnMenu();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeFilterMenu();
                    closeColumnMenu();
                }
            });

            window.addEventListener('resize', function() {
                if (activeFilterButton && !filterMenu.hidden) {
                    positionFilterMenu(activeFilterButton);
                }

                if (columnToggleButton && !columnMenu.hidden) {
                    positionColumnMenu(columnToggleButton);
                }
            });

            window.addEventListener('scroll', function() {
                if (activeFilterButton && !filterMenu.hidden) {
                    positionFilterMenu(activeFilterButton);
                }

                if (columnToggleButton && !columnMenu.hidden) {
                    positionColumnMenu(columnToggleButton);
                }
            }, true);

            setActiveButton(viewModeButtons, selectedMode, 'data-view-mode');
            setActiveButton(periodButtons, selectedPeriod, 'data-period');
            applyColumnVisibility();
            applyTableFilters();
            toggleViewMode();
        })();
    </script>
@endpush