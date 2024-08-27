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

@if (isset($currentWorkspace) && $currentWorkspace)
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/custom/js/jquery.easy-autocomplete.min.js') }}"></script>
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

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/index_projects.css') }}">
</head>
@section('action-button')
    <div class="d-flex justify-content-end">

        <div class="dropdown dash-h-item me-5 col-md-6 justify-content-start">
            <a class="dash-head-link dropdown-toggle arrow-none ms-0" data-bs-toggle="dropdown" href="#" role="button"
                aria-haspopup="false" aria-expanded="false">
                <img src="{{ asset('assets/img/search.png') }}" style="width: 40px; height: 40px;" class="m-1"
                    alt="{{ __('Search') }}">
            </a>
            <div class="dropdown-menu dash-h-dropdown drp-search drp-search-custom">
                <form class="form-inline mr-auto mb-0">
                    <div class="search-element" style="width: 300px !important;">
                        <input type="type here" placeholder="{{ __('Enter name or reference M.O') }}" aria-label="Search"
                            class="custom-search-input">
                        <div class="search-backdrop"></div>
                    </div>
                </form>
            </div>
        </div>

        @auth('web')
            <div class="col-sm-5">
                <div class="text-sm-right status-filter">
                    <div class="btn-group status-filter">
                        <button type="button" class="btn btn-light text-white btn_tab bg-primary active" data-filter="*"
                            data-status="All">{{ __('All') }}</button>
                        <button type="button" class="btn btn-light bg-primary text-white btn_tab"
                            data-filter=".Ongoing">{{ __('Ongoing') }}</button>
                        <button type="button" class="btn btn-light bg-primary text-white btn_tab"
                            data-filter=".Finished">{{ __('Finished') }}</button>
                        <button type="button" class="btn btn-light bg-primary text-white btn_tab"
                            data-filter=".OnHold">{{ __('OnHold') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endauth
@endsection
@section('content')
    <section class="section">
        <div class="row">
            @if ($projects && $currentWorkspace)
                <div class="col-md-8">
                    <div class="grid filters-content">
                        @foreach ($projects as $project)
                            <div class="card mb-3 zoom mt-0 ml-0 m-2 All {{ $project->status }}"
                                style="border-radius: 20px;">
                                <div class="row">
                                    <div class="col-md-2 project-type text-center m-2">
                                        <img src="{{ asset('assets/img/' . $project_type[$project->type - 1]->name . '.png') }}"
                                            style="width: 45px; height: 45px;" alt="...">
                                        <small class="text-muted tooltipCus" data-title="{{ __('Project type') }}">
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
                                                                        data-text="{{ trans('messages.This_action_can_not_be_undone._Do_you_want_to_continue?') }}"
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
                                                <div class="col-md-2 tooltipCus" data-title="{{ __('Country') }}">
                                                    <i class="fa-regular fa-building"></i>
                                                    {{ $currentWorkspace->name }}
                                                </div>
                                                <div class="col-md-3 tooltipCus" data-title="{{ __('Branch') }}">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    {{ 'Montacada i Reixach' }}
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
                                            <div class="card-text">
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
                    <div class="mt-0 pt-0 filter">
                        <div>
                            @auth('web')
                                <a href="#" class="btn-addnew tooltipCus" data-ajax-popup="true" data-size="md"
                                    data-title="{{ __('Create New Project') }}"
                                    data-url="{{ route('projects.create', $currentWorkspace->slug) }}">
                                    <div class="bg-primary iconaddproject">
                                        <i class="ti ti-plus"></i>
                                    </div>
                                    <h5 class="mt-4 mb-2">{{ __('Create New Project') }}</h5>
                                    <p class="text-muted text-center">
                                        {{ __('Click here to add New Project') }}</p>
                                </a>
                            @endauth
                        </div>
                        <hr class="mt-3" style="border: 1px solid black; opacity: 0.100; width: 95%">
                        <div class="mt-3">

                        </div>
                        <div class="mt-4 ms-4">
                            <h5>{{ __('Sort by') }}</h5>
                            @foreach ($project_type as $types)
                                <div class="d-flex">
                                    <a href="#" class="zoom m-2" style="background-color: transparent;">
                                        <img class="iconFilter" src="{{ asset('assets/img/' . $types->name . '.png') }}"
                                            alt="{{ $types->name }}">

                                        <small class="text-muted m-1">
                                            <b>{{ $types->name }}</b>
                                        </small>
                                    </a>
                                </div>
                            @endforeach
                            <div class="d-flex">
                                <a href="#" class="zoom m-2" style="background-color: transparent;">

                                    <i class="fa-regular fa-building fa-2xl m-2 text-primary"></i>
                                    <small class="text-muted m-2">
                                        <b>{{ __('Country') }}</b>
                                    </small>
                                </a>
                            </div>
                            <div class="d-flex">
                                <a href="#" class="zoom m-2 mt-3" style="background-color: transparent;">

                                    <i class="fa-solid fa-location-dot fa-2xl m-2 text-success"></i>
                                    <small class="text-muted m-2">
                                        <b>{{ __('Branch') }}</b>
                                    </small>
                                </a>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-3" style="border: 1px solid black; opacity: 0.100; width: 95%">
                    <div class="mt-2">
                        <h5>
                            {{ __('My projects') }}...
                        </h5>
                        <div style="width: 95%;">
                            <div class="card mb-2 zoom projects" style="border-radius: 10px;">
                                <p class="m-3">Formación Ingenieros
                                </p>
                            </div>
                            <div class="card mb-2 zoom projects" style="border-radius: 10px;">
                                <p class="m-3">Registro Oficina</p>
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
@push('scripts')
    <script src="{{ asset('assets/custom/js/isotope.pkgd.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var $buttons = $('.status-filter button');

            $buttons.click(function() {
                $buttons.removeClass('active');
                $(this).addClass('active');

                var data = $(this).attr('data-filter');
                $grid.isotope({
                    filter: data
                });
            });

            var $grid = $(".grid").isotope({
                itemSelector: ".All",
                percentPosition: true,
                masonry: {
                    columnWidth: ".All"
                }
            });
        });
    </script>
@endpush
