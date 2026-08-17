@extends('layouts.admin')
<!-- @include('tutorial.projectTutorial') -->
@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';

@endphp


<style>
    .pro-status {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    @media screen and (max-width: 1200px) and (min-width:1000px) {
        .taskRowWidth {
            width: 32% !important;
        }

        .colWidthTask {
            width: 49% !important;
        }
    }

    /* /New Styling/ */
    .summary {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-top: 10px;
        /* border: 1px solid black; */
        height: 120px;
        align-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .tabs {
        height: 100%;
        width: 32%;
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
    }

    .ctr {
        display: flex;
        align-items: center;
    }

    /* ============================================================
       Modern Summary Cards Layout
       ============================================================ */
    .tabs.ctr {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 20px;
        align-items: start;
        padding: 24px 28px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.10);
        border: 1px solid #e5e7eb;
        height: 100%;
        transition: box-shadow 0.2s ease;
        min-width: 0;
    }
    .tabs.ctr:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .tabs.ctr:has(.projectIcon) { border-color: #a1cd80; }
    .tabs.ctr:has(.milestoneIcon) { border-color: rgb(174 154 247); }
    .tabs.ctr:has(.taskIcon) { border-color: #72c8d4; }

    .card-header-inner {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 8px;
        min-width: 0;
        margin-bottom: 0;
        align-self: stretch;
        padding-right: 12px;
        border-right: 1px solid #e5e7eb;
    }

    .statusContainer {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 180px;
        width: 100%;
    }

    .stat-row {
        display: grid;
        grid-template-columns: auto 1fr auto;
        column-gap: 12px;
        align-items: center;
        min-width: 0;
        width: 100%;
    }

    .stat-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-left: 0;
    }

    .stat-label {
        color: #111827;
        font-weight: 500;
        font-size: 14px;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: left;
        padding-right: 8px;
    }

.stat-value {
        font-size: 14px;
        font-weight: 600;
        color: #6b7280;
        background: #fff;
        /* border: 1px solid #e5e7eb; */
        border-radius: 8px;
        /* padding: 2px 10px; */
        min-width: 44px;
        text-align: right;
        white-space: nowrap;
    }

.stat-dot--pending { background: #ff0000a3; }
    .stat-dot--review { background: #9ca3af; }
    .stat-dot--active { background: #22c55e; }
    .stat-dot--info { background: #53b446e0; }
    .stat-dot--warning { background: #f59e0b; }
    .stat-dot--change { background: #ff0000a3; }
    .stat-dot--info1 { background: #3b82f6; }

    /* Activity card: Prioridad de mis encargos */
    .stat-dot--sin-asignar { background: #a62330; }
    .stat-dot--por-revisar { background: #DB8D33; }
    .stat-dot--activo { background: #68bd5c; }
    .stat-dot--en-pausa { background: #9ca3af; }

    /* Global Projects card */
    .stat-dot--en-espera { background: #FFA21D; }
    .stat-dot--en-curso { background: #6C757D; }
    .stat-dot--finalizado { background: #6FD943; }

    .titleTecAndCom {
        padding-left: 10px;
        font-size: 16px;
    }

    .tabIcon {
        margin-left: 0;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        position: relative;
        background-color: #AA182C;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 10px;
        filter: drop-shadow(0px 1px 3px rgba(0, 0, 0, 0.2));
    }

    .tabIcon-badge {
        position: absolute;
        bottom: 0;
        right: 1px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #AA182C;
        color: #fff;
    }

    .projectIcon .tabIcon-badge { background: #a1cd80; }
    .milestoneIcon .tabIcon-badge { background: rgb(174 154 247); }
    .taskIcon .tabIcon-badge { background: #72c8d4; }

    .icons {
        width: 22px;
        height: 20px;
        filter: invert(1);
    }

    .projectIcon {
        background-color: #a1cd80;
    }

    .milestoneIcon {
        background-color: rgb(174 154 247);
    }

    .taskIcon {
        background-color: #72c8d4;
    }

    .tabTexts {
        margin-left: 0;
        font-size: 18px;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }

    .tabNumCounter {
        margin-left: 0;
        margin-top: 8px;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 30px;
        font-size: 15px;
        color: #AA182C;
        font-weight: 700;
        border: 1px solid #AA182C;
        border-radius: 7px;
        box-shadow: 2px 2px 5px 0px rgb(0 0 0 / 30%);
        flex-shrink: 0;
    }

    .projectIcon ~ .tabNumCounter { color: #a1cd80; border-color: #a1cd80; }
    .milestoneIcon ~ .tabNumCounter { color: rgb(174 154 247); border-color: rgb(174 154 247); }
    .taskIcon ~ .tabNumCounter { color: #72c8d4; border-color: #72c8d4; }

    .mst {
        width: 90% !important;
        display: flex !important;
        justify-content: space-between !important;
    }

    .mst>.statusNumContainer {
        margin-right: 10px;
    }

    .mst>.statusText {
        margin-left: 10px;
    }

    .milestoneTab {
        margin-left: 5% !important;
        width: 38% !important;
    }

    .stickyComercialTec {
        width: 97%;
        height: 100px;
        background-color: F8FAF9;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        position: absolute;
        bottom: 0;

    }

    .dropDownCT {
        background-color: #F8FAF9;
        box-shadow: inset 0px 8px 2px rgb(0 0 0 / 22%);
        /* border: 1px solid red; */
        border-top-left-radius: 23px;
        border-top-right-radius: 23px;
    }

    .dropdownHeaders {
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
        height: 100%;
        position: relative;
    }

    .dropdownHeaders>span {
        font-size: 20px;
        font-weight: 600;
        margin-left: 10px;
    }

    .comercial {
        width: 40%;
        height: 50px;
        /* background-color: #AA182C; */
    }

    .technicians {
        width: 40%;
        height: 50px;
        /* background-color: yellow; */
    }

    .comercialTecIcons {
        height: 23px;
        width: 34px;
        display: inline-flex;
        margin-bottom: 4px;
    }

    .dropdownContent {
        height: 469px;
        display: flex;
        flex-direction: column;
        align-content: center;
        align-items: center;
        background-color: #f8faf9;
        border-top: 3px solid #c2c3c2;
        border-right: 3px solid #c2c3c2;
        border-left: 3px solid #c2c3c2;
        overflow: scroll;
    }

    .dropdownContent::-webkit-scrollbar {
        width: 0px;
        background: transparent;
        /* Opcional: para asegurarse de que el área del scrollbar no sea visible */
    }

    .comercialAndTechnicians {
        background-color: #F8FAF9;
        margin-top: 20px;
        height: 60px;
        width: 99%;
        border-radius: 9px;
        box-shadow: 0px 0px 5px rgb(0 0 0 / 22%);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 20px;
        padding: 16 15px;
        position: relative;
        padding-right: 32px;
    }
    .email-icon-fixed {
        position: absolute;
        /* top: 50%; */
        right: 12px;
        /* transform: translateY(-50%); */
        font-size: 16px;
        cursor: pointer;
        color: #6c757d;
        transition: color 0.2s, transform 0.2s;
        line-height: 1;
        z-index: 1;
    }
    .email-icon-fixed:hover {
        color: #8f1425;
        transform: scale(1.15);
    }

    .ppcontainer {
        flex-shrink: 0;
        /* Prevent resizing of profile picture container */
    }

    .profilePicture {
        width: 40px;
        height: 40px;
        background-color: red;
        border-radius: 100%;
        margin-left: 12px;
    }

    .textContent {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-grow: 1;
        /* Allow the text content to take remaining space */
        text-align: center;
    }

    .fullName {
        font-size: larger;
        font-weight: 600;
    }

    .emailName {
        font-size: large;
        color: #949494;
        white-space: nowrap;
    }

    .statusContainer {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        height: 100%;
        width: 100%;
        justify-content: center;
        gap: 0px;

    }

    .status {
        height: 28px;
        color: black;
        font-size: 12px;
        width: 77%;
        border-radius: 7px;
        text-align: center;
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        padding: 1px;
        box-shadow: 1px 2px 1px 0px #00000061;

    }

    .hold {
        background-color: #e06c71;
    }

    .progressstat {
        background-color: #d3d3d3;
    }

    .ended {
        background-color: #c9edb9;
    }

    .statusNum {
        color: black
    }

    .statusNumContainer {
        background-color: white;
        border-radius: 100%;
        padding-left: 5px;
        padding-right: 5px;
    }

    .statusText {
        padding: 2px;
    }

    .testContent {
        background-color: yellow;
    }

    .hiddenTuto {
        display: none;
    }

    .formControlModified {
        height: 41px !important;
    }

    .modifiedDivTecAndCom {
        max-height: 400px;
        /* Asegura que el div tenga un límite de altura */
        overflow-y: auto;
        /* Permite el desplazamiento vertical si hay demasiado contenido */
        overflow-x: hidden;
        /* Evita desplazamiento horizontal */
        height: auto;
        /* Se ajusta automáticamente sin forzar una altura fija */
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        margin-bottom: 20px;
    }

    .divStatisticsButtons {
        margin-bottom: 10px;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
    }

    .divStatisticsButtons .btn.btn-primary.active {
        background: #AA182C !important;
        color: #fff !important;
        border-color: #AA182C !important;
        box-shadow: 0 0 0 2px rgba(170, 24, 44, 0.4) !important;
    }

    .divStatisticsButtons .btn-primary:not(.active) {
        background: #6c757d;
        border-color: #6c757d;
    }

    .formControlModified {
        cursor: pointer;
        width: 10% !important;
        margin-right: 1%;
        margin-left: 1%;
    }

    .marginRight1 {
        margin-right: 1%;
    }

    .yearListDiv {
        position: absolute;
        background: white;
        border: 1px solid rgb(204, 204, 204);
        width: 10%;
        left: 30px;
        z-index: 1000;
        top: 23%;
        border-radius: 10px;
        display: none;
    }

    .yearOption:hover {
        background-color: #AA182C;
        color: white;
        border-radius: 8px;
    }

    @media screen and (min-width:1440px) and (max-width: 1490px) {

        /* *{
        border: 3px solid green;
    } */
        .status {
            width: 95%;
        }

        .stickyComercialTec {
            height: 0px;
        }
    }


    .filter-input {
        width: 97%;
        padding: 10px;
        margin: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 0;
    }

    .milestonesTextSpan {
        font-size: 13px;
        font-weight: 800;
        margin-left: 9px;
    }

    .displayFlexAlignCenter {
        display: flex;
        align-content: center;
        align-items: center;
    }

    .alignArrowSelect {
        position: absolute;
        left: 10%;
        top: 102px;
    }

    @media screen and (max-width:1441px) and (min-width:1000px) {

        /* * {
            border: 1px solid magenta;
        } */
        .tabIcon {
            margin-left: 0;
        }

        .tabTexts {
            font-size: 16px;
        }

        .status {
            width: 100% !important;
        }

        .milestoneTab {
            width: 80% !important;
            margin-right: 6px;
        }

        .statusText {
            font-size: 9px;
            font-weight: 600;

        }

        .statusContainer {
            margin-left: 0;
        }

        .milestoneIcon {
            padding: 2px;
        }
    }

    @media screen and (min-width: 1439px) and (max-width:1600px) {
        .milestoneTab {
            /* width: 45% !important; */
            /* width: 114px !important; */

        }

        .milestoneIcon {
            margin-left: 0;
        }
    }

    /* Summary toggle track */
    .summary-wrapper {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .view-selector {
        display: inline-flex;
        padding: 4px;
        background: #f4f5f7;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        gap: 4px;
        margin-top: 4px;
        float: right;
        margin-bottom: 10px;
    }

    .view-segment {
        border: none;
        background: transparent;
        border-radius: 10px;
        padding: 10px 18px;
        height: 38px;
        font-size: 13px;
        font-weight: 600;
        color: #5b6472;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 1.3s cubic-bezier(0.4, 0, 0.2, 1),
                    color 1.3s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 1.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .view-segment:hover:not(.active) {
        background: #eceef3;
    }

    .view-segment.active {
        background: #AA182C;
        color: white;
        box-shadow: 0 4px 12px rgba(170, 24, 44, 0.25);
    }

    .view-segment i {
        font-size: 14px;
    }

    .summary-track {
        display: flex;
        width: 199.9%;
        height: 100%;
        will-change: transform;
    }

    .summary-page {
        width: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: stretch;
        transform: translateX(0) scale(1);
        opacity: 1;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform, opacity;
        margin-bottom: 20px;
    }

    .summary-page:first-child {
        z-index: 2;
    }

    .summary-page:last-child {
        z-index: 1;
    }

    .summary-page:first-child.page-out {
        opacity: 0;
        transform: translateX(-20%) scale(0.85);
        pointer-events: none;
    }

    .summary-page:last-child.page-out {
        opacity: 0;
        transform: translateX(20%) scale(0.85);
        pointer-events: none;
    }

    .summary-page:first-child.page-in-start {
        opacity: 0;
        transform: translateX(-20%) scale(0.85);
        transition: none;
        pointer-events: none;
    }

    .summary-page:last-child.page-in-start {
        opacity: 0;
        transform: translateX(20%) scale(0.85);
        transition: none;
        pointer-events: none;
    }

    .summary-page .summary {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        margin: 0;
    }

    .summary-wrapper.expanded .summary-track {
        transform: translateX(-50%);
    }

    .empty-card {
        background: #fff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 100% !important;
    }
    .empty-card-content {
        text-align: center;
        padding: 1rem;
    }
    .empty-label {
        color: #6c757d;
        font-weight: 500;
        font-size: 14px;
    }

    .tabTexts--long {
        font-size: 14px;
        line-height: 1.4;
        margin-left: 0;
        margin-right: -50px;
        text-align: left;
        white-space: normal;
        overflow-wrap: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        max-width: 100%;
    }

    @media screen and (max-width: 999px) {
        .tabs.ctr {
            padding: 16px 16px;
            gap: 6px;
        }

        .card-header-inner {
            max-width: 55px;
            padding-right: 4px;
        }

        .tabIcon {
            margin-left: 0;
            width: 34px;
            height: 34px;
        }

        .icons {
            width: 22px;
            height: 20px;
        }

        .tabTexts {
            margin-left: 0;
            font-size: 13px;
            max-width: 50px;
        }

        .tabTexts--long {
            font-size: 11px;
            max-width: 50px;
        }

        .tabNumCounter {
            width: 30px;
            height: 26px;
        }

        .tabIcon-badge {
            width: 13px;
            height: 13px;
            font-size: 10px;
        }

        .statusContainer {
            min-width: 120px;
        }

        .stat-dot {
            margin-left: 0;
        }
    }
</style>

@section('content')
    <section class="section" style="margin-top: -30px;">
        @if (Auth::user()->type == 'admin')
            <div class="row">
                <div class="col-12">
                    @if (empty(env('PUSHER_APP_ID')) ||
                            empty(env('PUSHER_APP_KEY')) ||
                            empty(env('PUSHER_APP_SECRET')) ||
                            empty(env('PUSHER_APP_CLUSTER')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Pusher Detail in Setting Page ') }}<u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                    @if (empty(env('MAIL_DRIVER')) ||
                            empty(env('MAIL_HOST')) ||
                            empty(env('MAIL_PORT')) ||
                            empty(env('MAIL_USERNAME')) ||
                            empty(env('MAIL_PASSWORD')) ||
                            empty(env('MAIL_PASSWORD')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Mail Details in Setting Page ') }} <u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <div class="row">

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Paid User') }} : <strong>{{ $totalPaidUsers }}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Users') }}</h6>
                                    <h3 class="mb-0">{{ $totalUsers }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-success">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">

                                        {{ __('Order Amount') }} :
                                        <strong>{{ (env('CURRENCY_SYMBOL') != '' ? env('CURRENCY_SYMBOL') : '$') . $totalOrderAmount }}</strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Orders') }}</h6>
                                    <h3 class="mb-0">{{ $totalOrders }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body total_plan">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Most purchase plan') }} : <strong>
                                            @if ($mostPlans)
                                                {{ $mostPlans->name }}
                                            @else
                                                -
                                            @endif
                                        </strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Plans') }}</h6>
                                    <h3 class="mb-0">{{ $totalPlans }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <h5>{{ __('Recent Orders') }}</h5>
                                </div>
                                <div class=" col-2"><small class="text-end"></small></div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- <div id="task-area-chart"></div> --}}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($currentWorkspace)
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <!-- <div class="row"> -->
                    <div class="page-header-title">
                        <h4 class="m-b-10">{{ __('Resume of') }} {{ $currentWorkspace->display_name }}</h4>
                    </div>
                    <div class="view-selector" id="viewSelector">
                        <button type="button" class="view-segment active" data-view="global">
                            <i class="fas fa-chart-column"></i>
                            <span>{{ __('Resumen global') }}</span>
                        </button>
                        <button type="button" class="view-segment" data-view="activity">
                            <i class="fas fa-user"></i>
                            <span>{{ 'Mi actividad' }}</span>
                        </button>
                    </div>
                    <div class="summary-wrapper" id="summaryWrapper">
                        <div class="summary-track" id="summaryTrack">
                            <div class="summary-page">
                                <div class="summary">
                                    <div class="tabs ctr">
                                        <div class="card-header-inner">
                                            <div class="tabIcon projectIcon">
                                                <img class="icons"
                                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/project-diagram.svg') }}"
                                                    alt="logo" />

                                            </div>
                                            <div class="tabTexts">
                                                {{ __('Projects') }}
                                            </div>
                                            <div class="tabTexts tabNumCounter">
                                                <span>
                                                    {{ $totalProject ?? 0 }}
                                                </span>

                                            </div>
                                        </div>
                                        <div class="statusContainer">
<div class="stat-row">
                                            <span class="stat-dot stat-dot--en-curso"></span>
                                            <span class="stat-label">{{ __('En curso') }}</span>
                                            <span class="stat-value">{{ $projectProcess['Ongoing'] ?? 0 }}</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-dot stat-dot--en-espera"></span>
                                            <span class="stat-label">{{ __('En espera') }}</span>
                                            <span class="stat-value">{{ $projectProcess['OnHold'] ?? 0 }}</span>
                                        </div>
                                        <div class="stat-row">
                                            <span class="stat-dot stat-dot--finalizado"></span>
                                            <span class="stat-label">{{ __('Finished') }}</span>
                                            <span class="stat-value">{{ $projectProcess['Finished'] ?? 0 }}</span>
                                        </div>

                                        </div>
                                    </div>
                                    <div class="tabs ctr">
                                        <div class="card-header-inner">
                                            <div class="tabIcon milestoneIcon">
                                                <img class="icons"
                                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/file-alt.svg') }}"
                                                    alt="logo" />

                                            </div>
                                            <div class="tabTexts">
                                                {{ __('Milestones') }}
                                            </div>
                                            <div class="tabTexts tabNumCounter">
                                                <span>
                                                    {{ $totalMilestonesGlobal ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="statusContainer">
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--activo"></span>
                                                <span class="stat-label">{{ __('Active') }}</span>
                                                <span class="stat-value">{{ $activeMilestones ?? 0 }}</span>
                                            </div>
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--por-revisar"></span>
                                                <span class="stat-label">{{ __('Under Review') }}</span>
                                                <span class="stat-value">{{ $reviewMilestones ?? 0 }}</span>
                                            </div>
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--finalizado"></span>
                                                <span class="stat-label">{{ __('Finished') }}</span>
                                                <span class="stat-value">{{ $finishedMilestones ?? 0 }}</span>
                                            </div>
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--sin-asignar"></span>
                                                <span class="stat-label">{{ __('Unassigned') }}</span>
                                                <span class="stat-value">{{ $unassignedMilestones ?? 0 }}</span>
                                            </div>
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--en-pausa"></span>
                                                <span class="stat-label">{{ __('Paused') }}</span>
                                                <span class="stat-value">{{ $pausedMilestones ?? 0 }}</span>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tabs ctr">
                                        <div class="card-header-inner">
                                            <div class="tabIcon taskIcon">
                                                <img class="icons"
                                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/tasks.svg') }}"
                                                    alt="logo" />

                                            </div>
                                            <div class="tabTexts">
                                                {{ __('Global tasks') }}
                                            </div>
                                            <div class="tabTexts tabNumCounter">
                                                <span>
                                                    {{ $totalTask ?? 0 }}
                                                </span>

                                            </div>
                                        </div>
                                        <div class="statusContainer">
                                            @foreach ($totalTaskByType ?? [] as $type => $count)
                                                <div class="stat-row">
                                                    <span class="stat-dot stat-dot--info1"></span>
                                                    <span class="stat-label">{{ __($type) }}</span>
                                                    <span class="stat-value">{{ $count }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="summary-page">
                                <div class="summary">
                                    <div class="tabs ctr" data-card="1">
                                        <div class="card-header-inner">
                                            <div class="tabIcon projectIcon">
                                                <img class="icons"
                                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/clipboard-list.svg') }}"
                                                    alt="logo" />
                                                <span class="tabIcon-badge"><i class="fas fa-user"></i></span>
                                            </div>
                                            <div class="tabTexts">
                                                {{ __('Mis encargos asignados') }}
                                            </div>
                                            <div class="tabTexts tabNumCounter">
                                                <span>
                                                    {{ ($myEnPlazoMilestones + $myFueraPlazoMilestones + $myEnRevisionMilestones + $myFinalizadosMilestones + $myEnPausaMilestones) ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="statusContainer">
                                            @if ((int) ($myEnPlazoMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--active"></span>
                                                <span class="stat-label">{{ __('En plazo') }}</span>
                                                <span class="stat-value">{{ $myEnPlazoMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myFueraPlazoMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--pending"></span>
                                                <span class="stat-label">{{ __('Fuera de plazo') }}</span>
                                                <span class="stat-value">{{ $myFueraPlazoMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myEnRevisionMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--por-revisar"></span>
                                                <span class="stat-label">{{ __('En revisión') }}</span>
                                                <span class="stat-value">{{ $myEnRevisionMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myFinalizadosMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--active"></span>
                                                <span class="stat-label">{{ __('Finalizados') }}</span>
                                                <span class="stat-value">{{ $myFinalizadosMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myEnPausaMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--en-pausa"></span>
                                                <span class="stat-label">{{ __('En pausa') }}</span>
                                                <span class="stat-value">{{ $myEnPausaMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tabs ctr" data-card="2">
                                        <div class="card-header-inner">
                                            <div class="tabIcon milestoneIcon">
                                                <img class="icons"
                                                    src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/flag.svg') }}"
                                                    alt="logo" />
                                                <span class="tabIcon-badge"><i class="fas fa-user"></i></span>
                                            </div>
                                            <div class="tabTexts">
                                                {{ __('Prioridad de mis encargos') }}
                                            </div>
                                            <div class="tabTexts tabNumCounter">
                                                <span>
                                                    {{ ($myAltaPriorityMilestones + $myMediaPriorityMilestones + $myBajaPriorityMilestones) ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="statusContainer">
                                            @if ((int) ($myAltaPriorityMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--pending"></span>
                                                <span class="stat-label">{{ __('Alta') }}</span>
                                                <span class="stat-value">{{ $myAltaPriorityMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myMediaPriorityMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--warning"></span>
                                                <span class="stat-label">{{ __('Media') }}</span>
                                                <span class="stat-value">{{ $myMediaPriorityMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myBajaPriorityMilestones ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--info"></span>
                                                <span class="stat-label">{{ __('Baja') }}</span>
                                                <span class="stat-value">{{ $myBajaPriorityMilestones ?? 0 }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tabs ctr" data-card="3">
                                        <div class="card-header-inner">
                                            <div class="tabIcon taskIcon">
                                                <img class="icons"
                                                     src="{{ asset('assets/custom/libs/@fontawesome/fontawesome-free/svgs/solid/tasks.svg') }}"
                                                     alt="logo" />
                                                <span class="tabIcon-badge"><i class="fas fa-user"></i></span>
                                            </div>
                                            <div class="tabTexts">{{ __('Mis tareas') }}</div>
                                            <div class="tabTexts tabNumCounter">
                                                <span>{{ $myTaskTotal ?? 0 }}</span>
                                            </div>
                                        </div>
                                        <div class="statusContainer">
                                            @if ((int) ($myTaskTotal ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--active"></span>
                                                <span class="stat-label">{{ __('En curso + Revisión') }}</span>
                                                <span class="stat-value">{{ $myTaskTotal ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myTaskReviewed ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--review"></span>
                                                <span class="stat-label">{{ __('Revisadas') }}</span>
                                                <span class="stat-value">{{ $myTaskReviewed ?? 0 }}</span>
                                            </div>
                                            @endif
                                            @if ((int) ($myTaskChanges ?? 0) > 0)
                                            <div class="stat-row">
                                                <span class="stat-dot stat-dot--change"></span>
                                                <span class="stat-label">{{ __('Cambio solicitado') }}</span>
                                                <span class="stat-value">{{ $myTaskChanges ?? 0 }}</span>
                                            </div>
                                            @endif
                                            <div class="stat-row" id="monthHoursRow"
                                                title="{{ __('Only in the current workspace') }}"
                                                style="cursor:pointer;">
                                                <span class="stat-dot stat-dot--active"></span>
                                                <span class="stat-label">{{ __('Hours imputed this month') }}</span>
                                                <span class="stat-value">{{ $myMonthHours ?? '00:00' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <span>Encargos totales del workspace {{ $totalWorkspaceMilestones ?? 0 }}</span>
                    <span>Tus hojas de encargo/Hojas de encargo creadas por ti {{ $totalMilestones ?? 0 }}</span>
                    <span>No asignados {{ $notAssignedMilestones }}</span>
                    <span>Asignados a ti {{ $assignedMilestones }}</span>
                    <span>En revision {{ $forReviewMilestones }}</span> --}}
                    <div class="col-md-12">
                        <div class="card min-h">
                            <div class="card-header">
                                {{ __('Statistics') }}
                            </div>
                            <div class="card-body p-3">
                                <div class="divStatisticsButtons">
                                    <div>
                                        <input class="yearInput" placeholder="No disponible" type="hidden"
                                            id="yearSelect" name="yearSelect"
                                            value="{{ collect($averageTimesKeys)->sortDesc()->first() }}">
                                        <i class="fa-solid fa-chevron-down alignArrowSelect"></i>
                                    </div>
                                    <div class="formControlModified form-control" id="yearDropdown"
                                        style="cursor: pointer;">
                                        <span
                                            id="yearDisplay">{{ collect($averageTimesKeys)->sortDesc()->first() }}</span>
                                    </div>

                                    <!-- Lista de años como opciones (Oculto inicialmente) -->
                                    <div id="yearList" class="yearListDiv">
                                        @foreach (collect($averageTimesKeys)->sortDesc() as $year)
                                            <div class="yearOption" data-year="{{ $year }}"
                                                style="padding: 5px; cursor: pointer;">{{ $year }}</div>
                                        @endforeach
                                    </div>

                                    <button onclick="updateChart('monthly')" data-view="monthly"
                                        class="marginRight1 btn btn-primary">{{ __('Monthly') }}
                                    </button>
                                    <button onclick="updateChart('quarterly')" data-view="quarterly"
                                        class="marginRight1 btn btn-primary">{{ __('Quarterly') }}</button>
                                    <button onclick="updateChart('yearly')" data-view="yearly"
                                        class="btn btn-primary">{{ __('Yearly') }}</button>
                                </div>
                                <canvas id="myChart" style="height: 400px; width:100%"></canvas>
                                <div id="chartEmptyMessage"
                                    style="display:none; min-height:400px; align-items:center; justify-content:center;"
                                    class="text-center text-muted">
                                    {{ __('No finalized milestones in this period') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <img src="{{ asset('assets/img/salesManager.png') }}" class="comercialTecIcons" />
                                    <span class="titleTecAndCom">{{ __('Sales managers') }}</span>
                                </div>
                                <div class="modifiedDivTecAndCom top-10-scroll" id="contentCom">
                                    <input type="text" class="filter-input" id="filterComerciales"
                                        placeholder="{{ __('Filter sales managers') }}"
                                        oninput="filterList('filterComerciales', 'contentCom')" />

                                    @foreach ($comerciales as $comercial)
                                        <div class="comercialAndTechnicians">
                                            <div class="ppcontainer">
                                                <img alt="{{ $comercial->name }}" class="profilePicture"
                                                    @if ($comercial->avatar) src="{{ asset($comercial->avatar) }}" @else avatar="{{ $comercial->name }}" @endif>
                                            </div>
                                            <div class="textContent">
                                                 <div class="email-reveal-wrapper">
                                                    <div class="d-flex align-items-center gap-2" style="justify-content: center">
                                                        <span class="fullName">{{ $comercial->name }}</span>
                                                    </div>
                                                    <div class="email-reveal-email"></div>
                                                 </div>
                                            </div>
                                            <i class="bi bi-envelope-fill email-reveal-icon tooltipCus email-icon-fixed"
                                               data-title="{{ __('messages.Mostrar correo') }}"
                                               data-show-text="{{ __('messages.Mostrar correo') }}"
                                               data-hide-text="{{ __('messages.Ocultar correo') }}"
                                               data-user-id="{{ $comercial->id }}"></i>
                                        </div>
                                    @endforEach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <img src="{{ asset('assets/img/technicians.png') }}" class="comercialTecIcons" />
                                    <span class="titleTecAndCom">{{ __('Technicians') }}</span>
                                </div>
                                <input type="text" class="filter-input" id="filterTechnicians"
                                    placeholder="{{ __('Filter technicians') }}"
                                    oninput="filterList('filterTechnicians', 'contentTec')" />
                                <div class="modifiedDivTecAndCom top-10-scroll" id="contentTec">

                                    @foreach ($technicians as $technician)
                                        <div class="comercialAndTechnicians">
                                            <div class="ppcontainer">
                                                <img alt="{{ $technician->name }}" class="profilePicture"
                                                    @if ($technician->avatar) src="{{ asset($technician->avatar) }}" @else avatar="{{ $technician->name }}" @endif>
                                            </div>
                                            <div class="textContent">
                                                 <div class="email-reveal-wrapper">
                                                    <div class="d-flex align-items-center gap-2" style="justify-content: center">
                                                        <span class="fullName">{{ $technician->name }}</span>
                                                    </div>
                                                    <div class="email-reveal-email"></div>
                                                 </div>
                                            </div>
                                            <i class="bi bi-envelope-fill email-reveal-icon tooltipCus email-icon-fixed"
                                               data-title="{{ __('messages.Mostrar correo') }}"
                                               data-show-text="{{ __('messages.Mostrar correo') }}"
                                               data-hide-text="{{ __('messages.Ocultar correo') }}"
                                               data-user-id="{{ $technician->id }}"></i>
                                        </div>
                                    @endforEach

                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-0 mt-3 text-center text-white bg-info">
                                    <div class="card-body">
                                        <h5 class="card-title mb-0">
                                            {{ __('There is no active Workspace. Please create Workspace from right side menu.') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
        @endif
    </section>

    <div class="modal fade" id="monthHoursModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Hours imputed this month') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-x:auto;">
                    @if (($myMonthTimesheets ?? collect())->count() > 0)
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Project') }}</th>
                                <th>{{ __('Hito') }}</th>
                                <th>{{ __('Task') }}</th>
                                <th>{{ __('Hours') }}</th>
                                <th>{{ __('Creado') }}</th>
                                <th>{{ __('Editado') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myMonthTimesheets as $ts)
                            <tr>
                                <td>{{ $ts->date }}</td>
                                <td>{{ $ts->project_name }}</td>
                                <td>{{ $ts->milestone_title }}</td>
                                <td>{{ $ts->task_name ?? '-' }}</td>
                                <td>{{ substr($ts->time, 0, 5) }}</td>
                                <td>{{ \Carbon\Carbon::parse($ts->created_at)->format('d-m H:i:s') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($ts->updated_at)->format('d-m H:i:s') }}
                                    @if ($ts->created_at != $ts->updated_at)
                                    <span class="text-warning" title="{{ __('Edited') }}">⚠️</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">{{ __('Total') }}</th>
                                <th>{{ $myMonthHours ?? '00:00' }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                    @else
                    <p class="text-center text-muted mb-0">{{ __('No data') }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/custom/libs/nicescroll/jquery.nicescroll.min.js') }} "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        $(document).ready(function() {
            if ($(".modifiedDivTecAndCom").length) {
                $(".modifiedDivTecAndCom").css({
                    "max-height": 300
                }).niceScroll();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var monthHoursRow = document.getElementById('monthHoursRow');
            var monthHoursModal = document.getElementById('monthHoursModal');
            if (monthHoursRow && monthHoursModal) {
                monthHoursRow.addEventListener('click', function() {
                    bootstrap.Modal.getOrCreateInstance(monthHoursModal).show();
                });
            }
        });
    </script>
    <script>
        // Datos y textos para el gráfico de Statistics (home-statistics.js)
        window.averageTimes = @json($averageTimes);
        window.statisticsI18n = {
            startingTime: @json(__('Starting time')),
            onTime: @json(__('On time')),
            delay: @json(__('Delay')),
            showValues: @json(__('Show values')),
            days: @json(__('Days')),
            averagePer: @json(__('Average per')),
            annualAverage: @json(__('Annual average')),
            month: @json(__('Month')),
            quarter: @json(__('Quarter')),
            noData: @json(__('No finalized milestones in this period'))
        };
    </script>
    <script src="{{ asset('assets/custom/js/home-statistics.js') }}"></script>
    <script>
        function filterList(inputId, containerId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const container = document.getElementById(containerId);
            const items = container.getElementsByClassName('comercialAndTechnicians');

            for (let i = 0; i < items.length; i++) {
                const name = items[i].getElementsByClassName('fullName')[0];
                const txt = name ? name.textContent.toLowerCase() : '';
                if (filter === "" || txt.indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }

        document.getElementById('filterComerciales').addEventListener('input', function() {
            filterList('filterComerciales', 'contentCom');
        });

        document.getElementById('filterTechnicians').addEventListener('input', function() {
            filterList('filterTechnicians', 'contentTec');
        });
    </script>
<script>
        const sleep = ms => new Promise(r => setTimeout(r, ms));

        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('summaryWrapper');
            const track = document.getElementById('summaryTrack');
            const viewSelector = document.getElementById('viewSelector');
            if (!wrapper || !track) return;

            const workspaceId = '{{ $currentWorkspace->id ?? "default" }}';
            const storageKey = 'summaryView_' + workspaceId;

            const page1 = track.querySelector('.summary-page:first-child');
            const page2 = track.querySelector('.summary-page:last-child');
            const viewSegments = document.querySelectorAll('#viewSelector .view-segment');

            async function activateView(view) {
                const currentView = localStorage.getItem(storageKey) || 'global';
                if (view === currentView) return;

                viewSegments.forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.view === view);
                });

                const expanding = view === 'activity';

                if (expanding) {
                    page1.classList.remove('page-out', 'page-in-start');
                    page1.classList.add('page-out');
                    await sleep(350);

                    page2.classList.remove('page-out', 'page-in-start');
                    page2.classList.add('page-in-start');
                    wrapper.classList.add('expanded');
                    track.offsetHeight;
                    page2.classList.remove('page-in-start');
                    await sleep(350);
                } else {
                    page2.classList.remove('page-out', 'page-in-start');
                    page2.classList.add('page-out');
                    await sleep(350);

                    page1.classList.remove('page-out', 'page-in-start');
                    page1.classList.add('page-in-start');
                    wrapper.classList.remove('expanded');
                    track.offsetHeight;
                    page1.classList.remove('page-in-start');
                    await sleep(350);
                }

                localStorage.setItem(storageKey, view);
            }

            // Initialize view
            const savedView = localStorage.getItem(storageKey) || 'global';
            viewSegments.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.view === savedView);
            });

            if (savedView === 'activity') {
                wrapper.classList.add('expanded');
            } else {
                wrapper.classList.remove('expanded');
            }
            track.offsetHeight;

            document.querySelectorAll('#summaryWrapper .card-header-inner > .tabTexts:not(.tabNumCounter)').forEach(el => {
                if (el.scrollWidth > el.clientWidth) {
                    el.classList.add('tabTexts--long');
                }
            });

            // Segment click handlers
            viewSegments.forEach(btn => {
                btn.addEventListener('click', function() {
                    activateView(this.dataset.view);
                });
            });
        });
    </script>
@endpush
