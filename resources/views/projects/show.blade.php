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
@endsection
@php
    use Carbon\Carbon;

    $objUser = Auth::user();
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
@endphp

<style type="text/css">
    .lastBreadCrumb {
        /* background-color: #AA182C !important; */
        /* width: 80%; */
        max-width: 700px;
        overflow: hidden;
        text-wrap: nowrap;
        text-overflow: ellipsis;
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
        justify-content: space-evenly;
        align-items: center;
        color: white;
        margin-bottom: 10px;
    }

    .uploaded-files-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        overflow-y: auto;
        overflow-x: hidden;
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

    .plusIcon {
        margin-left: 10px;
    }

    .fatherMilestoneDiv {
        /* max-height: 140px; */
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
@section('multiple-action-button')
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
        justify-content: space-evenly;
        align-items: center;
        color: white;
        margin-bottom: 10px;
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
    #orderFiltersModal .modal-dialog {
        max-width: 980px;
    }

    #orderFiltersModal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.10), 0 2px 6px rgba(0, 0, 0, 0.03);
        overflow: visible;
    }

    #orderFiltersModal .modal-header {
        padding: 24px 28px 16px;
        border-bottom: 1px solid #bfbfbf;
    }

    #orderFiltersModal .modal-body {
        padding: 18px 28px 24px;
        overflow: visible;
    }

    .orderFiltersGrid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0 20px;
    }

    .orderFiltersGrid.orderFiltersGrid--with-stage-phase {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .orderFilterBlock {
        border: 1px solid #c0c0c0;
        background: #fafafa;
        border-radius: 10px;
        margin-bottom: 10px;
        padding: 2px 15px;
    }

    .orderFilterHeader {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        padding: 10px 0;
    }

    .orderFilterLabel {
        font-size: 11px;
        font-weight: 700;
        color: black;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .orderFilterChevron {
        font-size: 1rem;
        color: #111;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .orderFilterBlock.open .orderFilterChevron {
        transform: rotate(180deg);
    }

    .orderFilterContent {
        display: block;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.3s ease, padding 0.3s ease;
        margin: 0;
        padding: 0;
    }

    .orderFilterBlock.open .orderFilterContent {
        max-height: 520px;
        opacity: 1;
        overflow: visible;
        margin: 0 0 8px;
        padding: 0 0 4px;
    }

    .orderAutocompleteWrap {
        position: relative;
    }

    .orderFilterInputWrap {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    #orderFiltersModal .form-control,
    #orderFiltersModal .form-select {
        border: 1px solid #c8c5c5;
        background: #ffffff;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        padding: 10px 14px;
        color: #333;
        transition: background 0.25s ease, box-shadow 0.25s ease;
    }

    #orderFiltersModal .form-control:focus,
    #orderFiltersModal .form-select:focus {
        background: #fff;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
        outline: none;
    }

    #orderFiltersModal .form-control::placeholder {
        color: #bbb;
        font-weight: 400;
    }

    #orderFiltersModal #ofDateField {
        cursor: pointer;
    }

    .orderSuggestions {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        z-index: 2000;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        display: none;
    }

    .orderSuggestions.visible {
        display: block;
    }

    .orderSuggestionItem {
        padding: 10px 14px;
        font-size: 13px;
        cursor: pointer;
        border-bottom: 1px solid #f5f5f5;
    }

    .orderSuggestionItem:last-child {
        border-bottom: none;
    }

    .orderSuggestionItem:hover {
        background: #f8f8f8;
    }

    .orderSelectedList {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .orderSelectedTag {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        color: #666;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .orderSelectedTag button {
        border: none;
        background: transparent;
        color: #888;
        line-height: 1;
        padding: 0;
        font-size: 13px;
    }

    .orderCheckList {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .orderCheckItem {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 500;
        color: #444;
        padding: 8px 10px;
        border-radius: 8px;
    }

    .orderCheckItem:hover {
        background-color: #f5f5f5;
    }

    .orderCheckItem input[type='checkbox'] {
        accent-color: #AA182C;
        cursor: pointer;
    }

    .orderDateRangeRow {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .orderDateLabel {
        font-size: 10px;
        font-weight: 700;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 6px;
    }

    .orderFiltersActions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 10px;
    }

    #ofAddNameBtn,
    #ofAddRequestedBtn,
    #ofAddAssignedBtn,
    #ofAddDateBtn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        background: #AA182C;
        color: #fff;
        transition: background 0.2s ease, transform 0.15s ease;
    }

    #ofAddNameBtn:hover,
    #ofAddRequestedBtn:hover,
    #ofAddAssignedBtn:hover,
    #ofAddDateBtn:hover {
        background: #8f1525;
        transform: scale(1.05);
    }

    #ofAddNameBtn i,
    #ofAddRequestedBtn i,
    #ofAddAssignedBtn i,
    #ofAddDateBtn i {
        font-size: 11px;
    }

    .orderActiveFiltersBar {
        margin-top: 12px;
        padding: 10px;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
    }

    .orderActiveFiltersTitle {
        font-size: 12px;
        font-weight: 700;
        color: #495057;
        margin: 0 0 8px;
    }

    .orderActiveFiltersList {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
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
        padding: 14px 18px;
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

    /* Text ellipsis para columna Name cuando excede 30 caracteres */
    .col-name h5 {
        max-width: 350px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card bg-primary widthAdjustDiv">
                        <div class="card-body pb-2">
                            <div class="projectHeaderMain">
                                <h3 class="text-white projectTitleH3"> {{ $project->name }}</h3>
                                <nav class="projectSubnav projectSubnav--header"
                                    aria-label="{{ __('Project navigation') }}">
                                    <a href="{{ route('projects.milestone.board', [$currentWorkspace->slug, $project->id]) }}"
                                        class="projectSubnavItem">
                                        <i class="fa-solid fa-file-lines"></i>
                                        <span>{{ __('Order forms') }}</span>
                                    </a>
                                    <a href="{{ route('projects.timesheet.index', [$currentWorkspace->slug, $project->id]) }}"
                                        class="projectSubnavItem">
                                        <i class="fas fa-tasks"></i>
                                        <span>{{ __('Timesheet') }}</span>
                                    </a>
                                </nav>
                            </div>
                            <div>
                                <div class="projectDivSubtitle">
                                    <div title="{{ __('Workspace') }}" id="workspaceNameTitle">
                                        <i class="fa-regular fa-building fa-xl me-2"></i>
                                        {{ $currentWorkspace->country }} / {{ $currentWorkspace->name }}
                                    </div>
                                    <div id="membersCountTitle" title="{{ __('Members') }}">
                                        <i class="fas fa-users fa-xl me-2"></i>
                                        {{ (int) $project->technicians->count() + (int) $project->salesManager->count() }}
                                    </div>

                                    <div id="creationDateTitle" title="{{ __('Creation date') }}">
                                        <i class="fas fas fa-calendar-day"></i>
                                        {{ App\Models\Utility::dateFormat($project->start_date) }}
                                    </div>

                                    <div id="projectTypeTitle" title="{{ __('Project type') }}">
                                        <i class="fa-solid fa-diagram-project  text-white"></i>
                                        {{ $project->ref_mo != '' ? $project->ref_mo : __($project->typeName()) }}
                                    </div>

                                    <div>
                                        @if ($project->status == 'Finished')
                                            <div class="badge bg-success p-2 px-3 rounded"> {{ __('Finished') }}
                                            </div>
                                        @elseif($project->status == 'Ongoing')
                                            <div class="badge bg-secondary p-2 px-3 rounded">
                                                {{ __('Ongoing') }}
                                            </div>
                                        @else
                                            <div class="badge bg-warning p-2 px-3 rounded">{{ __('OnHold') }}</div>
                                        @endif
                                    </div>

                                    <div>
                                        {{ __('Hours charged') }}: {{ $totalHours ? $totalHours : '00:00' }}h
                                    </div>

                                    <div>
                                        {{ __('Order forms createds') }}: {{ $totalMilestones ? $totalMilestones : '0' }}
                                    </div>
                                </div>

                                @if (!$project->is_active)
                                    <button class="btn btn-light d"> <a href="#" class=""
                                            title="{{ __('Locked') }}">
                                            <i data-feather="lock"> </i>
                                        </a></button>
                                @else
                                    @auth('web')
                                        @if ($objUser->type == 'admin')
                                            <div class="d-flex align-items-center ">

                                                <a href="#" class=""
                                                    data-url="{{ route('projects.edit', [$currentWorkspace->slug, $project->id]) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Edit Project') }}"
                                                    data-toggle="popover" title="{{ __('Edit') }}">
                                                    <button class="btn btn-light d-flex align-items-between me-3">
                                                        <i class="ti ti-edit"> </i>
                                                    </button>
                                                </a>
                                                <a href="#" class="bs-pass-para" data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $project->id }}" data-toggle="popover"
                                                    title="{{ __('Delete') }}">
                                                    <button class="btn btn-light d">
                                                        <i class="ti ti-trash"> </i>
                                                    </button>
                                                </a>
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
                    </div>

                    <div class="col-lg-12 widthAdjustDiv">
                        @if ($currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner')
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 d-flex align-items-center" style="gap: 8px;">
                                                <span>{{ __('Order forms') }} ({{ count($project->milestones) }})</span>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#orderFiltersModal" title="{{ __('Filter') }}">
                                                    <i class="fa-solid fa-filter"></i>
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="orderFormsHeaderActions">
                                            @if ($project->type == 3 || $project->type == 5)
                                                <a href="#" class="btn btn-primary" data-ajax-popup="true"
                                                    data-size="md" title="{{ __('See stages') }}"
                                                    data-url="{{ route('projects.stages.popup', [$currentWorkspace->slug, $project->id]) }}"
                                                    data-toggle="popover"><i class="fa-solid fa-layer-group me-2"></i>
                                                </a>
                                            @endif
                                            <a href="#" class="btn btn-primary addmMilestoneButton"
                                                title="{{ __('Create Order Form') }}" data-ajax-popup="true"
                                                data-url="{{ route('projects.milestone', [$currentWorkspace->slug, $project->id]) }}"
                                                data-toggle="popover"><i class="fa-solid fa-plus me-2"
                                                    style="color: #ffffff; font-size: 15px;"></i><i
                                                    class="fa-solid fa-file-lines" style="color: #ffffff;"></i></a>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 41vh;">
                                        <table id="orderFormsTable" class="table table-bordered"
                                            style="text-align: center;">
                                            <thead>
                                                {{-- <tr>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Requested by') }}</th>
                                                    <th>{{ __('Assigned to') }}</th>
                                                    <th>{{ __('Status') }}</th> --}}


                                                {{-- <th>{{ __('Created date') }}</th>
                                                    <th>{{ __('Desired delivery date') }}</th>
                                                    <th>{{ __('Expected delivery date') }}</th>
                                                    <th>{{ __('Task started date') }}</th>
                                                    <th>{{ __('Completion date') }}</th> --}}


                                                {{-- <th>{{ __('Created') }}</th>
                                                    <th>{{ __('Desired delivery') }}</th>
                                                    <th>{{ __('Expected delivery') }}</th>
                                                    <th>{{ __('Task started') }}</th>
                                                    <th>{{ __('Completion') }}</th>

                                                    <th>{{ __('Action') }}</th>
                                                </tr> --}}
                                                <tr>
                                                    @if ($project->type == 3 || $project->type == 5)
                                                        <th data-col-key="stage">
                                                            {{ __('Stage') }}</th>
                                                        <th data-col-key="phase">
                                                            {{ __('Phase') }}</th>
                                                    @endif
                                                    <th data-col-key="name">
                                                        {{ __('Name') }}</th>
                                                    <th data-col-key="requested_by">{{ __('Requested by') }}</th>
                                                    <th data-col-key="assigned_to">
                                                        {{ __('Assigned to') }}</th>
                                                    <th data-col-key="status">
                                                        {{ __('Status') }}</th>
                                                    <th data-col-key="created">
                                                        {{ __('Created') }}</th>
                                                    <th data-col-key="desired_delivery">
                                                        {{ __('Desired delivery') }}
                                                    </th>
                                                    <th data-col-key="expected_delivery">
                                                        {{ __('Expected delivery') }}
                                                    </th>
                                                    <th data-col-key="task_started">
                                                        {{ __('Task started') }}</th>
                                                    <th data-col-key="completion">
                                                        {{ __('Completion') }}</th>
                                                    <th data-col-key="action">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="orderFormsTableBody">
                                                @foreach ($project->milestones->sortByDesc('id') as $key => $milestone)
                                                    @php
                                                        $stageFilterValue = '';
                                                        $phaseFilterValue = '';

                                                        if ($project->type == 3 || $project->type == 5) {
                                                            $stageFilterValue = trim(
                                                                (string) ($milestone->resolved_stage_name ?? ''),
                                                            );
                                                            $phaseModel = $milestone->phase;
                                                            if ($phaseModel) {
                                                                $phaseFilterValue = trim(
                                                                    (string) __(
                                                                        \App\Models\MilestonePhases::translationKey(
                                                                            $phaseModel->phases,
                                                                        ),
                                                                    ),
                                                                );
                                                            }
                                                        }

                                                        $statusText =
                                                            $milestone->status == 3
                                                                ? __('For Review')
                                                                : ($milestone->status == 4
                                                                    ? __('Finished')
                                                                    : ($milestone->status == 1
                                                                        ? __('To Do')
                                                                        : __('Ongoing')));
                                                    @endphp
                                                    <tr data-name="{{ mb_strtolower(trim($milestone->title ?? '')) }}"
                                                        data-stage="{{ mb_strtolower(trim($stageFilterValue ?? '')) }}"
                                                        data-phase="{{ mb_strtolower(trim($phaseFilterValue ?? '')) }}"
                                                        data-requested-by="{{ mb_strtolower(trim(optional($milestone->getRequestedBy())->name ?? '')) }}"
                                                        data-assigned-to="{{ mb_strtolower(trim(optional($milestone->getAssignedToUser())->name ?? '')) }}"
                                                        data-status="{{ mb_strtolower(trim($statusText)) }}"
                                                        data-created-date="{{ !empty($milestone->start_date) && $milestone->start_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->start_date)->format('Y-m-d') : '' }}"
                                                        data-desired-date="{{ !empty($milestone->end_date) && $milestone->end_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->end_date)->format('Y-m-d') : '' }}"
                                                        data-expected-date="{{ !empty($milestone->planned_end_date) && $milestone->planned_end_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->planned_end_date)->format('Y-m-d') : '' }}"
                                                        data-task-started-date="{{ !empty($milestone->task_start_date) && $milestone->task_start_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->task_start_date)->format('Y-m-d') : '' }}"
                                                        data-completion-date="{{ !empty($milestone->finalization_date) && $milestone->finalization_date !== '0000-00-00' ? \Carbon\Carbon::parse($milestone->finalization_date)->format('Y-m-d') : '' }}">
                                                        @if ($project->type == 3 || $project->type == 5)
                                                            <td class="col-stage">
                                                                @php
                                                                    $phase = $milestone->phase;
                                                                @endphp
                                                                @if ($phase)
                                                                    <span
                                                                        style="font-weight: bold;">{{ __(\App\Models\MilestonePhases::translationKey($phase->phases)) }}</span>
                                                                @else
                                                                    <span class="text-muted">...</span>
                                                                @endif
                                                            <td class="col-phase">
                                                                @php
                                                                    $stageName = $milestone->resolved_stage_name;
                                                                @endphp
                                                                @if (filled($stageName))
                                                                    <span
                                                                        style="font-weight: bold;">{{ $stageName }}</span>
                                                                @else
                                                                    <span class="text-muted">...</span>
                                                                @endif
                                                            </td>
                                                        @endif
                                                        <td class="col-name"><a href="#"
                                                                class="d-block font-weight-500 mb-0"
                                                                data-ajax-popup="true"
                                                                data-title="{{ __('Order form details') }}"
                                                                data-url="{{ route('projects.milestone.show', [$currentWorkspace->slug, $milestone->id]) }}">
                                                                <h5 class="m-0" title="{{ $milestone->title }}">
                                                                    {{ $milestone->title }} </h5>
                                                            </a>
                                                        </td>
                                                        <td class="reqByImgContainer col-requested_by">
                                                            @if ($milestone->getRequestedBy() != null)
                                                                <img class="fix_img"
                                                                    title="{{ $milestone->getRequestedBy()->name }}"
                                                                    @if ($milestone->getRequestedBy()->avatar) src="{{ asset($milestone->getRequestedBy()->avatar) }}" @else avatar="{{ $milestone->getRequestedBy()->name }}" @endif>
                                                            @endif
                                                        </td>
                                                        <td class="assignedToImgContainer col-assigned_to">
                                                            @if ($milestone->getAssignedToUser() != null)
                                                                <img class="fix_img"
                                                                    title="{{ $milestone->getAssignedToUser()->name }}"
                                                                    @if ($milestone->getAssignedToUser()->avatar) src="{{ asset($milestone->getAssignedToUser()->avatar) }}" @else avatar="{{ $milestone->getAssignedToUser()->name }}" @endif>
                                                            @else
                                                                ...
                                                            @endif
                                                        </td>

                                                        <td class="col-status">
                                                            @if ($milestone->status == 3)
                                                                <label
                                                                    class="badge bg-warning p-2 px-3 rounded">{{ __('For Review') }}</label>
                                                            @elseif ($milestone->status == 4)
                                                                <label
                                                                    class="badge bg-success p-2 px-3 rounded">{{ __('Finished') }}</label>
                                                            @else
                                                                <label
                                                                    class="badge p-2 px-3 rounded {{ $milestone->status == 1 ? 'bg-info' : 'bg-secondary' }}">
                                                                    {{ $milestone->status == 1 ? __('To Do') : __('Ongoing') }}
                                                                </label>
                                                            @endif
                                                        </td>
                                                        <td class="col-created">
                                                            {{ $milestone->start_date ? Carbon::parse($milestone->start_date)->format('d-m-Y') : '...' }}
                                                        </td>
                                                        <td class="col-desired_delivery">
                                                            {{ $milestone->end_date ? Carbon::parse($milestone->end_date)->format('d-m-Y') : '...' }}
                                                        </td>
                                                        {{-- <td>{{ $milestone->planned_end_date ? Carbon::parse($milestone->planned_end_date)->format('d-m-Y') : '...' }}
                                                        </td> --}}
                                                        <td class="col-expected_delivery">
                                                            {{ $milestone->planned_end_date && $milestone->planned_end_date !== '0000-00-00'
                                                                ? \Carbon\Carbon::parse($milestone->planned_end_date)->format('d-m-Y')
                                                                : '...' }}
                                                        </td>
                                                        {{-- <td>{{ $milestone->planned_end_date }}</td> --}}
                                                        {{-- Task started date con lógica de color --}}
                                                        @php
                                                            // Determinar fecha de referencia (prevista o deseada)
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
                                                                // Si la tarea comenzó después de la entrega prevista/deseada
                                                                $startColor = '#db8d33';
                                                            }
                                                        @endphp
                                                        <td class="col-task_started" style="color: {{ $startColor }}">
                                                            {{ $taskStartDate ? $taskStartDate->format('d-m-Y') : '...' }}
                                                        </td>

                                                        {{-- Completion date con lógica de color --}}
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
                                                                    // Completado a tiempo o antes → verde
                                                                    $completionColor = '#53b446';
                                                                } else {
                                                                    // Completado después → rojo
                                                                    $completionColor = '#ff0000';
                                                                }
                                                            }
                                                        @endphp
                                                        <td class="col-completion" style="color: {{ $completionColor }}">
                                                            {{ $completionDate ? $completionDate->format('d-m-Y') : '...' }}
                                                        </td>

                                                        </td>
                                                        <td class="text-right col-action">
                                                            <div class="col-auto">
                                                                <a href="#"
                                                                    class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                    data-ajax-popup="true" data-size="lg"
                                                                    data-toggle="popover" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Milestone') }}"
                                                                    data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                        class="ti ti-edit"></i></a>
                                                                <a href="#"
                                                                    class="action-btn bg-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
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
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="modal fade" id="orderFiltersModal" tabindex="-1"
                                    aria-labelledby="orderFiltersModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="orderFiltersModalLabel">
                                                    {{ __('Filter Order Forms') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div
                                                    class="orderFiltersGrid {{ $project->type == 3 || $project->type == 5 ? 'orderFiltersGrid--with-stage-phase' : '' }}">
                                                    <div>
                                                        <div class="orderFilterBlock open" data-filter-block>
                                                            <div class="orderFilterHeader" data-filter-toggle>
                                                                <p class="orderFilterLabel">{{ __('Name') }}</p>
                                                                <i class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                            </div>
                                                            <div class="orderFilterContent">
                                                                <div class="orderFilterInputWrap">
                                                                    <div class="orderAutocompleteWrap w-100">
                                                                        <input type="text" class="form-control"
                                                                            id="ofNameInput"
                                                                            placeholder="{{ __('Search name') }}"
                                                                            autocomplete="off">
                                                                        <div id="ofNameSuggestions"
                                                                            class="orderSuggestions"></div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-primary"
                                                                        id="ofAddNameBtn"><i
                                                                            class="fa-solid fa-check"></i></button>
                                                                </div>
                                                                <div id="ofNameTags" class="orderSelectedList"></div>
                                                            </div>
                                                        </div>

                                                        <div class="orderFilterBlock" data-filter-block>
                                                            <div class="orderFilterHeader" data-filter-toggle>
                                                                <p class="orderFilterLabel">{{ __('Status') }}</p>
                                                                <i class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                            </div>
                                                            <div class="orderFilterContent">
                                                                <div id="ofStatusList" class="orderCheckList"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="orderFilterBlock open" data-filter-block>
                                                            <div class="orderFilterHeader" data-filter-toggle>
                                                                <p class="orderFilterLabel">{{ __('Requested by') }}</p>
                                                                <i class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                            </div>
                                                            <div class="orderFilterContent">
                                                                <div class="orderFilterInputWrap">
                                                                    <div class="orderAutocompleteWrap w-100">
                                                                        <input type="text" class="form-control"
                                                                            id="ofRequestedInput"
                                                                            placeholder="{{ __('Search requester') }}"
                                                                            autocomplete="off">
                                                                        <div id="ofRequestedSuggestions"
                                                                            class="orderSuggestions"></div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-primary"
                                                                        id="ofAddRequestedBtn"><i
                                                                            class="fa-solid fa-check"></i></button>
                                                                </div>
                                                                <div id="ofRequestedTags" class="orderSelectedList"></div>
                                                            </div>
                                                        </div>

                                                        <div class="orderFilterBlock" data-filter-block>
                                                            <div class="orderFilterHeader" data-filter-toggle>
                                                                <p class="orderFilterLabel">{{ __('Date range') }}</p>
                                                                <i class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                            </div>
                                                            <div class="orderFilterContent">
                                                                <div class="orderFilterInputWrap mb-2">
                                                                    <div class="w-100">
                                                                        <p class="orderDateLabel">{{ __('Date field') }}
                                                                        </p>
                                                                        <select id="ofDateField"
                                                                            class="form-select form-select-sm">
                                                                            <option value="created">{{ __('Created') }}
                                                                            </option>
                                                                            <option value="desired_delivery">
                                                                                {{ __('Desired delivery') }}</option>
                                                                            <option value="expected_delivery">
                                                                                {{ __('Expected delivery') }}</option>
                                                                            <option value="task_started">
                                                                                {{ __('Task started') }}
                                                                            </option>
                                                                            <option value="completion">
                                                                                {{ __('Completion') }}
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <button type="button" class="btn btn-primary mt-4"
                                                                        id="ofAddDateBtn"><i
                                                                            class="fa-solid fa-check"></i></button>
                                                                </div>
                                                                <div class="orderDateRangeRow">
                                                                    <div>
                                                                        <p class="orderDateLabel">{{ __('From') }}</p>
                                                                        <input type="date" id="ofDateFrom"
                                                                            class="form-control form-control-sm">
                                                                    </div>
                                                                    <div>
                                                                        <p class="orderDateLabel">{{ __('To') }}</p>
                                                                        <input type="date" id="ofDateTo"
                                                                            class="form-control form-control-sm">
                                                                    </div>
                                                                </div>
                                                                <div id="ofDateTags" class="orderSelectedList"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="orderFilterBlock open" data-filter-block>
                                                            <div class="orderFilterHeader" data-filter-toggle>
                                                                <p class="orderFilterLabel">{{ __('Assigned to') }}</p>
                                                                <i class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                            </div>
                                                            <div class="orderFilterContent">
                                                                <div class="orderFilterInputWrap">
                                                                    <div class="orderAutocompleteWrap w-100">
                                                                        <input type="text" class="form-control"
                                                                            id="ofAssignedInput"
                                                                            placeholder="{{ __('Search assignee') }}"
                                                                            autocomplete="off">
                                                                        <div id="ofAssignedSuggestions"
                                                                            class="orderSuggestions"></div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-primary"
                                                                        id="ofAddAssignedBtn"><i
                                                                            class="fa-solid fa-check"></i></button>
                                                                </div>
                                                                <div id="ofAssignedTags" class="orderSelectedList"></div>
                                                                <div class="orderCheckList mt-2">
                                                                    <label class="orderCheckItem mb-0">
                                                                        <input type="checkbox"
                                                                            id="ofAssignedNoneCheckbox">
                                                                        <span>{{ __('None') }}</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="orderFilterBlock" data-filter-block>
                                                            <div class="orderFilterHeader" data-filter-toggle>
                                                                <p class="orderFilterLabel">{{ __('Table Visibility') }}
                                                                </p>
                                                                <i class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                            </div>
                                                            <div class="orderFilterContent">
                                                                <div id="ofVisibilityList" class="orderCheckList"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if ($project->type == 3 || $project->type == 5)
                                                        <div>
                                                            <div class="orderFilterBlock open" data-filter-block>
                                                                <div class="orderFilterHeader" data-filter-toggle>
                                                                    <p class="orderFilterLabel">{{ __('Stage') }}</p>
                                                                    <i
                                                                        class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                                </div>
                                                                <div class="orderFilterContent">
                                                                    <div id="ofStageList" class="orderCheckList"></div>
                                                                </div>
                                                            </div>

                                                            <div class="orderFilterBlock" data-filter-block>
                                                                <div class="orderFilterHeader" data-filter-toggle>
                                                                    <p class="orderFilterLabel">{{ __('Phase') }}</p>
                                                                    <i
                                                                        class="fa-solid fa-chevron-down orderFilterChevron"></i>
                                                                </div>
                                                                <div class="orderFilterContent">
                                                                    <div id="ofPhaseList" class="orderCheckList"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div id="ofActiveFiltersBar" class="orderActiveFiltersBar d-none">
                                                    <p class="orderActiveFiltersTitle">{{ __('Active filters') }}</p>
                                                    <div id="ofActiveFiltersList" class="orderActiveFiltersList"></div>
                                                </div>

                                                <div class="orderFiltersActions">
                                                    <button type="button" class="btn btn-primary"
                                                        id="ofResetBtn">{{ __('Reset filters') }}</button>
                                                    <button type="button" class="btn btn-primary"
                                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>
                    {{-- ======================================================================================== --}}
                    {{-- Fila 1: Usuarios --}}
                    <div class="row">
                        {{-- Usuarios que han creado un encargo --}}
                        <div class="col-md-6">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            {{-- Usuarios que han creado milestones --}}
                                            <h5 class="mb-0">{{ __('Users who created Order forms') }}
                                                ({{ count($milestoneCreators) }})
                                            </h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-sm-flex align-items-center mb-0">
                                                @if (\Auth::user()->type == 'admin')
                                                    <a href="#" class="btn btn-sm btn-primary "
                                                        data-ajax-popup="true" data-title="{{ __('Invite') }}"
                                                        data-toggle="popover" title="{{ __('Invite') }}"
                                                        data-url="{{ route('projects.invite.popup', [$currentWorkspace->slug, $project->id]) }}">
                                                        <i class="ti ti-brand-telegram"></i>
                                                    </a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body pb-1">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($milestoneCreators as $user)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        @if ($user->avatar) src="{{ asset($user->avatar) }}" 
                                                @else avatar="{{ $user->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $user->name }}</h5>
                                                                    <small class="text-muted">
                                                                        {{ $user->email }}
                                                                        <span class="text-primary">
                                                                            - {{ $user->milestones_count }}
                                                                            {{ __('Order forms') }}
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                @if (\Auth::user()->type == 'admin')
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Permission') }}"
                                                                        data-title="{{ __('Edit Permission') }}"
                                                                        data-url="{{ route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id]) }}">
                                                                        <i class="ti ti-lock"></i>
                                                                    </a>
                                                                    <a href="#"
                                                                        class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover" title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-user-{{ $user->id }}">
                                                                        <i class="ti ti-trash ml-1"></i>
                                                                    </a>
                                                                    <form id="delete-user-{{ $user->id }}"
                                                                        action="{{ route('projects.user.delete', [$currentWorkspace->slug, $project->id, $user->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Usuarios que han imputado horas --}}
                        <div class="col-md-6 widthAdjustMediumDiv">
                            <div class="card min-h">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Users with imputed hours') }}
                                                ({{ count($usersWithHours) }})
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pb-1">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($usersWithHours as $user)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class="text-start">
                                                                    <img class="fix_img"
                                                                        @if ($user->avatar) src="{{ asset($user->avatar) }}"
                                                @else avatar="{{ $user->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $user->name }}</h5>
                                                                    <small class="text-muted">
                                                                        {{ $user->email }}
                                                                        <span class="text-primary">
                                                                            -
                                                                            {{ $user->total_time ? substr($user->total_time, 0, 5) : '00:00' }}h
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Si quieres añadir acciones de admin como antes --}}
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                @if (\Auth::user()->type == 'admin')
                                                                    {{-- Aquí podrías añadir botones de acciones si quieres --}}
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xxl-12">
                    {{-- Fila 2: Averages y Activity --}}
                    <div class="row">
                        <div class="col-md-6 d-flex flex-column">
                            <div class="row flex-grow-1">
                                <div class="col-md-6 mb-4 d-flex flex-column">
                                    <div class="card min-h flex-grow-1 mb-0">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0">
                                                        {{ __('Average delivery time') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-body alignMiddle d-flex align-items-center justify-content-center w-100 flex-grow-1">
                                            <span class="size40AndBold">
                                                {{ $averageDelivery }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 d-flex flex-column">
                                    <div class="card min-h flex-grow-1 mb-0">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0">
                                                        {{ __('Average working time') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-body alignMiddle d-flex align-items-center justify-content-center w-100 flex-grow-1">
                                            <span class="size40AndBold">
                                                {{ $averageWorkingTime }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 d-flex flex-column">
                                    <div class="card min-h flex-grow-1 mb-0">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0">
                                                        {{ __('Average start-up time') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-body alignMiddle d-flex align-items-center justify-content-center w-100 flex-grow-1">
                                            <span class="size40AndBold">
                                                {{ $averageStartUpTime }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 d-flex flex-column">
                                    <div class="card min-h flex-grow-1 mb-0">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0">
                                                        {{ __('Average delay time') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-body alignMiddle d-flex align-items-center justify-content-center w-100 flex-grow-1">
                                            <span class="size40AndBold">
                                                {{ $averageDelayTime }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 d-flex flex-column">
                            <div class="card flex-grow-1 mb-0">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Activity') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 activityContainer">
                                    <div class="timeline timeline-one-side" data-timeline-content="axis"
                                        data-timeline-axis-style="dashed">
                                        @if ($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member')
                                            @foreach ($project->activities as $activity)
                                                {{-- @dump($activity) --}}
                                                <div class="timeline-block px-2 pt-3"
                                                    style="display: flex; align-items: center;">
                                                    @if ($activity->log_type == 'Upload File')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-file"></i></span>
                                                    @elseif($activity->log_type == 'has uploaded a review file')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-warning text-white">
                                                            <i class="fas fa-file"></i></span>
                                                    @elseif($activity->log_type == 'Create Milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'Create Timesheet')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-clock-o"></i></span>
                                                    @elseif($activity->log_type == 'has delete a file')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-primary text-white"
                                                            style="border-color: #aa182c !important;">
                                                            <i class="fas fa-file"></i></span>
                                                    @elseif($activity->log_type == 'has created a new project')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fa-solid fa-diagram-project"></i></span>
                                                    @elseif($activity->log_type == 'has updated a milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'has updated the milestone status to')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @elseif($activity->log_type == 'has deleted a milestone')
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-primary text-white"
                                                            style="border-color: #aa182c !important;">
                                                            <i class="fas fa-cubes"></i></span>
                                                    @endif
                                                    <div class="last_notification_text">
                                                        <!-- Person who did the notification -->
                                                        <p> {!! $activity->getRemark() !!} : </p>
                                                        <br>
                                                        <div class="notification_time_main">
                                                            <p style="text-align:center; text-wrap:nowrap;"
                                                                title="{{ $activity->created_at }}">
                                                                {{ $activity->created_at->diffForHumans() }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fila 3: Files --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card files-section">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 d-flex align-items-center files-title" style="gap: 8px;">
                                                <span>{{ __('Files') }}</span>
                                                <button type="button" class="btn btn-sm btn-primary files-upload-toggle"
                                                    id="toggleUploadSectionBtn" title="{{ __('Upload Files') }}"
                                                    aria-expanded="false" aria-controls="files-upload-row">
                                                    <i class="fa-solid fa-file-arrow-up"></i>
                                                </button>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3 col-md-12 files-body" style="min-height: 374px;">
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
                                            <div class="custom-file-container files-grid folder-toggle-target"
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
                                                                <a onclick="downloadFile({{ $project->id }}, '', '{{ $file->file_path }}')"
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
                                            <div class="folder-toggle-target" id="milestone-files-content">
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
                                                                                    <a onclick="downloadFile({{ $project->id }}, '{{ $milestone['title'] }}', '{{ basename($file->file) }}')"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
    @include('projects.file_preview')
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
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
            const table = document.getElementById('orderFormsTable');
            const tbody = document.getElementById('orderFormsTableBody');
            if (!table || !tbody) {
                return;
            }

            const rows = Array.from(tbody.querySelectorAll('tr'));
            const filterState = {
                names: [],
                stages: [],
                phases: [],
                requestedBy: [],
                assignedTo: [],
                assignedToNone: false,
                statuses: [],
                dateRanges: []
            };

            let dateRangeIdCounter = 0;

            const visibilityState = {};
            const allHeaders = Array.from(table.querySelectorAll('thead th[data-col-key]'));
            allHeaders.forEach(header => {
                visibilityState[header.dataset.colKey] = true;
            });

            function normalize(text) {
                return (text || '').toString().trim().toLowerCase();
            }

            function uniqueDatasetValues(key) {
                const values = new Set();
                rows.forEach(row => {
                    const rawValue = normalize(row.dataset[key]);
                    if (rawValue) {
                        values.add(rawValue);
                    }
                });
                return Array.from(values).sort((a, b) => a.localeCompare(b, 'es', {
                    sensitivity: 'base'
                }));
            }

            function datasetValueCounts(key, visibleOnly = false) {
                const counts = new Map();
                const rowsToCount = visibleOnly ? rows.filter(row => row.style.display !== 'none') : rows;

                rowsToCount.forEach(row => {
                    const rawValue = normalize(row.dataset[key]);
                    if (!rawValue) {
                        return;
                    }
                    counts.set(rawValue, (counts.get(rawValue) || 0) + 1);
                });

                return Array.from(counts.entries())
                    .map(([value, count]) => ({
                        value,
                        count
                    }))
                    .sort((a, b) => a.value.localeCompare(b.value, 'es', {
                        sensitivity: 'base'
                    }));
            }

            function renderSuggestions(inputEl, containerEl, getValuesWithCount, onSelect) {
                const query = normalize(inputEl.value);
                const valuesWithCount = typeof getValuesWithCount === 'function' ? getValuesWithCount() :
                    getValuesWithCount;
                const filtered = valuesWithCount.filter(item => item.value.includes(query));
                containerEl.innerHTML = '';

                if (!filtered.length) {
                    containerEl.classList.remove('visible');
                    return;
                }

                filtered.forEach(itemData => {
                    const item = document.createElement('div');
                    item.className = 'orderSuggestionItem';
                    item.textContent = `${itemData.value} (${itemData.count})`;
                    item.addEventListener('click', function() {
                        onSelect(itemData.value);
                        containerEl.classList.remove('visible');
                    });
                    containerEl.appendChild(item);
                });

                containerEl.classList.add('visible');
            }

            function renderTags(containerId, values, onRemove) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';

                values.forEach(value => {
                    const tag = document.createElement('span');
                    tag.className = 'orderSelectedTag';
                    tag.innerHTML = `<span>${value}</span><button type="button">×</button>`;
                    tag.querySelector('button').addEventListener('click', function() {
                        onRemove(value);
                    });
                    container.appendChild(tag);
                });
            }

            function refreshStateTags(containerId, stateKey) {
                renderTags(containerId, filterState[stateKey], (toRemove) => {
                    filterState[stateKey] = filterState[stateKey].filter(item => item !== toRemove);
                    refreshStateTags(containerId, stateKey);
                    renderActiveFilterChips();
                    applyFilters();
                });
            }

            function parseDateSafe(value) {
                if (!value) {
                    return null;
                }
                const parsed = new Date(`${value}T00:00:00`);
                return Number.isNaN(parsed.getTime()) ? null : parsed;
            }

            function getRowDateByField(row, field) {
                const fieldMap = {
                    created: 'createdDate',
                    desired_delivery: 'desiredDate',
                    expected_delivery: 'expectedDate',
                    task_started: 'taskStartedDate',
                    completion: 'completionDate'
                };

                return parseDateSafe(row.dataset[fieldMap[field]] || '');
            }

            function rowMatchesDate(row) {
                if (!filterState.dateRanges.length) {
                    return true;
                }

                return filterState.dateRanges.every((dateRange) => {
                    const rowDate = getRowDateByField(row, dateRange.field);
                    const fromDate = parseDateSafe(dateRange.from || '');
                    const toDate = parseDateSafe(dateRange.to || '');

                    if (!fromDate && !toDate) {
                        return true;
                    }

                    if (!rowDate) {
                        return false;
                    }

                    if (fromDate && rowDate < fromDate) {
                        return false;
                    }

                    if (toDate && rowDate > toDate) {
                        return false;
                    }

                    return true;
                });
            }

            function getDateFieldLabel(field) {
                const labels = {
                    created: "{{ __('Created') }}",
                    desired_delivery: "{{ __('Desired delivery') }}",
                    expected_delivery: "{{ __('Expected delivery') }}",
                    task_started: "{{ __('Task started') }}",
                    completion: "{{ __('Completion') }}"
                };

                return labels[field] || field;
            }

            function formatDateForChip(value) {
                if (!value) {
                    return '...';
                }

                const parts = value.split('-');
                if (parts.length !== 3) {
                    return value;
                }

                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            }

            function renderDateRangeTags() {
                renderTags('ofDateTags', filterState.dateRanges.map((dateRange) => {
                    const fromLabel = formatDateForChip(dateRange.from);
                    const toLabel = formatDateForChip(dateRange.to);
                    return `${getDateFieldLabel(dateRange.field)}: ${fromLabel} → ${toLabel}`;
                }), (labelText) => {
                    const found = filterState.dateRanges.find((dateRange) => {
                        const fromLabel = formatDateForChip(dateRange.from);
                        const toLabel = formatDateForChip(dateRange.to);
                        return `${getDateFieldLabel(dateRange.field)}: ${fromLabel} → ${toLabel}` ===
                            labelText;
                    });

                    if (found) {
                        filterState.dateRanges = filterState.dateRanges.filter(item => item.id !== found
                            .id);
                        renderDateRangeTags();
                        renderActiveFilterChips();
                        applyFilters();
                    }
                });
            }

            function getVisibilityLabelByKey(key) {
                const checkbox = document.querySelector(`.of-visibility-checkbox[data-key="${key}"]`);
                if (!checkbox) {
                    return key;
                }

                const label = checkbox.closest('label');
                const textNode = label ? label.querySelector('span') : null;
                return (textNode?.textContent || key).trim();
            }

            function recalculateAndUpdateCounts() {
                // Actualizar Status checkboxes
                const statusList = document.getElementById('ofStatusList');
                if (statusList) {
                    const statusCounts = datasetValueCounts('status', true);
                    const statusCountMap = new Map(statusCounts.map(item => [item.value, item.count]));

                    statusList.querySelectorAll('.of-status-checkbox').forEach(checkbox => {
                        const countSpan = checkbox.closest('label').querySelector('span');
                        const value = normalize(checkbox.value);
                        const count = statusCountMap.get(value) || 0;
                        const labelText = checkbox.value;
                        countSpan.textContent = `${labelText} (${count})`;
                    });
                }

                // Actualizar Stage checkboxes
                const stageList = document.getElementById('ofStageList');
                if (stageList) {
                    const stageCounts = datasetValueCounts('stage', true);
                    const stageCountMap = new Map(stageCounts.map(item => [item.value, item.count]));

                    stageList.querySelectorAll('.of-stage-checkbox').forEach(checkbox => {
                        const countSpan = checkbox.closest('label').querySelector('span');
                        const value = normalize(checkbox.value);
                        const count = stageCountMap.get(value) || 0;
                        const labelText = checkbox.value;
                        countSpan.textContent = `${labelText} (${count})`;
                    });
                }

                // Actualizar Phase checkboxes
                const phaseList = document.getElementById('ofPhaseList');
                if (phaseList) {
                    const phaseCounts = datasetValueCounts('phase', true);
                    const phaseCountMap = new Map(phaseCounts.map(item => [item.value, item.count]));

                    phaseList.querySelectorAll('.of-phase-checkbox').forEach(checkbox => {
                        const countSpan = checkbox.closest('label').querySelector('span');
                        const value = normalize(checkbox.value);
                        const count = phaseCountMap.get(value) || 0;
                        const labelText = checkbox.value;
                        countSpan.textContent = `${labelText} (${count})`;
                    });
                }
            }

            function renderActiveFilterChips() {
                const bar = document.getElementById('ofActiveFiltersBar');
                const list = document.getElementById('ofActiveFiltersList');
                if (!bar || !list) {
                    return;
                }

                const chips = [];

                filterState.names.forEach(value => {
                    chips.push({
                        label: `{{ __('Name') }}: ${value}`,
                        remove: () => {
                            filterState.names = filterState.names.filter(v => v !== value);
                            refreshStateTags('ofNameTags', 'names');
                        }
                    });
                });

                filterState.stages.forEach(value => {
                    chips.push({
                        label: `{{ __('Stage') }}: ${value}`,
                        remove: () => {
                            filterState.stages = filterState.stages.filter(v => v !== value);
                            document.querySelectorAll('.of-stage-checkbox').forEach(cb => {
                                if (normalize(cb.value) === value) {
                                    cb.checked = false;
                                }
                            });
                        }
                    });
                });

                filterState.phases.forEach(value => {
                    chips.push({
                        label: `{{ __('Phase') }}: ${value}`,
                        remove: () => {
                            filterState.phases = filterState.phases.filter(v => v !== value);
                            document.querySelectorAll('.of-phase-checkbox').forEach(cb => {
                                if (normalize(cb.value) === value) {
                                    cb.checked = false;
                                }
                            });
                        }
                    });
                });

                filterState.requestedBy.forEach(value => {
                    chips.push({
                        label: `{{ __('Requested by') }}: ${value}`,
                        remove: () => {
                            filterState.requestedBy = filterState.requestedBy.filter(v => v !==
                                value);
                            refreshStateTags('ofRequestedTags', 'requestedBy');
                        }
                    });
                });

                filterState.assignedTo.forEach(value => {
                    chips.push({
                        label: `{{ __('Assigned to') }}: ${value}`,
                        remove: () => {
                            filterState.assignedTo = filterState.assignedTo.filter(v => v !==
                                value);
                            refreshStateTags('ofAssignedTags', 'assignedTo');
                        }
                    });
                });

                if (filterState.assignedToNone) {
                    chips.push({
                        label: `{{ __('Assigned to') }}: {{ __('Nobody') }}`,
                        remove: () => {
                            filterState.assignedToNone = false;
                            const assignedNoneCheckbox = document.getElementById(
                                'ofAssignedNoneCheckbox');
                            if (assignedNoneCheckbox) {
                                assignedNoneCheckbox.checked = false;
                            }
                        }
                    });
                }

                filterState.statuses.forEach(value => {
                    chips.push({
                        label: `{{ __('Status') }}: ${value}`,
                        remove: () => {
                            filterState.statuses = filterState.statuses.filter(v => v !==
                                value);
                            document.querySelectorAll('.of-status-checkbox').forEach(cb => {
                                if (normalize(cb.value) === value) {
                                    cb.checked = false;
                                }
                            });
                        }
                    });
                });

                filterState.dateRanges.forEach(dateRange => {
                    const fromLabel = formatDateForChip(dateRange.from);
                    const toLabel = formatDateForChip(dateRange.to);
                    chips.push({
                        label: `${getDateFieldLabel(dateRange.field)}: ${fromLabel} → ${toLabel}`,
                        remove: () => {
                            filterState.dateRanges = filterState.dateRanges.filter(item => item
                                .id !== dateRange.id);
                            renderDateRangeTags();
                        }
                    });
                });

                Object.entries(visibilityState).forEach(([key, isVisible]) => {
                    if (isVisible !== false) {
                        return;
                    }

                    chips.push({
                        label: `{{ __('Hide') }} ${getVisibilityLabelByKey(key)}`,
                        remove: () => {
                            visibilityState[key] = true;
                            const checkbox = document.querySelector(
                                `.of-visibility-checkbox[data-key="${key}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                            applyVisibility();
                        }
                    });
                });

                list.innerHTML = '';

                if (!chips.length) {
                    bar.classList.add('d-none');
                    return;
                }

                chips.forEach(chip => {
                    const chipNode = document.createElement('span');
                    chipNode.className = 'orderSelectedTag';
                    chipNode.innerHTML = `<span>${chip.label}</span><button type="button">×</button>`;
                    chipNode.querySelector('button').addEventListener('click', function() {
                        chip.remove();
                        renderActiveFilterChips();
                        applyFilters();
                    });
                    list.appendChild(chipNode);
                });

                bar.classList.remove('d-none');
            }

            function applyFilters() {
                rows.forEach(row => {
                    const name = normalize(row.dataset.name);
                    const stage = normalize(row.dataset.stage);
                    const phase = normalize(row.dataset.phase);
                    const requestedBy = normalize(row.dataset.requestedBy);
                    const assignedTo = normalize(row.dataset.assignedTo);
                    const status = normalize(row.dataset.status);

                    const matchName = !filterState.names.length || filterState.names.some(value => name
                        .includes(normalize(value)));
                    const matchStage = !filterState.stages.length || filterState.stages.includes(stage);
                    const matchPhase = !filterState.phases.length || filterState.phases.includes(phase);
                    const matchRequested = !filterState.requestedBy.length || filterState.requestedBy
                        .includes(requestedBy);
                    const matchAssignedValues = filterState.assignedTo.includes(assignedTo);
                    const matchAssignedNone = filterState.assignedToNone && !assignedTo;
                    const matchAssigned = (!filterState.assignedTo.length && !filterState
                            .assignedToNone) ||
                        matchAssignedValues || matchAssignedNone;
                    const matchStatus = !filterState.statuses.length || filterState.statuses.includes(
                        status);
                    const matchDate = rowMatchesDate(row);

                    row.style.display = matchName && matchStage && matchPhase && matchRequested &&
                        matchAssigned &&
                        matchStatus &&
                        matchDate ? '' : 'none';
                });

                recalculateAndUpdateCounts();
            }

            function applyVisibility() {
                allHeaders.forEach((header, index) => {
                    const key = header.dataset.colKey;
                    const visible = visibilityState[key] !== false;
                    header.style.display = visible ? '' : 'none';
                    rows.forEach(row => {
                        const cell = row.children[index];
                        if (cell) {
                            cell.style.display = visible ? '' : 'none';
                        }
                    });
                });
            }

            function setupAutocomplete({
                inputId,
                suggestionsId,
                addBtnId,
                getSourceValues,
                stateKey,
                tagsId
            }) {
                const input = document.getElementById(inputId);
                const suggestions = document.getElementById(suggestionsId);
                const addBtn = document.getElementById(addBtnId);

                function addValue(value) {
                    const normalized = normalize(value);
                    if (!normalized) {
                        return;
                    }
                    if (!filterState[stateKey].includes(normalized)) {
                        filterState[stateKey].push(normalized);
                    }
                    input.value = '';
                    refreshStateTags(tagsId, stateKey);
                    renderActiveFilterChips();
                    applyFilters();
                }

                input.addEventListener('input', function() {
                    renderSuggestions(input, suggestions, getSourceValues, addValue);
                });

                input.addEventListener('focus', function() {
                    renderSuggestions(input, suggestions, getSourceValues, addValue);
                });

                input.addEventListener('click', function() {
                    renderSuggestions(input, suggestions, getSourceValues, addValue);
                });

                addBtn.addEventListener('click', function() {
                    addValue(input.value);
                    suggestions.classList.remove('visible');
                });

                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        addValue(input.value);
                        suggestions.classList.remove('visible');
                    }
                });

                document.addEventListener('click', function(event) {
                    if (!suggestions.contains(event.target) && event.target !== input) {
                        suggestions.classList.remove('visible');
                    }
                });
            }

            const statusList = document.getElementById('ofStatusList');
            datasetValueCounts('status').forEach((statusItem) => {
                const label = document.createElement('label');
                label.className = 'orderCheckItem';
                label.innerHTML =
                    `<input type="checkbox" class="of-status-checkbox" value="${statusItem.value}"><span>${statusItem.value} (${statusItem.count})</span>`;
                statusList.appendChild(label);
            });

            statusList.addEventListener('change', function() {
                filterState.statuses = Array.from(statusList.querySelectorAll(
                    '.of-status-checkbox:checked')).map(cb => normalize(cb.value));
                renderActiveFilterChips();
                applyFilters();
            });

            const stageList = document.getElementById('ofStageList');
            if (stageList) {
                datasetValueCounts('stage').forEach((stageItem) => {
                    const label = document.createElement('label');
                    label.className = 'orderCheckItem';
                    label.innerHTML =
                        `<input type="checkbox" class="of-stage-checkbox" value="${stageItem.value}"><span>${stageItem.value} (${stageItem.count})</span>`;
                    stageList.appendChild(label);
                });

                stageList.addEventListener('change', function() {
                    filterState.stages = Array.from(stageList.querySelectorAll(
                        '.of-stage-checkbox:checked')).map(cb => normalize(cb.value));
                    renderActiveFilterChips();
                    applyFilters();
                });
            }

            const phaseList = document.getElementById('ofPhaseList');
            if (phaseList) {
                datasetValueCounts('phase').forEach((phaseItem) => {
                    const label = document.createElement('label');
                    label.className = 'orderCheckItem';
                    label.innerHTML =
                        `<input type="checkbox" class="of-phase-checkbox" value="${phaseItem.value}"><span>${phaseItem.value} (${phaseItem.count})</span>`;
                    phaseList.appendChild(label);
                });

                phaseList.addEventListener('change', function() {
                    filterState.phases = Array.from(phaseList.querySelectorAll(
                        '.of-phase-checkbox:checked')).map(cb => normalize(cb.value));
                    renderActiveFilterChips();
                    applyFilters();
                });
            }

            const assignedNoneCheckbox = document.getElementById('ofAssignedNoneCheckbox');
            if (assignedNoneCheckbox) {
                assignedNoneCheckbox.addEventListener('change', function() {
                    filterState.assignedToNone = assignedNoneCheckbox.checked;
                    renderActiveFilterChips();
                    applyFilters();
                });
            }

            const visibilityList = document.getElementById('ofVisibilityList');
            allHeaders.forEach(header => {
                const key = header.dataset.colKey;
                const labelText = (header.textContent || '').replace('⮝', '').replace('⮟', '').trim();
                const label = document.createElement('label');
                label.className = 'orderCheckItem';
                label.innerHTML =
                    `<input type="checkbox" class="of-visibility-checkbox" data-key="${key}" checked><span>${labelText}</span>`;
                visibilityList.appendChild(label);
            });

            visibilityList.addEventListener('change', function() {
                const checks = visibilityList.querySelectorAll('.of-visibility-checkbox');
                checks.forEach(check => {
                    visibilityState[check.dataset.key] = check.checked;
                });
                applyVisibility();
                renderActiveFilterChips();
            });

            setupAutocomplete({
                inputId: 'ofNameInput',
                suggestionsId: 'ofNameSuggestions',
                addBtnId: 'ofAddNameBtn',
                getSourceValues: () => datasetValueCounts('name', true),
                stateKey: 'names',
                tagsId: 'ofNameTags'
            });

            setupAutocomplete({
                inputId: 'ofRequestedInput',
                suggestionsId: 'ofRequestedSuggestions',
                addBtnId: 'ofAddRequestedBtn',
                getSourceValues: () => datasetValueCounts('requestedBy', true),
                stateKey: 'requestedBy',
                tagsId: 'ofRequestedTags'
            });

            setupAutocomplete({
                inputId: 'ofAssignedInput',
                suggestionsId: 'ofAssignedSuggestions',
                addBtnId: 'ofAddAssignedBtn',
                getSourceValues: () => datasetValueCounts('assignedTo', true),
                stateKey: 'assignedTo',
                tagsId: 'ofAssignedTags'
            });

            document.getElementById('ofAddDateBtn').addEventListener('click', function() {
                const field = document.getElementById('ofDateField').value;
                const from = document.getElementById('ofDateFrom').value;
                const to = document.getElementById('ofDateTo').value;

                if (!from && !to) {
                    return;
                }

                const exists = filterState.dateRanges.some(item => item.field === field && item.from ===
                    from && item.to === to);
                if (exists) {
                    return;
                }

                filterState.dateRanges.push({
                    id: ++dateRangeIdCounter,
                    field,
                    from,
                    to
                });

                document.getElementById('ofDateFrom').value = '';
                document.getElementById('ofDateTo').value = '';

                renderDateRangeTags();
                renderActiveFilterChips();
                applyFilters();
            });

            document.getElementById('ofResetBtn').addEventListener('click', function() {
                filterState.names = [];
                filterState.stages = [];
                filterState.phases = [];
                filterState.requestedBy = [];
                filterState.assignedTo = [];
                filterState.assignedToNone = false;
                filterState.statuses = [];
                filterState.dateRanges = [];

                document.getElementById('ofNameInput').value = '';
                document.getElementById('ofRequestedInput').value = '';
                document.getElementById('ofAssignedInput').value = '';
                document.getElementById('ofDateField').value = 'created';
                document.getElementById('ofDateFrom').value = '';
                document.getElementById('ofDateTo').value = '';

                if (assignedNoneCheckbox) {
                    assignedNoneCheckbox.checked = false;
                }

                document.querySelectorAll('.of-status-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.of-stage-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.of-phase-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.of-visibility-checkbox').forEach(cb => {
                    cb.checked = true;
                    visibilityState[cb.dataset.key] = true;
                });

                renderTags('ofNameTags', [], () => {});
                renderTags('ofRequestedTags', [], () => {});
                renderTags('ofAssignedTags', [], () => {});
                renderTags('ofDateTags', [], () => {});

                renderActiveFilterChips();
                applyVisibility();
                applyFilters();
                recalculateAndUpdateCounts();
            });

            document.querySelectorAll('#orderFiltersModal [data-filter-toggle]').forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const block = this.closest('[data-filter-block]');
                    block.classList.toggle('open');
                });
            });

            applyVisibility();
            renderActiveFilterChips();
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
@endpush
