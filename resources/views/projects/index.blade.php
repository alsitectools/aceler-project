@extends('layouts.admin')
@section('page-title')
    {{ __('Projects') }}
@endsection
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Projects') }}</li>
@endsection
@php
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp
@section('action-button')
    <div class="d-flex justify-content-end">
        @auth('web')
            <div class="col-sm-auto" style="padding-right: 1%">
                <button type="button" class="add_project btn btn-primary" data-ajax-popup="true"
                    data-title="{{ __('Create New Project') }}"
                    data-url="{{ route('projects.create', $currentWorkspace->slug) }}"> <i class="fa-solid fa-plus"
                        style="color: #ffffff;"></i>
                    {{ __('messages.Add_Project') }}

                </button>
            </div>
            <div class="col-sm-auto">
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
        @if ($projects && $currentWorkspace)
            <div class="filters-content">
                <div class="row grid">
                    @foreach ($projects as $project)
                        <div class="col-xl-3 col-lg-4 col-sm-6 All {{ $project->status }}">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <div style="padding-left: 68%">
                                        @if ($project->status == 'Finished')
                                            <span title="{{ __('dictionary.Status') }}" data-toggle="tooltip"
                                                data-placement="top"
                                                class="badge rounded-pill bg-success">{{ __('Finished') }}</span>
                                        @elseif($project->status == 'Ongoing')
                                            <span data-toggle="tooltip" data-placement="top"
                                                title="{{ __('dictionary.Status') }}"
                                                class="badge rounded-pill bg-secondary">{{ __('Ongoing') }}</span>
                                        @else
                                            <span data-toggle="tooltip" data-placement="top"
                                                title="{{ __('dictionary.Status') }}"
                                                class="badge rounded-pill bg-warning">{{ __('OnHold') }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center" style="min-width: 100px;">
                                        @if ($project->is_active)
                                            <a href="@auth('web'){{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}@endauth"
                                                class="" data-toggle="tooltip" data-placement="top"
                                                title="{{ $project->name }}">
                                                <img alt="{{ $project->name }}" class="me-2 fix_img"
                                                    avatar="{{ $project->name }}">
                                            </a>
                                        @else
                                            <a href="#" class="">
                                                <img alt="{{ $project->name }}" class="me-2 fix_img"
                                                    avatar="{{ $project->name }}">
                                            </a>
                                        @endif
                                        <h5 class="mb-0 mt-0">
                                            @if ($project->is_active)
                                                <a data-toggle="tooltip" title="{{ $project->name }}" data-placement="top"
                                                    href="@auth('web'){{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}@endauth"
                                                    title="{{ $project->name }}" class="">{{ $project->name }}</a>
                                            @else
                                                <a href="#" title="{{ __('Locked') }}"
                                                    class="">{{ $project->name }}</a>
                                            @endif
                                        </h5>
                                    </div>

                                    @if (($project->is_active && Auth::user()->type == 'admin') || $project->created_by == Auth::user()->id)
                                        <div class="card-header-right">
                                            <div class="btn-group card-option">
                                                @auth('web')
                                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">

                                                        <a href="#"
                                                            class="dropdown-item text-danger delete-popup bs-pass-para"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ trans('messages.This_action_can_not_be_undone._Do_you_want_to_continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $project->id }}">
                                                            <i class="ti ti-trash"></i> <span>{{ __('Delete') }}</span>
                                                        </a>
                                                        <form id="delete-form-{{ $project->id }}"
                                                            action="{{ route('projects.destroy', [$currentWorkspace->slug, $project->id]) }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </div>
                                                @endauth
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body pb-3 pt-3">
                                    <div class="text-center d-flex">
                                        <div class="mb-0 mt-0 mr-0 ml-0 col-3 location text-m" data-toggle="tooltip"
                                            data-placement="top" title="{{ __('dictionary.Company') }}">
                                            <i class="fa-regular fa-building fa-xl"
                                                style="color: #aa182c; margin: 10px;"></i>
                                            {{ $currentWorkspace->name }}
                                        </div>
                                        <div class="mb-1 mt-0 col-5 location text-m" data-toggle="tooltip"
                                            data-placement="top" title="{{ __('dictionary.Branch') }}">
                                            <i class="fa-solid fa-location-dot fa-xl"
                                                style="color: #aa182c; margin: 10px;"></i>
                                            {{ 'Montacada i Reixach' }}
                                        </div>
                                        {{-- mostrar 3 de los miembros del projecto actual --}}
                                        <div class="col-4 user-group">
                                            @if ($users = $project->users)
                                                @foreach ($users as $key => $user)
                                                    @if ($key < 2)
                                                        <a href="#" class="img_group" data-toggle="tooltip"
                                                            data-placement="top" title="{{ $user->name }}">
                                                            <img alt="{{ $user->name }}" class="iconUSer"
                                                                @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif>
                                                        </a>
                                                    @endif
                                                @endforeach
                                                @if (count($users) > 2)
                                                    <a href="#" class="img_group">
                                                        <img alt="image" data-toggle="tooltip"
                                                            data-original-title="{{ count($users) - 2 }} {{ __('more') }}"
                                                            avatar="+ {{ count($users) - 2 }}">
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card mb-0 mt-1">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                @if (isset($project_type[$project->type - 1]) && $project_type[$project->type - 1]->id == $project->type)
                                                    <div class="col-6">
                                                        <h6 class="text-muted mb-0" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="{{ __('dictionary.Project_type') }}">
                                                            <b>{{ $project_type[$project->type - 1]->name }}</b>
                                                        </h6>
                                                        <span class="text-muted" data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="{{ __('Ref. M.O') }}"><b>{{ $project->ref_mo }}</b></span>
                                                    </div>
                                                    <div class="col-6 text-end" data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="{{ $project_type[$project->type - 1]->name }}">
                                                        <img class="img-fluid" width="40px"
                                                            src="{{ asset('assets/img/' . $project_type[$project->type - 1]->name . '.png') }}"
                                                            alt="Project type">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
    </section>
@endsection

@push('css-page')
@endpush
<style>
    .btn_tab:not(.active) {
        opacity: 0.7;
    }

    .add_project {
        justify-items: center;
        height: 37px;
        width: 200px;
        text-align: center;
        border-radius: 7px;
        font-weight: bold;
        display: flex !important;
        justify-content: space-evenly !important;
        align-items: center;
    }

    .location {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-items: center;
        margin: 2%;
        min-height: 60px;
    }

    .iconUser {
        border-radius: 50%;
        width: 30px;
        height: 30px;
        padding-bottom: 2%;

    }
</style>

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
