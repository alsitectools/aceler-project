@extends('layouts.admin')
@section('page-title')
    {{ __('My Projects') }}
@endsection
@section('links')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item"> {{ __('My Projects') }}</li>
@endsection
@php

    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/index_projects.css') }}">
</head>
<style>
    .filterTypo {
        text-wrap: nowrap;
        text-align: center;

    }

    .filterTypo:hover {
        background-color: rgb(202, 202, 202) !important;
        border-radius: 5px !important;
    }

    .participantsSection {
        width: 20% !important;
    }

    .filterSection {
        display: flex;
        background-color: red;
        width: 96% !important;
        padding: 20px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        background-color: #ffffff;
        border-radius: 10px;
    }


    .divStatus {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    #eac-container-searchInput {
        display: none;
        position: absolute;
        z-index: 9999;
        top: 100%;
        left: 0;
        width: 100%;
    }


    .hover:hover {
        cursor: pointer;
    }

    /* no project icon section*/
    #gears {
        height: 370px;
        width: 842px;
        z-index: 3;



    }


    /* colors */
    .gear-outer.large {
        fill: #d5d5d5;
    }

    .gear-outer.small {
        fill: #d5d5d5;
    }

    .gear-inner {
        fill: #f7f9f9;
    }


    /* ------------------------------
    animation keyframes
------------------------------ */
    /* large gear rotation */
    @keyframes spin-clockwise {
        0% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(2600deg);
        }
    }


    /* small gears rotation */
    @keyframes spin-counter-clockwise {
        0% {
            transform: rotate(0);
        }

        100% {
            transform: rotate(-2600deg);
        }
    }


    /* ------------------------------
    gear animation
------------------------------ */
    .gear-outer {
        transform-box: fill-box;
        transform-origin: center center;
        animation: spin-clockwise 50s infinite linear;
    }

    .gear-outer.small {
        animation: spin-counter-clockwise 50s infinite linear;
    }

    .noProjectText {
        color: #d5d5d5;
        -webkit-text-stroke: 0.5px #c7c7c7;
    }

    /* en no project icon section */


    .createBut {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 25%;
    }

    .custom {
        /* box-shadow: none !important; */
        background-color: #ffffff !important;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        width: 95%;
        /* background-color: #f7f9f9 !important; */
    }

    .formSearch {
        width: 100%;
    }



    @media screen and (max-width:1441px) and (min-width:900px) {

        /* * {
            border: 1px dashed green !important;
        } */

        .participantsSection {
            width: 30% !important;
        }

        .filterTypo {
            font-size: 11px !important;
            text-wrap: nowrap !important;
        }

        .projectsContainer {
            width: 58.33333% !important;
        }

        .rightColumn {
            width: 41.66667% !important;
        }

        .createBut {
            height: 90%;
        }

        .innerButtonText {
            font-size: 13px !important;
        }

        .responsiveButton {
            font-size: 12px !important;
        }
    }

    @media (min-width: 768px) {

        .createBut {
            width: 30% !important;
        }

    }
</style>

<style>
    .active-filters,
    .status-filter,
    .workspace-filter {
        display: flex;
        padding: 2%;
        background-color: #f6f6f6;
        width: 100%;
    }

    .type-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 2%;
        background-color: #f6f6f6;
        width: 100%;
    }

    .type-filter > div {
        flex: 1;
    }

    .workspace-filter {
        flex-wrap: wrap;
        gap: 8px;
    }

    .workspace-filter button {
        flex: 0 0 auto;
        max-width: 48%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        position: relative;
        z-index: 1;
    }

    .workspace-filter button.truncated:hover {
        max-width: none;
        overflow: visible;
        white-space: nowrap;
        z-index: 10;
    }

    .status-indicator {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        display: inline-block;
    }

    .btn.active {
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
        background-color: white !important;
        color: #454545 !important;
        transform: scale(1.05);
    }

    .disp-btn {
        display: flex;
        align-items: center;
        width: 100%;
        text-align: left !important;
    }

    .btn-active-filters {
        width: 45%;
        background-color: #eeeeee !important;
        border: none;
        border-radius: 4%;
        padding: 2%;
        margin: 0;
        text-decoration: none;
        padding-left: 5%;
    }

    .legendIcon {
        width: 20px;
        margin-left: 8px;
        cursor: pointer;
        transition: transform 0.1s ease-in-out;
    }

    #eac-container-searchInput {
        max-height: 100px !important;
        overflow-y: auto !important;
        background-color: white !important;
        border: 1px solid #cdcdcd;
        border-radius: 10px;
        padding: 0px 10px !important;
    }

    .easy-autocomplete-container ul {
        background-color: rgba(255, 255, 255, 0) !important;
        border: none !important;
        border-radius: none !important;
        padding: 0px !important;
    }

    .delegationTag {
        background-color: #be0202 !important;
    }

    .delegationName {
        background-color: #392c34 !important;
    }

    #btnSortProjectsAlpha {
        min-width: 52px;
        height: 35px;
        padding: 0.25rem 0.45rem;
        background-color: #aa182c;
        color: #ffffff;
        justify-content: center;
        white-space: nowrap;
        line-height: 1;
        gap: 0.5rem !important;
    }

    #btnSortProjectsAlpha .sort-direction-arrow {
        width: 6px;
        height: 6px;
        border-right: 1.6px solid currentColor;
        border-bottom: 1.6px solid currentColor;
        transform: rotate(45deg);
        transition: transform 0.2s ease-in-out;
        margin-top: -1px;
    }

    #btnSortProjectsAlpha.active {
        background-color: #aa182c !important;
        border-color: #aa182c !important;
        color: #FFFFFF !important;
    }

    #btnSortProjectsAlpha.active .sort-direction-arrow {
        transform: rotate(-135deg);
    }

    #btnSortProjectsAlpha.active.sort-desc .sort-direction-arrow {
        transform: rotate(45deg);
    }
</style>

@section('multiple-action-button')
    <div class="d-flex justify-content-start row1">
        <div id="searchInputProjects" data-popper-placement="bottom-start">
            <form class="form-inline mr-auto mb-0 formSearch">
                <div class="search-element" style="width: 77%; !important">
                    <input type="text" class="input" id="searchInput"
                        placeholder="{{ __('Enter name or reference M.O') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="search-backdrop"></div>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end row1 align-items-center"></div>
@endsection

@section('content')
    <section class="section">
        <div class="row">
            @if ($projects && $currentWorkspace)
                <div class="col-md-8 projectsContainer">
                    <div class="grid filters-content">
                        @if ($projects->isEmpty())
                            <!-- seccion para cuando no hay proyectos -->
                            <div class="col-md-2 project-type text-center m-2"
                                style="display: flex; align-items: center; justify-content: center; width:100%">

                                <svg version="1.1" id="gears" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 250"
                                    style="enable-background:new 0 0 250 250;" xml:space="preserve">
                                    <g>
                                        <path class="gear-outer large"
                                            d="M145.6,134.6l10.6,5.3c3.5,2.1,4.6,5.1,3.1,9c-1.9,5.4-6.2,12.3-13.1,20.6c-2.7,3.1-5.8,3.6-9.4,1.6l-9.4-5.3 c-5,4.4-10.6,7.7-16.8,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                10v10.6c0,1.7-0.6,3.2-1.9,4.7c-1.2,1.5-2.7,2.3-4.4,2.5c-7.9,1.5-15.8,1.5-23.7,0c-1.9-0.2-3.4-1-4.5-2.5 c-1.1-1.5-1.7-3-1.7-4.7v-10.6c-6.4-2.3-12.2-5.6-17.2-10l-9,5.3c-3.5,2.1-6.7,1.6-9.4-1.6c-6.7-8.1-11-15-13.1-20.6
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              c-1.5-3.9-0.4-7,3.1-9l10.3-5.3c-1.2-6.7-1.2-13.2,0-19.7l-10.3-5.3c-3.7-2.1-4.8-5.1-3.1-9C27.8,95,32.2,88.1,38.9,80 c2.5-3.1,5.6-3.6,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              9.4-1.6l9,5c5.2-4.4,10.9-7.6,17.2-9.7V63.2c0-1.9,0.6-3.5,1.7-4.8c1.1-1.4,2.7-2.1,4.5-2.3 c7.9-1.5,15.8-1.5,23.7,0c0.9,0.2,1.5,0.4,2.3,0.8c0.8,0.4,1.4,1.1,2,1.7c0.6,0.6,1.1,1.4,1.4,2.2c0.3,0.8,0.5,1.7,0.5,2.5v10.6
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              c6.2,2.3,11.9,5.5,16.8,9.7l9.4-5c3.5-2.3,6.7-1.8,9.4,1.6c6.7,7.9,11,14.8,13.1,20.6c1.5,4,0.4,7-3.1,9l-10.6,5.3 C146.8,121.4,146.8,127.9,145.6,134.6z" />
                                        <circle class="gear-inner" cx="92.5" cy="124.8" r="23.1" />
                                        <path class="gear-outer small"
                                            d="M185.1,105l-2.5,4.4c-1,1.9-2.6,2.5-4.7,1.9c-3.7-1.5-7.1-3.4-10-5.9c-1.7-1.5-2-3.1-0.9-5l2.5-4.4
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            c-2.1-2.5-3.7-5.3-5-8.4h-5c-2.3,0-3.5-1.1-3.7-3.4c-0.8-3.7-0.8-7.6,0-11.5c0.2-2.1,1.5-3.1,3.7-3.1h5c1.2-3.1,2.9-5.9,5-8.4
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            l-2.5-4.7c-1-1.9-0.7-3.4,0.9-4.7c2.9-2.5,6.2-4.5,10-5.9c2.1-0.8,3.6-0.3,4.7,1.6l2.5,4.7c3.3-0.6,6.7-0.6,10,0l2.5-4.7
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      c1-1.9,2.6-2.4,4.7-1.6c3.7,1.2,7.1,3.2,10,5.9c1.7,1.2,2,2.8,0.9,4.7l-2.5,4.7c2.1,2.5,3.7,5.3,5,8.4h5c2.3,0,3.5,1,3.7,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      3.1 c0.8,4,0.8,7.8,0,11.5c-0.2,2.3-1.5,3.4-3.7,3.4h-5c-1.2,3.1-2.9,5.9-5,8.4l2.5,4.4c1,2.1,0.7,3.7-0.9,5c-2.9,2.5-6.2,4.5-10,5.9
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       c-2.1,0.6-3.6,0-4.7-1.9L195,105C191.7,105.6,188.4,105.6,185.1,105z" />
                                        <circle class="gear-inner" cx="190" cy="78.4" r="11.5" />
                                        <path class="gear-outer small"
                                            d="M185.1,198.2l-2.5,4.4c-1,1.9-2.6,2.5-4.7,1.9c-3.7-1.5-7.1-3.4-10-5.9c-1.7-1.5-2-3.1-0.9-5l2.5-4.4 c-2.1-2.5-3.7-5.3-5-8.4h-5c-2.3,0-3.5-1.1-3.7-3.4c-0.8-3.7-0.8-7.6,0-11.5c0.2-2.1,1.5-3.1,3.7-3.1h5c1.2-3.1,2.9-6,5-8.7
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         l-2.5-4.4c-1-1.9-0.7-3.5,0.9-5c2.9-2.5,6.2-4.4,10-5.6c2.1-0.8,3.6-0.3,4.7,1.6l2.5,4.7c3.3-0.6,6.7-0.6,10,0l2.5-4.7
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          c1-1.9,2.6-2.4,4.7-1.6c3.7,1.2,7.1,3.1,10,5.6c1.7,1.5,2,3.1,0.9,5l-2.5,4.4c2.1,2.7,3.7,5.6,5,8.7h5c2.3,0,3.5,1,3.7,3.1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             c0.8,4,0.8,7.8,0,11.5c-0.2,2.3-1.5,3.4-3.7,3.4h-5c-1.2,3.1-2.9,5.9-5,8.4l2.5,4.4c1,1.9,0.7,3.5-0.9,5c-2.9,2.5-6.2,4.5-10,5.9
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                c-2.1,0.6-3.6,0-4.7-1.9l-2.5-4.4C191.7,198.8,188.4,198.8,185.1,198.2z" />
                                        <circle class="gear-inner" cx="190.1" cy="171.6" r="11.5" />
                                    </g>
                                </svg>
                                <h1 class="noProjectText">{{ __('You still have no projects') }}</h1>

                            </div>
                            <div class="col-md-9">
                                <div class="card-header pt-3 pb-1 d-flex p-3">

                                    <div class="card-header-right">
                                        <div class="btn-group card-option">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- fin seccion para cuando no hay proyectos -->
                        @else
                            @foreach ($projects as $project)
                                @php
                                    $workspaceSlug = $project->workspaceData
                                        ? $project->workspaceData->slug
                                        : 'unknown-' . $project->workspace;
                                    // Debug info
                                    if (!$project->workspaceData) {
                                        \Log::warning(
                                            "Project {$project->id} no tiene workspaceData cargado. workspace_id: {$project->workspace}",
                                        );
                                    }
                                @endphp
                                <div onclick="if (!event.target.closest('#deleteFormContainer')) { 
        console.log('Opening project:', { projectId: {{ $project->id }}, workspaceSlug: '{{ $workspaceSlug }}', workspaceId: {{ $project->workspace }} });
        redirectToCurrentProject('{{ route('projects.show', [$workspaceSlug, $project->id]) }}'); 
    }"
                                    data-project-name="{{ mb_strtolower($project->name) }}"
                                    class="hover card mb-3 zoom mt-0 ml-0 m-2 All {{ $project->status }} type-{{ $project->type }} workspace-{{ $workspaceSlug }}">

                                    <div class="row ms-2">
                                        <div class="col-md-2 project-type text-center m-2">
                                            <img src="{{ asset('assets/img/' . $project_type[$project->type - 1]->name . '.png') }}"
                                                style="width: 45px; height: 45px;" alt="...">
                                            <small class="text-muted">
                                                <b>{{ __($project_type[$project->type - 1]->name) }}</b>
                                            </small>
                                            <span class="text-muted">
                                                <b>{{ $project->ref_mo }}</b>
                                            </span>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="card-header pt-3 pb-1 d-flex p-3">
                                                <a style="width: 83%">
                                                    <h4>{{ $project->name }}</h4>
                                                </a>

                                                <div class="card-header-right">
                                                    <div class="btn-group card-option">
                                                        <div class="mt-2 me-5 d-flex flex-wrap gap-2">

                                                            {{-- Delegation --}}
                                                            @if ($project->delegation)
                                                                <span class="badge rounded-pill p-2 delegationName">
                                                                    {{ \Illuminate\Support\Str::title($project->delegation->delegation_name) }}
                                                                </span>
                                                                <span class="badge rounded-pill p-2 delegationTag">
                                                                    {{ \Illuminate\Support\Str::upper($project->ref_delegation) }}
                                                                </span>
                                                            @endif

                                                            {{-- Status --}}
                                                            @if ($project->status == 'Finished')
                                                                <span class="badge rounded-pill bg-success p-2">
                                                                    {{ __('Finished') }}
                                                                </span>
                                                            @elseif($project->status == 'Ongoing')
                                                                <span class="badge rounded-pill bg-secondary p-2">
                                                                    {{ __('Ongoing') }}
                                                                </span>
                                                            @else
                                                                <span class="badge rounded-pill bg-warning p-2">
                                                                    {{ __('OnHold') }}
                                                                </span>
                                                            @endif

                                                            {{-- Workspace name --}}
                                                            @if ($project->workspaceData)
                                                                <span class="badge rounded-pill bg-dark p-2">
                                                                     {{ $project->workspaceData->display_name }}
                                                                </span>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body p-3">
                                                <div class="card-text text-muted d-flex align-items-center">
                                                    <div class="participantsSection">
                                                        {{ __('Participants') }}
                                                    </div>

                                                    <div class="col-md-2">
                                                        @foreach ($project->users as $key => $user)
                                                            @if ($key < 2)
                                                                <img class="iconUSer"
                                                                    @if ($user->avatar) src="{{ asset($user->avatar) }}"
                                        @else
                                            avatar="{{ $user->name }}" @endif>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="card-text mt-2">
                                                    <small class="text-body-secondary">
                                                        {{ __('Last updated') }}
                                                        {{ $project->updated_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-4 position-sticky text-muted rightColumn">
                    <div class="card " id="v-pills-filterSection">
                        <div class="card-header buttonColocation">
                            <div>
                                <h5><i class="bi bi-filter"></i> {{ __('Filter projects') }}</h5>
                                <p class="text-muted mt-3">
                                    {{ __('Remember that the projects shown are those of your delegation/group.') }}
                                </p>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                @if (isset($projects) && $projects && !$projects->isEmpty())
                                    <button type="button" id="btnSortProjectsAlpha"
                                        class="btn btn-sm d-flex align-items-center gap-1"
                                        title="{{ __('Sort alphabetically') }}"
                                        aria-label="{{ __('Sort alphabetically') }}">
                                        <span class="sort-direction-arrow" aria-hidden="true"></span>
                                        <span style="font-size:0.78rem; font-weight: 700;">AZ</span>
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-primary toggle-section buttonColapse"
                                    data-target="#filterSection-content">-</button>
                            </div>
                        </div>
                        <div class="card-body collapse-section" id="filterSection-content">
                            <div class="col-12 d-flex flex-column">
                                <h5>
                                    {{ __('Status') }}
                                </h5>
                                <div class="divStatus">

                                    <!-- Filtro de Status -->
                                    <div class="btn-group status-filter">
                                        <button type="button"
                                            class="btn d-flex align-items-center zoom  text-muted filterTypo "
                                            data-filter=".Ongoing">
                                            <span class="status-indicator bg-secondary me-2"></span>
                                            {{ __('Ongoing') }}
                                        </button>
                                        <button type="button"
                                            class="btn d-flex align-items-center zoom text-muted filterTypo "
                                            data-filter=".OnHold">
                                            <span class="status-indicator bg-warning me-2"></span>
                                            {{ __('OnHold') }}
                                        </button>
                                        <button type="button"
                                            class="btn d-flex align-items-center zoom text-muted filterTypo "
                                            data-filter=".Finished">
                                            <span class="status-indicator bg-success me-2"></span>
                                            {{ __('Finished') }}
                                        </button>
                                    </div>
                                </div>

                                <h5 class="mt-4">
                                    {{ __('Project type') }}
                                </h5>
                                <!-- Filtro de Type -->
                                <div class="btn-group type-filter">
                                    <div>
                                        <button type="button" class="btn disp-btn zoom text-muted filterTypo"
                                            data-filter=".type-1">
                                            <img src="{{ asset('assets/img/Jobsite.png') }}"
                                                style="width: 25px; height: 25px;" alt="..." class="me-2">

                                            {{ __('Jobsite') }}
                                        </button>
                                        <button type="button" class="btn disp-btn zoom  text-muted filterTypo"
                                            data-filter=".type-2">
                                            <img src="{{ asset('assets/img/Innovation.png') }}"
                                                style="width: 25px; height: 25px;" alt="..." class="me-2">
                                            {{ __('Innovation') }}
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn disp-btn zoom  text-muted filterTypo"
                                            data-filter=".type-3">
                                            <img src="{{ asset('assets/img/I+D Project.png') }}"
                                                style="width: 25px; height: 25px;" alt="..." class="me-1">
                                            {{ __('I+D Project') }}
                                        </button>
                                        <button type="button" class="btn disp-btn zoom  text-muted filterTypo"
                                            data-filter=".type-5">
                                            <img src="{{ asset('assets/img/I+D Development.png') }}"
                                                style="width: 25px; height: 25px;" alt="..." class="me-1">
                                            {{ __('I+D Development') }}
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn disp-btn zoom  text-muted filterTypo"
                                            data-filter=".type-4">
                                            <img src="{{ asset('assets/img/Office.png') }}"
                                                style="width: 25px; height:25px;" alt="..." class="me-2">
                                            {{ __('Office') }}
                                        </button>
                                    </div>
                                </div>
                                <hr class="mt-3" style="border: 1px solid #eeeeee; width: 100%">

                                <h5 class="mt-4">
                                    {{ __('Workspace') }}
                                </h5>
                                <!-- Filtro de Workspace -->
                                <div class="btn-group workspace-filter">
                                    @php
                                        $workspaces = $projects
                                            ->map(function ($project) {
                                                return $project->workspaceData;
                                            })
                                            ->unique('id')
                                            ->values();
                                    @endphp
                                    @foreach ($workspaces as $workspace)
                                        <button type="button" class="btn disp-btn zoom text-muted filterTypo"
                                            data-filter=".workspace-{{ $workspace->slug }}"
                                                                     style="    background-color: #f6f6f6;">
                                                                     <i class="fa-solid fa-layer-group"style="width: 18px; height: 18px;"></i>
                                                                     {{ $workspace->display_name }}
                                        </button>
                                    @endforeach
                                </div>

                                <hr class="mt-3" style="border: 1px solid #eeeeee; width: 100%">
                                <div class="mt-4">
                                    <h5>
                                        {{ __('Active filters') }}
                                    </h5>
                                    <div id="activeFilters" class="mt-3 d-flex flex-wrap gap-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="container mt-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="page-error">
                                <div class="page-inner">
                                    <h1>404</h1>
                                    <div class="page-description">
                                        {{ __('Page Not Found') }}
                                    </div>
                                    <div class="page-search">
                                        <p class="text-muted mt-3">
                                            {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                                        </p>
                                        <div class="mt-3">
                                            <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                                    class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

<script>
    function redirectToCurrentProject(url) {
        window.location.href = url;
    }
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".buttonColapse").forEach(button => {
            button.addEventListener("click", function() {
                let target = document.querySelector(this.dataset.target);

                if (target) {
                    target.classList.toggle("d-none");
                }

                this.textContent = this.textContent === "-" ? "+" : "-";
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Detectar botones de workspace con texto truncado
        const workspaceButtons = document.querySelectorAll('.workspace-filter button');
        workspaceButtons.forEach(button => {
            if (button.scrollWidth > button.clientWidth) {
                button.classList.add('truncated');
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const buttons = document.querySelectorAll(".btn-group button, .disp-btn");
        const activeFiltersContainer = document.getElementById("activeFilters");

        function updateActiveFilters() {

            activeFiltersContainer.innerHTML = "";

            buttons.forEach(button => {
                if (button.classList.contains("active") || button.classList.contains("selected")) {
                    const clone = button.cloneNode(true);
                    clone.classList.remove("btn");
                    clone.classList.add("btn-active-filters");
                    activeFiltersContainer.appendChild(clone);
                }
            });
        }

        buttons.forEach(button => {
            button.addEventListener("click", function() {
                setTimeout(updateActiveFilters,
                    100);
            });
        });

        updateActiveFilters();
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@if (isset($currentWorkspace) && $currentWorkspace)
    <script src="{{ asset('assets/custom/js/jquery.easy-autocomplete.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('searchInput');
            const divContainer = document.getElementById('searchInputProjects');

            // Usar un observador de mutaciones para detectar la aparición del contenedor de autocompletar
            const observer = new MutationObserver(function(mutationsList) {
                for (const mutation of mutationsList) {
                    if (mutation.addedNodes) {
                        mutation.addedNodes.forEach((node) => {
                            if (node.id === 'eac-container-searchInput') {
                                node.style.display = 'none';
                                observer.disconnect();
                            }
                        });
                    }
                }
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            input.addEventListener('input', function() {
                const autocompleteContainer = document.getElementById('eac-container-searchInput');
                if (autocompleteContainer) {
                    if (this.value.trim().length > 0) {
                        autocompleteContainer.style.display = 'block';
                        autocompleteContainer.style.textAlign = 'left';
                        autocompleteContainer.style.width = '77%';
                        autocompleteContainer.style.maxHeight = '1px';
                        // divContainer.classList.add('dropdown-menu');
                    } else {
                        autocompleteContainer.style.display = 'none';
                        divContainer.classList.remove('dropdown-menu');
                    }
                }
            });
        });
    </script>
    <script>
        jQuery(document).ready(function($) {
            var options = {
                adjustWidth: false,
                url: function(phrase) {
                    return "{{ route('search.json', $currentWorkspace->slug) }}/" + encodeURIComponent(
                        phrase);
                },
                categories: [{
                    listLocation: "Projects",
                    // header: "{{ __('Projects') }}" //Para mostrar el título "Projects" en la lista de autocompletado
                }],
                getValue: "text",
                template: {
                    type: "links",
                    fields: {
                        link: "link"
                    }
                },
            };
            $(".search-element input").easyAutocomplete(options);
        });
    </script>
@endif
@push('scripts')
    <script src="{{ asset('assets/custom/js/isotope.pkgd.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            if (typeof $.fn.isotope === 'undefined') {
                console.error('Isotope is not loaded');
                return;
            }

            // Inicializar Isotope
            var sortAlphaMode = 'none'; // none | asc | desc (after first click, toggles asc <-> desc)
            var $grid = $(".grid").isotope({
                itemSelector: ".All",
                percentPosition: true,
                masonry: {
                    columnWidth: ".All"
                },
                getSortData: {
                    name: '[data-project-name]'
                }
            });

            $('#btnSortProjectsAlpha').on('click', function() {
                var $btn = $(this);

                if (sortAlphaMode === 'none' || sortAlphaMode === 'desc') {
                    sortAlphaMode = 'asc';
                    $grid.isotope({ sortBy: 'name', sortAscending: true });
                    $btn.addClass('active').removeClass('sort-desc');
                } else {
                    sortAlphaMode = 'desc';
                    $grid.isotope({ sortBy: 'name', sortAscending: false });
                    $btn.addClass('active').addClass('sort-desc');
                }

                $grid.isotope('layout');
            });

            var filterStatus = '*';
            var filterType = '*';
            var filterWorkspace = '*';

            function applyFilter() {

                var filterValue = (filterStatus === '*' && filterType === '*' && filterWorkspace === '*') ?
                    '*' :
                    filterStatus + filterType + filterWorkspace;


                $grid.isotope({
                    filter: filterValue
                });
            }

            function toggleActiveClass($element, groupSelector, zoomClass, selectedClass) {
                var isActive = $element.hasClass('active');
                $(groupSelector).removeClass('active').removeClass(selectedClass);
                if (!isActive) {
                    $element.addClass('active').addClass(selectedClass);
                } else {
                    $element.removeClass(selectedClass);
                }
            }


            $('.status-filter button, .type-filter button, .workspace-filter button').click(function(e) {
                var $this = $(this);
                var selectedFilter = $this.attr('data-filter');
                var filterTypeSelector = '.type-filter button';
                var filterStatusSelector = '.status-filter button';
                var filterWorkspaceSelector = '.workspace-filter button';


                if ($this.closest(filterStatusSelector).length > 0) {

                    toggleActiveClass($this, filterStatusSelector);
                    filterStatus = $this.hasClass('active') ? selectedFilter : '*';
                }

                if ($this.closest(filterTypeSelector).length > 0) {

                    toggleActiveClass($this, filterTypeSelector, 'types', 'selected');
                    filterType = $this.hasClass('active') ? selectedFilter : '*';
                }

                if ($this.closest(filterWorkspaceSelector).length > 0) {

                    toggleActiveClass($this, filterWorkspaceSelector, 'workspace', 'selected');
                    filterWorkspace = $this.hasClass('active') ? selectedFilter : '*';
                }


                applyFilter();
            });
        });
    </script>
    <script>
        // After the section loads or changes
        $(document).ready(function() {
            // Re-initialize tooltips
            $('.legendIcon.tooltipCus').tooltip({
                placement: 'auto',
                title: function() {
                    return $(this).data('title');
                }
            });
        });
    </script>
@endpush
