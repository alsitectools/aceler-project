@extends('layouts.admin')
@section('page-title')
    {{ __('Projects') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"> {{ __('Projects') }}</li>
@endsection
@php

    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/index_projects.css') }}">
</head>
<style>
    .filterSection {
        display: flex;
        background-color: red;
        width: 96% !important;
        padding: 20px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        background-color: #ffffff;
        border-radius: 10px;
    }

    .secondFilter {
        margin-bottom: 15px !important;
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

    #searchInputProjects {
        transform: none !important;
        margin: 0px !important;
    }

    .inputWrapper {
        background-color: #ffffff;
        height: 90px;
        display: flex;
        flex-direction: column;
        align-content: center;
        align-items: center;
        justify-content: center;
        width: 95%;
        margin-bottom: 20px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        border-radius: 10px;
    }

    .custom {
        /* box-shadow: none !important; */
        background-color: #ffffff !important;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        width: 95%;
        /* background-color: #f7f9f9 !important; */
    }

    @media screen and (max-width:1200px) and (min-width:1000px) {
        .responsiveButton {
            font-size: 12px !important;
        }
    }
</style>
@section('action-button')
    <div class="d-flex justify-content-end me-2">
        <div class="d-flex col-sm-7">

        </div>
        @auth('web')
        @endauth
    </div>
@endsection
@section('content')
    <section class="section">
        <div class="row">
            @if ($projects && $currentWorkspace)
                <div class="col-md-8">
                    <div class="grid filters-content">
                        @if ($projects->isEmpty())
                            <!-- seccion para cuando no hay proyectos -->


                            <div class="col-md-2 project-type text-center m-2"
                                style="display: flex; align-items: center; justify-content: center; width:100%">

                                <svg version="1.1" id="gears" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250 250"
                                    style="enable-background:new 0 0 250 250;" xml:space="preserve">
                                    <g>
                                        <path class="gear-outer large" d="M145.6,134.6l10.6,5.3c3.5,2.1,4.6,5.1,3.1,9c-1.9,5.4-6.2,12.3-13.1,20.6c-2.7,3.1-5.8,3.6-9.4,1.6l-9.4-5.3
      c-5,4.4-10.6,7.7-16.8,10v10.6c0,1.7-0.6,3.2-1.9,4.7c-1.2,1.5-2.7,2.3-4.4,2.5c-7.9,1.5-15.8,1.5-23.7,0c-1.9-0.2-3.4-1-4.5-2.5
      c-1.1-1.5-1.7-3-1.7-4.7v-10.6c-6.4-2.3-12.2-5.6-17.2-10l-9,5.3c-3.5,2.1-6.7,1.6-9.4-1.6c-6.7-8.1-11-15-13.1-20.6
      c-1.5-3.9-0.4-7,3.1-9l10.3-5.3c-1.2-6.7-1.2-13.2,0-19.7l-10.3-5.3c-3.7-2.1-4.8-5.1-3.1-9C27.8,95,32.2,88.1,38.9,80
      c2.5-3.1,5.6-3.6,9.4-1.6l9,5c5.2-4.4,10.9-7.6,17.2-9.7V63.2c0-1.9,0.6-3.5,1.7-4.8c1.1-1.4,2.7-2.1,4.5-2.3
      c7.9-1.5,15.8-1.5,23.7,0c0.9,0.2,1.5,0.4,2.3,0.8c0.8,0.4,1.4,1.1,2,1.7c0.6,0.6,1.1,1.4,1.4,2.2c0.3,0.8,0.5,1.7,0.5,2.5v10.6
      c6.2,2.3,11.9,5.5,16.8,9.7l9.4-5c3.5-2.3,6.7-1.8,9.4,1.6c6.7,7.9,11,14.8,13.1,20.6c1.5,4,0.4,7-3.1,9l-10.6,5.3
      C146.8,121.4,146.8,127.9,145.6,134.6z" />
                                        <circle class="gear-inner" cx="92.5" cy="124.8" r="23.1" />
                                        <path class="gear-outer small" d="M185.1,105l-2.5,4.4c-1,1.9-2.6,2.5-4.7,1.9c-3.7-1.5-7.1-3.4-10-5.9c-1.7-1.5-2-3.1-0.9-5l2.5-4.4
      c-2.1-2.5-3.7-5.3-5-8.4h-5c-2.3,0-3.5-1.1-3.7-3.4c-0.8-3.7-0.8-7.6,0-11.5c0.2-2.1,1.5-3.1,3.7-3.1h5c1.2-3.1,2.9-5.9,5-8.4
      l-2.5-4.7c-1-1.9-0.7-3.4,0.9-4.7c2.9-2.5,6.2-4.5,10-5.9c2.1-0.8,3.6-0.3,4.7,1.6l2.5,4.7c3.3-0.6,6.7-0.6,10,0l2.5-4.7
      c1-1.9,2.6-2.4,4.7-1.6c3.7,1.2,7.1,3.2,10,5.9c1.7,1.2,2,2.8,0.9,4.7l-2.5,4.7c2.1,2.5,3.7,5.3,5,8.4h5c2.3,0,3.5,1,3.7,3.1
      c0.8,4,0.8,7.8,0,11.5c-0.2,2.3-1.5,3.4-3.7,3.4h-5c-1.2,3.1-2.9,5.9-5,8.4l2.5,4.4c1,2.1,0.7,3.7-0.9,5c-2.9,2.5-6.2,4.5-10,5.9
      c-2.1,0.6-3.6,0-4.7-1.9L195,105C191.7,105.6,188.4,105.6,185.1,105z" />
                                        <circle class="gear-inner" cx="190" cy="78.4" r="11.5" />
                                        <path class="gear-outer small" d="M185.1,198.2l-2.5,4.4c-1,1.9-2.6,2.5-4.7,1.9c-3.7-1.5-7.1-3.4-10-5.9c-1.7-1.5-2-3.1-0.9-5l2.5-4.4
      c-2.1-2.5-3.7-5.3-5-8.4h-5c-2.3,0-3.5-1.1-3.7-3.4c-0.8-3.7-0.8-7.6,0-11.5c0.2-2.1,1.5-3.1,3.7-3.1h5c1.2-3.1,2.9-6,5-8.7
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

                                    <!-- <a style="width: 100%; text-align: center;"
                                                         >
                                                        <h4>¡Vaya, parece que aún no tienes ningún proyecto!</h4>
                                                    </a> -->
                                    <div class="card-header-right">
                                        <div class="btn-group card-option">
                                        </div>
                                    </div>

                                </div>

                            </div>


                            <!-- fin seccion para cuando no hay proyectos -->
                        @else
                            @foreach ($projects as $project)
                                <div onclick="if (!event.target.closest('#deleteFormContainer')) { redirectToCurrentProject('{{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}'); }"
                                    class="hover card mb-3 zoom mt-0 ml-0 m-2 All {{ $project->status }} type-{{ $project->type }}">
                                    <div class="row ms-2">
                                        <div class="col-md-2 project-type text-center m-2">
                                            <img src="{{ asset('assets/img/' . $project_type[$project->type - 1]->name . '.png') }}"
                                                style="width: 45px; height: 45px;" alt="...">
                                            <small class="text-muted tooltipCus" data-title="{{ __('Project type') }}"
                                                data-type="$project_type[$project->type - 1]->name">
                                                <b>{{ $project_type[$project->type - 1]->name }}</b>
                                            </small>
                                            <span class="text-muted tooltipCus" data-title="{{ __('Reference M.O') }}">
                                                <b>{{ $project->ref_mo }}</b></span>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-header pt-3 pb-1 d-flex p-3">
                                                @if ($project->is_active)
                                                    <a style="width: 83%" class="tooltipCus"
                                                        data-title="{{ __('Project Name') }}">
                                                        <h4>{{ $project->name }} </h4>
                                                    </a>
                                                    <div class="card-header-right">
                                                        <div class="btn-group card-option">
                                                            <div class="mt-3 me-5">
                                                                @if ($project->status == 'Finished')
                                                                    <span data-title="{{ __('Status') }}"
                                                                        class="badge rounded-pill bg-success tooltipCus">{{ __('Finished') }}</span>
                                                                @elseif($project->status == 'Ongoing')
                                                                    <span data-title="{{ __('Status') }}"
                                                                        class="badge rounded-pill bg-secondary tooltipCus">{{ __('Ongoing') }}</span>
                                                                @else
                                                                    <span data-title="{{ __('Status') }}"
                                                                        class="badge rounded-pill bg-warning tooltipCus">{{ __('OnHold') }}</span>
                                                                @endif
                                                            </div>
                                                            @if ($project->is_active && $project->created_by == Auth::user()->id)
                                                                @auth('web')
                                                                    <button type="button" class="btn dropdown-toggle"
                                                                        onclick="event.stopPropagation();"
                                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                                        aria-expanded="false">
                                                                        <i class="feather icon-more-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <a href="#" id="deleteFormContainer"
                                                                            class="dropdown-item text-danger delete-popup bs-pass-para"
                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                            data-text="{{ trans('This action can not be undone. Do you want to continue?') }}"
                                                                            data-confirm-yes="delete-form-{{ $project->id }}">
                                                                            <i class="ti ti-trash"></i>
                                                                            <span>{{ __('Delete') }}</span>
                                                                        </a>
                                                                        <form id="delete-form-{{ $project->id }}"
                                                                            action="{{ route('projects.destroy', [$currentWorkspace->slug, $project->id]) }}"
                                                                            method="POST" style="display: none;">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    </div>
                                                                @endauth
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="#" class=""></a>
                                                @endif
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="card-text text-muted d-flex">
                                                    <div class="col-md-6 tooltipCus" data-title="{{ __('Location') }}">
                                                        <i class="fa-solid fa-location-dot"></i>
                                                        {{ $currentWorkspace->name }}
                                                    </div>

                                                    <div class="col-md-2 text-end">
                                                        @if ($users = $project->users)
                                                            @foreach ($users as $key => $user)
                                                                @if ($key < 2)
                                                                    <a href="#" class="img_group tooltipCus"
                                                                        data-title="{{ $user->name }}">
                                                                        <img alt="{{ $user->name }}" class="iconUSer"
                                                                            @if ($user->avatar) src="{{ asset($user->avatar) }}" @else avatar="{{ $user->name }}" @endif>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                            @if (count($users) > 2)
                                                                <a href="#" class="img_group">
                                                                    <img alt="image" class="iconUSer"
                                                                        data-original-title="{{ count($users) - 2 }} {{ __('more') }}"
                                                                        avatar="+ {{ count($users) - 2 }}">
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-text mt-2">
                                                    <small class="text-body-secondary tooltipCus"
                                                        data-title="{{ __('Update') }}">
                                                        {{ __('Last updated') }}{{ ' ' . $project->updated_at->diffForHumans() }}
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
                <div class="col-md-4 position-sticky text-muted">
                    <div class="mt-0 pt-0">
                        <div>
                            @auth('web')
                                <a href="#" class="btn-addnew tooltipCus card custom " data-ajax-popup="true"
                                    data-title="{{ __('Create New Project') }}"
                                    data-url="{{ route('projects.create', $currentWorkspace->slug) }}">
                                    <div class="bg-primary iconaddproject zoom">
                                        <h6 class="m-1 btnlabel">{{ __('Create New Project') }}</h6>
                                        <i class="ti ti-plus"></i>
                                    </div>


                                </a>
                            @endauth
                        </div>
                        <div class="inputWrapper">
                            <div id="searchInputProjects" data-popper-placement="bottom-start">
                                <form class="form-inline mr-auto mb-0">
                                    <div class="search-element">
                                        <div class="input-wrapper">
                                            <input type="text" class="input" id="searchInput"
                                                placeholder="{{ __('Enter name or reference M.O') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="input-icon"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="search-backdrop"></div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- inicio botones filtro  -->
                        <div class="col-sm-5 filterSection">
                            <div class="divStatus">
                                <!-- Filtro de Status -->
                                <div class="btn-group status-filter secondFilter">
                                    <!-- <button type="button" data-filter="*" class="btn btn-light text-white btn_tab bg-primary active"
                    data-status="All">{{ __('All') }}</button> -->
                                    <button type="button"
                                        class="btn btn-light bg-primary text-white btn_tab responsiveButton"
                                        data-filter=".OnHold">{{ __('OnHold') }}</button>
                                    <button type="button"
                                        class="btn btn-light bg-primary text-white btn_tab responsiveButton"
                                        data-filter=".Ongoing">{{ __('Ongoing') }}</button>
                                    <button type="button"
                                        class="btn btn-light bg-primary text-white btn_tab responsiveButton"
                                        data-filter=".Finished">{{ __('Finished') }}</button>

                                </div>

                                <!-- Filtro de Type -->
                                <div class="btn-group type-filter">
                                    @foreach ($project_type as $index => $type)
                                        <button type="button" class="btn btn-light text-white btn_tab bg-primary"
                                            data-filter=".type-{{ $type->id }}">
                                            {{ __($type->name) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Final botones filtro  -->
                        <!-- <hr class="mt-3" style="border: 1px solid black; opacity: 0.100; width: 95%"> -->
                        <!-- <div class="mt-4">
                                <h5><i class="bi bi-filter"></i> {{ __('Filter by') }}</h5>
                                <div class="type-filter">
                                    @foreach ($project_type as $type)
    <div class="d-flex">
                                            <a href="#" class="types m-2 filter-link"
                                                data-filter=".type-{{ $type->id }}"
                                                style="background-color: transparent;">
                                                <b class="text-muted ms-3">{{ __($type->name) }}</b>
                                            </a>
                                        </div>
    @endforeach
                                </div>
                            </div> -->
                    </div>

                    <hr class="mt-3" style="border: 1px solid black; opacity: 0.100; width: 95%">
                    <div class="mt-4">
                        <h5>
                            {{ __('My projects') }}
                        </h5>
                        <div style="width: 95%;" class="mt-3">
                            @foreach (Auth::user()->projects() as $project)
                                @if (Auth::user()->currant_workspace == $project->workspace)
                                    <div class="list-group mb-2">
                                        <a a href="@auth('web'){{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}@endauth"
                                            class="list-group-item list-group-item-action tooltipCus" aria-current="true"
                                            data-title="{{ __('Project') }}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="text-muted">{{ $project->name }} </h5>
                                                <small
                                                    class="me-0 text-muted">{{ ucfirst($project->created_at->isoFormat('ddd DD MMM YYYY')) }}</small>
                                            </div>
                                            <small
                                                class="text-muted">{{ $project_type[$project->type - 1]->name }}</small>
                                            <small class="text-muted"><b>{{ $project->ref_mo }}</b></small>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
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
                        divContainer.classList.add('dropdown-menu');
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
                url: function(phrase) {
                    return "{{ route('search.json', $currentWorkspace->slug) }}/" + encodeURIComponent(
                        phrase);
                },
                categories: [{
                    listLocation: "Projects",
                    header: "{{ __('Projects') }}"
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
            var $grid = $(".grid").isotope({
                itemSelector: ".All",
                percentPosition: true,
                masonry: {
                    columnWidth: ".All"
                }
            });

            var filterStatus = '*';
            var filterType = '*';

            function applyFilter() {

                var filterValue = (filterStatus === '*' && filterType === '*') ?
                    '*' :
                    filterStatus + filterType;


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


            $('.status-filter button, .type-filter button').click(function(e) {
                var $this = $(this);
                var selectedFilter = $this.attr('data-filter');
                var filterTypeSelector = '.type-filter button';
                var filterStatusSelector = '.status-filter button';


                if ($this.closest(filterStatusSelector).length > 0) {

                    toggleActiveClass($this, filterStatusSelector);
                    filterStatus = $this.hasClass('active') ? selectedFilter : '*';
                }

                if ($this.closest(filterTypeSelector).length > 0) {

                    toggleActiveClass($this, filterTypeSelector, 'types', 'selected');
                    filterType = $this.hasClass('active') ? selectedFilter : '*';
                }


                applyFilter();
            });
        });
    </script>
@endpush
