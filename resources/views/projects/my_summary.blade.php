@extends('layouts.admin')

@section('page-title')
    {{ __('My Summary') }}
@endsection

@section('links')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('My Summary') }}</li>
@endsection

@php
    $isChartMode = request()->boolean('chart_mode');
@endphp

<style>
    :root {
        --my-summary-section-max-height: min(70vh, 720px);
        --my-summary-scrollbar-width: 6px;
        --my-summary-scrollbar-thumb: #aa182c;
        --my-summary-scrollbar-radius: 8px;
    }

    .my-summary-page {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 700px), 1fr));
        gap: clamp(14px, 2vw, 18px);
        align-items: stretch;
    }

    .my-summary-score-wrapper {
        grid-column: 1 / -1;
        min-width: 0;
    }

    .my-summary-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .my-summary-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .my-summary-filter-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .my-summary-filter-toggle:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .my-summary-filter-toggle.is-active {
        background: #aa182c;
        color: #fff;
    }

    .my-summary-filter-wrapper {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0;
    }

    .my-summary-filter-wrapper.is-open {
        grid-template-rows: 1fr;
        margin-bottom: 16px;
    }

    .my-summary-filter-inner {
        overflow: hidden;
    }

    .my-summary-card {
        border: 0;
        border-radius: clamp(12px, 2vw, 16px);
        box-shadow: 0 10px 24px rgba(17, 24, 39, 0.08);
        overflow: hidden;
    }

    .my-summary-section-shell {
        padding: clamp(16px, 3vw, 24px);
        display: flex;
        flex-direction: column;
        gap: clamp(16px, 2.5vw, 20px);
        position: relative;
        min-height: 480px;
    }

    .my-summary-top-area {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .my-summary-controls-row,
    .my-summary-content-wrapper {
        min-width: 0;
    }

    .my-summary-content-wrapper {
        max-height: var(--my-summary-section-max-height);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--my-summary-scrollbar-thumb) transparent;
    }

    .my-summary-content-wrapper::-webkit-scrollbar {
        width: var(--my-summary-scrollbar-width);
    }

    .my-summary-content-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .my-summary-content-wrapper::-webkit-scrollbar-thumb {
        background-color: var(--my-summary-scrollbar-thumb);
        border-radius: var(--my-summary-scrollbar-radius);
    }

    .my-summary-filter-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        min-width: 0;
    }

    .my-summary-view-switch {
        position: relative;
        display: inline-grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: center;
        padding: 2px;
        border: 1px solid #aa182c;
        border-radius: 999px;
        background: #ffffff;
        overflow: hidden;
        isolation: isolate;
    }

    .my-summary-view-switch::before {
        content: '';
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 2px;
        width: calc(50% - 2px);
        border-radius: 999px;
        background: #aa182c;
        transform: translateX(0);
        transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
        z-index: 0;
    }

    .my-summary-view-switch[data-chart-mode='true']::before {
        transform: translateX(100%);
    }

    .my-summary-view-option {
        position: relative;
        z-index: 1;
        border: 0;
        background: transparent;
        color: #546274;
        border-radius: 999px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        text-decoration: none;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .my-summary-view-option:hover {
        color: #243244;
    }

    .my-summary-view-switch[data-chart-mode='false'] #mySummaryCardToggle,
    .my-summary-view-switch[data-chart-mode='true'] #mySummaryChartToggle,
    .my-summary-view-switch[data-status-mode='todo'] #myDayTodoToggle,
    .my-summary-view-switch[data-status-mode='progress'] #myDayProgressToggle {
        color: #fff;
    }

    .my-summary-view-switch[data-chart-mode='false'] #mySummaryChartToggle,
    .my-summary-view-switch[data-chart-mode='true'] #mySummaryCardToggle,
    .my-summary-view-switch[data-status-mode='todo'] #myDayProgressToggle,
    .my-summary-view-switch[data-status-mode='progress'] #myDayTodoToggle {
        color: #546274;
    }

    .my-summary-view-switch[data-status-mode='progress']::before {
        transform: translateX(100%);
    }

    .my-summary-view-option.is-active {
        color: inherit;
    }

    @media (prefers-reduced-motion: reduce) {

        .my-summary-view-switch::before,
        .my-summary-view-option {
            transition: none;
        }
    }

    .my-summary-filter-button {
        border: 1px solid #aa182c;
        background: #fff;
        color: #000000;
        border-radius: 999px;
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 13px;
        line-height: 1;
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .my-summary-filter-button:hover,
    .my-summary-filter-button.is-active {
        background: #aa182c;
        border-color: #aa182c;
        color: #fff;
    }

    .my-summary-custom-trigger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .my-summary-filter-modal .modal-dialog {
        max-width: 560px;
        margin: 1rem auto;
    }

    .my-summary-filter-modal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.1), 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .my-summary-filter-modal .modal-header {
        padding: 22px 24px 14px;
        border-bottom: 1px solid #ececec;
    }

    .my-summary-filter-modal .modal-body {
        padding: 18px 24px 24px;
    }

    .my-summary-filter-modal-grid,
    .my-summary-filter-form,
    .my-summary-filter-block {
        display: grid;
        gap: 10px;
    }

    .my-summary-filter-modal-grid {
        gap: 12px;
    }

    .my-summary-filter-block {
        gap: 8px;
    }

    .my-summary-date-range-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .my-summary-filter-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8c5560;
        margin-bottom: 0;
    }

    .my-summary-filter-control {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 9px 11px;
        color: #1f2937;
        background: #fff;
        min-height: 40px;
        font-size: 16px;
    }

    .my-summary-filter-submit,
    .my-summary-filter-clear {
        min-height: 42px;
    }

    .my-summary-filter-submit {
        border: 0;
        border-radius: 10px;
        padding: 10px 12px;
        background: #aa182c;
        color: #fff;
        font-weight: 700;
        font-size: 13px;
    }

    .my-summary-filter-actions {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-top: 6px;
        flex-wrap: wrap;
        align-items: stretch;
    }

    .my-summary-filter-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ead3d8;
        border-radius: 10px;
        padding: 10px 12px;
        background: #fff;
        color: #aa182c;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
    }

    .my-summary-empty-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, rgba(170, 24, 44, 1) 0%, rgb(174 0 24) 100%);
        color: #fff;
        font-size: 20px;
    }

    .my-summary-content-wrapper {
        position: relative;
        min-height: 200px;
    }

    .my-summary-visualization-shell.is-hidden,
    .my-summary-content-panel.is-hidden {
        display: none;
    }

    .my-summary-visualization-shell {
        display: grid;
        gap: 18px;
        padding: 18px;
        border: 1px solid #ebeef3;
        border-radius: 18px;
        background: linear-gradient(180deg, #fdf8f9 0%, #ffffff 44%);
    }

    .my-summary-chart-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .my-summary-chart-kicker {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8c5560;
        margin-bottom: 6px;
    }

    .my-summary-chart-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #1f2937;
    }

    .my-summary-chart-summary {
        display: grid;
        gap: 2px;
        justify-items: end;
        text-align: right;
    }

    .my-summary-chart-range {
        font-size: 12px;
        font-weight: 600;
        color: #7c8594;
    }

    .my-summary-chart-total {
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
        color: #aa182c;
    }

    .my-summary-chart-scroll {
        overflow-x: auto;
        padding-bottom: 6px;
        flex-grow: 1;
        min-width: 0;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .my-summary-chart-scroll::-webkit-scrollbar {
        height: 5px;
    }

    .my-summary-chart-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .my-summary-chart-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .my-summary-chart-root {
        position: relative;
    }

    .my-summary-chart-layout {
        display: flex;
        gap: 12px;
        align-items: stretch;
    }

    .my-summary-chart-y-axis {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-bottom: 34px;
        padding-top: 10px;
        flex-shrink: 0;
        text-align: right;
        font-size: 11px;
        font-weight: 600;
        color: #7c8594;
        min-width: 38px;
        user-select: none;
    }

    .my-summary-chart-inner {
        position: relative;
        min-height: 280px;
        padding-top: 16px;
        min-width: 100%;
        display: flex;
        flex-direction: column;
    }

    .my-summary-chart-grid-lines {
        position: absolute;
        top: 16px;
        bottom: 34px;
        left: 0;
        right: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        pointer-events: none;
        z-index: 0;
    }

    .my-summary-chart-grid-line {
        border-bottom: 1px dashed #e2e8f0;
        width: 100%;
    }

    .my-summary-chart-track {
        position: relative;
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(72px, 96px);
        justify-content: flex-start;
        align-items: end;
        gap: 8px;
        height: 100%;
        min-height: 280px;
        z-index: 1;
        width: 100%;
    }

    .my-summary-chart-column,
    .my-summary-chart-column-link {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        height: 100%;
        text-decoration: none;
        color: inherit;
        gap: 8px;
        min-width: 0;
    }

    .my-summary-chart-bar-wrap {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        flex-grow: 1;
        width: 100%;
        padding: 0 4px;
        cursor: pointer;
    }

    .my-summary-chart-bar {
        width: 100%;
        max-width: var(--my-summary-chart-bar-max-width, 48px);
        border-radius: 6px 6px 0 0;
        background: linear-gradient(180deg, #d64a5c 0%, #aa182c 100%);
        box-shadow: 0 4px 12px rgba(170, 24, 44, 0.15);
        transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s;
    }

    .my-summary-chart-column-link:hover .my-summary-chart-bar,
    .my-summary-chart-bar-wrap:hover .my-summary-chart-bar {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(170, 24, 44, 0.25);
        opacity: 0.9;
    }

    .my-summary-chart-label {
        font-size: 11px;
        font-weight: 600;
        color: #4b5563;
        text-align: center;
        line-height: 1.25;
        height: 28px;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-word;
    }

    .my-summary-chart-empty {
        min-height: 240px;
        display: grid;
        place-items: center;
        padding: 24px;
        border: 1px dashed #d7dde5;
        border-radius: 16px;
        background: #fbfcfe;
        text-align: center;
    }

    .my-summary-chart-empty p {
        max-width: 420px;
        margin: 8px auto 0;
        color: #6b7280;
        font-size: 13px;
    }

    .my-summary-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.75);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 12px;
    }

    .my-summary-loading-overlay.is-active {
        display: flex;
    }

    .my-summary-spinner {
        width: 42px;
        height: 42px;
        border: 4px solid rgba(170, 24, 44, 0.15);
        border-left-color: #aa182c;
        border-radius: 50%;
        animation: my-summary-spin 1s linear infinite;
    }

    @keyframes my-summary-spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .my-summary-section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .my-summary-filter-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
        }

        .my-summary-filter-button,
        .my-summary-custom-trigger,
        .my-summary-view-switch {
            width: 100%;
        }

        .my-summary-view-switch {
            display: grid;
        }

        .my-summary-view-option {
            width: 100%;
        }

        .my-summary-filter-modal .modal-dialog {
            max-width: calc(100vw - 1rem);
            margin: 0.5rem auto;
        }

        .my-summary-filter-modal .modal-header,
        .my-summary-filter-modal .modal-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .my-summary-date-range-row {
            grid-template-columns: 1fr;
        }

        .my-summary-filter-actions {
            flex-direction: column-reverse;
        }

        .my-summary-filter-clear,
        .my-summary-filter-submit {
            width: 100%;
        }

        .my-summary-content-wrapper {
            min-height: 160px;
        }

        .my-summary-chart-summary {
            justify-items: start;
            text-align: left;
        }

        .my-summary-chart-track {
            grid-auto-columns: minmax(72px, 1fr);
        }
    }

    @media (max-width: 520px) {
        .my-summary-section-shell {
            padding: 14px;
        }

        .my-summary-filter-row {
            grid-template-columns: 1fr;
        }

        .my-summary-filter-button {
            justify-content: flex-start;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .my-summary-filter-row {
            gap: 10px;
        }

        .my-summary-filter-button {
            flex: 0 1 auto;
        }
    }
</style>

@section('content')
    <section class="section my-summary-page">
        <div class="my-summary-score-wrapper" id="mySummaryScore">
            @include('projects.partials.my_summary_score')
        </div>

        <div class="card my-summary-card my-summary-section-shell" id="mySummaryDay">
            @include('projects.partials.my_summary_day')
        </div>

        <div class="card my-summary-card my-summary-section-shell" id="mySummarySection">
            <div class="my-summary-loading-overlay" id="mySummaryLoadingOverlay">
                <div class="my-summary-spinner"></div>
            </div>

            <div class="my-summary-top-area">
                <div class="my-summary-section-header">
                    <div class="my-summary-title-wrap">
                        <h3 class="mb-0" style="font-weight: 700">{{ __('Imputed Hours By Project') }}</h3>
                        <button type="button" class="my-summary-filter-toggle is-active" id="mySummaryFilterToggle"
                            aria-expanded="true" aria-label="{{ __('Toggle filters') }}"
                            onclick="
                            const w = document.getElementById('mySummaryFilterWrapper');
                            const isOp = w.classList.toggle('is-open');
                            this.classList.toggle('is-active', isOp);
                            this.setAttribute('aria-expanded', isOp);
                        ">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </div>

                <div class="my-summary-filter-wrapper is-open" id="mySummaryFilterWrapper">
                    <div class="my-summary-filter-inner">
                        <div class="my-summary-controls-row">
                            <div class="my-summary-filter-row">
                                <div id="mySummaryViewSwitch" class="my-summary-view-switch" role="tablist"
                                    aria-label="{{ __('Summary view mode') }}"
                                    data-chart-mode="{{ $isChartMode ? 'true' : 'false' }}">
                                    <button type="button"
                                        class="my-summary-view-option {{ $isChartMode ? '' : 'is-active' }}"
                                        id="mySummaryCardToggle" aria-pressed="{{ $isChartMode ? 'false' : 'true' }}">
                                        {{ __('Card view') }}
                                    </button>
                                    <button type="button"
                                        class="my-summary-view-option {{ $isChartMode ? 'is-active' : '' }}"
                                        id="mySummaryChartToggle" aria-pressed="{{ $isChartMode ? 'true' : 'false' }}">
                                        {{ __('Graph view') }}
                                    </button>
                                </div>
                                <a href="{{ route('my_summary', ['range_type' => 'preset', 'preset' => 'last_week']) }}"
                                    class="my-summary-filter-button my-summary-range-button {{ $selectedFilters['range_type'] === 'preset' && $selectedFilters['preset'] === 'last_week' ? 'is-active' : '' }}">
                                    {{ __('Last week') }}
                                </a>
                                <a href="{{ route('my_summary', ['range_type' => 'preset', 'preset' => 'last_month']) }}"
                                    class="my-summary-filter-button my-summary-range-button {{ $selectedFilters['range_type'] === 'preset' && $selectedFilters['preset'] === 'last_month' ? 'is-active' : '' }}">
                                    {{ __('Last month') }}
                                </a>
                                <a href="{{ route('my_summary', ['range_type' => 'preset', 'preset' => 'last_quarter']) }}"
                                    class="my-summary-filter-button my-summary-range-button {{ $selectedFilters['range_type'] === 'preset' && $selectedFilters['preset'] === 'last_quarter' ? 'is-active' : '' }}">
                                    {{ __('Last quarter') }}
                                </a>
                                <a href="{{ route('my_summary', ['range_type' => 'preset', 'preset' => 'last_year']) }}"
                                    class="my-summary-filter-button my-summary-range-button {{ $selectedFilters['range_type'] === 'preset' && $selectedFilters['preset'] === 'last_year' ? 'is-active' : '' }}">
                                    {{ __('Last year') }}
                                </a>
                                <button type="button"
                                    class="my-summary-filter-button my-summary-range-button my-summary-custom-trigger {{ $selectedFilters['range_type'] === 'custom' ? 'is-active' : '' }}"
                                    data-bs-toggle="modal" data-bs-target="#mySummaryCustomRangeModal">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    {{ __('Custom Range') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade my-summary-filter-modal" id="mySummaryCustomRangeModal" tabindex="-1"
                    aria-labelledby="mySummaryCustomRangeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mySummaryCustomRangeModalLabel">{{ __('Custom Range') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="{{ __('Close') }}"></button>
                            </div>
                            <div class="modal-body">
                                <form method="GET" action="{{ route('my_summary') }}" class="my-summary-filter-form">
                                    <input type="hidden" name="range_type" value="custom">

                                    <div class="my-summary-filter-modal-grid">
                                        <div class="my-summary-filter-block">
                                            <span class="my-summary-filter-label">{{ __('Date range') }}</span>
                                            <div class="my-summary-date-range-row">
                                                <div>
                                                    <span class="my-summary-filter-label">{{ __('From') }}</span>
                                                    <input type="date" name="start_date"
                                                        value="{{ $selectedFilters['start_date'] }}"
                                                        class="my-summary-filter-control" required>
                                                </div>
                                                <div>
                                                    <span class="my-summary-filter-label">{{ __('To') }}</span>
                                                    <input type="date" name="end_date"
                                                        value="{{ $selectedFilters['end_date'] }}"
                                                        class="my-summary-filter-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-summary-filter-actions">
                                        {{-- <a href="{{ route('my_summary', ['range_type' => 'preset', 'preset' => 'last_month']) }}"
                                            class="my-summary-filter-clear">{{ __('Reset to last month') }}</a> --}}
                                        <button type="submit"
                                            class="my-summary-filter-submit">{{ __('Apply') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-summary-content-wrapper">
                    <div class="my-summary-visualization-shell {{ $isChartMode ? '' : 'is-hidden' }}"
                        id="mySummaryChartShell">
                        <div class="my-summary-chart-header">
                            <div>
                                <span class="my-summary-chart-kicker">{{ __('Graph View') }}</span>
                                <h5 class="my-summary-chart-title">{{ __('Hours By Project') }}</h5>
                            </div>
                            <div class="my-summary-chart-summary">
                                <span class="my-summary-chart-range"
                                    id="mySummaryChartRange">{{ $chartData['rangeLabel'] }}</span>
                                <strong class="my-summary-chart-total"
                                    id="mySummaryChartTotal">{{ $chartData['totalTime'] }}</strong>
                            </div>
                        </div>
                        <div class="my-summary-chart-scroll">
                            <div class="my-summary-chart-root" id="mySummaryChart"></div>
                        </div>
                    </div>
                    <div id="mySummaryContent" class="my-summary-content-panel {{ $isChartMode ? 'is-hidden' : '' }}">
                        @include('projects.partials.my_summary_content')
                    </div>
                </div>
            </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const summaryContainer = document.getElementById('mySummaryContent');
            const myDayContainer = document.getElementById('mySummaryDay');
            const scoreContainer = document.getElementById('mySummaryScore');
            const chartContainer = document.getElementById('mySummaryChart');
            const chartShell = document.getElementById('mySummaryChartShell');
            const chartRange = document.getElementById('mySummaryChartRange');
            const chartTotal = document.getElementById('mySummaryChartTotal');
            const cardToggle = document.getElementById('mySummaryCardToggle');
            const chartToggle = document.getElementById('mySummaryChartToggle');
            const viewSwitch = document.getElementById('mySummaryViewSwitch');
            const customModal = document.getElementById('mySummaryCustomRangeModal');
            let chartData = @json($chartData);
            let isChartMode = @json($isChartMode);
            let bootstrapModal = null;
            if (typeof bootstrap !== 'undefined') {
                bootstrapModal = new bootstrap.Modal(customModal);
            }

            renderChart(chartData);
            applyVisualizationMode();

            const crossFilterKeys = [
                'range_type',
                'preset',
                'start_date',
                'end_date',
                'my_day_range_type',
                'my_day_preset',
                'my_day_start_date',
                'my_day_end_date',
                'my_day_status',
            ];

            document.querySelectorAll('a.my-summary-filter-button, a.my-summary-filter-clear').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();

                    document.querySelectorAll('.my-summary-range-button').forEach(btn => btn
                        .classList.remove('is-active'));
                    if (this.classList.contains('my-summary-range-button')) {
                        this.classList.add('is-active');
                    } else if (this.classList.contains('my-summary-filter-clear')) {
                        document.querySelector('a.my-summary-range-button[href*="last_month"]')
                            .classList.add('is-active');
                    }

                    const url = withChartMode(withPreservedFilters(this.getAttribute('href')));
                    fetchAndUpdate(url, 'summary');

                    if (this.closest('.modal')) {
                        if (bootstrapModal) bootstrapModal.hide();
                    }
                });
            });

            const form = document.querySelector('.my-summary-filter-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    document.querySelectorAll('.my-summary-range-button').forEach(btn => btn.classList
                        .remove('is-active'));
                    document.querySelector('.my-summary-custom-trigger').classList.add('is-active');

                    const formData = new FormData(this);
                    const params = new URLSearchParams(formData);
                    const url = withChartMode(withPreservedFilters(this.getAttribute('action') + '?' +
                        params
                        .toString()));

                    fetchAndUpdate(url, 'summary');

                    if (bootstrapModal) bootstrapModal.hide();
                });
            }

            if (cardToggle) {
                cardToggle.addEventListener('click', function() {
                    if (!isChartMode) {
                        return;
                    }

                    isChartMode = false;
                    applyVisualizationMode();
                    window.history.replaceState({}, '', withChartMode(window.location.pathname + window
                        .location.search));
                });
            }

            if (chartToggle) {
                chartToggle.addEventListener('click', function() {
                    if (isChartMode) {
                        return;
                    }

                    isChartMode = true;
                    applyVisualizationMode();
                    window.history.replaceState({}, '', withChartMode(window.location.pathname + window
                        .location.search));
                });
            }

            document.addEventListener('click', function(e) {
                const dayButton = e.target.closest(
                    'a.my-day-filter-button, a.my-day-filter-clear, a.my-day-status-toggle');
                if (!dayButton) {
                    return;
                }

                e.preventDefault();

                if (myDayContainer) {
                    if (dayButton.classList.contains('my-day-status-toggle')) {
                        const switchContainer = dayButton.closest('.my-summary-view-switch');
                        if (switchContainer) {
                            switchContainer.querySelectorAll('.my-day-status-toggle').forEach(btn => btn
                                .classList.remove('is-active'));
                            dayButton.classList.add('is-active');
                            switchContainer.setAttribute('data-status-mode', dayButton.id ===
                                'myDayTodoToggle' ? 'todo' : 'progress');
                        }
                    } else if (dayButton.classList.contains('my-day-range-button')) {
                        myDayContainer.querySelectorAll('.my-day-range-button').forEach(btn => btn.classList
                            .remove('is-active'));
                        dayButton.classList.add('is-active');
                    } else {
                        myDayContainer.querySelectorAll('.my-day-range-button').forEach(btn => btn.classList
                            .remove('is-active'));
                    }
                }

                const url = withChartMode(withPreservedFilters(dayButton.getAttribute('href')));
                fetchAndUpdate(url, 'my-day');

                if (dayButton.closest('.modal')) {
                    hideMyDayModal();
                }
            });

            document.addEventListener('submit', function(e) {
                const dayForm = e.target.closest('.my-day-filter-form');
                if (!dayForm) {
                    return;
                }

                e.preventDefault();

                if (myDayContainer) {
                    myDayContainer.querySelectorAll('.my-day-range-button').forEach(btn => btn.classList
                        .remove(
                            'is-active'));
                    const customTrigger = myDayContainer.querySelector('.my-day-custom-trigger');
                    if (customTrigger) {
                        customTrigger.classList.add('is-active');
                    }
                }

                const params = new URLSearchParams(new FormData(dayForm));
                const url = withChartMode(withPreservedFilters(dayForm.getAttribute('action') + '?' + params
                    .toString()));
                fetchAndUpdate(url, 'my-day');
                hideMyDayModal();
            });

            function hideMyDayModal() {
                const myDayModal = document.getElementById('myDayCustomRangeModal');
                if (!myDayModal || typeof bootstrap === 'undefined') {
                    return;
                }

                const modalInstance = bootstrap.Modal.getInstance(myDayModal) || new bootstrap.Modal(myDayModal);
                modalInstance.hide();
            }

            function withPreservedFilters(url) {
                const parsedUrl = new URL(url, window.location.origin);
                const currentUrl = new URL(window.location.href);

                crossFilterKeys.forEach(key => {
                    if (!parsedUrl.searchParams.has(key) && currentUrl.searchParams.has(key)) {
                        parsedUrl.searchParams.set(key, currentUrl.searchParams.get(key));
                    }
                });

                return parsedUrl.pathname + parsedUrl.search;
            }

            function withChartMode(url) {
                const parsedUrl = new URL(url, window.location.origin);

                if (isChartMode) {
                    parsedUrl.searchParams.set('chart_mode', '1');
                } else {
                    parsedUrl.searchParams.delete('chart_mode');
                }

                return parsedUrl.pathname + parsedUrl.search;
            }

            function applyVisualizationMode() {
                if (viewSwitch) {
                    viewSwitch.dataset.chartMode = isChartMode ? 'true' : 'false';
                }

                chartShell.classList.toggle('is-hidden', !isChartMode);
                summaryContainer.classList.toggle('is-hidden', isChartMode);
                if (cardToggle) {
                    cardToggle.classList.toggle('is-active', !isChartMode);
                    cardToggle.setAttribute('aria-pressed', isChartMode ? 'false' : 'true');
                }

                chartToggle.classList.toggle('is-active', isChartMode);
                chartToggle.setAttribute('aria-pressed', isChartMode ? 'true' : 'false');
            }

            function renderChart(data) {
                if (!chartContainer) {
                    return;
                }

                chartContainer.innerHTML = '';

                if (chartRange && data.rangeLabel !== undefined) {
                    chartRange.textContent = data.rangeLabel;
                }

                if (chartTotal && data.totalTime !== undefined) {
                    chartTotal.textContent = data.totalTime;
                }

                if (!data.projects || !data.projects.length) {
                    chartContainer.innerHTML = `
                        <div class="my-summary-chart-empty">
                            <div>
                                <div class="my-summary-empty-icon mx-auto mb-3">
                                    <i class="fa-solid fa-chart-column"></i>
                                </div>
                                <h3>{{ __('No chart data available') }}</h3>
                                <p>{{ __('There are no imputed hours for the selected range') }}</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                const rawMaxHours = data.maxHours && data.maxHours > 0 ? data.maxHours : 1;
                const scale = buildNiceChartScale(rawMaxHours);

                const layout = document.createElement('div');
                layout.className = 'my-summary-chart-layout';

                const yAxis = document.createElement('div');
                yAxis.className = 'my-summary-chart-y-axis';
                for (let i = scale.ticks.length - 1; i >= 0; i--) {
                    const span = document.createElement('span');
                    span.textContent = formatChartTick(scale.ticks[i]);
                    yAxis.appendChild(span);
                }

                const scrollArea = document.createElement('div');
                scrollArea.className = 'my-summary-chart-scroll';

                const innerArea = document.createElement('div');
                innerArea.className = 'my-summary-chart-inner';

                const gridLines = document.createElement('div');
                gridLines.className = 'my-summary-chart-grid-lines';
                for (let i = 0; i < scale.ticks.length; i++) {
                    const line = document.createElement('div');
                    line.className = 'my-summary-chart-grid-line';
                    gridLines.appendChild(line);
                }

                const track = document.createElement('div');
                track.className = 'my-summary-chart-track';
                applyChartDensity(track, data.projects.length);

                data.projects.forEach(project => {
                    const column = document.createElement(project.project_url ? 'a' : 'div');
                    const height = Math.max((project.hours / scale.max) * 100, 1.5);

                    column.className = project.project_url ?
                        'my-summary-chart-column-link' :
                        'my-summary-chart-column';

                    if (project.project_url) {
                        column.href = project.project_url;
                    }

                    column.innerHTML = `
                        <span class="my-summary-chart-bar-wrap" title="${project.formatted_time} - ${escapeHtml(project.name)}">
                            <span class="my-summary-chart-bar" style="height:${height}%"></span>
                        </span>
                        <span class="my-summary-chart-label" title="${escapeHtml(project.name)}">${escapeHtml(project.short_name)}</span>
                    `;

                    track.appendChild(column);
                });

                innerArea.appendChild(gridLines);
                innerArea.appendChild(track);
                scrollArea.appendChild(innerArea);

                layout.appendChild(yAxis);
                layout.appendChild(scrollArea);
                chartContainer.appendChild(layout);
            }

            function buildNiceChartScale(maxHours) {
                const safeMax = Number(maxHours) > 0 ? Number(maxHours) : 1;

                if (safeMax <= 5) {
                    return {
                        max: 5,
                        ticks: [0, 1, 2, 3, 4, 5],
                    };
                }

                const ticksCount = 4;
                const roughStep = safeMax / ticksCount;
                const magnitude = Math.pow(10, Math.max(Math.floor(Math.log10(roughStep || 1)) - 1, 0));
                const candidates = [5, 10, 15, 20, 25, 30, 40, 50, 75, 100].map(value => value * magnitude);
                const step = candidates.find(value => value >= roughStep) || (100 * magnitude);
                const ticks = [];

                for (let i = 0; i <= ticksCount; i++) {
                    ticks.push(i * step);
                }

                return {
                    max: step * ticksCount,
                    ticks,
                };
            }

            function formatChartTick(value) {
                const roundedValue = Number.isInteger(value) ? value : Number(value.toFixed(1));
                return `${roundedValue}h`;
            }

            function applyChartDensity(track, projectCount) {
                const density = getChartDensity(projectCount);

                track.style.gridAutoColumns = `minmax(${density.minColumnWidth}px, ${density.maxColumnWidth}px)`;
                track.style.gap = `${density.gap}px`;
                track.style.justifyContent = density.justifyContent;
                track.style.minWidth = density.minWidth;
                track.style.setProperty('--my-summary-chart-bar-max-width', `${density.barMaxWidth}px`);
            }

            function getChartDensity(projectCount) {
                if (projectCount <= 1) {
                    return {
                        minColumnWidth: 220,
                        maxColumnWidth: 280,
                        barMaxWidth: 120,
                        gap: 0,
                        justifyContent: 'center',
                        minWidth: '100%',
                    };
                }

                if (projectCount === 2) {
                    return {
                        minColumnWidth: 170,
                        maxColumnWidth: 220,
                        barMaxWidth: 96,
                        gap: 32,
                        justifyContent: 'space-evenly',
                        minWidth: '100%',
                    };
                }

                if (projectCount <= 4) {
                    return {
                        minColumnWidth: 120,
                        maxColumnWidth: 170,
                        barMaxWidth: 76,
                        gap: 20,
                        justifyContent: 'center',
                        minWidth: '100%',
                    };
                }

                if (projectCount <= 8) {
                    return {
                        minColumnWidth: 92,
                        maxColumnWidth: 120,
                        barMaxWidth: 58,
                        gap: 12,
                        justifyContent: 'flex-start',
                        minWidth: '100%',
                    };
                }

                return {
                    minColumnWidth: 72,
                    maxColumnWidth: 96,
                    barMaxWidth: 48,
                    gap: 8,
                    justifyContent: 'flex-start',
                    minWidth: `max(100%, ${Math.max(projectCount * 84, 320)}px)`,
                };
            }

            function escapeHtml(value) {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function fetchAndUpdate(url, loadingTarget = 'summary') {
                const overlay = getLoadingOverlay(loadingTarget);
                if (overlay) {
                    overlay.classList.add('is-active');
                }

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (scoreContainer && data.scoreHtml !== undefined) {
                            scoreContainer.innerHTML = data.scoreHtml;
                        }
                        if (myDayContainer && data.myDayHtml !== undefined) {
                            myDayContainer.innerHTML = data.myDayHtml;
                        }
                        summaryContainer.innerHTML = data.contentHtml;
                        if (data.chartData !== undefined) {
                            chartData = data.chartData;
                            renderChart(chartData);
                        }
                        applyVisualizationMode();
                        if (overlay) {
                            overlay.classList.remove('is-active');
                        }
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Error fetching summary:', error);
                        if (overlay) {
                            overlay.classList.remove('is-active');
                        }
                    });
            }

            function getLoadingOverlay(loadingTarget) {
                if (loadingTarget === 'my-day') {
                    return ensureSectionOverlay(myDayContainer, 'mySummaryDayLoadingOverlay');
                }

                return document.getElementById('mySummaryLoadingOverlay');
            }

            function ensureSectionOverlay(container, overlayId) {
                if (!container) {
                    return null;
                }

                let overlay = document.getElementById(overlayId);
                if (overlay && overlay.parentElement === container) {
                    return overlay;
                }

                overlay = document.createElement('div');
                overlay.id = overlayId;
                overlay.className = 'my-summary-loading-overlay';
                overlay.innerHTML = '<div class="my-summary-spinner"></div>';
                container.appendChild(overlay);

                return overlay;
            }
        });
    </script>
@endsection
