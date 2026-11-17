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
    @include('projects.partials.my_tasks_help')
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/milestoneboard.css') }}">
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
            gap: 12px;
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
            gap: 12px;
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
        .my-tasks-td[data-col-key="project"] { flex: 1 1 auto; width: 240px; min-width: 240px; overflow: hidden; display: flex; align-items: center; margin-right: 14px; }

        .my-tasks-td[data-col-key="project"] .my-tasks-main,
        .my-tasks-td[data-col-key="milestone"] .my-tasks-main {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .my-tasks-th[data-col-key="milestone"],
        .my-tasks-td[data-col-key="milestone"] { flex: 1 1 auto; width: 200px; min-width: 200px; overflow: hidden; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="stage"],
        .my-tasks-td[data-col-key="stage"] { flex: 1 1 auto; width: 120px; min-width: 120px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="phase"],
        .my-tasks-td[data-col-key="phase"] { flex: 1 1 auto; width: 120px; min-width: 120px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="task"],
        .my-tasks-td[data-col-key="task"] { flex: 1 1 auto; width: 160px; min-width: 160px; overflow: hidden; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="start_date"],
        .my-tasks-td[data-col-key="start_date"] { flex: 1 1 auto; width: 120px; min-width: 120px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="estimated_date"],
        .my-tasks-td[data-col-key="estimated_date"] { flex: 1 1 auto; width: 140px; min-width: 140px; display: flex; align-items: center; }

        .my-tasks-th[data-col-key="finalization_date"],
        .my-tasks-td[data-col-key="finalization_date"] { flex: 1 1 auto; width: 160px; min-width: 160px; display: flex; align-items: center; }

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
        .my-tasks-truncate-milestone {
            display: block;
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .my-tasks-truncate-task {
            display: block;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1400px) {
            .my-tasks-truncate-project,
            .my-tasks-truncate-milestone {
                max-width: 150px;
            }
            .my-tasks-truncate-task {
                max-width: 100px;
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

        .task-review-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            font-size: 10px;
            vertical-align: middle;
            flex-shrink: 0;
        }
        .task-badge-reviewed { background: #e0f2e5; }
        .task-badge-changes  { background: #ffe0e0; }

        .my-tasks-body-row.task-row-reviewed .my-tasks-truncate-task .my-tasks-main {
            text-decoration: line-through;
            color: #6c757d;
        }

        .my-tasks-body-row.task-row-changes .my-tasks-truncate-task .my-tasks-main {
            color: #dc3545;
        }

        .my-tasks-td-milestone {
            position: relative;
        }
        .my-tasks-review-card-btn {
            display: none;
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            background: #AA182C;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 11px;
            line-height: 18px;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
        }
        .my-tasks-td-milestone:hover .my-tasks-review-card-btn {
            display: inline-block;
        }
        .my-tasks-td-milestone:hover .my-tasks-truncate-milestone {
            padding-right: 126px;
        }
        .my-tasks-review-card-btn:hover {
            background: #8f1325;
            color: #fff;
        }

        .milestoneCardModalBody .milestone-card-frame {
            max-width: 360px;
            margin: 0 auto;
            position: relative;
        }
        .milestoneCardModalBody {
            overflow-x: hidden;
        }
        .milestoneCardModalBody .milestone-flip,
        .milestoneCardModalBody .milestone-flip-face-front {
            width: 100%;
        }
        .milestoneCardModalBody .milestone-card {
            width: 100%;
        }
        .milestoneCardModalBody .milestone-busy-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.75);
            border-radius: inherit;
            z-index: 30;
            backdrop-filter: blur(1px);
        }
        .milestoneCardModalBody .milestone-busy-spinner {
            width: 28px;
            height: 28px;
            border: 3px solid #e5e7eb;
            border-top-color: #AA182C;
            border-radius: 50%;
            animation: mt-spin 0.8s linear infinite;
        }
        .milestoneCardModalBody .milestone-busy-text {
            font-size: 12.5px;
            font-weight: 600;
            color: #4b5563;
        }
        @keyframes mt-spin {
            to {
                transform: rotate(360deg);
            }
        }
        .milestoneCardModalBody .milestone-card-frame .card-body {
            padding: 12px;
        }
        .milestoneCardModalBody .milestone-card-frame .card-header {
            padding-left: 12px;
            padding-right: 12px;
        }
        .milestoneCardModalBody .milestone-status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .milestoneCardModalBody .milestone-status-header h4 {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 0;
        }
        .milestoneCardModalBody .milestone-status-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            color: #4b5563;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
            user-select: none;
            flex: 0 0 auto;
        }
        .milestoneCardModalBody .milestone-status-btn i {
            font-size: 15px;
            line-height: 1;
            transition: transform 0.2s ease;
        }
        .milestoneCardModalBody .milestone-status-btn:hover {
            background: #AA182C;
            border-color: #AA182C;
            color: #fff;
            box-shadow: 0 2px 6px rgba(170, 24, 44, 0.35);
            transform: translateY(-1px);
        }
        .milestoneCardModalBody .milestone-status-btn-right:hover i {
            transform: translateX(2px);
        }
        .milestoneCardModalBody .milestone-status-btn-left:hover i {
            transform: translateX(-2px);
        }
        .milestoneCardModalBody .milestone-status-btn:active {
            transform: translateY(0) scale(0.97);
        }
        .milestoneCardModalBody .milestone-status-btn.invisible {
            transform: none;
        }
        .milestoneCardModalBody .milestone-flip-inner.mt-half {
            transform: rotateY(90deg);
        }
        .milestoneCardModalBody .milestone-flip-inner.mt-flip-fast {
            transition-duration: 0.35s;
        }
        .milestoneCardModalBody .milestone-card-frame.is-busy .milestone-status-btn {
            opacity: 0.5;
            pointer-events: none;
        }
        #milestoneCardModal .modal-content {
            position: relative;
        }
        #milestoneCardModal .milestone-modal-alert {
            position: absolute;
            bottom: 14px;
            right: 14px;
            max-width: 280px;
            margin: 0;
            padding: 8px 14px;
            font-size: 12.5px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            z-index: 20;
            animation: mtToastIn 0.25s ease;
        }
        @keyframes mtToastIn {
            from {
                opacity: 0;
                transform: translateX(12px);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }

        .milestoneCardModalBody .milestone-flip {
            perspective: 1200px;
            overflow: hidden;
        }
        .milestoneCardModalBody .milestone-flip-inner {
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.7s cubic-bezier(0.4, 0.2, 0.2, 1);
        }
        .milestoneCardModalBody .milestone-flip-inner.is-flipped {
            transform: rotateY(180deg);
        }
        .milestoneCardModalBody .milestone-flip-face {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        .milestoneCardModalBody .milestone-flip-face-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            transform: rotateY(180deg);
        }

        /* =============================================
           MIS TAREAS · resumen compacto, buscador, filtros rápidos, estado del encargo y orden por fecha
           ============================================= */
        .my-tasks-hero--compact {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px 14px;
            padding: 12px 16px;
        }

        .my-tasks-hero--compact .my-tasks-kpi {
            display: flex;
            gap: 8px;
            margin: 0 0 0 auto;
        }

        .my-tasks-hero--compact .my-tasks-kpi-card {
            display: inline-flex;
            align-items: baseline;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            border-color: #eceef2;
        }

        .my-tasks-hero--compact .my-tasks-kpi-value {
            font-size: 17px;
        }

        .my-tasks-hero--compact .my-tasks-kpi-label {
            text-transform: none;
            letter-spacing: 0;
            font-size: 12.5px;
        }

        .my-tasks-kpi-go {
            font-size: 9px;
            color: var(--mt-accent);
        }

        .my-tasks-hero--compact .my-tasks-controls {
            margin-top: 0;
        }

        .my-tasks-search {
            display: flex;
            align-items: center;
            gap: 8px;
            height: 34px;
            width: 260px;
            max-width: 100%;
            margin: 0;
            padding: 0 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #9ca3af;
            background: #fff;
        }

        .my-tasks-search:focus-within {
            border-color: var(--mt-accent);
        }

        .my-tasks-search input {
            flex: 1;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            font-size: 13px;
            color: #1f2937;
        }

        .my-tasks-quick {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .my-tasks-quick-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 32px;
            padding: 0 11px;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #fff;
            font-size: 12.5px;
            color: #374151;
        }

        .my-tasks-quick-btn b {
            padding: 0 6px;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: 11px;
        }

        .my-tasks-quick-btn[data-quick="late"] b { color: #c0392b; }
        .my-tasks-quick-btn[data-quick="changes"] b { color: #dc3545; }

        .my-tasks-quick-btn:hover {
            border-color: #f3c7cd;
        }

        .my-tasks-quick-btn.is-active {
            background: #fbeaec;
            border-color: #f3c7cd;
            color: var(--mt-accent);
            font-weight: 600;
        }

        .my-tasks-quick-btn.is-active b {
            background: #fff;
        }

        #tableViewCard .card-header .my-tasks-header-tools {
            margin-left: auto;
        }

        .my-tasks-active-filters {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 36px 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #fcfcfd;
            border: 1px solid #f0f1f4;
            font-size: 12.5px;
            color: #6b7280;
        }

        .my-tasks-active-filters > i {
            color: var(--mt-accent);
        }

        .my-tasks-active-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-right: 6px;
            padding: 2px 4px 2px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #fff;
            color: #374151;
        }

        .my-tasks-active-chip button {
            border: 0;
            background: none;
            padding: 0 4px;
            font-size: 15px;
            line-height: 1;
            color: #9ca3af;
        }

        .my-tasks-active-chip button:hover {
            color: #c0392b;
        }

        /* estado del encargo */
        .my-tasks-th[data-col-key="status"],
        .my-tasks-td[data-col-key="status"] { flex: 1 1 auto; width: 165px; min-width: 165px; display: flex; align-items: center; }

        /* fechas: etiqueta + orden + embudo en la cabecera; la estimada lleva además «hace X días» */
        .my-tasks-th[data-col-key="start_date"],
        .my-tasks-td[data-col-key="start_date"] { width: 150px; min-width: 150px; }

        .my-tasks-th[data-col-key="estimated_date"],
        .my-tasks-td[data-col-key="estimated_date"] { width: 200px; min-width: 200px; }

        .my-tasks-th[data-col-key="finalization_date"],
        .my-tasks-td[data-col-key="finalization_date"] { width: 175px; min-width: 175px; }

        .my-tasks-th[data-col-key="project"],
        .my-tasks-td[data-col-key="project"] { width: 210px; min-width: 210px; }

        .my-tasks-th[data-col-key="milestone"],
        .my-tasks-td[data-col-key="milestone"] { width: 180px; min-width: 180px; }

        .my-tasks-status {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .my-tasks-status--1 { background: #f3f4f6; color: #6b7280; }
        .my-tasks-status--2 { background: #eef2ff; color: #4338ca; }
        .my-tasks-status--3 { background: #fff7e6; color: #b7791f; }
        .my-tasks-status--4 { background: #e9f8ee; color: #2f9e44; }

        /* «No requerido» discreto */
        .my-tasks-nr {
            color: #c4c8cf;
            cursor: help;
        }

        /* fecha estimada con contexto y marca de vencida */
        .my-tasks-td[data-col-key="estimated_date"] {
            gap: 6px;
            white-space: nowrap;
        }

        .my-tasks-td[data-col-key="estimated_date"] .is-late {
            color: #c0392b;
            font-weight: 600;
        }

        .my-tasks-td[data-col-key="estimated_date"] .is-soon {
            color: #b7791f;
            font-weight: 600;
        }

        .my-tasks-rel {
            font-size: 11.5px;
            color: #9ca3af;
            font-weight: 400 !important;
        }

        .my-tasks-body-row.is-late {
            box-shadow: inset 3px 0 0 #e06c71;
        }

        /* orden por fecha */
        .my-tasks-sort-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 0;
            border: 0;
            background: none;
            font: inherit;
            letter-spacing: inherit;
            text-transform: inherit;
            color: inherit;
            cursor: pointer;
        }

        .my-tasks-sort-icon {
            width: 12px;
            height: 12px;
            fill: #c4c8cf;
        }

        .my-tasks-sort-btn:hover .my-tasks-sort-icon,
        .my-tasks-sort-btn.is-sorted {
            color: var(--mt-accent);
        }

        .my-tasks-sort-btn.is-sorted .my-tasks-sort-icon {
            fill: var(--mt-accent);
            clip-path: inset(0 0 50% 0);
        }

        .my-tasks-sort-btn.is-sorted.is-desc .my-tasks-sort-icon {
            clip-path: inset(50% 0 0 0);
        }

        /* pie de la tabla: cuántas se ven y qué hace el clic */
        .my-tasks-table-foot {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 10px 36px 4px;
            font-size: 12.5px;
            color: #6b7280;
        }

        .my-tasks-table-foot-hint {
            margin-left: auto;
            color: #9ca3af;
        }

        /* =============================================
           VISTA EN DIAGRAMA · barras horizontales por proyecto
           ============================================= */
        .my-tasks-diag-head {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .my-tasks-diag-range {
            font-size: 12px;
            color: #9ca3af;
        }

        .my-tasks-diag-sp {
            flex: 1;
        }

        #diagramPeriod {
            display: inline-flex;
            gap: 6px;
        }

        .my-tasks-metric {
            display: inline-flex;
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            overflow: hidden;
        }

        .my-tasks-metric button {
            border: 0;
            background: #fff;
            padding: 6px 14px;
            font-size: 12.5px;
            color: #374151;
        }

        .my-tasks-metric button.is-active {
            background: #1f2937;
            color: #fff;
            font-weight: 600;
        }

        .my-tasks-diag-sum {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .my-tasks-diag-stat {
            border: 1px solid #eef0f3;
            border-radius: 10px;
            padding: 8px 12px;
        }

        .my-tasks-diag-stat small {
            display: block;
            font-size: 10.5px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .my-tasks-diag-stat b {
            font-size: 20px;
            color: #1f2937;
        }

        .my-tasks-diag-delta {
            margin-left: 6px;
            font-size: 11.5px;
            color: #9ca3af;
        }

        .my-tasks-diag-delta.is-up { color: #2f9e44; }
        .my-tasks-diag-delta.is-down { color: #c0392b; }

        .my-tasks-diag-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0 0 6px;
            font-size: 12px;
            color: #6b7280;
        }

        .my-tasks-diag-row {
            display: grid;
            grid-template-columns: minmax(180px, 280px) 1fr 130px;
            gap: 14px;
            align-items: center;
            width: 100%;
            padding: 7px 8px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            text-align: left;
        }

        button.my-tasks-diag-row {
            cursor: pointer;
        }

        button.my-tasks-diag-row:hover,
        button.my-tasks-diag-row:focus-visible {
            background: #fdf7f8;
            outline: 0;
        }

        .my-tasks-diag-name {
            min-width: 0;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .my-tasks-diag-name small {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: #9ca3af;
        }

        .my-tasks-diag-track {
            display: flex;
            height: 20px;
            border-radius: 6px;
            background: #f6f7f9;
            overflow: hidden;
        }

        .my-tasks-diag-track i {
            display: block;
            height: 100%;
        }

        .my-tasks-diag-s1 { background: #cbd5e1; }
        .my-tasks-diag-s2 { background: #6b7fe0; }
        .my-tasks-diag-s3 { background: #f0b429; }
        .my-tasks-diag-s4 { background: #4cbb6c; }
        .my-tasks-diag-sh { background: #AA182C; }

        .my-tasks-diag-val {
            text-align: right;
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
        }

        .my-tasks-diag-val small {
            margin-left: 4px;
            font-weight: 400;
            color: #9ca3af;
        }

        .my-tasks-diag-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px 16px;
            margin-top: 10px;
            font-size: 12px;
            color: #6b7280;
        }

        .my-tasks-diag-legend span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .my-tasks-diag-legend i {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
        }

        @media (max-width: 900px) {
            .my-tasks-diag-sum {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .my-tasks-diag-row {
                grid-template-columns: 140px 1fr 90px;
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
            $phaseValue = optional(optional($task->milestone)->phase)->phases;
            $stageName = $supportsStageAndPhase
                ? ($phaseValue ? __(\App\Models\MilestonePhases::translationKey($phaseValue)) : $naText)
                : $notApplicableStagePhaseText;

            return $stageName !== $notApplicableStagePhaseText;
        });
        $showPhaseColumn = $taskCollection->contains(function ($task) use ($projectTypesWithStageAndPhase, $notApplicableStagePhaseText, $naText) {
            $supportsStageAndPhase = in_array((int) optional($task->project)->type, $projectTypesWithStageAndPhase, true);
            $resolvedStageName = optional($task->milestone)->resolved_stage_name;
            $phaseName = $supportsStageAndPhase
                ? ($resolvedStageName ?: $naText)
                : $notApplicableStagePhaseText;

            return $phaseName !== $notApplicableStagePhaseText;
        });
        $visibleOptionalColumns = (int) $showStageColumn + (int) $showPhaseColumn;
        $stageColumnIndex = $showStageColumn ? 2 : null;
        $phaseColumnIndex = $showPhaseColumn ? 2 + ((int) $showStageColumn) : null;
        $taskColumnIndex = 2 + $visibleOptionalColumns;
        $statusColumnIndex = 3 + $visibleOptionalColumns;
        $startDateColumnIndex = 4 + $visibleOptionalColumns;
        $estimatedDateColumnIndex = 5 + $visibleOptionalColumns;
        $finalizationDateColumnIndex = 6 + $visibleOptionalColumns;
        // Estado del encargo (milestones.status): 1 por hacer · 2 en curso · 3 en revisión · 4 hecho
        $milestoneStatusLabels = [1 => __('To Do'), 2 => __('In Progress'), 3 => __('Review'), 4 => __('Done')];
        $sortIcon = '<svg class="my-tasks-sort-icon" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 2.5 11.5 6h-7L8 2.5Zm0 11L4.5 10h7L8 13.5Z"></path></svg>';
    @endphp

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="my-tasks-wrap mt-3">
                    {{-- Resumen compacto: título, cifras y vista en una sola franja --}}
                    <div class="my-tasks-hero my-tasks-hero--compact">
                        <div class="my-tasks-title-row">
                            <h4 class="mb-0">{{ __('Task Overview') }}</h4>
                            <img class="my-tasks-help-icon" id="taskOverviewHelpIcon" src="{{ asset('assets/img/questionCircle.svg') }}"
                                alt="{{ __('Task Overview info') }}" title="{{ __('Task Overview info') }}" />
                            <div class="my-tasks-help-popup" id="taskOverviewHelpPopup">
                                <p>{{ __('Quick view of your assigned work and upcoming delivery dates.') }}</p>
                                <p>{!! __('In <strong>Diagram View</strong>, the tasks shown respect the task\'s creation date.') !!}</p>
                                <p>{!! __('<strong>N/A</strong> means there is no value for that field.') !!}</p>
                            </div>
                        </div>

                        <div class="my-tasks-kpi">
                            <div class="my-tasks-kpi-card">
                                <div class="my-tasks-kpi-value">{{ $taskCollection->count() }}</div>
                                <div class="my-tasks-kpi-label">{{ __('Total assigned') }}</div>
                            </div>
                            <div class="my-tasks-kpi-card" data-logged-range="weekly" role="button" tabindex="0"
                                title="{{ __('See the tasks you logged') }}">
                                <div class="my-tasks-kpi-value">{{ $loggedTasksThisWeekCount ?? 0 }}</div>
                                <div class="my-tasks-kpi-label">{{ __('Logged this week') }}</div>
                                <i class="fa-solid fa-chevron-right my-tasks-kpi-go" aria-hidden="true"></i>
                            </div>
                            <div class="my-tasks-kpi-card" data-logged-range="thirty_days" role="button" tabindex="0"
                                title="{{ __('See the tasks you logged') }}">
                                <div class="my-tasks-kpi-value">{{ $loggedTasksLastThirtyDaysCount ?? 0 }}</div>
                                <div class="my-tasks-kpi-label">{{ __('Logged in 30 days') }}</div>
                                <i class="fa-solid fa-chevron-right my-tasks-kpi-go" aria-hidden="true"></i>
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
                        </div>
                    </div>

                    <div class="card my-tasks-card" id="tableViewCard">
                        <div class="card-header d-flex  align-items-center flex-wrap gap-2">
                            <div class="my-tasks-title-row">
                                <h5 class="mb-0">{{ __('Assigned tasks') }}</h5>
                                <img class="my-tasks-help-icon" id="assignedTasksHelpIcon" src="{{ asset('assets/img/questionCircle.svg') }}"
                                    alt="{{ __('Assigned tasks info') }}" title="{{ __('Assigned tasks info') }}" />
                                <div class="my-tasks-help-popup" id="assignedTasksHelpPopup">
                                    <p>{!! __('<strong>Click</strong> on a row to log hours for that task.') !!}</p>
                                    <p>{!! __('Use the <strong>funnel icon</strong> in a column header to filter that column, and the <strong>eye button</strong> next to this title to show or hide columns.') !!}</p>
                                    <p>{!! __('<strong>Right click</strong> on a row to mark the task as <strong>Revisado</strong> or <strong>Solicitar cambio</strong>.') !!}</p>
                                    <p>{!! __('<strong>Click the green or red badge</strong> next to the task name to read the comment and confirm the change.') !!}</p>
                                </div>
                            </div>
                            @unless ($taskCollection->isEmpty())
                                <label class="my-tasks-search" for="myTasksSearch">
                                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                    <input type="search" id="myTasksSearch" placeholder="{{ __('Search project, order form or task…') }}" autocomplete="off">
                                </label>
                                {{-- Filtros rápidos (se suman a los embudos de cada columna) --}}
                                <div class="my-tasks-quick" id="myTasksQuickFilters" role="group" aria-label="{{ __('Quick filters') }}">
                                    <button type="button" class="my-tasks-quick-btn" data-quick="open">{{ __('Not finished') }} <b data-quick-count="open">0</b></button>
                                    <button type="button" class="my-tasks-quick-btn" data-quick="late">{{ __('Overdue tasks') }} <b data-quick-count="late">0</b></button>
                                    <button type="button" class="my-tasks-quick-btn" data-quick="changes">{{ __('Change requested') }} <b data-quick-count="changes">0</b></button>
                                    <button type="button" class="my-tasks-quick-btn" data-quick="finished">{{ __('Finished tasks') }} <b data-quick-count="finished">0</b></button>
                                    <button type="button" class="my-tasks-quick-btn" data-quick="all">{{ __('All tasks') }} <b data-quick-count="all">0</b></button>
                                </div>
                            @endunless
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
                            {{-- Embudos de columna aplicados: se ven y se quitan desde aquí --}}
                            <div class="my-tasks-active-filters" id="myTasksActiveFilters" hidden>
                                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                                <span class="my-tasks-active-label">{{ __('Column filters') }}:</span>
                                <span id="myTasksActiveFiltersList"></span>
                                <button type="button" class="my-tasks-filter-link" id="myTasksClearFilters">{{ __('Clear all') }}</button>
                            </div>
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
                                        <div class="my-tasks-th" data-col-key="status">
                                            <div class="my-tasks-th-content">
                                                <span>{{ __('Order form status') }}</span>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="status" data-filter-label="{{ __('Order form status') }}" data-column-index="{{ $statusColumnIndex }}" aria-label="{{ __('Filter Order form status') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="start_date">
                                            <div class="my-tasks-th-content">
                                                <button type="button" class="my-tasks-sort-btn" data-sort-key="start" title="{{ __('Sort') }}">{{ __('Start date') }}{!! $sortIcon !!}</button>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="start_date" data-filter-label="{{ __('Start date') }}" data-column-index="{{ $startDateColumnIndex }}" aria-label="{{ __('Filter Start date') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="estimated_date">
                                            <div class="my-tasks-th-content">
                                                <button type="button" class="my-tasks-sort-btn" data-sort-key="estimated" title="{{ __('Sort') }}">{{ __('Estimated date') }}{!! $sortIcon !!}</button>
                                                <button type="button" class="my-tasks-filter-btn" data-filter-key="estimated_date" data-filter-label="{{ __('Estimated date') }}" data-column-index="{{ $estimatedDateColumnIndex }}" aria-label="{{ __('Filter Estimated date') }}">
                                                    <svg class="my-tasks-filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="my-tasks-th" data-col-key="finalization_date">
                                            <div class="my-tasks-th-content">
                                                <button type="button" class="my-tasks-sort-btn" data-sort-key="finalization" title="{{ __('Sort') }}">{{ __('Finalization date') }}{!! $sortIcon !!}</button>
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
                                                $phaseValue = optional(optional($task->milestone)->phase)->phases;
                                                $stageName = $supportsStageAndPhase
                                                    ? ($phaseValue ? __(\App\Models\MilestonePhases::translationKey($phaseValue)) : __('N/A'))
                                                    : $notApplicableStagePhaseText;
                                                $resolvedStageName = optional($task->milestone)->resolved_stage_name;
                                                $phaseName = $supportsStageAndPhase
                                                    ? ($resolvedStageName ?: __('N/A'))
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

                                                $reviewState = $task->reviewState ? $task->reviewState->state_code : null;
                                                $reviewComment = $task->reviewState ? $task->reviewState->comment : '';
                                                $reviewUser = $task->reviewState && $task->reviewState->mark_user_id
                                                    ? optional(\App\Models\User::find($task->reviewState->mark_user_id))->name
                                                    : null;

                                                // Filtros rápidos, orden y fecha relativa
                                                $isFinished = (bool) $finalizationDate;
                                                $isLate = !$isFinished && $estimatedDate && $estimatedDate->copy()->startOfDay()->lt($today);
                                                $isSoon = !$isFinished && !$isLate && $estimatedDate && $estimatedDate->copy()->startOfDay()->lte($weekEnd);
                                                $relativeText = null;
                                                if (!$isFinished && $estimatedDate) {
                                                    $daysDiff = (int) $today->diffInDays($estimatedDate->copy()->startOfDay(), false);
                                                    $relativeText = $daysDiff === 0 ? __('today')
                                                        : ($daysDiff < 0 ? __(':n days ago', ['n' => abs($daysDiff)]) : __('in :n days', ['n' => $daysDiff]));
                                                }
                                                $milestoneStatus = (int) optional($task->milestone)->status;
                                                $statusLabel = $milestoneStatusLabels[$milestoneStatus] ?? $naText;
                                            @endphp
                                            <div class="my-tasks-body-row {{ $reviewState ? 'task-row-' . $reviewState : '' }} {{ $isLate ? 'is-late' : '' }}"
                                                data-finished="{{ $isFinished ? '1' : '0' }}"
                                                data-late="{{ $isLate ? '1' : '0' }}"
                                                data-review="{{ $reviewState ?? '' }}"
                                                data-sort-start="{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : '' }}"
                                                data-sort-estimated="{{ $estimatedDate ? $estimatedDate->format('Y-m-d') : '' }}"
                                                data-sort-finalization="{{ $finalizationDate ? $finalizationDate->format('Y-m-d') : '' }}"
                                                data-search="{{ mb_strtolower($projectName . ' ' . $milestoneTitle . ' ' . $displayTypeName . ' ' . ($task->referencia ?? '')) }}"
                                                data-task-id="{{ $task->id }}"
                                                data-task-name="{{ $displayTypeName }}"
                                                data-milestone-id="{{ $task->milestone_id }}"
                                                data-milestone-status="{{ optional($task->milestone)->status }}"
                                                data-project-id="{{ $task->project_id }}"
                                                data-project-type-id="{{ optional($task->project)->type ?? '' }}"
                                                data-technician-name="{{ $task->assign_to }}"
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
                                                <div class="my-tasks-td my-tasks-td-milestone" data-col-key="milestone" data-cell-value="{{ $milestoneTitle }}">
                                                    <div class="my-tasks-main my-tasks-truncate-milestone {{ $milestoneClass }}" title="{{ $milestoneTitle }}">{{ $milestoneTitle }}</div>
                                                    <button type="button" class="my-tasks-review-card-btn"
                                                        data-milestone-id="{{ $task->milestone_id }}"
                                                        data-workspace-slug="{{ $task->workspace_slug ?? '' }}"
                                                        title="{{ __('Revisar card') }}">
                                                        {{ __('Revisar card') }}
                                                    </button>
                                                </div>
                                                @if ($showStageColumn)
                                                <div class="my-tasks-td" data-col-key="stage" data-cell-value="{{ $stageName }}">
                                                    @if ($stageName === $notApplicableStagePhaseText)
                                                        <span class="my-tasks-nr" title="{{ $notApplicableStagePhaseText }}">—</span>
                                                    @else
                                                        <span class="{{ $stageClass }}">{{ $stageName }}</span>
                                                    @endif
                                                </div>
                                                @endif
                                                @if ($showPhaseColumn)
                                                <div class="my-tasks-td" data-col-key="phase" data-cell-value="{{ $phaseName }}">
                                                    @if ($phaseName === $notApplicableStagePhaseText)
                                                        <span class="my-tasks-nr" title="{{ $notApplicableStagePhaseText }}">—</span>
                                                    @else
                                                        <span class="{{ $phaseClass }}">{{ $phaseName }}</span>
                                                    @endif
                                                </div>
                                                @endif
                                                <div class="my-tasks-td" data-col-key="task" data-cell-value="{{ $displayTypeName }}">
                                                    <div class="my-tasks-truncate-task" title="{{ $displayTypeName }}">
                                                        @if ($reviewState === 'changes')
                                                            <span class="me-2 badge task-review-badge task-badge-changes"
                                                                  title="{{ $reviewComment ? __('Change request comment') . ': ' . $reviewComment : '' }} {{ $reviewUser ? '— ' . __('Requested by') . ': ' . $reviewUser : '' }}"
                                                                  data-task-name="{{ $displayTypeName }}"
                                                                  data-review-comment="{{ $reviewComment }}"
                                                                  data-review-user="{{ $reviewUser ?? '' }}"
                                                                  data-task-id="{{ $task->id }}"
                                                                  style="cursor:pointer;">
                                                                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                                            </span>
                                                        @elseif ($reviewState === 'reviewed')
                                                            <span class="me-2 badge task-review-badge task-badge-reviewed"
                                                                  style="cursor:pointer;"
                                                                  data-review-mode="view"
                                                                  data-task-name="{{ $displayTypeName }}"
                                                                  data-task-id="{{ $task->id }}"
                                                                  title="{{ __('Revisado') }}">
                                                                <i class="fa-solid fa-check text-success"></i>
                                                            </span>
                                                        @endif
                                                        <span class="{{ $taskTypeClass }}">{{ $displayTypeName }}</span>
                                                    </div>
                                                </div>
                                                <div class="my-tasks-td" data-col-key="status" data-cell-value="{{ $statusLabel }}">
                                                    @if ($milestoneStatus >= 1 && $milestoneStatus <= 4)
                                                        <span class="my-tasks-status my-tasks-status--{{ $milestoneStatus }}">{{ $statusLabel }}</span>
                                                    @else
                                                        <span class="{{ $placeholderClass }}">{{ $naText }}</span>
                                                    @endif
                                                </div>
                                                <div class="my-tasks-td" data-col-key="start_date">
                                                    <span class="{{ $startDateClass }}">{{ $startDateText }}</span>
                                                </div>
                                                <div class="my-tasks-td" data-col-key="estimated_date" data-cell-value="{{ $estimatedDateText }}">
                                                    <span class="{{ $estimatedDateClass }} {{ $isLate ? 'is-late' : ($isSoon ? 'is-soon' : '') }}">{{ $estimatedDateText }}</span>
                                                    @if ($relativeText)
                                                        <small class="my-tasks-rel {{ $isLate ? 'is-late' : ($isSoon ? 'is-soon' : '') }}">{{ $relativeText }}</small>
                                                    @endif
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
                        <div class="my-tasks-table-foot">
                            <span id="myTasksVisibleCount"></span>
                            <span class="my-tasks-table-foot-hint">
                                <i class="fa-solid fa-computer-mouse" aria-hidden="true"></i>
                                {{ __('Click a row: log hours') }} · {{ __('Right click: review') }}
                            </span>
                        </div>
                        @endif
                        </div>
                    </div>

                    {{-- Vista en diagrama: barras horizontales por proyecto (tareas por estado del encargo u horas imputadas) --}}
                    <div class="card my-tasks-card" id="diagramViewCard" style="display: none;">
                        <div class="card-header my-tasks-diag-head">
                            <h5 class="mb-0">{{ __('By project') }}</h5>
                            <span class="my-tasks-diag-range" id="diagramRangeLabel"></span>
                            <span class="my-tasks-diag-sp"></span>
                            <div class="my-tasks-period" id="diagramPeriod">
                                <button type="button" class="my-tasks-period-btn is-active" data-period="weekly">{{ __('Week') }}</button>
                                <button type="button" class="my-tasks-period-btn" data-period="monthly">{{ __('Month') }}</button>
                                <button type="button" class="my-tasks-period-btn" data-period="quarterly">{{ __('Quarter') }}</button>
                                <button type="button" class="my-tasks-period-btn" data-period="yearly">{{ __('Year') }}</button>
                            </div>
                            <div class="my-tasks-metric" id="diagramMetric" role="group" aria-label="{{ __('Measure') }}">
                                <button type="button" class="is-active" data-metric="tasks">{{ __('Tasks') }}</button>
                                <button type="button" data-metric="hours">{{ __('Hours') }}</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="my-tasks-diag-sum" id="diagramSummary"></div>
                            <div class="my-tasks-diag-hint" id="diagramHint"></div>
                            <div class="my-tasks-diag-bars" id="diagramBars"></div>
                            <div id="myTasksChartEmpty" class="my-tasks-chart-empty">
                                <h6 class="mb-2" id="diagramEmptyTitle">{{ __('No tasks created in this period') }}</h6>
                                <p class="mb-0">{{ __('Try another period.') }}</p>
                            </div>
                            <div class="my-tasks-diag-legend" id="diagramLegend"></div>
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

                <div class="modal fade" id="milestoneCardModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title mb-0">{{ __('Revisar card') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                </div>
                                <div class="modal-body milestoneCardModalBody" id="milestoneCardModalBody">
                                    <div class="text-center text-muted py-4">{{ __('Loading...') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="modal fade" id="myTasksStatusChangeModal" tabindex="-1" role="dialog"
                    aria-labelledby="myTasksStatusChangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myTasksStatusChangeModalLabel">{{ __('Volver a En curso') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="myTasksStatusChangeComment">{{ __('Motivo') }}</label>
                                    <textarea class="form-control" id="myTasksStatusChangeComment" name="status_change_comment" rows="4"
                                        placeholder="{{ __('Indica el motivo para volver a En curso...') }}"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="button" class="btn btn-primary" onclick="submitMyTasksStatusChange()">{{ __('Volver a En curso') }}</button>
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
    @include('projects.partials.task_review_modal')
    <script>
        (function() {
            if (typeof window.updateMyTasksReviewRow !== 'function') {
                window.updateMyTasksReviewRow = function(response) {
                    if (!response || !response.task_id) return;

                    var row = document.querySelector('.my-tasks-body-row[data-task-id="' + response.task_id + '"]');
                    if (!row) return;

                    var reviewState = response.review_state;

                    row.classList.remove('task-row-changes', 'task-row-reviewed');
                    if (reviewState && reviewState !== 'cleared') {
                        row.classList.add('task-row-' + reviewState);
                    }

                    var truncateEl = row.querySelector('.my-tasks-truncate-task');
                    if (!truncateEl) return;

                    truncateEl.querySelectorAll('.task-review-badge').forEach(function(b) { b.remove(); });

                    if (reviewState === 'cleared') return;

                    var badge = document.createElement('span');
                    badge.className = 'me-2 badge task-review-badge ' + (reviewState === 'changes' ? 'task-badge-changes' : 'task-badge-reviewed');
                    badge.style.cursor = 'pointer';
                    badge.setAttribute('data-task-name', row.getAttribute('data-task-name') || '');
                    badge.setAttribute('data-task-id', response.task_id);

                    if (reviewState === 'changes') {
                        badge.setAttribute('data-review-comment', response.review_comment || '');
                        badge.setAttribute('data-review-user', response.review_user || '');
                        badge.title = '{{ __("Change request comment") }}: ' + (response.review_comment || '') + ' — {{ __("Requested by") }}: ' + (response.review_user || '');
                        badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-danger"></i>';
                    } else if (reviewState === 'reviewed') {
                        badge.setAttribute('data-review-mode', 'view');
                        badge.title = '{{ __("Revisado") }}';
                        badge.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                    } else {
                        return;
                    }

                    var nameEl = truncateEl.querySelector('.my-tasks-main');
                    truncateEl.insertBefore(badge, nameEl || truncateEl.firstChild);
                };
            }
        })();
        (function() {
            const modalEl = document.getElementById('milestoneCardModal');
            const bodyEl = document.getElementById('milestoneCardModalBody');
            if (!modalEl || !bodyEl) return;

            const cardUrlTemplate = @json(route('projects.milestone.card', ['__SLUG__', '__ID__']));
            const orderUrlTemplate = @json(route('milestone.update.order', ['__SLUG__', '__PROJECT__']));
            const checkHoursUrlTemplate = @json(route('projects.milestone.checkTaskHours', ['__SLUG__', '__ID__']));
            const notifyUrl = @json(route('notifications.add'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const currentWorkspaceId = {{ $currentWorkspace->id }};
            const currentWorkspaceSlug = @json($currentWorkspace->slug);

            let isBusy = false;

            function reExecScripts(container) {
                container.querySelectorAll('script').forEach(function(s) {
                    const ns = document.createElement('script');
                    if (s.src) {
                        ns.src = s.src;
                    } else {
                        ns.textContent = s.textContent;
                    }
                    s.parentNode.replaceChild(ns, s);
                });
            }

            function loadMilestoneCard(slug, id) {
                bodyEl.innerHTML = '<div class="text-center text-muted py-4">{{ __('Loading...') }}</div>';
                return fetch(cardUrlTemplate.replace('__SLUG__', slug).replace('__ID__', id), {
                    headers: { 'Accept': 'text/html' }
                })
                    .then(function(response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.text();
                    })
                    .then(function(html) {
                        bodyEl.innerHTML = html;
                        reExecScripts(bodyEl);
                    })
                    .catch(function() {
                        bodyEl.innerHTML = '<div class="text-center text-danger py-4">{{ __('Error') }}</div>';
                    });
            }

            function fetchCardHtml(slug, id) {
                return fetch(cardUrlTemplate.replace('__SLUG__', slug).replace('__ID__', id), {
                    headers: { 'Accept': 'text/html' }
                }).then(function(response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.text();
                });
            }

            function removeBusyOverlay() {
                const frame = bodyEl.querySelector('.milestone-card-frame');
                if (!frame) return;
                const overlay = frame.querySelector('.milestone-busy-overlay');
                if (overlay) overlay.remove();
            }

            function setBusy(busy) {
                isBusy = busy;
                const frame = bodyEl.querySelector('.milestone-card-frame');
                if (!frame) return;
                frame.classList.toggle('is-busy', busy);
                if (busy) {
                    if (!frame.querySelector('.milestone-busy-overlay')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'milestone-busy-overlay';
                        const spinner = document.createElement('div');
                        spinner.className = 'milestone-busy-spinner';
                        const text = document.createElement('div');
                        text.className = 'milestone-busy-text';
                        text.textContent = "{{ __('Procesando...') }}";
                        overlay.appendChild(spinner);
                        overlay.appendChild(text);
                        frame.appendChild(overlay);
                    }
                } else {
                    removeBusyOverlay();
                }
            }

            function waitTransform(el) {
                return new Promise(function(resolve) {
                    let done = false;
                    const onEnd = function(e) {
                        if (e && e.propertyName && e.propertyName !== 'transform') return;
                        if (done) return;
                        done = true;
                        el.removeEventListener('transitionend', onEnd);
                        resolve();
                    };
                    el.addEventListener('transitionend', onEnd);
                    setTimeout(function() { onEnd(null); }, 700);
                });
            }

            function swapCardContent(freshHtml) {
                removeBusyOverlay();
                const inner = bodyEl.querySelector('.milestone-flip-inner');
                if (!inner) {
                    bodyEl.innerHTML = freshHtml;
                    reExecScripts(bodyEl);
                    return Promise.resolve();
                }
                return new Promise(function(resolve) {
                    inner.classList.add('mt-flip-fast');
                    const half = waitTransform(inner);
                    inner.classList.add('mt-half');
                    half.then(function() {
                        const holder = document.createElement('div');
                        holder.innerHTML = freshHtml;
                        const freshInner = holder.querySelector('.milestone-flip-inner');
                        const freshFrame = holder.querySelector('.milestone-card-frame');
                        const frame = bodyEl.querySelector('.milestone-card-frame');
                        let targetFlipped = false;
                        if (freshInner) {
                            inner.innerHTML = freshInner.innerHTML;
                            targetFlipped = freshInner.classList.contains('is-flipped');
                        }
                        const rest = waitTransform(inner);
                        inner.classList.remove('mt-half');
                        inner.classList.toggle('is-flipped', targetFlipped);
                        if (freshFrame && frame) {
                            frame.className = freshFrame.className;
                            if (isBusy) frame.classList.add('is-busy');
                        }
                        inner.classList.remove('mt-flip-fast');
                        reExecScripts(inner);
                        rest.then(resolve);
                    });
                });
            }

            function showModalToast(message, type) {
                const container = modalEl.querySelector('.modal-content');
                if (!container) return;
                const existing = container.querySelector('.milestone-modal-alert');
                if (existing) existing.remove();
                const alert = document.createElement('div');
                alert.className = 'milestone-modal-alert alert ' + (type === 'danger' ? 'alert-danger' : 'alert-success');
                alert.setAttribute('role', 'alert');
                alert.textContent = message;
                container.appendChild(alert);
                setTimeout(function() {
                    if (alert.isConnected) alert.remove();
                }, 3500);
            }

            function sendOrderUpdate(slug, data) {
                return fetch(orderUrlTemplate.replace('__SLUG__', slug).replace('__PROJECT__', data.project_id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: data.milestone_id,
                        old_status: data.old_status,
                        new_status: data.new_status,
                        project_id: data.project_id,
                        status_change_comment: data.status_change_comment || ''
                    })
                }).then(function(response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                });
            }

            function sendReviewNotification(slug, milestoneId, projectName, milestoneTitle, technicianId) {
                const msg = milestoneTitle + ' en el proyecto ' + projectName;
                if (!msg) return Promise.resolve();
                return fetch(notifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        workspace_id: currentWorkspaceId,
                        msg: msg,
                        ntipe: 5,
                        milestoneAssignedTo: technicianId,
                        milestone_id: milestoneId
                    })
                }).catch(function() {});
            }

            function getCardMeta() {
                const card = bodyEl.querySelector('.milestone-card');
                if (!card) return null;
                return {
                    slug: card.getAttribute('data-workspace-slug') || currentWorkspaceSlug,
                    milestoneId: card.getAttribute('id'),
                    projectId: card.getAttribute('data-project-id'),
                    status: parseInt(card.getAttribute('data-status'), 10) || 0
                };
            }

function moveToReview(meta) {
                setBusy(true);
                const checkUrl = checkHoursUrlTemplate.replace('__SLUG__', meta.slug).replace('__ID__', meta.milestoneId);
                return fetch(checkUrl + '?id=' + encodeURIComponent(meta.milestoneId), {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function(response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.json();
                    })
                    .then(function(data) {
                        if (!data.all_exist) {
                            setBusy(false);
                            showModalToast(
                                data.has_tasks
                                    ? "{{ __('Todas las tareas tienen que tener horas imputadas') }}"
                                    : "{{ __('No se puede mover un encargo sin tareas') }}",
                                'danger'
                            );
                            return false;
                        }
                        return sendOrderUpdate(meta.slug, {
                            milestone_id: meta.milestoneId,
                            old_status: 2,
                            new_status: 3,
                            project_id: meta.projectId
                        }).then(function() {
                            const req = bodyEl.querySelector('#milestoneReqName');
                            sendReviewNotification(
                                meta.slug,
                                meta.milestoneId,
                                req ? req.getAttribute('data-project-name') : '',
                                (bodyEl.querySelector('.milestone-title') || {}).textContent || '',
                                req ? req.getAttribute('data-technician-id') : ''
                            );
                            return fetchCardHtml(meta.slug, meta.milestoneId);
                        }).then(function(freshHtml) {
                            return swapCardContent(freshHtml);
                        }).then(function() {
                            setBusy(false);
                            showModalToast("{{ __('El estado se actualizó correctamente') }}", 'success');
                        }).catch(function() {
                            setBusy(false);
                            showModalToast("{{ __('Error') }}", 'danger');
                        });
                    }).catch(function() {
                        setBusy(false);
                        showModalToast("{{ __('Error') }}", 'danger');
                    });
            }

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.my-tasks-review-card-btn');
                if (!btn) return;
                e.preventDefault();

                const milestoneId = btn.getAttribute('data-milestone-id');
                const slug = btn.getAttribute('data-workspace-slug');
                if (!milestoneId || !slug) return;

                loadMilestoneCard(slug, milestoneId);
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });

            modalEl.addEventListener('hidden.bs.modal', function() {
                bodyEl.innerHTML = '';
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.milestone-status-btn');
                if (!btn) return;
                e.preventDefault();
                if (isBusy) return;
                const meta = getCardMeta();
                if (!meta) return;
                const to = btn.getAttribute('data-flip-to');

                if (to === 'revision') {
                    moveToReview(meta);
                    return;
                }

                if (to === 'hecho') {
                    setBusy(true);
                    sendOrderUpdate(meta.slug, {
                        milestone_id: meta.milestoneId,
                        old_status: 3,
                        new_status: 4,
                        project_id: meta.projectId
                    }).then(function() {
                        return fetchCardHtml(meta.slug, meta.milestoneId);
                    }).then(function(freshHtml) {
                        return swapCardContent(freshHtml);
                    }).then(function() {
                        setBusy(false);
                        showModalToast("{{ __('El estado se actualizó correctamente') }}", 'success');
                    }).catch(function() {
                        setBusy(false);
                        showModalToast("{{ __('Error') }}", 'danger');
                    });
                    return;
                }

                if (to === 'curso') {
                    const scModalEl = document.getElementById('myTasksStatusChangeModal');
                    const commentEl = document.getElementById('myTasksStatusChangeComment');
                    scModalEl.dataset.milestoneId = meta.milestoneId;
                    scModalEl.dataset.slug = meta.slug;
                    scModalEl.dataset.projectId = meta.projectId;
                    commentEl.value = '';
                    bootstrap.Modal.getOrCreateInstance(scModalEl).show();
                }
            });

            window.submitMyTasksStatusChange = function() {
                const scModalEl = document.getElementById('myTasksStatusChangeModal');
                const commentEl = document.getElementById('myTasksStatusChangeComment');
                const comment = commentEl.value;
                if (!comment.trim()) {
                    alert("{{ __('Debes indicar el motivo para volver a En curso.') }}");
                    return;
                }
                const milestoneId = scModalEl.dataset.milestoneId;
                const slug = scModalEl.dataset.slug;
                const projectId = scModalEl.dataset.projectId;

                setBusy(true);
                sendOrderUpdate(slug, {
                    milestone_id: milestoneId,
                    old_status: 3,
                    new_status: 2,
                    project_id: projectId,
                    status_change_comment: comment
                }).then(function() {
                    bootstrap.Modal.getOrCreateInstance(scModalEl).hide();
                    return fetchCardHtml(slug, milestoneId);
                }).then(function(freshHtml) {
                    return swapCardContent(freshHtml);
                }).then(function() {
                    setBusy(false);
                    showModalToast("{{ __('El estado se actualizó correctamente') }}", 'success');
                }).catch(function() {
                    setBusy(false);
                    showModalToast("{{ __('Error') }}", 'danger');
                });
            };
        })();
    </script>
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
            const assignedTasksHelpIcon = document.getElementById('assignedTasksHelpIcon');
            const assignedTasksHelpPopup = document.getElementById('assignedTasksHelpPopup');
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

            // Valor de la celda para los embudos: data-cell-value si lo tiene (así el texto extra de la celda,
            // como «Revisar card», la fecha relativa o el «—» de «No requerido», no cambia las opciones del filtro)
            function getTableCellValue(row, columnIndex) {
                const cell = row.children[columnIndex];
                if (cell && cell.dataset.cellValue !== undefined) {
                    return cell.dataset.cellValue || "{{ __('N/A') }}";
                }
                const value = cell ? cell.textContent.replace(/\s+/g, ' ').trim() : '';
                return value || "{{ __('N/A') }}";
            }

            // --- filtros rápidos, buscador y orden por fecha ---
            const quickButtons = Array.from(document.querySelectorAll('#myTasksQuickFilters [data-quick]'));
            const searchInput = document.getElementById('myTasksSearch');
            const sortButtons = Array.from(document.querySelectorAll('.my-tasks-sort-btn'));
            const bodyRowsContainer = taskTable ? taskTable.querySelector('.my-tasks-body-rows') : null;
            const quickStorageKey = 'aceler.myTasks.quick';
            const quickMatchers = {
                open: (row) => row.dataset.finished !== '1',
                late: (row) => row.dataset.late === '1',
                changes: (row) => row.dataset.review === 'changes',
                finished: (row) => row.dataset.finished === '1',
                all: () => true,
            };
            let quickFilter = 'open';
            try {
                const saved = localStorage.getItem(quickStorageKey);
                if (saved && quickMatchers[saved]) quickFilter = saved;
            } catch (e) {}
            let sortState = { key: null, dir: 1 };
            taskTableRows.forEach((row, i) => { row.dataset.originalIndex = String(i); });

            function updateQuickCounts() {
                Object.keys(quickMatchers).forEach(function(key) {
                    const el = document.querySelector('#myTasksQuickFilters [data-quick-count="' + key + '"]');
                    if (el) el.textContent = taskTableRows.filter(quickMatchers[key]).length;
                });
                quickButtons.forEach(function(btn) {
                    const active = btn.dataset.quick === quickFilter;
                    btn.classList.toggle('is-active', active);
                    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
            }

            // Embudos aplicados, visibles encima de la tabla
            function renderActiveFilters() {
                const box = document.getElementById('myTasksActiveFilters');
                const list = document.getElementById('myTasksActiveFiltersList');
                if (!box || !list) return;
                const items = tableFilterButtons
                    .filter((button) => isColumnFiltered(button.dataset.filterKey))
                    .map(function(button) {
                        const values = Array.from(tableFilterState[button.dataset.filterKey]);
                        const shown = values.length ? values.slice(0, 2).join(', ') + (values.length > 2 ? ' +' + (values.length - 2) : '') : "{{ __('none') }}";
                        return '<span class="my-tasks-active-chip">' + escapeHtml(button.dataset.filterLabel) + ': ' + escapeHtml(shown) +
                            '<button type="button" data-clear-filter="' + escapeHtml(button.dataset.filterKey) + '" aria-label="{{ __('Remove filter') }}">&times;</button></span>';
                    });
                list.innerHTML = items.join('');
                box.hidden = items.length === 0;
            }

            function applySort() {
                if (!bodyRowsContainer) return;
                const key = sortState.key;
                const rows = taskTableRows.slice().sort(function(a, b) {
                    if (!key) return Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex);
                    const attr = 'sort' + key.charAt(0).toUpperCase() + key.slice(1);
                    const va = a.dataset[attr] || '', vb = b.dataset[attr] || '';
                    // Sin fecha, siempre al final
                    if (!va && !vb) return Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex);
                    if (!va) return 1;
                    if (!vb) return -1;
                    return va === vb ? Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex) : (va < vb ? -1 : 1) * sortState.dir;
                });
                rows.forEach((row) => bodyRowsContainer.appendChild(row));
                sortButtons.forEach(function(btn) {
                    const on = btn.dataset.sortKey === key;
                    btn.classList.toggle('is-sorted', on);
                    btn.classList.toggle('is-desc', on && sortState.dir === -1);
                });
            }

            sortButtons.forEach(function(btn) {
                btn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    // asc → desc → orden original
                    if (sortState.key !== btn.dataset.sortKey) sortState = { key: btn.dataset.sortKey, dir: 1 };
                    else if (sortState.dir === 1) sortState.dir = -1;
                    else sortState = { key: null, dir: 1 };
                    applySort();
                });
            });

            quickButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    quickFilter = btn.dataset.quick;
                    try { localStorage.setItem(quickStorageKey, quickFilter); } catch (e) {}
                    applyTableFilters();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function() { applyTableFilters(); });
            }

            document.addEventListener('click', function(event) {
                const clear = event.target.closest('[data-clear-filter]');
                if (clear) {
                    tableFilterState[clear.dataset.clearFilter] = null;
                    applyTableFilters();
                    return;
                }
                if (event.target.closest('#myTasksClearFilters')) {
                    Object.keys(tableFilterState).forEach((key) => { tableFilterState[key] = null; });
                    applyTableFilters();
                }
            });

            // La tabla ocupa el alto que queda en la ventana (antes, una caja fija de 350 px)
            function fitTableHeight() {
                const scroll = document.querySelector('.my-tasks-table-scroll');
                if (!scroll || !scroll.offsetParent) return;
                const top = scroll.getBoundingClientRect().top + window.scrollY;
                const foot = document.querySelector('.my-tasks-table-foot');
                const below = (foot ? foot.offsetHeight : 0) + 40;
                let h = Math.max(320, window.innerHeight - top - below);
                scroll.style.maxHeight = h + 'px';
                // Lo que aún sobresale de la ventana (márgenes del layout) se descuenta de la tabla
                const over = document.documentElement.scrollHeight - window.innerHeight;
                if (over > 0 && h > 320) {
                    h = Math.max(320, h - over - 2);
                    scroll.style.maxHeight = h + 'px';
                }
            }
            window.addEventListener('resize', fitTableHeight);

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
                const quickMatch = quickMatchers[quickFilter] || quickMatchers.all;
                const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

                taskTableRows.forEach(function(row) {
                    const isVisible = quickMatch(row)
                        && (!query || (row.dataset.search || '').indexOf(query) !== -1)
                        && tableFilterButtons.every(function(button) {
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

                const countEl = document.getElementById('myTasksVisibleCount');
                if (countEl) {
                    countEl.textContent = "{{ __(':shown of :total tasks') }}"
                        .replace(':shown', visibleRows).replace(':total', taskTableRows.length);
                }

                updateFilterButtonStates();
                updateQuickCounts();
                renderActiveFilters();
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

                if (isDiagramMode) {
                    renderDiagram(selectedPeriod);
                }
            }

            // --- vista en diagrama: barras horizontales por proyecto ---
            let selectedMetric = 'tasks';
            const metricButtons = Array.from(document.querySelectorAll('#diagramMetric [data-metric]'));
            const diagramBars = document.getElementById('diagramBars');
            const diagramSummary = document.getElementById('diagramSummary');
            const diagramHint = document.getElementById('diagramHint');
            const diagramLegend = document.getElementById('diagramLegend');
            const diagramEmptyTitle = document.getElementById('diagramEmptyTitle');
            const diagT = {
                states: [@json(__('To Do')), @json(__('In Progress')), @json(__('Review')), @json(__('Done'))],
                created: @json(__('Tasks created')),
                finished: @json(__('Finished tasks')),
                hours: @json(__('Hours logged')),
                projects: @json(__('Projects')),
                days: @json(__('Days with hours')),
                perDay: @json(__('Average per day')),
                vsPrev: @json(__('vs. previous period')),
                task: @json(__('task')),
                tasks: @json(__('tasks')),
                orderForm: @json(__('order form')),
                orderForms: @json(__('order forms')),
                hintTasks: @json(__('Click a project to see its tasks in the table')),
                hintHours: @json(__('Hours from your timesheet in the chosen period. Click a project to see its tasks.')),
                legendHours: @json(__('Each bar: hours of the project compared with the one with most hours · % of the period total')),
                emptyTasks: @json(__('No tasks created in this period')),
                emptyHours: @json(__('No hours logged in this period')),
                seeInTable: @json(__('Click: see in the table')),
            };

            const fmtMinutes = (m) => Math.floor((m || 0) / 60) + ':' + String(Math.round(m || 0) % 60).padStart(2, '0');

            function deltaHtml(now, prev, isMinutes) {
                const diff = (now || 0) - (prev || 0);
                if (!diff) return '<span class="my-tasks-diag-delta" title="' + escapeHtml(diagT.vsPrev) + '">=</span>';
                const text = isMinutes ? fmtMinutes(Math.abs(diff)) : Math.abs(diff);
                return '<span class="my-tasks-diag-delta ' + (diff > 0 ? 'is-up' : 'is-down') + '" title="' + escapeHtml(diagT.vsPrev) + '">' +
                    (diff > 0 ? '▲ ' : '▼ ') + text + '</span>';
            }

            function stat(label, value, extra) {
                return '<div class="my-tasks-diag-stat"><small>' + escapeHtml(label) + '</small><b>' + value + '</b>' + (extra || '') + '</div>';
            }

            function subLine(delegation, extra) {
                const parts = [delegation, extra].filter(Boolean);
                return parts.length ? '<small>' + escapeHtml(parts.join(' · ')) + '</small>' : '';
            }

            function renderDiagram(period) {
                const source = diagramDataByPeriod[period] || {};
                const s = source.summary || {};
                const isHours = selectedMetric === 'hours';
                const projects = Array.isArray(source.projects) ? source.projects : [];
                const hours = Array.isArray(source.hours) ? source.hours : [];

                diagramRangeLabel.textContent = source.range_label || getPeriodLabel(period);

                // Resumen del periodo (con la diferencia frente al periodo anterior de la misma duración)
                if (isHours) {
                    const perDay = s.days_with_hours ? (s.minutes || 0) / s.days_with_hours : 0;
                    diagramSummary.innerHTML = stat(diagT.hours, fmtMinutes(s.minutes), deltaHtml(s.minutes, s.minutes_prev, true)) +
                        stat(diagT.days, s.days_with_hours || 0) +
                        stat(diagT.perDay, fmtMinutes(perDay)) +
                        stat(diagT.projects, hours.length);
                } else {
                    diagramSummary.innerHTML = stat(diagT.created, s.created || 0, deltaHtml(s.created, s.created_prev, false)) +
                        stat(diagT.finished, s.done || 0, deltaHtml(s.done, s.done_prev, false)) +
                        stat(diagT.hours, fmtMinutes(s.minutes), deltaHtml(s.minutes, s.minutes_prev, true)) +
                        stat(diagT.projects, projects.length);
                }

                diagramHint.innerHTML = '<i class="fa-solid ' + (isHours ? 'fa-clock' : 'fa-hand-pointer') + '"></i>' +
                    escapeHtml(isHours ? diagT.hintHours : diagT.hintTasks);

                const rows = isHours ? hours : projects;
                chartEmptyState.classList.toggle('is-visible', rows.length === 0);
                if (diagramEmptyTitle) diagramEmptyTitle.textContent = isHours ? diagT.emptyHours : diagT.emptyTasks;

                if (isHours) {
                    const max = Math.max(1, ...hours.map((r) => r.minutes || 0));
                    const total = hours.reduce((acc, r) => acc + (r.minutes || 0), 0) || 1;
                    diagramBars.innerHTML = hours.map(function(r) {
                        const pct = Math.round((r.minutes || 0) * 100 / total);
                        return '<button type="button" class="my-tasks-diag-row" data-project-name="' + escapeHtml(r.name) + '" title="' + escapeHtml(diagT.seeInTable) + '">' +
                            '<span class="my-tasks-diag-name">' + escapeHtml(r.name) + subLine(r.delegation, '') + '</span>' +
                            '<span class="my-tasks-diag-track"><i class="my-tasks-diag-sh" style="width:' + ((r.minutes || 0) * 100 / max) + '%"></i></span>' +
                            '<span class="my-tasks-diag-val">' + fmtMinutes(r.minutes) + '<small>' + pct + ' %</small></span>' +
                            '</button>';
                    }).join('');
                    diagramLegend.innerHTML = '<span>' + escapeHtml(diagT.legendHours) + '</span>';
                    return;
                }

                // Tareas: cada barra dividida por el estado del encargo, a escala del proyecto con más tareas
                const max = Math.max(1, ...projects.map((p) => p.total || 0));
                diagramBars.innerHTML = projects.map(function(p) {
                    const states = Array.isArray(p.states) ? p.states : [0, 0, 0, 0];
                    // Orden de lectura: hecho · revisión · en curso · por hacer
                    const order = [3, 2, 1, 0];
                    const segments = order.map(function(i) {
                        return states[i] ? '<i class="my-tasks-diag-s' + (i + 1) + '" style="width:' + (states[i] * 100 / max) + '%"></i>' : '';
                    }).join('');
                    const breakdown = order.filter((i) => states[i]).map((i) => diagT.states[i] + ' ' + states[i]).join(' · ');
                    const milestonesText = p.milestones ? p.milestones + ' ' + (p.milestones === 1 ? diagT.orderForm : diagT.orderForms) : '';
                    return '<button type="button" class="my-tasks-diag-row" data-project-name="' + escapeHtml(p.name) + '" title="' +
                        escapeHtml(p.name + ' · ' + breakdown + '\n' + diagT.seeInTable) + '">' +
                        '<span class="my-tasks-diag-name">' + escapeHtml(p.name) + subLine(p.delegation, milestonesText) + '</span>' +
                        '<span class="my-tasks-diag-track">' + segments + '</span>' +
                        '<span class="my-tasks-diag-val">' + (p.total || 0) + '<small>' + escapeHtml(p.total === 1 ? diagT.task : diagT.tasks) + '</small></span>' +
                        '</button>';
                }).join('');
                diagramLegend.innerHTML = [0, 1, 2, 3].map(function(i) {
                    return '<span><i class="my-tasks-diag-s' + (i + 1) + '"></i>' + escapeHtml(diagT.states[i]) + '</span>';
                }).join('');
            }

            // Pulsar un proyecto: vuelve a la tabla filtrada por ese proyecto (todas sus tareas)
            function showProjectInTable(projectName) {
                Object.keys(tableFilterState).forEach(function(key) { tableFilterState[key] = null; });
                tableFilterState.project = new Set([projectName]);
                quickFilter = 'all';
                if (searchInput) searchInput.value = '';
                selectedMode = 'table';
                setActiveButton(viewModeButtons, selectedMode, 'data-view-mode');
                toggleViewMode();
                applyTableFilters();
                fitTableHeight();
            }

            if (diagramBars) {
                diagramBars.addEventListener('click', function(event) {
                    const row = event.target.closest('[data-project-name]');
                    if (row) showProjectInTable(row.dataset.projectName);
                });
            }

            metricButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    selectedMetric = button.dataset.metric;
                    metricButtons.forEach((b) => b.classList.toggle('is-active', b === button));
                    renderDiagram(selectedPeriod);
                });
            });

            viewModeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    selectedMode = this.getAttribute('data-view-mode');
                    setActiveButton(viewModeButtons, selectedMode, 'data-view-mode');
                    toggleViewMode();
                    fitTableHeight();
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

            function setupHelpPopup(icon, popup) {
                if (!icon || !popup) {
                    return;
                }

                popup.style.opacity = '0';
                popup.style.transform = 'translateY(-10px)';
                popup.style.visibility = 'hidden';

                const hide = function() {
                    popup.style.opacity = '0';
                    popup.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        popup.style.visibility = 'hidden';
                    }, 300);
                };

                icon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const isVisible = popup.style.visibility === 'visible';

                    if (isVisible) {
                        hide();
                    } else {
                        popup.style.visibility = 'visible';
                        popup.style.opacity = '1';
                        popup.style.transform = 'translateY(0)';
                    }

                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });

                document.addEventListener('click', function(event) {
                    if (popup.style.visibility !== 'visible') {
                        return;
                    }

                    if (!popup.contains(event.target) && event.target !== icon) {
                        hide();
                    }
                });

                popup.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }

            setupHelpPopup(taskOverviewHelpIcon, taskOverviewHelpPopup);
            setupHelpPopup(assignedTasksHelpIcon, assignedTasksHelpPopup);

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
            fitTableHeight();
        })();
    </script>
@endpush
