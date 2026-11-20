@extends('layouts.admin')

@section('page-title')
    {{ __('Project Detail') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Projects') }}</a>
    </li>
    <li class="breadcrumb-item lastBreadCrumb">{{ $project->name }}</li>
    @include('projects.partials.project_detail_help')
@endsection
@php
    use Carbon\Carbon;

    $objUser = Auth::user();
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
@endphp

@section('multiple-action-button')
    {{-- @auth('web')
        @if ($project->users->contains('id', $objUser->id))
            @if ((int) $project->created_by !== (int) $objUser->id)
                <div class="col-md-auto col-sm-4 pb-3">
                    <button type="button"
                        class="btn btn-xs btn-icon-only col-12 join-leave-btn-show" data-toggle="tooltip"
                        title="{{ __('Leave project') }}"
                        onclick="event.preventDefault(); document.getElementById('leave-project-form').submit();">
                        <i class="ti ti-logout"></i>
                    </button>
                    <form id="leave-project-form"
                        action="{{ route('projects.leave', [$currentWorkspace->slug, $project->id]) }}"
                        method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @endif
        @else
            <div class="col-md-auto col-sm-4 pb-3">
                <button type="button"
                    class="btn btn-xs btn-primary btn-icon-only col-12" data-toggle="tooltip"
                    title="{{ __('Join project') }}"
                    onclick="event.preventDefault(); document.getElementById('join-project-form').submit();">
                    <i class="ti ti-login"></i>
                </button>
                <form id="join-project-form"
                    action="{{ route('projects.join', [$currentWorkspace->slug, $project->id]) }}"
                    method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @endif
    @endauth --}}
    @if (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12" data-toggle="popover"
                title="{{ trans('messages.Shared_Project_Settings') }}" data-ajax-popup="true" data-size="md"
                data-title="{{ trans('messages.Shared_Project_Settings') }}"
                data-url="{{ route('projects.copylink.setting.create', [$currentWorkspace->slug, $project->id]) }}"
                data-toggle="tooltip" title="{{ __('Add Project') }}">
                <i class="ti ti-settings"></i>
            </a>
        </div>
    @endif
@endsection

<style type="text/css">
    .lastBreadCrumb {
        max-width: 700px;
        overflow: hidden;
        text-wrap: nowrap;
        text-overflow: ellipsis;
    }

    .join-leave-btn-show {
        background-color: #aa182c !important;
        color: #fff !important;
    }

    .join-leave-btn-show:hover {
        background-color: #b9515f !important;
        color: #fff !important;
    }

    .uploaded-file-buttons {
        display: flex;
        gap: 3px;
        align-items: center;
        padding-right: 5px;
    }

    .buttonFiles {
        background-color: #aa182c !important;
        width: 25px !important;
        height: 25px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 5px !important;
    }

    .buttonFiles:hover {
        background-color: #b9515f;
        color: white;
        text-decoration: none;
        border-color: #b9515f;
    }

    .fatherMilestoneDiv {
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
    }

    .custom-file-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
    }

    .custom-file {
        display: flex;
        justify-content: space-between;
        background: #f4f5ff;
        padding: 0.5%;
        margin: 5px;
        -moz-box-shadow: 10px 10px 5px 0px rgba(237, 237, 237, 1);
        box-shadow: -3px 3px 0px 0px rgb(239 239 239);
        border-radius: 6px;
    }

    .milestoneGridDisplay {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .styleIconFiles {
        width: 28px;
        height: 21px;
        padding-left: 5px;
        padding-right: 5px;
    }

    .size40AndBold {
        font-weight: bold;
        font-size: 40px;
    }

    .titleFiles {
        display: flex !important;
        align-items: baseline;
    }

    .file-folder-toggle {
        cursor: pointer;
        user-select: none;
        transition: opacity 0.2s ease;
    }

    .file-folder-toggle:hover {
        opacity: 0.8;
    }

    .folder-toggle-icon {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .file-folder-toggle:hover .folder-toggle-icon {
        transform: scale(1.05);
    }

    #files-upload-row {
        transition: opacity 0.22s ease, transform 0.22s ease;
    }

    #files-upload-row:not(.is-visible) {
        opacity: 0;
        transform: translateY(-8px);
    }

    #files-upload-row.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    #toggleUploadSectionBtn i {
        transition: transform 0.2s ease;
    }

    #toggleUploadSectionBtn[aria-expanded='true'] i {
        transform: scale(1.08);
    }

    .reqByImgContainer {
        display: flex;
        justify-content: center;
        align-items: center;
        align-content: center;
    }

    .assignedToImgContainer {
        text-align: center
    }

    .fix_img {
        width: 40px !important;
        border-radius: 50%;
    }

    .buttonCenterText {
        padding-top: 7px !important;
    }

    .min-h {
        min-height: 180px;
    }

    .min-end {
        min-height: 300px;
    }

    .projectTitleH3 {
        text-align: center;
        font-size: 28px;
    }

    .projectDivSubtitle {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 16px;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 15px;
        padding-top: 5px;
    }

    /* Chips de metadatos: pill blanco sobre fondo carmesí */
    .projectDivSubtitle>div {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.92);
        border: none;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.05px;
        white-space: nowrap;
        cursor: default;
        user-select: none;
        color: #7a0e1e;
    }

    .projectDivSubtitle>div i {
        margin-right: 6px;
        font-size: 12px;
        opacity: 0.7;
    }

    .projectDivSubtitle>div.badge-container {
        padding: 0;
        border: none;
        background: transparent;
        box-shadow: none;
    }

    .projectDivSubtitle .badge {
        font-size: 12.5px;
        font-weight: 500;
        padding: 4px 12px !important;
        border-radius: 20px !important;
        display: inline-flex;
        align-items: center;
        margin: 0;
        border: none;
        letter-spacing: 0.15px;
        cursor: default;
        user-select: none;
        text-transform: none;
    }

    .projectDivSubtitle .badge.bg-success {
        background: rgba(83, 180, 70, 0.95) !important;
    }

    .projectDivSubtitle .badge.bg-secondary {
        background: rgba(144, 150, 158, 0.95) !important;
        color: #fff !important;
    }

    .projectDivSubtitle .badge.bg-warning {
        background: rgba(219, 141, 51, 0.95) !important;
    }

    .uploaded-files-container {
        display: grid;
        /* Cambia a un diseño de cuadrícula */
        grid-template-columns: repeat(3, 1fr);
        /* Limita a 3 elementos por fila */
        gap: 10px;
        /* Espaciado entre los archivos */
        /* max-height: 140px; */
        /* Limita la altura del contenedor */
        overflow-y: auto;
        /* Permite el desplazamiento vertical si hay demasiados archivos */
        overflow-x: hidden;
        /* Evita el desplazamiento horizontal */
    }

    .uploaded-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        min-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 95%
    }

    .uploaded-file p {
        margin: 0;
        font-size: 14px;
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    .buttonFiles {
        background-color: #aa182c;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
    }

    .buttonFiles:hover {
        background-color: #b9515f;
        color: white;
        text-decoration: none;
        border-color: #b9515f;
    }

    .plusIcon {
        margin-left: 10px;
    }

    .alignMiddle {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .activityContainer {
        max-height: 374px;
        overflow-y: scroll;
    }

    /* Scrollbar en navegadores WebKit */
    .activityContainer::-webkit-scrollbar {
        width: 8px;
        height: 10px;
        /* Ancho del scrollbar */
    }

    /* Fondo del scrollbar */
    .activityContainer::-webkit-scrollbar-track {
        background: #ffffff;
        /* Color del fondo */
        border-radius: 4px;
        /* Bordes redondeados */
    }

    /* Parte deslizable del scrollbar */
    .activityContainer::-webkit-scrollbar-thumb {
        background: #AA182C;
        /* Color del scrollbar */
        border-radius: 4px;
        height: 10px;
    }



    @media (max-width: 1300px) {
        .header_breadcrumb {
            width: 100% !important;
        }

        .row1 {
            display: flex;
            flex-wrap: wrap;
        }
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .widthAdjustDiv {
            width: 99% !important;
        }

        .widthAdjustMediumDiv {
            width: 49%;
        }

        .uploaded-files-container {
            gap: 5px;
        }

        .uploaded-file {
            width: 92%;
        }

        .last_notification_text {
            padding: 0 0 0 5px !important;
        }

        .last_notification_text p {
            margin-right: 10px !important;
            font-size: 9px !important;
        }
    }
</style>
<style>
    .project-order-table-shell {
        border-radius: 14px;
        padding: 0 10px 10px;
        max-height: 41vh;
        overflow: auto;
        width: 100%;
    }

    .project-order-table-inner {
        /* display: inline-block; */
        min-width: 100%;
        width: max-content;
        zoom: 0.9;
    }

@media (max-width: 1920px) {
    .project-order-table-inner { zoom: .80; }
}

@media (max-width: 1600px) {
    .project-order-table-inner { zoom: .75; }
}

@media (max-width: 1440px) {
    .project-order-table-inner { zoom: .70; }
}

@media (max-width: 1366px) {
    .project-order-table-inner { zoom: .70; }
}

@media (max-width: 1280px) {
    .project-order-table-inner { zoom: .65; }
}


    .project-order-header-row {
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

    .project-order-th {
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

    .project-order-body-rows {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-top: 12px;
        width: 100%;
    }

    .project-order-body-row {
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

    /* Encargo abierto desde un aviso de la campana (?milestone=ID) */
    .project-order-body-row.pd-row-flash {
        border-color: #b6122e;
        box-shadow: 0 0 0 3px rgba(182, 18, 46, .18);
        background: #fff0f4;
        transition: box-shadow .3s ease, background .3s ease;
    }

    .project-order-body-row:hover {
        border-color: #b6122e;
        background: #fff0f4;
        color: #7b1528;
        transform: translateY(-1px);
    }

    .project-order-td {
        padding: 12px 8px;
        flex-shrink: 0;
    }

    .project-order-th[data-col-key="name"] { flex: 3; min-width: 150px; overflow: hidden; }
    .project-order-td[data-col-key="name"] { flex: 3; min-width: 150px; overflow: hidden; display: flex; align-items: center; }
    .project-order-td[data-col-key="name"] h5 {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .project-order-th[data-col-key="requested_by"] { flex: 1.5; min-width: 85px; justify-content: center; }
    .project-order-td[data-col-key="requested_by"] { flex: 1.5; min-width: 85px; text-align: center; }
    .project-order-th[data-col-key="assigned_to"] { flex: 1.5; min-width: 85px; justify-content: center; }
    .project-order-td[data-col-key="assigned_to"] { flex: 1.5; min-width: 85px; text-align: center; }
    .project-order-th[data-col-key="status"] { flex: 1.5; min-width: 95px; justify-content: center; text-align: center; }
    .project-order-td[data-col-key="status"] { flex: 1.5; min-width: 95px; text-align: center; }
    .project-order-th[data-col-key="created"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; }
    .project-order-td[data-col-key="created"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; display: flex; align-items: center; }
    .project-order-th[data-col-key="desired_delivery"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; }
    .project-order-td[data-col-key="desired_delivery"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; display: flex; align-items: center; }
    .project-order-th[data-col-key="expected_delivery"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; }
    .project-order-td[data-col-key="expected_delivery"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; display: flex; align-items: center; }
    .project-order-th[data-col-key="task_started"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; }
    .project-order-td[data-col-key="task_started"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; display: flex; align-items: center; }
    .project-order-th[data-col-key="completion"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; }
    .project-order-td[data-col-key="completion"] { flex: 1.5; min-width: 85px; justify-content: center; text-align: center; display: flex; align-items: center; }
    .project-order-th[data-col-key="action"],
    .project-order-td[data-col-key="action"] { flex: 0.8; min-width: 95px; display: flex; align-items: center; justify-content: center; gap: 6px; }

    .project-order-th-content {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .project-order-th[data-col-key="created"],
    .project-order-th[data-col-key="desired_delivery"],
    .project-order-th[data-col-key="expected_delivery"],
    .project-order-th[data-col-key="task_started"],
    .project-order-th[data-col-key="completion"] {
        position: relative;
    }



    .project-order-filter-btn {
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
    }

    .project-order-filter-btn:hover {
        background: #ffe8ee;
        border-color: #efc6d1;
        color: #7b1528;
    }

    .project-order-filter-btn.is-active {
        background: #b6122e;
        border-color: #b6122e;
        color: #fff;
    }

    .project-order-filter-icon {
        width: 14px;
        height: 14px;
        fill: currentColor;
        pointer-events: none;
    }

    .project-order-header-tools {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .project-order-column-toggle-btn {
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

    .project-order-column-toggle-btn:hover {
        border-color: #b6122e;
        background: #fff0f4;
        color: #7b1528;
        transform: translateY(-1px);
    }

    .project-order-column-toggle-btn:focus-visible {
        outline: 0;
        border-color: #b6122e;
        box-shadow: 0 0 0 3px rgba(182, 18, 46, 0.15);
    }

    .project-order-column-toggle-btn.is-active {
        border-color: #b6122e;
        background: #b6122e;
        color: #fff;
        box-shadow: 0 2px 8px rgba(182, 18, 46, 0.22);
    }

    .project-order-column-toggle-icon {
        width: 15px;
        height: 15px;
        fill: currentColor;
    }

    .project-order-column-toggle-count {
        font-size: 11px;
        color: #975363;
    }

    .project-order-column-toggle-btn.is-active .project-order-column-toggle-count {
        color: rgba(255, 255, 255, 0.88);
    }

    .project-order-filter-menu,
    .project-order-column-menu {
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

    .project-order-filter-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }

    .project-order-filter-menu-title,
    .project-order-column-menu-title {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #4a1421;
    }

    .project-order-filter-link {
        border: 0;
        background: transparent;
        color: #aa182c;
        font-size: 12px;
        font-weight: 700;
        padding: 0;
    }

    .project-order-filter-menu-actions {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .project-order-filter-search {
        width: 100%;
        border: 1px solid #efc6d1;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .project-order-filter-search:focus {
        outline: 0;
        border-color: #b6122e;
        box-shadow: 0 0 0 3px rgba(182, 18, 46, 0.12);
    }

    .project-order-filter-options {
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-height: 280px;
        overflow: auto;
    }

    .project-order-filter-option {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #4d4d4d;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
    }

    .project-order-filter-option:hover {
        background: #fff5f7;
    }

    .project-order-filter-option input[type='checkbox'] {
        accent-color: #aa182c;
        cursor: pointer;
    }

    .project-order-filter-option span:first-of-type {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .project-order-filter-option-count {
        color: #8b6b73;
        font-size: 11px;
        font-weight: 700;
    }

    .project-order-filtered-empty-state {
        display: none;
        text-align: center;
        padding: 28px 16px 18px;
        color: #7c5a63;
    }

    .project-order-filtered-empty-state.is-visible {
        display: block;
    }

    .addmMilestoneButton {
        padding: 8px;
        /* padding: 8px !important; */
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .orderFormsHeaderActions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 8px;
    }

    .orderFormsHeaderActions .addmMilestoneButton {
        min-width: 42px;
    }

    .files-section {
        border: 1px solid #eceef3;
        border-radius: 12px;
    }

    .files-section .card-header {
        padding: 17px 25px;
        border-bottom: 1px solid #eceef3;
    }

    .files-title {
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .files-upload-toggle {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    }

    .files-upload-toggle[aria-expanded='true'] {
        background: #8f1525;
        box-shadow: 0 6px 14px rgba(170, 24, 44, 0.22);
    }

    .files-upload-toggle:hover {
        transform: translateY(-1px);
    }

    .files-body {
        padding: 16px 18px !important;
    }

    .files-body .author-box-name {
        margin-bottom: 0 !important;
    }

    .files-column {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .files-group-title {
        align-items: center !important;
        padding: 4px 2px 10px;
        border-bottom: 1px solid #f0f1f4;
    }

    .files-group-title h5,
    .files-group-title h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
    }

    .files-section .file-folder-toggle {
        border-radius: 8px;
        transition: background-color 0.2s ease, opacity 0.2s ease;
    }

    .files-section .file-folder-toggle:hover {
        background-color: #f7f8fa;
        opacity: 1;
    }

    .files-section .file-folder-toggle:focus-visible {
        outline: 2px solid rgba(170, 24, 44, 0.25);
        outline-offset: 2px;
    }

    .files-section .folder-toggle-target {
        padding-top: 4px;
    }

    .files-section .files-scroll-area {
        max-height: clamp(320px, 52vh, 460px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
    }

    .files-section .custom-file-container {
        gap: 8px;
    }

    .files-section .custom-file {
        background: #ffffff;
        border: 1px solid #eceef3;
        box-shadow: none;
        padding: 6px 8px;
        margin: 0;
        transition: border-color 0.2s ease, transform 0.15s ease;
    }

    .files-section .custom-file:hover {
        border-color: #d7dce6;
        transform: translateY(-1px);
    }

    .files-section .milestone-files {
        padding: 6px 8px;
        border: 1px solid #f0f1f4;
        border-radius: 10px;
        background: #fcfcfd;
    }

    .files-section .milestone-files hr {
        margin: 6px 0 10px !important;
        border-color: #eceef3 !important;
    }

    .files-section #files-upload-row .dropzone {
        min-height: 146px;
        border-radius: 12px;
    }

    @media (max-width: 992px) {
        .files-body {
            padding: 14px !important;
        }

        .project-order-column-toggle-count {
            display: none;
        }
    }

    .projectSubnav {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 14px 0 16px;
        padding: 6px;
        border: 1px solid #eceef3;
        border-radius: 12px;
        background: #fff;
        overflow-x: auto;
    }

    .projectSubnavItem {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 9px;
        border: 1px solid transparent;
        color: #515a66;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease,
            transform 0.15s ease;
    }


    .divisorLineNav {
        background-color: #c66572;
        height: 37px !important;
        width: 1px !important;
    }

    .projectSubnavItem:hover {
        background: #f7f8fa;
        border-color: #e4e7ee;
        color: #2f3741;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .projectSubnavItem.is-active {
        background: #aa182c;
        border-color: #aa182c;
        color: #ffffff;
        box-shadow: 0 6px 14px rgba(170, 24, 44, 0.18);
    }

    .projectSubnavItem:focus-visible {
        outline: 2px solid rgba(170, 24, 44, 0.25);
        outline-offset: 2px;
    }

    .projectSubnav.projectSubnav--header {
        justify-content: flex-end;
        margin: 0 0 0 auto;
        width: max-content;
        max-width: 100%;
        border-color: rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(2px);
    }

    .projectHeaderMain {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        min-height: 50px;
    }

    .projectHeaderMain .projectTitleH3 {
        margin: 0;
        text-align: center;
        width: 100%;
    }

    .projectHeaderMain .projectSubnav.projectSubnav--header {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
    }

    @media (max-width: 768px) {
        .projectHeaderMain {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            min-height: 0;
        }

        .projectHeaderMain .projectTitleH3 {
            text-align: center;
        }

        .projectHeaderMain .projectSubnav.projectSubnav--header {
            position: static;
            transform: none;
        }

        .projectSubnav.projectSubnav--header {
            margin: 0;
            width: 100%;
            justify-content: flex-start;
        }
    }

    .projectSubnav--header .projectSubnavItem {
        color: rgba(255, 255, 255, 0.92);
    }

    .projectSubnav--header .projectSubnavItem:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.22);
        color: #ffffff;
    }

    .projectSubnav--header .projectSubnavItem.is-active {
        background: #ffffff;
        border-color: #ffffff;
        color: #aa182c;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }

    .projectSubnav--header .projectSubnavItem:focus-visible {
        outline-color: rgba(255, 255, 255, 0.45);
    }

    .project-order-body-row:has(> [data-col-key]:first-child[style*="display: none"]) {
        display: none !important;
    }
</style>
@section('content')
    @php
        // ===== Datos de la cabecera y los indicadores =====
        $pdTypeName = $project->type ? optional($project->typeRel)->name : null;
        $pdMilestones = $project->milestones ?? collect();
        $pdStatusCounts = $pdMilestones->countBy(fn($m) => (int) $m->status);
        $pdTotalOrders = $pdMilestones->count();
        $pdStatuses = [
            1 => ['label' => __('To Do'), 'color' => '#cbd5e1'],
            2 => ['label' => __('Ongoing'), 'color' => '#60a5fa'],
            3 => ['label' => __('For Review'), 'color' => '#fbbf24'],
            4 => ['label' => __('Finished'), 'color' => '#22c55e'],
        ];
        // Horas imputadas: llegan en decimal (2.75) → 2:45
        $pdHoursRaw = (float) ($totalHours ?? 0);
        $pdHoursH = (int) floor($pdHoursRaw);
        $pdHoursM = (int) round(($pdHoursRaw - $pdHoursH) * 60);
        if ($pdHoursM === 60) { $pdHoursH++; $pdHoursM = 0; }
        $pdHours = $pdHoursH . ':' . str_pad($pdHoursM, 2, '0', STR_PAD_LEFT);
        $pdMembersCount = (int) $project->technicians->count() + (int) $project->salesManager->count();
        $pdMembers = ($project->users ?? collect())->take(3);
        $pdProjectStatus = [
            'Finished' => ['Finished', 'is-finished'],
            'Ongoing' => ['Ongoing', 'is-ongoing'],
        ][$project->status] ?? ['OnHold', 'is-onhold'];
        $pdCanEditOrders = $currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner';

        // ===== Equipo: quien creó encargos y quien imputó horas, una sola vez por persona =====
        $pdTeam = [];
        foreach ($milestoneCreators as $u) {
            $pdTeam[$u->id] = ['user' => $u, 'orders' => (int) $u->milestones_count, 'hours' => null];
        }
        foreach ($usersWithHours as $u) {
            $pdTeam[$u->id] = $pdTeam[$u->id] ?? ['user' => $u, 'orders' => null, 'hours' => null];
            $pdTeam[$u->id]['hours'] = $u->total_time ?: null;
        }

        // ===== Actividad: icono y color por tipo =====
        $pdActivityTypes = [
            'Upload File' => ['fa-file-arrow-up', 'is-green'],
            'has uploaded a review file' => ['fa-file-circle-check', 'is-amber'],
            'Create Milestone' => ['fa-plus', 'is-red'],
            'Create Timesheet' => ['fa-clock', 'is-blue'],
            'has delete a file' => ['fa-trash', 'is-red'],
            'has created a new project' => ['fa-diagram-project', 'is-green'],
            'has updated a milestone' => ['fa-pen', 'is-indigo'],
            'has updated the milestone status to' => ['fa-right-left', 'is-blue'],
            'has deleted a milestone' => ['fa-trash', 'is-red'],
        ];
        $pdDayLabel = function ($date) {
            if ($date->isToday()) return __('Today');
            if ($date->isYesterday()) return __('Yesterday');
            return $date->locale(app()->getLocale())->translatedFormat('j M Y');
        };
    @endphp

    <div class="pd">
        {{-- ================= Cabecera ================= --}}
        <div class="pd-hero">
            <div class="pd-hero-icon">
                @if ($pdTypeName)
                    <img src="{{ asset('assets/img/types/' . $pdTypeName . '.svg') }}" alt="{{ __($pdTypeName) }}">
                @else
                    <i class="fa-solid fa-diagram-project"></i>
                @endif
            </div>
            <div class="pd-hero-main">
                <h1 class="pd-title">
                    <span class="pd-title-text" title="{{ $project->name }}">{{ $project->name }}</span>
                    <span class="pd-status-pill {{ $pdProjectStatus[1] }}"><i></i>{{ __($pdProjectStatus[0]) }}</span>
                    @if (!$project->is_active)
                        <span class="pd-status-pill is-locked" title="{{ __('Locked') }}"><i class="fa-solid fa-lock"></i>{{ ucfirst(__('Locked')) }}</span>
                    @endif
                </h1>
                <div class="pd-meta">
                    @if ($pdTypeName)
                        <span class="pd-meta-type pl-type-{{ $project->type }}">{{ __($pdTypeName) }}</span>
                    @endif
                    @if (!empty($project->ref_mo))
                        <span class="pd-meta-mo" title="{{ __('Reference M.O') }}">MO {{ $project->ref_mo }}</span>
                    @endif
                    @if ($project->delegation)
                        <span title="{{ __('Delegation') }}"><i class="fa-solid fa-location-dot"></i>{{ \Illuminate\Support\Str::title($project->delegation->delegation_name) }} · {{ \Illuminate\Support\Str::upper($project->ref_delegation) }}</span>
                    @endif
                    <span title="{{ __('Workspace') }}"><i class="fa-solid fa-layer-group"></i>{{ $currentWorkspace->country }} / {{ $currentWorkspace->display_name }}</span>
                    <span title="{{ __('Creation date') }}"><i class="fa-regular fa-calendar"></i>{{ __('Created on :date', ['date' => App\Models\Utility::dateFormat($project->start_date)]) }}</span>
                    <span title="{{ __('Members') }}">
                        @if ($pdMembers->count())
                            <span class="pd-avatars">
                                @foreach ($pdMembers as $member)
                                    @include('projects.partials.pd_avatar', ['user' => $member, 'size' => 24])
                                @endforeach
                            </span>
                        @else
                            <i class="fa-solid fa-users"></i>
                        @endif
                        {{ trans_choice(':count member|:count members', $pdMembersCount, ['count' => $pdMembersCount]) }}
                    </span>
                </div>
            </div>
            <div class="pd-hero-actions">
                <a href="{{ route('projects.milestone.board', [$currentWorkspace->slug, $project->id]) }}" class="pd-btn">
                    <i class="fa-solid fa-table-columns"></i><span>{{ __('Order forms board') }}</span>
                </a>
                <a href="{{ route('projects.timesheet.index', [$currentWorkspace->slug, $project->id]) }}" class="pd-btn">
                    <i class="fa-solid fa-list-check"></i><span>{{ __('Timesheet') }}</span>
                </a>
                @if ($pdCanEditOrders)
                    <a href="#" class="pd-btn pd-btn-primary addmMilestoneButton" data-ajax-popup="true"
                        data-title="{{ __('Create Order Form') }}"
                        data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project->id]) }}">
                        <i class="fa-solid fa-plus"></i><span>{{ __('New order') }}</span>
                    </a>
                @endif
                @if ($project->is_active)
                    @auth('web')
                        @if ($objUser->type == 'admin')
                            <div class="dropdown">
                                <button type="button" class="pd-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Action') }}">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end pd-menu">
                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-title="{{ __('Edit Project') }}"
                                        data-url="{{ route('projects.edit', [$currentWorkspace->slug, $project->id]) }}">
                                        <i class="ti ti-edit"></i>{{ __('Edit') }}
                                    </a>
                                    <a href="#" class="dropdown-item text-danger bs-pass-para" data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="delete-form-{{ $project->id }}">
                                        <i class="ti ti-trash"></i>{{ __('Delete') }}
                                    </a>
                                </div>
                            </div>
                            <form id="delete-form-{{ $project->id }}"
                                action="{{ route('projects.destroy', [$currentWorkspace->slug, $project->id]) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <form id="leave-form-{{ $project->id }}"
                                action="{{ route('projects.leave', [$currentWorkspace->slug, $project->id]) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        {{-- ================= Indicadores ================= --}}
        <div class="pd-kpis">
            <div class="pd-kpi pd-kpi-wide">
                <div class="pd-kpi-label"><i class="fa-regular fa-file-lines"></i>{{ __('Order forms by status') }}</div>
                <div class="pd-kpi-value">{{ $pdTotalOrders }} <small>{{ trans_choice('order form|order forms', $pdTotalOrders) }}</small></div>
                <div class="pd-stack">
                    @foreach ($pdStatuses as $statusId => $st)
                        @if (($pdStatusCounts[$statusId] ?? 0) > 0)
                            <span style="flex: {{ $pdStatusCounts[$statusId] }}; background: {{ $st['color'] }}" title="{{ $st['label'] }}: {{ $pdStatusCounts[$statusId] }}"></span>
                        @endif
                    @endforeach
                </div>
                <div class="pd-kpi-legend">
                    @foreach ($pdStatuses as $statusId => $st)
                        <span><i style="background: {{ $st['color'] }}"></i>{{ $st['label'] }} {{ $pdStatusCounts[$statusId] ?? 0 }}</span>
                    @endforeach
                </div>
            </div>
            <div class="pd-kpi">
                <div class="pd-kpi-label"><i class="fa-regular fa-clock"></i>{{ __('Hours charged') }}</div>
                <div class="pd-kpi-value">{{ $pdHours }} <small>h</small></div>
                <div class="pd-kpi-hint">{{ trans_choice(':count person|:count people', count($usersWithHours), ['count' => count($usersWithHours)]) }}</div>
            </div>
            @foreach ([
                ['fa-flag-checkered', __('Average delivery time'), $averageDelivery, __('from creation to completion')],
                ['fa-briefcase', __('Average working time'), $averageWorkingTime, __('time actually working')],
                ['fa-play', __('Average start-up time'), $averageStartUpTime, __('until the first task starts')],
            ] as [$icon, $label, $value, $hint])
                <div class="pd-kpi">
                    <div class="pd-kpi-label"><i class="fa-solid {{ $icon }}"></i>{{ $label }}</div>
                    @if ($pdTotalOrders > 0)
                        <div class="pd-kpi-value">{{ $value }} <small>{{ trans_choice('day|days', (int) $value) }}</small></div>
                    @else
                        <div class="pd-kpi-value is-empty">—</div>
                    @endif
                    <div class="pd-kpi-hint">{{ $hint }}</div>
                </div>
            @endforeach
            <div class="pd-kpi">
                <div class="pd-kpi-label"><i class="fa-solid fa-triangle-exclamation"></i>{{ __('Average delay time') }}</div>
                @if ($pdTotalOrders === 0)
                    <div class="pd-kpi-value is-empty">—</div>
                @elseif ((int) $averageDelayTime === 0)
                    <div class="pd-kpi-value is-good">{{ __('No delay') }}</div>
                @else
                    <div class="pd-kpi-value is-bad">{{ $averageDelayTime }} <small>{{ trans_choice('day|days', (int) $averageDelayTime) }}</small></div>
                @endif
                <div class="pd-kpi-hint">{{ __('compared with the desired delivery') }}</div>
            </div>
        </div>

        {{-- ================= Encargos ================= --}}
        @if ($pdCanEditOrders)
            <section class="pd-card pd-orders">
                <header class="pd-card-head">
                    <h3>{{ __('Order forms') }}</h3>
                    <span class="pd-count">{{ count($project->milestones) }}</span>
                    <div class="pd-card-tools orderFormsHeaderActions">
                        <label class="pd-search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="search" id="pdOrderSearch" placeholder="{{ __('Search order form') }}" autocomplete="off">
                        </label>
                                            <div class="project-order-header-tools">
                                                <button type="button" id="orderFormsColumnsToggleBtn"
                                                    class="project-order-column-toggle-btn"
                                                    aria-label="{{ __('Show or hide table columns') }}"
                                                    title="{{ __('Show or hide table columns') }}" aria-expanded="false">
                                                    <svg class="project-order-column-toggle-icon" viewBox="0 0 16 16"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M8 3.2c3.3 0 5.8 2.3 6.9 4.8-1.1 2.5-3.6 4.8-6.9 4.8S2.2 10.5 1.1 8C2.2 5.5 4.7 3.2 8 3.2Zm0 1.2c-2.6 0-4.7 1.7-5.8 3.6 1.1 1.9 3.2 3.6 5.8 3.6s4.7-1.7 5.8-3.6c-1.1-1.9-3.2-3.6-5.8-3.6Zm0 1.4a2.2 2.2 0 1 1 0 4.4 2.2 2.2 0 0 1 0-4.4Zm0 1.2a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z">
                                                        </path>
                                                    </svg>
                                                    <span class="pd-col-label">{{ __('Columns') }}</span>
                                                    <span class="project-order-column-toggle-count"
                                                        id="orderFormsColumnsToggleCount">0/0</span>
                                                </button>
                                            </div>
                                            @if ($project->type == 3 || $project->type == 5)
                                                <a href="#" class="pd-btn pd-btn-tool" data-ajax-popup="true"
                                                    data-size="md" title="{{ __('Edit Phases') }}"
                                                    data-url="{{ route('projects.stages.popup', [$currentWorkspace->slug, $project->id]) }}"
                                                    data-toggle="popover"><i class="fa-solid fa-layer-group"></i><span>{{ __('Phases') }}</span>
                                                </a>
                                            @endif
                    </div>
                </header>
                <nav class="pd-tabs" id="pdOrderTabs" role="tablist">
                    <button type="button" class="pd-tab is-active" data-status-id="*">{{ __('All items') }} <b>{{ $pdTotalOrders }}</b></button>
                    @foreach ($pdStatuses as $statusId => $st)
                        <button type="button" class="pd-tab" data-status-id="{{ $statusId }}"><i style="background: {{ $st['color'] }}"></i>{{ $st['label'] }} <b>{{ $pdStatusCounts[$statusId] ?? 0 }}</b></button>
                    @endforeach
                </nav>
                <div class="pd-card-body">
                                    <div class="project-order-table-shell">
                                        <div class="project-order-table-inner">
                                        <div class="project-order-header-row">
                                            <div class="project-order-th" data-col-key="name">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Name') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="name"
                                                        data-filter-label="{{ __('Name') }}"
                                                        data-column-index="0"
                                                        aria-label="{{ __('Filter Name') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="requested_by">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Requested by') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="requested_by"
                                                        data-filter-label="{{ __('Requested by') }}"
                                                        data-column-index="1"
                                                        aria-label="{{ __('Filter Requested by') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="assigned_to">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Assigned to') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="assigned_to"
                                                        data-filter-label="{{ __('Assigned to') }}"
                                                        data-column-index="2"
                                                        aria-label="{{ __('Filter Assigned to') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="status">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Status') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="status"
                                                        data-filter-label="{{ __('Status') }}"
                                                        data-column-index="3"
                                                        aria-label="{{ __('Filter Status') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="created">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Created') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="created"
                                                        data-filter-label="{{ __('Created') }}"
                                                        data-column-index="4"
                                                        aria-label="{{ __('Filter Created') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="desired_delivery">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Desired delivery') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="desired_delivery"
                                                        data-filter-label="{{ __('Desired delivery') }}"
                                                        data-column-index="5"
                                                        aria-label="{{ __('Filter Desired delivery') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="expected_delivery">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Expected delivery') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="expected_delivery"
                                                        data-filter-label="{{ __('Expected delivery') }}"
                                                        data-column-index="6"
                                                        aria-label="{{ __('Filter Expected delivery') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="task_started">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Task started') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="task_started"
                                                        data-filter-label="{{ __('Task started') }}"
                                                        data-column-index="7"
                                                        aria-label="{{ __('Filter Task started') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="completion">
                                                <div class="project-order-th-content">
                                                    <span>{{ __('Completion') }}</span>
                                                    <button type="button" class="project-order-filter-btn"
                                                        data-filter-key="completion"
                                                        data-filter-label="{{ __('Completion') }}"
                                                        data-column-index="8"
                                                        aria-label="{{ __('Filter Completion') }}">
                                                        <svg class="project-order-filter-icon" viewBox="0 0 16 16"
                                                            aria-hidden="true">
                                                            <path
                                                                d="M2 3.25A1.25 1.25 0 0 1 3.25 2h9.5A1.25 1.25 0 0 1 14 3.25c0 .3-.11.6-.31.82L9.5 8.45v3.3a1 1 0 0 1-.55.9l-2 1A1 1 0 0 1 5.5 12.75V8.45L2.31 4.07A1.25 1.25 0 0 1 2 3.25Z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="project-order-th" data-col-key="action">{{ __('Action') }}</div>
                                        </div>

                                        <div class="project-order-body-rows" id="orderFormsTableBody">
                                            @php
                                                $dateFilterLabel = static function ($date) {
                                                    if (empty($date) || $date === '0000-00-00') {
                                                        return __('N/A');
                                                    }

                                                    $parsedDate = \Carbon\Carbon::parse($date)->locale(
                                                        app()->getLocale(),
                                                    );

                                                    if (str_starts_with(app()->getLocale(), 'es')) {
                                                        return $parsedDate->translatedFormat('F \\d\\e Y');
                                                    }

                                                    return $parsedDate->translatedFormat('F Y');
                                                };

                                                $dateFilterSortValue = static function ($date) {
                                                    if (empty($date) || $date === '0000-00-00') {
                                                        return '';
                                                    }

                                                    return \Carbon\Carbon::parse($date)->format('Y-m');
                                                };
                                            @endphp
                                            @foreach ($project->milestones->sortByDesc('id') as $key => $milestone)
                                                @php
                                                    $statusText =
                                                        $milestone->status == 3
                                                            ? __('For Review')
                                                            : ($milestone->status == 4
                                                                ? __('Finished')
                                                                : ($milestone->status == 1
                                                                    ? __('To Do')
                                                                    : __('Ongoing')));
                                                @endphp
                                                <div class="project-order-body-row"
                                                    data-title="{{ __('Order form details') }}"
                                                    data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone->id]) }}"
                                                    data-name="{{ mb_strtolower(trim($milestone->title ?? '')) }}"
                                                    data-requested-by="{{ mb_strtolower(trim(optional($milestone->getRequestedBy())->name ?? '')) }}"
                                                    data-assigned-to="{{ mb_strtolower(trim(optional($milestone->getAssignedToUser())->name ?? '')) }}"
                                                    data-status="{{ mb_strtolower(trim($statusText)) }}"
                                                    data-status-id="{{ (int) $milestone->status }}"
                                                    data-created-date="{{ !empty($milestone->start_date) && $milestone->start_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->start_date)->format('Y-m-d') : '' }}"
                                                    data-desired-date="{{ !empty($milestone->end_date) && $milestone->end_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->end_date)->format('Y-m-d') : '' }}"
                                                    data-expected-date="{{ !empty($milestone->planned_end_date) && $milestone->planned_end_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->planned_end_date)->format('Y-m-d') : '' }}"
                                                    data-task-started-date="{{ !empty($milestone->task_start_date) && $milestone->task_start_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->task_start_date)->format('Y-m-d') : '' }}"
                                                    data-completion-date="{{ !empty($milestone->finalization_date) && $milestone->finalization_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->finalization_date)->format('Y-m-d') : '' }}">
                                                    <div class="project-order-td" data-col-key="name"
                                                        data-filter-value="{{ trim($milestone->title ?? '') !== '' ? trim($milestone->title) : __('N/A') }}">
                                                        <h5 class="m-0" title="{{ $milestone->title }}" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                            {{ $milestone->title }} </h5>
                                                    </div>
                                                    <div class="project-order-td" data-col-key="requested_by"
                                                        data-filter-value="{{ trim(optional($milestone->getRequestedBy())->name ?? '') !== '' ? trim(optional($milestone->getRequestedBy())->name) : __('N/A') }}">
                                                        @if ($milestone->getRequestedBy() != null)
                                                            @include('projects.partials.pd_avatar', ['user' => $milestone->getRequestedBy()])
                                                        @endif
                                                    </div>
                                                    <div class="project-order-td" data-col-key="assigned_to"
                                                        data-filter-value="{{ trim(optional($milestone->getAssignedToUser())->name ?? '') !== '' ? trim(optional($milestone->getAssignedToUser())->name) : __('N/A') }}">
                                                        @if ($milestone->getAssignedToUser() != null)
                                                            @include('projects.partials.pd_avatar', ['user' => $milestone->getAssignedToUser()])
                                                        @else
                                                            <span class="pd-nobody">—</span>
                                                        @endif
                                                    </div>
                                                    <div class="project-order-td" data-col-key="status"
                                                        data-filter-value="{{ $statusText }}">
                                                        <span class="pd-status pd-status-{{ (int) $milestone->status }}"><i></i>{{ $statusText }}</span>
                                                    </div>
                                                    <div class="project-order-td" data-col-key="created"
                                                        data-filter-value="{{ $dateFilterLabel($milestone->start_date) }}"
                                                        data-filter-sort-value="{{ $dateFilterSortValue($milestone->start_date) }}">
                                                        <span class="pd-date-plain">{{ $milestone->start_date ? Carbon::parse($milestone->start_date)->format('d-m-Y') : '—' }}</span>
                                                    </div>
                                                    <div class="project-order-td" data-col-key="desired_delivery"
                                                        data-filter-value="{{ $dateFilterLabel($milestone->end_date) }}"
                                                        data-filter-sort-value="{{ $dateFilterSortValue($milestone->end_date) }}">
                                                        <span class="pd-date {{ $milestone->end_date ? '' : 'is-na' }}">{{ $milestone->end_date ? Carbon::parse($milestone->end_date)->format('d-m-Y') : '—' }}</span>
                                                    </div>
                                                    <div class="project-order-td" data-col-key="expected_delivery"
                                                        data-filter-value="{{ $dateFilterLabel($milestone->planned_end_date) }}"
                                                        data-filter-sort-value="{{ $dateFilterSortValue($milestone->planned_end_date) }}">
                                                        @php $pdPlanned = $milestone->planned_end_date && $milestone->planned_end_date !== '0000-00-00'; @endphp
                                                        <span class="pd-date {{ $pdPlanned ? '' : 'is-na' }}">{{ $pdPlanned ? \Carbon\Carbon::parse($milestone->planned_end_date)->format('d-m-Y') : '—' }}</span>
                                                    </div>
                                                    @php
                                                        $expectedDate = null;
                                                        if (
                                                            !empty($milestone->planned_end_date) &&
                                                            $milestone->planned_end_date !== '0000-00-00'
                                                        ) {
                                                            $expectedDate = \Carbon\Carbon::parse(
                                                                $milestone->planned_end_date,
                                                            );
                                                        } elseif (
                                                            !empty($milestone->end_date) &&
                                                            $milestone->end_date !== '0000-00-00'
                                                        ) {
                                                            $expectedDate = \Carbon\Carbon::parse(
                                                                $milestone->end_date,
                                                            );
                                                        }

                                                        $taskStartDate =
                                                            !empty($milestone->task_start_date) &&
                                                            $milestone->task_start_date !== '0000-00-00'
                                                                ? \Carbon\Carbon::parse($milestone->task_start_date)
                                                                : null;

                                                        $startColor = '';
                                                        if (
                                                            $taskStartDate &&
                                                            $expectedDate &&
                                                            $taskStartDate->gt($expectedDate)
                                                        ) {
                                                            $startColor = '#db8d33';
                                                        }
                                                    @endphp
                                                    <div class="project-order-td" data-col-key="task_started"
                                                                                                                data-filter-value="{{ $dateFilterLabel($milestone->task_start_date) }}"
                                                        data-filter-sort-value="{{ $taskStartDate ? $taskStartDate->format('Y-m') : '' }}">
                                                        <span class="pd-date {{ $taskStartDate ? ($startColor ? 'is-warn' : '') : 'is-na' }}">{{ $taskStartDate ? $taskStartDate->format('d-m-Y') : '—' }}</span>
                                                    </div>
                                                    @php
                                                        $completionDate =
                                                            !empty($milestone->finalization_date) &&
                                                            $milestone->finalization_date !== '0000-00-00'
                                                                ? \Carbon\Carbon::parse(
                                                                    $milestone->finalization_date,
                                                                )
                                                                : null;

                                                        $completionColor = '';
                                                        if ($completionDate && $expectedDate) {
                                                            if ($completionDate->lte($expectedDate)) {
                                                                $completionColor = '#53b446';
                                                            } else {
                                                                $completionColor = '#ff0000';
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="project-order-td" data-col-key="completion"
                                                                                                                data-filter-value="{{ $dateFilterLabel($milestone->finalization_date) }}"
                                                        data-filter-sort-value="{{ $completionDate ? $completionDate->format('Y-m') : '' }}">
                                                        <span class="pd-date {{ $completionDate ? ($completionColor === '#53b446' ? 'is-ok' : ($completionColor ? 'is-late' : '')) : 'is-na' }}">{{ $completionDate ? $completionDate->format('d-m-Y') : '—' }}</span>
                                                    </div>
                                                    <div class="project-order-td" data-col-key="action">
                                                        <div class="col-auto">
                                                            <a href="#"
                                                                class="pd-row-btn"
                                                                data-ajax-popup="true" data-size="lg"
                                                                data-toggle="popover" title="{{ __('Edit') }}"
                                                                data-title="{{ __('Edit Milestone') }}"
                                                                data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                    class="ti ti-edit"></i></a>
                                                            <a href="#"
                                                                class="pd-row-btn is-danger bs-pass-para"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-toggle="popover" title="{{ __('Delete') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                    class="ti ti-trash"></i></a>
                                                            <form id="delete-form1-{{ $milestone->id }}"
                                                                action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        </div>
                                        <div id="orderFormsFilteredEmptyState" class="project-order-filtered-empty-state">
                                            <h6 class="mb-2">{{ __('No order forms match the selected filters') }}</h6>
                                            <p class="mb-0">{{ __('Adjust or clear filters to see more results.') }}
                                            </p>
                                        </div>
                                    </div>
                    <div class="pd-orders-empty" id="pdOrdersEmpty" hidden>
                        <i class="fa-regular fa-file-lines"></i>
                        <span>{{ __('There are no order forms in this status.') }}</span>
                    </div>
                </div>
            </section>
        @endif

        {{-- ================= Archivos + equipo y actividad ================= --}}
        <div class="pd-grid">
            <div class="pd-main-col">
            <section class="pd-card files-section">
                <header class="pd-card-head">
                    <h3>{{ __('Files') }}</h3>
                    <span class="pd-count">{{ count($projectFiles ?? []) + collect($milestoneFiles ?? [])->sum(fn($m) => count($m['files'] ?? [])) }}</span>
                    <div class="pd-card-tools">
                        <button type="button" class="pd-btn files-upload-toggle" id="toggleUploadSectionBtn"
                            aria-expanded="false" aria-controls="files-upload-row">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i><span>{{ __('Upload file') }}</span>
                        </button>
                    </div>
                </header>
                <div class="pd-card-body files-body">
                                    <div class="author-box-name form-control-label mb-4"></div>
                                    <div class="row g-4">
                                        <div class="col-12 d-none" id="files-upload-row">
                                            <div class="dropzone browse-file" id="dropzonewidget">
                                                <div class="dz-message" data-dz-message>
                                                    <span> {{ __('Drop files here to upload') }}</span>
                                                    <p>
                                                        {{ __('You can Also hold click + Control + V to paste the content of the clipboard') }}
                                                    </p>
                                                    <p class="text-muted" style="font-size:15px; margin:5px;">50MB</p>
                                                    <small class="text-muted">.jpg .jpeg .png .gif .pdf .txt .doc .docx
                                                        .zip .rar
                                                        .dwg
                                                        .dxf</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 files-column">
                                            <div class="titleFiles files-group-title file-folder-toggle" role="button"
                                                tabindex="0" aria-expanded="true" aria-controls="project-files-content"
                                                data-folder-toggle="project-files-content">
                                                <i
                                                    class="fa-regular fa-folder-open d-inline me-2 fa-xl folder-toggle-icon"></i>
                                                <h5>{{ __('Project files') }}</h5>
                                            </div>
                                            <div class="custom-file-container files-grid folder-toggle-target files-scroll-area"
                                                id="project-files-content">
                                                @if (!empty($projectFiles) && count($projectFiles) > 0)
                                                    @foreach ($projectFiles as $file)
                                                        <div class="custom-file">
                                                            <div class="d-flex align-items-center flex-grow-1"
                                                                style="cursor: pointer; overflow: hidden;"
                                                                onclick='previewFile({{ $project->id }}, "", @json($file->file_path), @json($file->extension))'>
                                                                <img src="{{ asset('assets/iconFilesTypes/' . $file->extension . '.png') }}"
                                                                    alt="{{ $file->extension }} icon"
                                                                    class="styleIconFiles mt-2">
                                                                <p class="m-2"
                                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                    {{ $file->file_name }}
                                                                </p>
                                                            </div>
                                                            <div class="uploaded-file-buttons">
                                                                {{-- @php
                                                                dump($project);
                                                                dump($file);
                                                            @endphp --}}
                                                                <a onclick="downloadFile({{ $project->id }}, '', @json($file->file_path))"
                                                                    class="buttonFiles btn btn-sm">
                                                                    <i class="ti ti-download" style="color:white"></i>
                                                                </a>
                                                                <a class="bs-pass-para buttonFiles btn btn-sm"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-toggle="popover"
                                                                    title="{{ __('Delete File') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-file-{{ $file->id }}">
                                                                    <i class="fa-solid fa-trash" style="color:white"></i>
                                                                </a>
                                                                <form id="delete-file-{{ $file->id }}"
                                                                    action="{{ route('project.deleteFile', ['idProject' => $project->id, 'milestoneTitle' => '', 'fileID' => $file->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">{{ __('No project files uploaded yet.') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 files-column">
                                            <div class="titleFiles files-group-title file-folder-toggle" role="button"
                                                tabindex="0" aria-expanded="true"
                                                aria-controls="milestone-files-content"
                                                data-folder-toggle="milestone-files-content">
                                                <i
                                                    class="fa-regular fa-folder-open d-inline me-2 fa-xl folder-toggle-icon"></i>
                                                <h6>{{ __('Milestone files') }}</h6>
                                            </div>
                                            <div class="folder-toggle-target files-scroll-area"
                                                id="milestone-files-content">
                                                <!-- Sección de archivos de Milestones -->
                                                @if (!empty($milestoneFiles) && count($milestoneFiles) > 0)
                                                    @foreach ($milestoneFiles as $milestone)
                                                        <div class="milestone-files mb-4">
                                                            <div class="mt-2">
                                                                <div class="titleFiles file-folder-toggle" role="button"
                                                                    tabindex="0" aria-expanded="true"
                                                                    aria-controls="milestone-item-content-{{ $loop->index }}"
                                                                    data-folder-toggle="milestone-item-content-{{ $loop->index }}">
                                                                    <i
                                                                        class="fa-regular fa-folder-open me-2 text-dark folder-toggle-icon"></i>
                                                                    <h6>{{ $milestone['title'] }}</h6>
                                                                    <span class="pd-folder-count">{{ count($milestone['files'] ?? []) }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="folder-toggle-target"
                                                                id="milestone-item-content-{{ $loop->index }}">
                                                                <hr class="mt-0" style="border: 1px solid #eeeeee;">
                                                                <div class="custom-file-container">
                                                                    @if (!empty($milestone['files']) && count($milestone['files']) > 0)
                                                                        @foreach ($milestone['files'] as $file)
                                                                            <div class="custom-file position-relative"
                                                                                @if (str_contains(basename($file->file), '_rf')) style="border: 2px solid #aa182c; border-radius: 5px;"
         data-bs-toggle="tooltip"
         data-bs-placement="bottom"
         title="Archivo de replanteo" @endif>
                                                                                <div class="d-flex align-items-center flex-grow-1"
                                                                                    style="cursor: pointer; overflow: hidden;"
                                                                                    onclick='previewFile({{ $project->id }}, @json($milestone['title']), @json(basename($file->file)), @json($file->extension))'>
                                                                                    <img src="{{ asset('assets/iconFilesTypes/' . $file->extension . '.png') }}"
                                                                                        alt="{{ $file->extension }} icon"
                                                                                        class="styleIconFiles mt-2">
                                                                                    <p class="m-2"
                                                                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                                        {{ $file->name }}
                                                                                    </p>
                                                                                </div>
                                                                                <div class="uploaded-file-buttons">
                                                                                    <a onclick="downloadFile({{ $project->id }}, @json($milestone['title']), @json(basename($file->file)))"
                                                                                        class="buttonFiles btn btn-sm">
                                                                                        <i class="ti ti-download"
                                                                                            style="color:white"></i>
                                                                                    </a>
                                                                                    @if (!str_contains(basename($file->file), '_rf'))
                                                                                        <a class="bs-pass-para buttonFiles btn btn-sm"
                                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                                            data-toggle="popover"
                                                                                            title="{{ __('Delete File') }}"
                                                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                            data-confirm-yes="delete-file-{{ $file->id }}">
                                                                                            <i class="fa-solid fa-trash"
                                                                                                style="color:white"></i>
                                                                                        </a>
                                                                                        <form
                                                                                            id="delete-file-{{ $file->id }}"
                                                                                            action="{{ route('project.deleteFile', ['idProject' => $project->id, 'milestoneTitle' => $milestone['title'], 'fileID' => $file->id]) }}"
                                                                                            method="POST"
                                                                                            style="display: none;">
                                                                                            @csrf
                                                                                            @method('DELETE')
                                                                                        </form>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <p class="text-muted">
                                                                            {{ __('No files uploaded for this milestone.') }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">
                                                        {{ __('No milestone files uploaded yet.') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                </div>
            </section>

                {{-- Equipo --}}
                <section class="pd-card">
                    <header class="pd-card-head">
                        <h3>{{ __('Team') }}</h3>
                        <span class="pd-count">{{ count($pdTeam) }}</span>
                        @if (\Auth::user()->type == 'admin')
                            <div class="pd-card-tools">
                                <a href="#" class="pd-icon-btn" data-ajax-popup="true" data-title="{{ __('Invite') }}"
                                    title="{{ __('Invite') }}"
                                    data-url="{{ route('projects.invite.popup', [$currentWorkspace->slug, $project->id]) }}">
                                    <i class="fa-solid fa-user-plus"></i>
                                </a>
                            </div>
                        @endif
                    </header>
                    <div class="pd-team top-10-scroll">
                        @forelse ($pdTeam as $member)
                            @php $user = $member['user']; @endphp
                            <div class="pd-person">
                                @include('projects.partials.pd_avatar', ['user' => $user, 'size' => 36])
                                <div class="pd-person-main email-reveal-wrapper">
                                    <div class="pd-person-name">
                                        <span title="{{ $user->name }}">{{ $user->name }}</span>
                                        <i class="fa-regular fa-envelope email-reveal-icon tooltipCus" data-title="{{ __('messages.Mostrar correo') }}" data-show-text="{{ __('messages.Mostrar correo') }}" data-hide-text="{{ __('messages.Ocultar correo') }}" data-user-id="{{ $user->id }}"></i>
                                    </div>
                                    <small class="email-reveal-email"></small>
                                    <div class="pd-chips">
                                        @if ($member['orders'])
                                            <span class="pd-chip is-red"><i class="fa-regular fa-file-lines"></i>{{ trans_choice(':count order form created|:count order forms created', $member['orders'], ['count' => $member['orders']]) }}</span>
                                        @endif
                                        @if ($member['hours'])
                                            <span class="pd-chip"><i class="fa-regular fa-clock"></i>{{ $member['hours'] }} h</span>
                                        @endif
                                    </div>
                                </div>
                                @auth('web')
                                    @if (\Auth::user()->type == 'admin')
                                        <div class="dropdown pd-person-actions">
                                            <button type="button" class="pd-icon-btn is-small" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Action') }}">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end pd-menu">
                                                <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="lg"
                                                    data-title="{{ __('Edit Permission') }}"
                                                    data-url="{{ route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id]) }}">
                                                    <i class="ti ti-lock"></i>{{ __('Permission') }}
                                                </a>
                                                <a href="#" class="dropdown-item text-danger bs-pass-para" data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-user-{{ $user->id }}">
                                                    <i class="ti ti-trash"></i>{{ __('Delete') }}
                                                </a>
                                            </div>
                                            <form id="delete-user-{{ $user->id }}"
                                                action="{{ route('projects.user.delete', [$currentWorkspace->slug, $project->id, $user->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @empty
                            <p class="pd-empty-text">{{ __('Nobody has created order forms or logged hours yet.') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="pd-activity-col">
                {{-- Actividad --}}
                <section class="pd-card pd-activity-card">
                    <header class="pd-card-head">
                        <h3>{{ __('Activity') }}</h3>
                        @if ($pdCanEditOrders && count($project->activities))
                            <span class="pd-count">{{ count($project->activities) }}</span>
                        @endif
                    </header>
                    <div class="pd-activity">
                        @if ($pdCanEditOrders && count($project->activities))
                            @php $pdLastDay = null; $pdActivityVisible = 8; @endphp
                            @foreach ($project->activities as $activity)
                                @php
                                    [$pdIcon, $pdTone] = $pdActivityTypes[$activity->log_type] ?? ['fa-circle-info', 'is-gray'];
                                    $pdDay = $pdDayLabel($activity->created_at);
                                    $pdExtra = $loop->index >= $pdActivityVisible ? ' is-extra' : '';
                                    // getRemark() empieza por el nombre de la persona: se quita y se muestra debajo
                                    $pdRemark = $activity->getRemark();
                                    $pdWho = $activity->user ? e($activity->user->name) : '';
                                    if ($pdWho !== '' && str_starts_with($pdRemark, $pdWho . ' ')) {
                                        $pdRemark = substr($pdRemark, strlen($pdWho) + 1);
                                        $pdRemark = mb_strtoupper(mb_substr($pdRemark, 0, 1)) . mb_substr($pdRemark, 1);
                                    }
                                    $pdWhoShort = $activity->user ? implode(' ', array_slice(preg_split('/\s+/', trim($activity->user->name)), 0, 2)) : '';
                                @endphp
                                @if ($pdDay !== $pdLastDay)
                                    <div class="pd-day{{ $pdExtra }}">{{ $pdDay }}</div>
                                    @php $pdLastDay = $pdDay; @endphp
                                @endif
                                <div class="pd-event{{ $pdExtra }}">
                                    <span class="pd-event-icon {{ $pdTone }}"><i class="fa-solid {{ $pdIcon }}"></i></span>
                                    <div class="pd-event-main">
                                        <div class="pd-event-text">{!! $pdRemark !!}</div>
                                        <div class="pd-event-time" title="{{ $activity->created_at }}">@if ($pdWhoShort){{ $pdWhoShort }} · @endif{{ $activity->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="pd-empty-text">{{ __('There is no activity yet.') }}</p>
                        @endif
                    </div>
                    @if ($pdCanEditOrders && count($project->activities) > 8)
                        <button type="button" class="pd-more" id="pdActivityMore"
                            data-less="{{ __('Show less') }}">{{ __('See all activity (:count)', ['count' => count($project->activities)]) }}</button>
                    @endif
                </section>
            </div>
        </div>
    </div>
    @include('projects.file_preview')
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/project_detail.css') }}?v={{ filemtime(public_path('assets/css/project_detail.css')) }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        function downloadFile(idProject, titleMilestone, file) {
            const downloadUrl = "{{ route('project.downloadFile') }}";
            $.ajax({
                url: downloadUrl,
                method: 'POST',
                data: {
                    "idProject": idProject,
                    "milestoneTitle": titleMilestone,
                    "fileName": file,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        console.log("Download URL: ", response.file_url);

                        // Crear un enlace temporal para descargar el archivo
                        let downloadLink = document.createElement("a");
                        downloadLink.href = response.file_url;
                        downloadLink.target = "_blank";
                        downloadLink.download = file; // Nombre del archivo
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    } else {
                        alert("Error: File not found.");
                    }
                },
                error: function(xhr) {
                    alert("An error occurred while downloading the file.");
                    console.error(xhr.responseText);
                }
            });
        }

        (function() {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: {!! json_encode($chartData['color']) !!},
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [
                    @foreach ($chartData['stages'] as $id => $name)
                        {
                            name: "{{ __($name) }}",
                            // data:
                            data: {!! json_encode($chartData[$id]) !!},
                        },
                    @endforeach
                ],
                xaxis: {
                    type: "category",
                    categories: {!! json_encode($chartData['label']) !!},
                    title: {
                        text: '{{ __('Days') }}'
                    },
                    tooltip: {
                        enabled: false,
                    }
                },
                yaxis: {
                    show: true,
                    position: "left",
                    title: {
                        text: '{{ __('Tasks') }}'
                    },
                },
                grid: {
                    show: true,
                    borderColor: "#EBEBEB",
                    strokeDashArray: 0,
                    position: "back",
                    xaxis: {
                        show: true,
                        lines: {
                            show: true,
                        },
                    },
                    yaxis: {
                        show: false,
                        lines: {
                            show: false,
                        },
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5,
                    },
                    column: {
                        position: "back",
                        colors: undefined,
                        opacity: 0.5,
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0,
                    },
                },
                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },

                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#task-chart"), options);
            chart.render();
        })();
    </script>
    <script src="{{ asset('assets/custom/libs/nicescroll/jquery.nicescroll.min.js') }} "></script>
    <script>
        $(document).ready(function() {

            $(".uploaded-files-container").niceScroll();

            if ($(".top-10-scroll").length) {
                $(".top-10-scroll").niceScroll();
            }

        });
    </script>
    <script src="{{ asset('assets/custom/js/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            maxFilesize: 52428800, // Tamaño máximo = 200MB
            parallelUploads: 1,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg,.pdf,.txt,.doc,.docx,.zip,.rar,.dwg,.dxf",
            url: "{{ route('projects.file.upload', [$currentWorkspace->slug, $project->id]) }}",

            success: function(file, response) {
                if (response.is_success) {
                    show_toastr('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('{{ __('Error') }}', 'Error while storing the document.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                let errorMsg = 'Error while storing the document.';
                if (response && response.error) {
                    errorMsg = response.error;
                } else if (response && response.size) {
                    errorMsg = 'File too big';
                }
                show_toastr('{{ __('Error') }}', errorMsg, 'error');
            },

            complete: function(file) {
                // Verifica si Dropzone ha terminado con todos los archivos
                if (this.getQueuedFiles().length === 0 && this.getUploadingFiles().length === 0) {
                    // Recarga la página después de que se suban todos los archivos
                    setTimeout(function() {
                            location.reload(); // Recarga la página actual
                        },
                        1000
                    ); // Opcional: Agrega un pequeño delay para asegurarte de que el backend procese todo.
                }
            }
        });
        // Función para agregar un botón de eliminación al archivo en Dropzone
        function addDeleteButton(file, filePath) {
            const deleteButton = Dropzone.createElement(`
                <button class="btn btn-sm btn-danger ml-2">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `);

            // Agregar evento al botón de eliminación
            deleteButton.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (confirm("Are you sure you want to delete this file?")) {
                    deleteFile({{ $project->id }}, filePath); // Usar la lógica de eliminación existente
                    myDropzone.removeFile(file);
                }
            });

            // Agregar el botón de eliminación al contenedor del archivo
            file.previewElement.appendChild(deleteButton);
        }

        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("project_id", {{ $project->id }});
        });

        @if (isset($permisions) && in_array('show uploading', $permisions))
            $(".dz-hidden-input").prop("disabled", true);
            myDropzone.removeEventListeners();
        @endif

        function dropzoneBtn(file, response) {

            var html = document.createElement('span');
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "popover");
            download.setAttribute('download', "");
            download.setAttribute('title', "{{ __('Download') }}");
            // download.innerHTML = "<i class='fas fa-download mt-2'></i>";
            download.innerHTML = "<i class='ti ti-download'> </i>";
            html.appendChild(download);

            @if (isset($permisions) && in_array('show uploading', $permisions))
            @else
                var del = document.createElement('a');
                del.setAttribute('href', response.delete);
                del.setAttribute('class', "action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center");
                del.setAttribute('data-toggle', "popover");
                del.setAttribute('title', "{{ __('Delete') }}");
                del.innerHTML = "<i class='ti ti-trash '></i>";

                del.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm("Are you sure ?")) {
                        var btn = $(this);
                        $.ajax({
                            url: btn.attr('href'),
                            type: 'DELETE',
                            success: function(response) {
                                if (response.is_success) {
                                    btn.closest('.dz-image-preview').remove();
                                    show_toastr('{{ __('Success') }}', 'File Successfully Deleted',
                                        'success');
                                } else {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                }
                            },
                            error: function(response) {
                                response = response.responseJSON;
                                if (response.is_success) {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                } else {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                }
                            }
                        })
                    }
                });
                html.appendChild(del);
            @endif

            file.previewTemplate.appendChild(html);
        }
        @php($setting = App\Models\Utility::getAdminPaymentSettings())

        @php($files = $project->files)
        @foreach ($files as $file)
            @php($storage_file = asset($logo_project_files . $file->file_path))

            // Create the mock file:
            @if (Storage::disk($setting['storage_setting'])->exists('/project_files/' . $file->file_path))

                var mockFile = {
                    name: "{{ $file->file_name }}",
                    size: {{ filesize('storage/project_files/' . $file->file_path) }}
                };
            @endif
            // Call the default addedfile event handler
            myDropzone.emit("addedfile", mockFile);
            // And optionally show the thumbnail of the file:
            myDropzone.emit("thumbnail", mockFile, "{{ asset($logo_project_files . $file->file_path) }}");
            myDropzone.emit("complete", mockFile);

            dropzoneBtn(mockFile, {
                download: "{{ route('projects.file.download', [$currentWorkspace->slug, $project->id, $file->id]) }}",
                delete: "{{ route('projects.file.delete', [$currentWorkspace->slug, $project->id, $file->id]) }}"
            });
        @endforeach
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const headerRow = document.querySelector('.project-order-header-row');
            const tbody = document.getElementById('orderFormsTableBody');
            const columnToggleButton = document.getElementById('orderFormsColumnsToggleBtn');
            const filteredEmptyState = document.getElementById('orderFormsFilteredEmptyState');

            if (!headerRow || !tbody || !columnToggleButton || !filteredEmptyState) {
                return;
            }

            const rows = Array.from(tbody.querySelectorAll('.project-order-body-row'));
            const headers = Array.from(headerRow.querySelectorAll('.project-order-th[data-col-key]'));
            const filterButtons = Array.from(headerRow.querySelectorAll('.project-order-filter-btn'));
            const filterState = {};
            const columnVisibilityState = new Map();
            const filterMenu = document.createElement('div');
            const columnMenu = document.createElement('div');
            const naLabel = "{{ __('N/A') }}";
            let activeFilterButton = null;

            filterMenu.className = 'project-order-filter-menu';
            filterMenu.hidden = true;
            document.body.appendChild(filterMenu);

            columnMenu.className = 'project-order-column-menu';
            columnMenu.hidden = true;
            document.body.appendChild(columnMenu);

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function getCellValue(row, columnIndex) {
                const meta = getCellFilterMeta(row, columnIndex);
                return meta.value;
            }

            function getCellFilterMeta(row, columnIndex) {
                const cell = row.children[columnIndex];
                if (!cell) {
                    return {
                        value: naLabel,
                        sortValue: ''
                    };
                }

                const explicitValue = cell.getAttribute('data-filter-value');
                if (explicitValue !== null) {
                    const trimmedExplicitValue = explicitValue.replace(/\s+/g, ' ').trim();
                    return {
                        value: trimmedExplicitValue || naLabel,
                        sortValue: cell.getAttribute('data-filter-sort-value') || ''
                    };
                }

                const textValue = cell.textContent.replace(/\s+/g, ' ').trim();
                return {
                    value: textValue || naLabel,
                    sortValue: textValue || ''
                };
            }

            function getColumnOptions(filterKey, columnIndex) {
                const counts = new Map();

                rows.forEach(function(row) {
                    const meta = getCellFilterMeta(row, columnIndex);
                    const existingOption = counts.get(meta.value);

                    if (existingOption) {
                        existingOption.count += 1;
                        return;
                    }

                    counts.set(meta.value, {
                        count: 1,
                        sortValue: meta.sortValue || meta.value
                    });
                });

                return Array.from(counts.entries())
                    .sort(function(left, right) {
                        if (left[0] === naLabel) {
                            return 1;
                        }

                        if (right[0] === naLabel) {
                            return -1;
                        }

                        return left[1].sortValue.localeCompare(right[1].sortValue, undefined, {
                            numeric: true,
                            sensitivity: 'base'
                        });
                    })
                    .map(function(entry) {
                        return {
                            value: entry[0],
                            count: entry[1].count
                        };
                    });
            }

            function isColumnFiltered(filterKey) {
                return filterState[filterKey] instanceof Set;
            }

            function updateFilterButtonStates() {
                filterButtons.forEach(function(button) {
                    button.classList.toggle('is-active', isColumnFiltered(button.dataset.filterKey));
                });
            }

            function applyFilters() {
                let visibleRows = 0;

                rows.forEach(function(row) {
                    const isVisible = filterButtons.every(function(button) {
                        const filterKey = button.dataset.filterKey;
                        const activeValues = filterState[filterKey];

                        if (!(activeValues instanceof Set)) {
                            return true;
                        }

                        return activeValues.has(getCellValue(row, Number(button.dataset
                            .columnIndex)));
                    });

                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        visibleRows++;
                    }
                });

                filteredEmptyState.classList.toggle('is-visible', visibleRows === 0);
                updateFilterButtonStates();
            }

            function closeFilterMenu() {
                filterMenu.hidden = true;
                if (activeFilterButton) activeFilterButton.setAttribute('aria-expanded', 'false');
                activeFilterButton = null;
            }

            function closeColumnMenu() {
                columnMenu.hidden = true;
                columnToggleButton.classList.remove('is-active');
                columnToggleButton.setAttribute('aria-expanded', 'false');
            }

            function positionMenu(menu, button) {
                const rect = button.getBoundingClientRect();
                const menuWidth = 260;
                const viewportWidth = window.innerWidth;
                const left = Math.max(12, Math.min(rect.right - menuWidth, viewportWidth - menuWidth - 12));

                menu.style.top = (rect.bottom + 8) + 'px';
                menu.style.left = left + 'px';
            }

            function getHeaderLabelByIndex(index) {
                const header = headers[index];
                if (!header) {
                    return '';
                }

                const label = header.querySelector('.project-order-th-content span');
                return (label ? label.textContent : header.textContent).replace(/\s+/g, ' ').trim();
            }

            function updateColumnToggleSummary() {
                const visibleCount = headers.reduce(function(total, _, columnIndex) {
                    return total + (columnVisibilityState.get(columnIndex) !== false ? 1 : 0);
                }, 0);
                const totalCount = headers.length;
                const toggleCount = document.getElementById('orderFormsColumnsToggleCount');

                if (toggleCount) {
                    toggleCount.textContent = visibleCount + '/' + totalCount;
                }
            }

            function applyColumnVisibility() {
                headers.forEach(function(header, columnIndex) {
                    const isVisible = columnVisibilityState.get(columnIndex) !== false;
                    header.style.display = isVisible ? '' : 'none';
                });

                rows.forEach(function(row) {
                    headers.forEach(function(_, columnIndex) {
                        const cell = row.children[columnIndex];
                        if (!cell) {
                            return;
                        }

                        const isVisible = columnVisibilityState.get(columnIndex) !== false;
                        cell.style.display = isVisible ? '' : 'none';
                    });
                });

                updateColumnToggleSummary();
                applyFilters();
            }

            function renderColumnMenu() {
                columnMenu.innerHTML = [
                    '<div class="project-order-filter-menu-header">',
                    '<h6 class="project-order-column-menu-title">{{ __('Visible columns') }}</h6>',
                    '<button type="button" class="project-order-filter-link" data-column-close="1">{{ __('Close') }}</button>',
                    '</div>',
                    '<div class="project-order-filter-menu-actions">',
                    '<button type="button" class="project-order-filter-link" data-column-reset="1">{{ __('Hide all') }}</button>',
                    '<button type="button" class="project-order-filter-link" data-column-select-all="1">{{ __('Show all') }}</button>',
                    '</div>',
                    '<div class="project-order-filter-options">',
                    headers.map(function(_, columnIndex) {
                        const label = getHeaderLabelByIndex(columnIndex);
                        const isChecked = columnVisibilityState.get(columnIndex) !== false;

                        return [
                            '<label class="project-order-filter-option" data-column-option="1">',
                            '<input type="checkbox" data-column-index="' + columnIndex + '" ' + (
                                isChecked ? 'checked' : '') + '>',
                            '<span>' + escapeHtml(label) + '</span>',
                            '</label>'
                        ].join('');
                    }).join(''),
                    '</div>'
                ].join('');

                positionMenu(columnMenu, columnToggleButton);
                columnToggleButton.classList.add('is-active');
                columnToggleButton.setAttribute('aria-expanded', 'true');
                columnMenu.hidden = false;
            }

            function renderFilterMenu(button) {
                const filterKey = button.dataset.filterKey;
                const filterLabel = button.dataset.filterLabel;
                const columnIndex = Number(button.dataset.columnIndex);
                const options = getColumnOptions(filterKey, columnIndex);
                const activeValues = filterState[filterKey];

                filterMenu.innerHTML = [
                    '<div class="project-order-filter-menu-header">',
                    '<h6 class="project-order-filter-menu-title">' + escapeHtml(filterLabel) + '</h6>',
                    '<button type="button" class="project-order-filter-link" data-filter-close="1">{{ __('Close') }}</button>',
                    '</div>',
                    '<div class="project-order-filter-menu-actions">',
                    '<button type="button" class="project-order-filter-link" data-filter-reset="1">{{ __('Clear') }}</button>',
                    '<button type="button" class="project-order-filter-link" data-filter-select-all="1">{{ __('Select all') }}</button>',
                    '</div>',
                    '<input type="search" class="project-order-filter-search" placeholder="{{ __('Search') }}..." />',
                    '<div class="project-order-filter-options">',
                    options.map(function(option) {
                        const isChecked = !(activeValues instanceof Set) || activeValues.has(option.value);

                        return [
                            '<label class="project-order-filter-option" data-filter-option="1">',
                            '<input type="checkbox" value="' + escapeHtml(option.value) + '" ' + (
                                isChecked ? 'checked' : '') + '>',
                            '<span>' + escapeHtml(option.value) + '</span>',
                            '<span class="project-order-filter-option-count">' + option.count +
                            '</span>',
                            '</label>'
                        ].join('');
                    }).join(''),
                    '</div>'
                ].join('');

                filterMenu.dataset.filterKey = filterKey;
                filterMenu.dataset.columnIndex = String(columnIndex);
                filterMenu.hidden = false;
                if (activeFilterButton && activeFilterButton !== button) activeFilterButton.setAttribute('aria-expanded', 'false');
                activeFilterButton = button;
                button.setAttribute('aria-expanded', 'true'); // mantiene el embudo visible mientras su menú está abierto
                positionMenu(filterMenu, button);
            }

            function syncFilterStateFromMenu(filterKey, columnIndex) {
                const options = getColumnOptions(filterKey, columnIndex);
                const checkedValues = Array.from(filterMenu.querySelectorAll('input[type="checkbox"]:checked')).map(
                    function(input) {
                        return input.value;
                    }
                );

                if (checkedValues.length === options.length) {
                    filterState[filterKey] = null;
                } else {
                    filterState[filterKey] = new Set(checkedValues);
                }

                applyFilters();
            }

            filterButtons.forEach(function(button) {
                filterState[button.dataset.filterKey] = null;

                button.addEventListener('click', function(event) {
                    event.stopPropagation();

                    if (activeFilterButton === button && !filterMenu.hidden) {
                        closeFilterMenu();
                        return;
                    }

                    renderFilterMenu(button);
                });
            });

            headers.forEach(function(_, columnIndex) {
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

                    filterState[filterMenu.dataset.filterKey] = new Set();
                    applyFilters();
                    return;
                }

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                });

                filterState[filterMenu.dataset.filterKey] = null;
                applyFilters();
            });

            filterMenu.addEventListener('change', function(event) {
                if (!event.target.matches('input[type="checkbox"]')) {
                    return;
                }

                syncFilterStateFromMenu(filterMenu.dataset.filterKey, Number(filterMenu.dataset
                    .columnIndex));
            });

            filterMenu.addEventListener('input', function(event) {
                if (!event.target.matches('.project-order-filter-search')) {
                    return;
                }

                const query = event.target.value.trim().toLowerCase();
                Array.from(filterMenu.querySelectorAll('[data-filter-option]')).forEach(function(option) {
                    const optionText = option.textContent.toLowerCase();
                    option.style.display = optionText.includes(query) ? '' : 'none';
                });
            });

            columnMenu.addEventListener('click', function(event) {
                const closeButton = event.target.closest('[data-column-close]');
                const resetButton = event.target.closest('[data-column-reset]');
                const selectAllButton = event.target.closest('[data-column-select-all]');

                if (closeButton) {
                    closeColumnMenu();
                    return;
                }

                if (resetButton) {
                    headers.forEach(function(_, columnIndex) {
                        columnVisibilityState.set(columnIndex, false);
                    });
                    applyColumnVisibility();
                    renderColumnMenu();
                    return;
                }

                if (selectAllButton) {
                    headers.forEach(function(_, columnIndex) {
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

            document.addEventListener('click', function(event) {
                if (!filterMenu.hidden && !filterMenu.contains(event.target) && !event.target.closest(
                        '.project-order-filter-btn')) {
                    closeFilterMenu();
                }

                if (!columnMenu.hidden && !columnMenu.contains(event.target) && !event.target.closest(
                        '#orderFormsColumnsToggleBtn')) {
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
                    positionMenu(filterMenu, activeFilterButton);
                }

                if (!columnMenu.hidden) {
                    positionMenu(columnMenu, columnToggleButton);
                }
            });

            window.addEventListener('scroll', function() {
                if (activeFilterButton && !filterMenu.hidden) {
                    positionMenu(filterMenu, activeFilterButton);
                }

                if (!columnMenu.hidden) {
                    positionMenu(columnMenu, columnToggleButton);
                }
            }, true);

            rows.forEach(function(row) {
                row.addEventListener('click', function(event) {
                    if (event.target.closest('button, a, input, label, form')) return;
                    var url = row.getAttribute('data-url');
                    var title = row.getAttribute('data-title');
                    if (!url) return;
                    $.ajax({
                        url: url,
                        cache: false,
                        success: function(data) {
                            $('#commonModal .body').html(data);
                            $('#commonModal .modal-title').html(title);
                            bootstrap.Modal.getOrCreateInstance(document.getElementById('commonModal')).show();
                            commonLoader();
                        }
                    });
                });
            });

            applyColumnVisibility();
            applyFilters();
        });
    </script>
    <script>
        /**
         * Integración de subida de archivos por Ctrl+V, drag & drop y selección manual
         * - El <div class="dropzone" tabindex="0"> permite foco y captura de “paste”
         * - El <input type="file" id="fileInput" multiple style="display: none;"> permite selección manual
         * - El CSS ya define borde punteado y efecto dragover
         * - La función handleFiles(files) es central y única para todos los métodos
         */

        // --- Selección de elementos ---
        const dropzone = document.querySelector('.dropzone');
        let fileInput = document.getElementById('fileInput');
        if (!fileInput) {
            fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.id = 'fileInput';
            fileInput.multiple = true;
            fileInput.style.display = 'none';
            dropzone.parentNode.insertBefore(fileInput, dropzone.nextSibling);
        }

        // --- Extensiones y tipos MIME permitidos ---
        const allowed = [{
                ext: 'png',
                mime: 'image/png'
            },
            {
                ext: 'gif',
                mime: 'image/gif'
            },
            {
                ext: 'pdf',
                mime: 'application/pdf'
            },
            {
                ext: 'txt',
                mime: 'text/plain'
            },
            {
                ext: 'doc',
                mime: 'application/msword'
            },
            {
                ext: 'docx',
                mime: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            },
            {
                ext: 'zip',
                mime: 'application/zip'
            },
            {
                ext: 'rar',
                mime: 'application/vnd.rar'
            },
            {
                ext: 'rar',
                mime: 'application/x-rar-compressed'
            },
            {
                ext: 'dwg',
                mime: 'application/acad'
            },
            {
                ext: 'dwg',
                mime: 'application/autocad_dwg'
            },
            {
                ext: 'dxf',
                mime: 'application/dxf'
            }
        ];
        const allowedExts = allowed.map(a => a.ext);
        const allowedMimes = allowed.map(a => a.mime);

        // --- Drag & Drop visual feedback ---
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        dropzone.addEventListener('dragleave', function(e) {
            dropzone.classList.remove('dragover');
        });
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                handleFiles(Array.from(e.dataTransfer.files));
                // Al acabar de procesar, refocuseamos
                dropzone.focus();
            }
        });

        // --- Selección manual desde input file ---
        dropzone.addEventListener('dblclick', function() {
            fileInput.value = '';
            fileInput.click();
        });
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files.length) {
                handleFiles(Array.from(fileInput.files));
                dropzone.focus();
            }
        });

        // --- Permitimos que la dropzone reciba foco y capture paste ---
        dropzone.setAttribute('tabindex', '0'); // hace que se pueda enfocar

        // Si el usuario hace clic en la dropzone (cualquier parte), la enfocamos
        dropzone.addEventListener('click', () => {
            dropzone.focus();
        });

        // --- Capturar paste a nivel de document, pero sólo procesar si foco está dentro de dropzone ---
        document.addEventListener('paste', function(e) {
            // Si el elemento actualmente enfocado NO es la dropzone ni ninguno de sus hijos, salimos
            const focused = document.activeElement;
            if (focused !== dropzone && !dropzone.contains(focused)) {
                return;
            }

            e.preventDefault(); // Evitamos comportamiento nativo no deseado

            if (!e.clipboardData || !e.clipboardData.items) {
                return;
            }

            const items = Array.from(e.clipboardData.items);
            const conversionPromises = items.map(item => {
                if (item.kind !== 'file') {
                    return Promise.resolve(null);
                }
                const file = item.getAsFile();
                if (!file) {
                    return Promise.resolve(null);
                }
                const ext = file.name.split('.').pop().toLowerCase();
                const mime = file.type;

                // Si es PNG o GIF → convertir a JPG
                if (
                    mime === 'image/png' || mime === 'image/gif' ||
                    ext === 'png' || ext === 'gif'
                ) {
                    return new Promise(resolve => {
                        convertImageToJPG(file, function(jpgFile) {
                            resolve(jpgFile);
                        });
                    });
                }

                // Si es otro formato permitido, devolvemos el File tal cual
                if (
                    allowedExts.includes(ext) ||
                    allowedMimes.includes(mime)
                ) {
                    return Promise.resolve(file);
                }

                // De lo contrario, no lo tomamos
                return Promise.resolve(null);
            });

            Promise.all(conversionPromises).then(results => {
                const archivosValidos = results.filter(f => f instanceof File);
                if (archivosValidos.length > 0) {
                    handleFiles(archivosValidos);
                } else {
                    alert('El portapapeles no contiene un archivo válido');
                }
                // Refocuseamos la dropzone para seguir recibiendo Ctrl+V indefinidamente
                dropzone.focus();
            });
        });

        // --- Función ÚNICA para procesar archivos subidos (pegados, arrastrados o seleccionados) ---
        function handleFiles(files) {
            files.forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase();
                const mime = file.type;

                // 1) Si el archivo ya es un JPEG (resultado de la conversión), lo subimos directamente
                if (mime === 'image/jpeg') {
                    myDropzone.addFile(file);
                    return;
                }

                // 2) Si es PNG o GIF (arrastrado o seleccionado manualmente), convertimos a JPG
                if (
                    mime === 'image/png' || mime === 'image/gif' ||
                    ext === 'png' || ext === 'gif'
                ) {
                    convertImageToJPG(file, function(jpgFile) {
                        myDropzone.addFile(jpgFile);
                    });
                    return;
                }

                // 3) Si es cualquier otro formato permitido, lo subimos tal cual
                if (
                    allowedExts.includes(ext) ||
                    allowedMimes.includes(mime)
                ) {
                    myDropzone.addFile(file);
                    return;
                }

                // 4) Cualquier otro, ignorar completamente
            });
        }

        // --- Conversión de imagen PNG/GIF a JPG usando canvas ---
        function convertImageToJPG(blobOrFile, callback) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    canvas.toBlob(function(jpgBlob) {
                        // Conservamos el nombre y cambiamos extensión a .jpg
                        const nuevoNombre = (blobOrFile.name || 'clipboard').replace(/\.(png|gif)$/i,
                            '.jpg');
                        const jpgFile = new File([jpgBlob], nuevoNombre, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });
                        callback(jpgFile);
                    }, 'image/jpeg', 0.92);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(blobOrFile);
        }

        // --- Evento para asegurar que Dropzone vuelve a enfocar tras cada archivo añadido ---
        // Suponiendo que ya inicializaste `myDropzone = new Dropzone(...)` en algún punto anterior:
        // Así te aseguras de que, aunque Dropzone injecte previews u otros elementos que roben foco,
        // la dropzone recupere inmediatamente el foco.
        if (window.myDropzone) {
            myDropzone.on('addedfile', function() {
                // Tras cada archivo agregado, refocuseamos
                dropzone.focus();
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const folderToggles = document.querySelectorAll('[data-folder-toggle]');

            function setExpanded(toggleElement, isExpanded, animate = true) {
                const targetId = toggleElement.getAttribute('data-folder-toggle');
                if (!targetId) {
                    return;
                }

                const content = document.getElementById(targetId);
                if (!content) {
                    return;
                }

                toggleElement.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');

                const icon = toggleElement.querySelector('.folder-toggle-icon');
                if (icon) {
                    icon.classList.remove('fa-folder-open', 'fa-folder');
                    icon.classList.add(isExpanded ? 'fa-folder-open' : 'fa-folder');
                }

                if (window.jQuery) {
                    const $content = window.jQuery(content);
                    if (isExpanded) {
                        animate ? $content.stop(true, true).slideDown(160) : $content.show();
                    } else {
                        animate ? $content.stop(true, true).slideUp(160) : $content.hide();
                    }
                    return;
                }

                content.style.display = isExpanded ? '' : 'none';
            }

            folderToggles.forEach(function(toggleElement) {
                const targetId = toggleElement.getAttribute('data-folder-toggle') || '';
                const isMilestoneInnerToggle = targetId.startsWith('milestone-item-content-');

                setExpanded(toggleElement, !isMilestoneInnerToggle, false);

                toggleElement.addEventListener('click', function() {
                    const isCurrentlyExpanded = toggleElement.getAttribute('aria-expanded') ===
                        'true';
                    setExpanded(toggleElement, !isCurrentlyExpanded, true);
                });

                toggleElement.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        const isCurrentlyExpanded = toggleElement.getAttribute('aria-expanded') ===
                            'true';
                        setExpanded(toggleElement, !isCurrentlyExpanded, true);
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleUploadBtn = document.getElementById('toggleUploadSectionBtn');
            const uploadRow = document.getElementById('files-upload-row');
            let hideTimer = null;

            if (!toggleUploadBtn || !uploadRow) {
                return;
            }

            function setUploadVisible(visible) {
                toggleUploadBtn.setAttribute('aria-expanded', visible ? 'true' : 'false');

                if (hideTimer) {
                    clearTimeout(hideTimer);
                    hideTimer = null;
                }

                if (visible) {
                    uploadRow.classList.remove('d-none');
                    requestAnimationFrame(function() {
                        uploadRow.classList.add('is-visible');
                    });
                } else {
                    uploadRow.classList.remove('is-visible');
                    hideTimer = setTimeout(function() {
                        uploadRow.classList.add('d-none');
                    }, 220);
                }
            }

            setUploadVisible(false);

            toggleUploadBtn.addEventListener('click', function() {
                const isVisible = !uploadRow.classList.contains('d-none');
                setUploadVisible(!isVisible);
            });
        });
    </script>
    <script>
        // Pestañas de estado y buscador de encargos. Usan sus propias clases para ocultar filas,
        // así se combinan con los filtros por columna (que usan style.display) sin pisarse.
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.getElementById('pdOrderTabs');
            const search = document.getElementById('pdOrderSearch');
            const empty = document.getElementById('pdOrdersEmpty');
            const rows = Array.from(document.querySelectorAll('#orderFormsTableBody .project-order-body-row'));
            if (!tabs || !rows.length) return;

            let status = '*';
            const norm = (v) => (v || '').toString().normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

            function apply() {
                const q = norm(search ? search.value : '');
                let shown = 0;
                rows.forEach(function(row) {
                    const tabOk = status === '*' || row.dataset.statusId === status;
                    const searchOk = !q || norm(row.dataset.name).includes(q);
                    row.classList.toggle('pd-hidden-by-tab', !tabOk);
                    row.classList.toggle('pd-hidden-by-search', !searchOk);
                    if (tabOk && searchOk) shown++;
                });
                if (empty) empty.hidden = shown > 0;
            }

            tabs.addEventListener('click', function(e) {
                const tab = e.target.closest('.pd-tab');
                if (!tab) return;
                tabs.querySelectorAll('.pd-tab').forEach((t) => t.classList.toggle('is-active', t === tab));
                status = tab.dataset.statusId;
                apply();
            });
            if (search) search.addEventListener('input', apply);
        });
    </script>
    <script>
        // Actividad: se ven los 8 últimos eventos; «Ver toda la actividad» despliega el resto (y lo vuelve a plegar)
        document.addEventListener('DOMContentLoaded', function() {
            const more = document.getElementById('pdActivityMore');
            const list = document.querySelector('.pd .pd-activity');
            if (!more || !list) return;
            const label = more.textContent;
            more.addEventListener('click', function() {
                const open = list.classList.toggle('show-all');
                more.textContent = open ? more.dataset.less : label;
                if (!open) list.scrollTop = 0;
            });
        });
    </script>
    <script>
        // ?milestone=ID (al abrir un aviso de la campana): se resalta ese encargo en la tabla y se abre su detalle
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const id = parseInt(params.get('milestone'), 10);
            if (!id) return;
            params.delete('milestone');
            const clean = window.location.pathname + (params.toString() ? '?' + params.toString() : '') + window.location.hash;
            window.history.replaceState(null, '', clean);

            const suffix = '/' + id + '/show';
            const row = Array.from(document.querySelectorAll('#orderFormsTableBody .project-order-body-row'))
                .find(function(r) { return (r.getAttribute('data-url') || '').endsWith(suffix); });
            if (!row) return;
            // Que se vea aunque una pestaña o el buscador lo estuvieran ocultando
            const allTab = document.querySelector('#pdOrderTabs .pd-tab[data-status-id="*"]');
            if (allTab && row.classList.contains('pd-hidden-by-tab')) allTab.click();
            setTimeout(function() {
                row.scrollIntoView({ block: 'center', behavior: 'smooth' });
                row.classList.add('pd-row-flash');
                setTimeout(function() { row.classList.remove('pd-row-flash'); }, 2600);
                row.click();
            }, 150);
        });
    </script>
@endpush
