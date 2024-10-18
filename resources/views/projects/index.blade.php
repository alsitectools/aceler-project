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
    #eac-container-searchInput {
        display: none;
        position: absolute;
        z-index: 9999;
        top: 100%;
        left: 0;
        width: 100%;
    }

    #searchInputProjects {
        position: relative;
    }
</style>
@section('action-button')
    <div class="d-flex justify-content-end me-2">
        <div class="d-flex col-sm-7">
            <div id="searchInputProjects" data-popper-placement="bottom-start">
                <form class="form-inline mr-auto mb-0">
                    <div class="search-element">
                        <div class="input-wrapper">
                            <input type="text" class="input" id="searchInput"
                                placeholder="{{ __('Enter name or reference M.O') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="input-icon" viewBox="0 0 20 20"
                                fill="currentColor">
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
        @auth('web')
            <div class="col-sm-5">
                <div class="divStatus">
                    <div class="btn-group status-filter">
                        <button type="button" data-filter="*" class="btn btn-light text-white btn_tab bg-primary active"
                            data-filter="*" data-status="All">{{ __('All') }}</button>
                        <button type="button" class="btn btn-light bg-primary text-white btn_tab"
                            data-filter=".Ongoing">{{ __('Ongoing') }}</button>
                        <button type="button" class="btn btn-light bg-primary text-white btn_tab"
                            data-filter=".Finished">{{ __('Finished') }}</button>
                        <button type="button" class="btn btn-light bg-primary text-white btn_tab"
                            data-filter=".OnHold">{{ __('OnHold') }}</button>
                    </div>
                </div>
            </div>
        @endauth
    </div>
@endsection
@section('content')
    <section class="section">
        <div class="row">
            @if ($projects && $currentWorkspace)
                <div class="col-md-8">
                    <div class="grid filters-content">
                        @foreach ($projects as $project)
                            <div class="card mb-3 zoom mt-0 ml-0 m-2 All  {{ $project->status }} type-{{ $project->type }}"
                                style="border-radius: 10px;">
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
                                                <a href="@auth('web'){{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}@endauth"
                                                    class="tooltipCus" data-title="{{ __('Project Name') }}">
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
                                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    <i class="feather icon-more-vertical"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <a href="#"
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
                                                <div class="col-md-6 tooltipCus" data-title="{{ __('BU') }}">
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
                                                                        @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif>
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
                    </div>
                </div>
                <div class="col-md-4 position-sticky text-muted">
                    <div class="mt-0 pt-0">
                        <div>
                            @auth('web')
                                <a href="#" class="btn-addnew tooltipCus card zoom" data-ajax-popup="true"
                                    data-title="{{ __('Create New Project') }}"
                                    data-url="{{ route('projects.create', $currentWorkspace->slug) }}">
                                    <div class="bg-primary iconaddproject">
                                        <i class="ti ti-plus"></i>
                                    </div>
                                    <h5 class="m-1">{{ __('Create New Project') }}</h5>
                                    <p class="text-muted text-center m-1">
                                        {{ __('Click here to add New Project') }}</p>
                                </a>
                            @endauth
                        </div>
                        <hr class="mt-3" style="border: 1px solid black; opacity: 0.100; width: 95%">
                        <div class="mt-4">
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
                        </div>
                    </div>
                    <hr class="mt-3" style="border: 1px solid black; opacity: 0.100; width: 95%">
                    <div class="mt-4">
                        <h5>
                            {{ __('My projects') }}
                        </h5>
                        <div style="width: 95%;" class="mt-3">
                            @foreach (Auth::user()->projects() as $project)
                                <div class="list-group mb-2">
                                    <a a href="@auth('web'){{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}@endauth"
                                        class="list-group-item list-group-item-action tooltipCus" aria-current="true"
                                        data-title="{{ __('Project') }}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="text-muted">{{ $project->name }} </h5>
                                            <small
                                                class="me-0 text-muted">{{ ucfirst($project->created_at->isoFormat('ddd DD MMM YYYY')) }}</small>
                                        </div>

                                        <small class="text-muted">{{ $project_type[$project->type - 1]->name }}</small>
                                        <small class="text-muted"><b>{{ $project->ref_mo }}</b></small>
                                    </a>
                                </div>
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
                                            {{ __("It's looking like you may have taken a wrong turn. Don't worry...
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             it happens to the best of us. Here's a little tip that might help you get back on track.") }}
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
@if (isset($currentWorkspace) && $currentWorkspace)
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            // Observar el body para detectar cuando el contenedor de autocompletar se inserte
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
                }
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
                var filterValue = filterStatus + filterType;
                $grid.isotope({
                    filter: filterValue
                });
            }

            function toggleActiveClass($element, groupSelector, zoomClass, selectedClass) {
                var isActive = $element.hasClass('active');
                $(groupSelector).removeClass('active').removeClass(zoomClass).removeClass(selectedClass);
                if (!isActive) {
                    $element.addClass('active').addClass(zoomClass).addClass(selectedClass);
                } else {
                    $element.removeClass(selectedClass);
                }
            }

            $('.status-filter button').click(function() {
                var $this = $(this);
                toggleActiveClass($this, '.status-filter button');
                filterStatus = $this.hasClass('active') ? $this.attr('data-filter') : '*';

                if (filterStatus === '*') {
                    filterType = '*';
                    $('.type-filter a').removeClass('active').removeClass('types selected');
                }
                applyFilter();
            });

            $('.type-filter a').click(function(e) {
                e.preventDefault();
                var $this = $(this);
                toggleActiveClass($this, '.type-filter a', 'types', 'selected');

                filterType = $this.hasClass('active') ? $this.attr('data-filter') : '*';
                applyFilter();
            });
        });
    </script>
@endpush
