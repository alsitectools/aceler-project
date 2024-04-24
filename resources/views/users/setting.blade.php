@extends('layouts.admin')
@php
    $languages = \App\Models\Utility::languages();
    $logo = \App\Models\Utility::get_file('logo/');
    if (Auth::user()->type == 'admin') {
        $setting = App\Models\Utility::getAdminPaymentSettings();
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = $setting['site_rtl'];
    } else {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $settings = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
    }

    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSettings();
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }
@endphp


@section('page-title', __('Settings'))
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Settings') }}</li>
@endsection
<style type="text/css">
    .row>* {
        flex-shrink: 0;
        /* width: 100%; */
        width: none !important;
        max-width: 100% !important;
        padding-right: calc(var(--bs-gutter-x) * .5);
        padding-left: calc(var(--bs-gutter-x) * .5);
        margin-top: var(--bs-gutter-y);
        /* width: auto; */
    }
</style>
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            {{-- modificado --}}
                            @if (Auth::user()->type == 'admin')
<<<<<<< HEAD
                                {{-- <a href="#workspace-settings"
                                    class="list-group-item list-group-item-action border-0 ">{{ trans('messages.Workspace_Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> --}}
                                {{-- <a href="#task-stage-settings"
                                    class="list-group-item list-group-item-action border-0 ">{{ trans('messages.Task_Stage_Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> --}}
                                {{-- <a href="#bug-stage-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Bug Stage Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> --}}
=======
                                <a href="#workspace-settings"
                                    class="list-group-item list-group-item-action border-0 ">{{ trans('messages.Workspace_Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#task-stage-settings"
                                    class="list-group-item list-group-item-action border-0 ">{{ trans('messages.Task_Stage_Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#bug-stage-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Bug Stage Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
>>>>>>> production
                                <a href="#tax-settings"
                                    class="list-group-item list-group-item-action border-0">{{ trans('messages.Tax_Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#company-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                {{-- <a href="#payment-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a> --}}
                                {{-- 
                                <a href="#invoice-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Invoice Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div></a> --}}
                                {{-- 
                                <a href="#email-notification-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> --}}
                                {{-- 
                            <a href="#time-tracker-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Time Tracker Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a> --}}

                                {{-- <a href="#zoom-meeting-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Zoom Meeting Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a> --}}

                                {{-- <a href="#slack-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Slack Settings') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a> --}}
                                {{-- <a href="#telegram-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Telegram Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> --}}
                                <a href="#google-calender"
                                    class="list-group-item list-group-item-action border-0">{{ __('Google Calender') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>

                                {{-- <a href="#webhook-settings"
                                   class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a> --}}

<<<<<<< HEAD
                                {{-- <a href="#email-settings"
                                    class="list-group-item list-group-item-action dash-link border-0">{{ __('Email Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a> --}}
=======
                                <a href="#email-settings"
                                    class="list-group-item list-group-item-action dash-link border-0">{{ __('Email Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
>>>>>>> production
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="workspace-settings" class="">
                        {{ Form::open(['route' => ['workspace.settings.store', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="row">
<<<<<<< HEAD
                            {{-- <div class="col-12">
=======
                            <div class="col-12">
>>>>>>> production
                                <div class="card ">
                                    <div class="card-header">
                                        <h5>{{ trans('messages.Workspace_Settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Dark Logo') }}</h5>

                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
<<<<<<< HEAD
                                                                <img src="@if ($currentWorkspace->logo) {{ $logo . $currentWorkspace->logo  .'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')}} @else{{ $logo . 'logo-light.png' }} @endif"
                                                                    class="small_logo" id="dark_logo" style="filter: drop-shadow(2px 3px 7px #011c4b);"/>
=======
                                                                {{-- <img src="@if ($currentWorkspace->logo) {{ $logo . $currentWorkspace->logo  .'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')}} @else{{ $logo . 'logo-light.png' }} @endif"
                                                                    class="small_logo" id="dark_logo" style="filter: drop-shadow(2px 3px 7px #011c4b);"/> --}}
>>>>>>> production
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="logo">

                                                                    <div class=" bg-primary"
                                                                        style="cursor: pointer;transform: translateY(+110%);">
                                                                        <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file"
                                                                        class="form-control choose_file_custom"
                                                                        name="logo" id="logo"
                                                                        data-filename="edit-logo">
                                                                </label>
                                                                <p class="edit-logo"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card ">
                                                        <div class="card-header">
                                                            <h5>{{ __('Light Logo') }}</h5>

                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
<<<<<<< HEAD
                                                                <img src="@if ($currentWorkspace->logo_white) {{ $logo . $currentWorkspace->logo_white .'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '') }} @else{{ $logo . 'logo-dark.png' }} @endif"
                                                                    id="image" class="small_logo" style="filter: drop-shadow(2px 3px 7px #011c4b);" />
=======
                                                                {{-- <img src="@if ($currentWorkspace->logo_white) {{ $logo . $currentWorkspace->logo_white .'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '') }} @else{{ $logo . 'logo-dark.png' }} @endif"
                                                                    id="image" class="small_logo" style="filter: drop-shadow(2px 3px 7px #011c4b);" /> --}}
>>>>>>> production
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="logo_white">

                                                                    <div class=" bg-primary"
                                                                        style="cursor: pointer;transform: translateY(+110%);">
                                                                        <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file"
                                                                        class="form-control choose_file_custom"
                                                                        name="logo_white" id="logo_white"
                                                                        data-filename="edit-logo_white">
                                                                </label>
                                                                <p class="edit-logo_white"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card ">
                                                        <div class="card-header">
                                                            <h5>{{ __('Favicon') }}</h5>

                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
<<<<<<< HEAD
                                                                <img src="@if ($currentWorkspace->favicon) {{ $logo . $currentWorkspace->favicon.'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '') }} @else{{ $logo . 'favicon.png' }} @endif"
                                                                    id="favicon" class="favicon"
                                                                    style="width:60px !important" />
=======
                                                                {{-- <img src="@if ($currentWorkspace->favicon) {{ $logo . $currentWorkspace->favicon.'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '') }} @else{{ $logo . 'favicon.png' }} @endif"
                                                                    id="favicon" class="favicon"
                                                                    style="width:60px !important" /> --}}
>>>>>>> production
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="small-favicon">

                                                                    <div class=" bg-primary"
                                                                        style="cursor: pointer;transform: translateY(+110%);">
                                                                        <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file"
                                                                        class="form-control choose_file_custom"
                                                                        name="favicon" id="small-favicon"
                                                                        data-filename="edit-favicon">
                                                                </label>
                                                                <p class="edit-favicon"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                                    {{ Form::text('name', $currentWorkspace->name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                                    @error('name')
                                                        <span class="invalid-name" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                @php
                                                    $DEFAULT_LANG = $currentWorkspace->lang ? $currentWorkspace->lang : 'en';
<<<<<<< HEAD
                                                @endphp 
                                                <div class="form-group">
                                                    {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                    <div class="changeLanguage">

                                                        <select name="default_language" id="default_language"
                                                           class="form-control select2">
=======
                                                @endphp
                                                <div class="form-group">
                                                    {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                    <div class="changeLanguage">
                                                        {{-- <select name="default_language" id="default_language"
                                                            class="form-control select2">
                                                            @foreach (\App\Models\Utility::languages() as $lang)
                                                                <option value="{{ $lang }}"
                                                                    @if ($DEFAULT_LANG == $lang) selected @endif>
                                                                    {{ ucfirst( \App\Models\Utility::getlang_fullname($lang)) }}
                                                                </option>
                                                            @endforeach
                                                        </select> --}}

                                                        <select name="default_language" id="default_language"
                                                            class="form-control select2">
                                                            {{-- @foreach (\App\Models\Utility::languages() as $lang)
                                                            <option value="{{ $lang }}"
                                                                @if ($DEFAULT_LANG == $lang) selected @endif>
                                                                {{ ucfirst( \App\Models\Utility::getlang_fullname($lang)) }}
                                                            </option>
                                                        @endforeach --}}

>>>>>>> production
                                                            @foreach ($languages as $languageCode => $languageFullName)
                                                                <option value="{{ $languageCode }}"
                                                                    @if ($DEFAULT_LANG == $languageCode) selected @endif>
                                                                    {{ $languageFullName }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="small-title mb-4">{{ trans('messages.Theme_Customizer') }}</h4>
                                            <div class="col-12">
                                                <div class="pct-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <h6 class="">
                                                                <i data-feather="credit-card"
                                                                    class="me-2"></i>{{ trans('messages.Primary_color_settings') }}
                                                            </h6>
                                                            <hr class="my-2" />

                                                            <div class="theme-color themes-color">
                                                                <input type="hidden" name="color" id="color_value"
                                                                    value="{{ $color }}">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                                    data-value="theme-1"
                                                                    onclick="check_theme('theme-1')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-1" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-2' ? 'active_color' : '' }} "
                                                                    data-value="theme-2"
                                                                    onclick="check_theme('theme-2')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-2" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                                    data-value="theme-3"
                                                                    onclick="check_theme('theme-3')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-3" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                                    data-value="theme-4"
                                                                    onclick="check_theme('theme-4')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-4" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                                    data-value="theme-5"
                                                                    onclick="check_theme('theme-5')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-5" style="display: none;">
                                                                <br>
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                                    data-value="theme-6"
                                                                    onclick="check_theme('theme-6')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-6" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                                    data-value="theme-7"
                                                                    onclick="check_theme('theme-7')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-7" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                                    data-value="theme-8"
                                                                    onclick="check_theme('theme-8')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-8" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                                    data-value="theme-9"
                                                                    onclick="check_theme('theme-9')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-9" style="display: none;">
                                                                <a href="#!"
                                                                    class="{{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                                    data-value="theme-10"
                                                                    onclick="check_theme('theme-10')"></a>
                                                                <input type="radio" class="theme_color" name="color"
                                                                    value="theme-10" style="display: none;">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h6 class="">
                                                                <i data-feather="layout"
                                                                    class="me-2"></i>{{ trans('messages.Sidebar_settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-theme-bg" name="cust_theme_bg"
                                                                    @if ($cust_theme_bg == 'on') checked @endif />
                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-theme-bg">{{ trans('messages.Transparent_layout') }}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h6 class="">
                                                                <i data-feather="sun"
                                                                    class=""></i>{{ trans('messages.Layout_settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch mt-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-darklayout" name="cust_darklayout"
                                                                    @if ($dark_mode == 'on') checked @endif />

                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-darklayout">{{ trans('messages.Dark_Layout') }}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="col switch-width">
                                                                <div class="form-group ml-2 mr-3 ">
                                                                    <label
                                                                        class="form-label mb-1">{{ __('Enable RTL') }}</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="site_rtl" id="site_rtl"
                                                                            {{ !empty($SITE_RTL) && $SITE_RTL == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="site_rtl"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end mt-2">
                                                <input type="submit" value="{{ __('Save Changes') }}"
                                                    class="btn btn-primary">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
<<<<<<< HEAD
                        </div> --}}
                            {{ Form::close() }}
                        </div>

                        {{-- <div id="task-stage-settings" class="">
=======
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="task-stage-settings" class="">
>>>>>>> production
                        <div class="">
                            <div class="col-md-12">
                                <div class="card task-stages" data-value="{{ json_encode($stages) }}"
                                    style="overflow-x: auto">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="pb-2">
                                                    {{ trans('messages.Task_Stage_Settings') }}

                                                </h5>
                                                <small
                                                    class="">{{ trans('messages.System_will_consider_the_last_stage_as_a_completed/done_project_or_task_status.') }}</small>
                                            </div>
                                            <div class="col-auto text-end">

                                                <button data-repeater-create type="button"
                                                    class="btn-submit btn btn-sm btn-primary btn-icon "
                                                    data-toggle="tooltip" title="{{ __('Add') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <form method="post"
                                            action="{{ route('stages.store', $currentWorkspace->slug) }}">
                                            @csrf
                                            <table class="table table-hover" data-repeater-list="stages">
                                                <thead>
                                                    <th>
                                                        <div data-toggle="tooltip" data-placement="left"
                                                            data-title="{{ __('Drag Stage to Change Order') }}"
                                                            data-original-title="" title="">
                                                            <i class="fas fa-crosshairs"></i>
                                                        </div>
                                                    </th>
                                                    <th>{{ __('Color') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th class="text-right">{{ __('Delete') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr data-repeater-item>
                                                        <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                                        <td>
                                                            <input type="color" name="color">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id" id="id" />
                                                            <input type="text" name="name"
                                                                class="form-control mb-0" required />
                                                        </td>
                                                        <td class="text-right ">
                                                            <a data-repeater-delete
                                                                class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end pt-2">
                                                <button class="btn-submit btn btn-primary"
                                                    type="submit">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD
                    </div> --}}

                        {{-- <div id="bug-stage-settings" class="tab-pane">
=======
                    </div>

                    <div id="bug-stage-settings" class="tab-pane">
>>>>>>> production
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card bug-stages" data-value="{{ json_encode($bugStages) }}"
                                    style="overflow-x: auto">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="pb-2">
                                                    {{ __('Bug Stage Settings') }}

                                                </h5>
                                                <small
                                                    class="">{{ trans('messages.System_will_consider_the_last_stage_as_a_completed/done_project_or_bug_status.') }}</small>
                                            </div>
                                            <div class=" col-auto text-end">
                                                <button data-repeater-create type="button"
                                                    class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                                                    title="{{ __('Add') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form method="post"
                                            action="{{ route('bug.stages.store', $currentWorkspace->slug) }}">
                                            @csrf
                                            <table class="table table-hover" data-repeater-list="stages">
                                                <thead>
                                                    <th>
                                                        <div data-toggle="tooltip" data-placement="left"
                                                            data-title="{{ __('Drag Stage to Change Order') }}"
                                                            data-original-title="" title="">
                                                            <i class="fas fa-crosshairs"></i>
                                                        </div>
                                                    </th>
                                                    <th>{{ __('Color') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th class="text-right">{{ __('Delete') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr data-repeater-item>
                                                        <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                                        <td>
                                                            <input type="color" name="color">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id" id="id" />
                                                            <input type="text" name="name"
                                                                class="form-control mb-0" required />
                                                        </td>
                                                        <td class="text-right">
                                                            <a data-repeater-delete
                                                                class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end pt-2">
                                                <button class="btn-submit btn btn-primary"
                                                    type="submit">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD
                    </div> --}}

                        <div id="tax-settings" class="">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-11">
                                                    <h5 class="">
                                                        {{ trans('messages.Tax_Settings') }}
                                                    </h5>
                                                </div>
                                                <div class="text-end  col-auto">
                                                    <button class="btn-submit btn btn-sm btn-primary" type="button"
                                                        data-ajax-popup="true" data-title="{{ __('Add Tax') }}"
                                                        data-url="{{ route('tax.create', $currentWorkspace->slug) }}"
                                                        data-toggle="tooltip" title="{{ __('Add Tax') }}">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">

                                                <table id="" class="table table-bordered px-2">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Name') }}</th>
                                                            <th>{{ __('Rate') }}</th>
                                                            <th width="200px" class="text-right">{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($taxes as $tax)
                                                            <tr>
                                                                <td>{{ $tax->name }}</td>
                                                                <td>{{ $tax->rate }}%</td>
                                                                <td class="text-right">
                                                                    <a href="#"
                                                                        class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true"
                                                                        data-title="{{ __('Edit Tax') }}"
                                                                        data-url="{{ route('tax.edit', [$currentWorkspace->slug, $tax->id]) }}"
                                                                        data-toggle="tooltip" title="{{ __('Edit Tax') }}">
                                                                        <i class="ti ti-pencil text-white"></i>
                                                                    </a>
                                                                    <a href="#"
                                                                        class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-form-{{ $tax->id }}"data-toggle="tooltip"
                                                                        title="{{ __('Delete') }}">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                    <form id="delete-form-{{ $tax->id }}"
                                                                        action="{{ route('tax.destroy', [$currentWorkspace->slug, $tax->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
=======
                    </div>

                    <div id="tax-settings" class="">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="">
                                                    {{ trans('messages.Tax_Settings') }}
                                                </h5>
                                            </div>
                                            <div class="text-end  col-auto">
                                                <button class="btn-submit btn btn-sm btn-primary" type="button"
                                                    data-ajax-popup="true" data-title="{{ __('Add Tax') }}"
                                                    data-url="{{ route('tax.create', $currentWorkspace->slug) }}"
                                                    data-toggle="tooltip" title="{{ __('Add Tax') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">

                                            <table id="" class="table table-bordered px-2">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Name') }}</th>
                                                        <th>{{ __('Rate') }}</th>
                                                        <th width="200px" class="text-right">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($taxes as $tax)
                                                        <tr>
                                                            <td>{{ $tax->name }}</td>
                                                            <td>{{ $tax->rate }}%</td>
                                                            <td class="text-right">
                                                                <a href="#"
                                                                    class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('Edit Tax') }}"
                                                                    data-url="{{ route('tax.edit', [$currentWorkspace->slug, $tax->id]) }}"
                                                                    data-toggle="tooltip" title="{{ __('Edit Tax') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                                <a href="#"
                                                                    class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form-{{ $tax->id }}"data-toggle="tooltip"
                                                                    title="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                                <form id="delete-form-{{ $tax->id }}"
                                                                    action="{{ route('tax.destroy', [$currentWorkspace->slug, $tax->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
>>>>>>> production
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD

                        <div id="company-settings" class="tab-pane">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Company Settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            <form method="post"
                                                action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                                class="payment-form">
                                                @csrf
                                                <div class="row mt-3">
                                                    <div class="form-group col-md-6">
                                                        <label for="company"
                                                            class="form-label">{{ __('Name') }}</label>
                                                        <input type="text" name="company" id="company"
                                                            class="form-control" value="{{ $currentWorkspace->company }}"
                                                            required="required" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="address"
                                                            class="form-label">{{ trans('messages.Address') }}</label>
                                                        <input type="text" name="address" id="address"
                                                            class="form-control" value="{{ $currentWorkspace->address }}"
                                                            required="required" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="city"
                                                            class="form-label">{{ __('City') }}</label>
                                                        <input class="form-control" name="city" type="text"
                                                            value="{{ $currentWorkspace->city }}" id="city">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="state"
                                                            class="form-label">{{ trans('messages.State') }}</label>
                                                        <input class="form-control" name="state" type="text"
                                                            value="{{ $currentWorkspace->state }}" id="state">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="zipcode"
                                                            class="form-label">{{ trans('messages.Zip/Post_Code') }}</label>
                                                        <input class="form-control" name="zipcode" type="text"
                                                            value="{{ $currentWorkspace->zipcode }}" id="zipcode">
                                                    </div>
                                                    <div class="form-group  col-md-6">
                                                        <label for="country"
                                                            class="form-label">{{ __('Country') }}</label>
                                                        <input class="form-control" name="country" type="text"
                                                            value="{{ $currentWorkspace->country }}" id="country">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="telephone"
                                                            class="form-label">{{ trans('messages.Telephone') }}</label>
                                                        <input class="form-control" name="telephone" type="text"
                                                            value="{{ $currentWorkspace->telephone }}" id="telephone">
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit"
                                                        class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                                </div>
                                            </form>
                                        </div>
=======
                    </div>

                    <div id="company-settings" class="tab-pane">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Company Settings') }}
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post"
                                            action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                            class="payment-form">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="form-group col-md-6">
                                                    <label for="company" class="form-label">{{ __('Name') }}</label>
                                                    <input type="text" name="company" id="company"
                                                        class="form-control" value="{{ $currentWorkspace->company }}"
                                                        required="required" />
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="address"
                                                        class="form-label">{{ trans('messages.Address') }}</label>
                                                    <input type="text" name="address" id="address"
                                                        class="form-control" value="{{ $currentWorkspace->address }}"
                                                        required="required" />
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                                    <input class="form-control" name="city" type="text"
                                                        value="{{ $currentWorkspace->city }}" id="city">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="state"
                                                        class="form-label">{{ trans('messages.State') }}</label>
                                                    <input class="form-control" name="state" type="text"
                                                        value="{{ $currentWorkspace->state }}" id="state">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="zipcode"
                                                        class="form-label">{{ trans('messages.Zip/Post_Code') }}</label>
                                                    <input class="form-control" name="zipcode" type="text"
                                                        value="{{ $currentWorkspace->zipcode }}" id="zipcode">
                                                </div>
                                                <div class="form-group  col-md-6">
                                                    <label for="country" class="form-label">{{ __('Country') }}</label>
                                                    <input class="form-control" name="country" type="text"
                                                        value="{{ $currentWorkspace->country }}" id="country">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="telephone"
                                                        class="form-label">{{ trans('messages.Telephone') }}</label>
                                                    <input class="form-control" name="telephone" type="text"
                                                        value="{{ $currentWorkspace->telephone }}" id="telephone">
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <button type="submit"
                                                    class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
>>>>>>> production
                                    </div>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD
                        {{-- 
=======
                    </div>
                    {{-- 
>>>>>>> production
                    <div class="" id="email-notification-settings">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Email Notification Settings') }}</h5>
                            </div>
                            <div class="card-body table-border-style ">
                                <div class="col-md-12">

                                    <form method="post"
                                        action="{{ route('status.email.language', $currentWorkspace->slug) }}"
                                        class="payment-form row m-1">
                                        @csrf
                                        @foreach ($EmailTemplates as $EmailTemplate)
                                            <div
                                                class="col-md-6  d-flex align-items-center justify-content-between list_colume_notifi">
                                                <div class="mb-3 mb-sm-0">
                                                    <h6>{{ $EmailTemplate->name }}
                                                    </h6>
                                                </div>
                                                <div class="text-end">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input type="checkbox" class=" form-check-input"
                                                            name="{{ $EmailTemplate->name }}"
                                                            id="email_tempalte_{{ $EmailTemplate->template ? $EmailTemplate->template->id : '' }}"
                                                            @if ($EmailTemplate->template ? $EmailTemplate->template->is_active == 1 : '') checked="checked" @endif
                                                            type="checkbox" value={{ $EmailTemplate->template->id }}>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="text-end py-3">
                                            <button type="submit"
                                                class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> --}}


<<<<<<< HEAD
                        @if (Auth::user()->type == 'user')
                            <div class="" id="slack-settings">
                                {{ Form::open(['route' => ['workspace.settings.Slack', $currentWorkspace->slug], 'method' => 'post', 'class' => 'd-contents']) }}
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="">
                                                    {{ __('Slack Settings') }}
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row company-setting">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                        {{ Form::label('Slack Webhook URL', __('Slack Webhook URL'), ['class' => 'form-label']) }}
                                                        {{ Form::text('slack_webhook', isset($payment_detail['slack_webhook']) ? $payment_detail['slack_webhook'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Slack Webhook URL'), 'required' => 'required']) }}

                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group mb-3">
                                                        {{ Form::label('Module Settings', __('Module Settings'), ['class' => 'form-label']) }}
                                                    </div>


                                                    <div class="col-md-4">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> {{ Form::label('New Project', __('New Project'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('project_notificaation', '1', isset($payment_detail['project_notificaation']) && $payment_detail['project_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'project_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> {{ Form::label('New Task', __('New Task'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('task_notificaation', '1', isset($payment_detail['task_notificaation']) && $payment_detail['task_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'task_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6>{{ Form::label('Task Stage Updated', __('Task Stage Updated'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('taskmove_notificaation', '1', isset($payment_detail['taskmove_notificaation']) && $payment_detail['taskmove_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskmove_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> {{ Form::label('New Milestone', __('New Milestone'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('milestone_notificaation', '1', isset($payment_detail['milestone_notificaation']) && $payment_detail['milestone_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestone_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> {{ Form::label('Milestone Status Updated', __('Milestone Status Updated'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('milestonest_notificaation', '1', isset($payment_detail['milestonest_notificaation']) && $payment_detail['milestonest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestonest_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> {{ Form::label('New Task Comment', __('New Task Comment'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('taskcom_notificaation', '1', isset($payment_detail['taskcom_notificaation']) && $payment_detail['taskcom_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskcom_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6>{{ Form::label('New Invoice', __('New Invoice'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('invoice_notificaation', '1', isset($payment_detail['invoice_notificaation']) && $payment_detail['invoice_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> {{ Form::label('Invoice Status Updated', __('Invoice Status Updated'), ['class' => 'form-label']) }}
                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    {{ Form::checkbox('invoicest_notificaation', '1', isset($payment_detail['invoicest_notificaation']) && $payment_detail['invoicest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoicest_notificaation']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class=" text-end">
                                                        {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
=======
                    @if (Auth::user()->type == 'user')
                        <div class="" id="slack-settings">
                            {{ Form::open(['route' => ['workspace.settings.Slack', $currentWorkspace->slug], 'method' => 'post', 'class' => 'd-contents']) }}
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Slack Settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row company-setting">
                                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                    {{ Form::label('Slack Webhook URL', __('Slack Webhook URL'), ['class' => 'form-label']) }}
                                                    {{ Form::text('slack_webhook', isset($payment_detail['slack_webhook']) ? $payment_detail['slack_webhook'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Slack Webhook URL'), 'required' => 'required']) }}

                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 form-group mb-3">
                                                    {{ Form::label('Module Settings', __('Module Settings'), ['class' => 'form-label']) }}
                                                </div>


                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Project', __('New Project'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('project_notificaation', '1', isset($payment_detail['project_notificaation']) && $payment_detail['project_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'project_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Task', __('New Task'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('task_notificaation', '1', isset($payment_detail['task_notificaation']) && $payment_detail['task_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'task_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('Task Stage Updated', __('Task Stage Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('taskmove_notificaation', '1', isset($payment_detail['taskmove_notificaation']) && $payment_detail['taskmove_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskmove_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Milestone', __('New Milestone'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('milestone_notificaation', '1', isset($payment_detail['milestone_notificaation']) && $payment_detail['milestone_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestone_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('Milestone Status Updated', __('Milestone Status Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('milestonest_notificaation', '1', isset($payment_detail['milestonest_notificaation']) && $payment_detail['milestonest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestonest_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Task Comment', __('New Task Comment'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('taskcom_notificaation', '1', isset($payment_detail['taskcom_notificaation']) && $payment_detail['taskcom_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskcom_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('New Invoice', __('New Invoice'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('invoice_notificaation', '1', isset($payment_detail['invoice_notificaation']) && $payment_detail['invoice_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('Invoice Status Updated', __('Invoice Status Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('invoicest_notificaation', '1', isset($payment_detail['invoicest_notificaation']) && $payment_detail['invoicest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoicest_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" text-end">
                                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    @endif


                    @if (Auth::user()->type == 'user' || Auth::user()->type == 'admin')
                        <div class="" id="google-calender">
                            <div class="card">
                                {{ Form::open(['route' => ['google.calender.settings', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                <div class="card-header">
                                    <div class="row justify-content-between">
                                        <div class="col-10">
                                            <h5 class="">{{ __('Google Calendar') }}</h5>
                                        </div>
                                        <div class=" text-end  col-auto">
                                            <div class="col switch-width">
                                                <div class="form-group ml-2 mr-3 ">
                                                    <div class="custom-control custom-switch">

                                                        <input type="checkbox" data-toggle="switchbutton"
                                                            data-onstyle="primary" class=""
                                                            name="is_googlecalendar_enabled"
                                                            id="is_googlecalendar_enabled"
                                                            {{ isset($currentWorkspace->is_googlecalendar_enabled) && $currentWorkspace->is_googlecalendar_enabled == 'on' ? 'checked' : '' }}>
>>>>>>> production
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
<<<<<<< HEAD
                                {{ Form::close() }}
                            </div>
                        @endif


                        @if (Auth::user()->type == 'user' || Auth::user()->type == 'admin')
                            <div class="" id="google-calender">
                                <div class="card">
                                    {{ Form::open(['route' => ['google.calender.settings', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                    <div class="card-header">
                                        <div class="row justify-content-between">
                                            <div class="col-10">
                                                <h5 class="">{{ __('Google Calendar') }}</h5>
                                            </div>
                                            <div class=" text-end  col-auto">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        <div class="custom-control custom-switch">

                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class=""
                                                                name="is_googlecalendar_enabled"
                                                                id="is_googlecalendar_enabled"
                                                                {{ isset($currentWorkspace->is_googlecalendar_enabled) && $currentWorkspace->is_googlecalendar_enabled == 'on' ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                                {{ Form::label('Google calendar id', __('Google Calendar Id'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('google_calender_id', !empty($currentWorkspace['google_calender_id']) ? $currentWorkspace['google_calender_id'] : '', ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id']) }}
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                                {{ Form::label('Google calendar json file', __('Google Calendar json File'), ['class' => 'col-form-label']) }}
                                                <input type="file" class="form-control"
                                                    name="google_calender_json_file" id="google_calender_json_file">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <button class="btn-submit btn btn-primary" type="submit">
                                            {{ __('Save Changes') }}
                                        </button>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        @endif


                        {{-- <div id="email-settings" class="tab-pane">
=======
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                            {{ Form::label('Google calendar id', __('Google Calendar Id'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('google_calender_id', !empty($currentWorkspace['google_calender_id']) ? $currentWorkspace['google_calender_id'] : '', ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id']) }}
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                            {{ Form::label('Google calendar json file', __('Google Calendar json File'), ['class' => 'col-form-label']) }}
                                            <input type="file" class="form-control" name="google_calender_json_file"
                                                id="google_calender_json_file">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn-submit btn btn-primary" type="submit">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    @endif


                    <div id="email-settings" class="tab-pane">
>>>>>>> production
                        <div class="col-md-12">

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="">
                                        {{ __('Email Settings') }}
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    {{ Form::open(['route' => ['company.email.settings.store', $currentWorkspace->slug], 'method' => 'post']) }}
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_driver', $payment_detail['mail_driver'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver'), 'id' => 'mail_driver']) }}
                                            @error('mail_driver')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_host', $payment_detail['mail_host'], ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                                            @error('mail_host')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_port', $payment_detail['mail_port'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                                            @error('mail_port')
                                                <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_username', $payment_detail['mail_username'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                                            @error('mail_username')
                                                <span class="invalid-mail_username" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_password', $payment_detail['mail_password'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                                            @error('mail_password')
                                                <span class="invalid-mail_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_encryption', $payment_detail['mail_encryption'], ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                                            @error('mail_encryption')
                                                <span class="invalid-mail_encryption" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_address', $payment_detail['mail_from_address'], ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                                            @error('mail_from_address')
                                                <span class="invalid-mail_from_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-6 form-group">
                                            {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_name', $payment_detail['mail_from_name'], ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                                            @error('mail_from_name')
                                                <span class="invalid-mail_from_name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="col-lg-12 ">
                                        <div class="row">

                                            <div class="text-start text-light col-6">
                                                <a data-size="md" data-url="{{ route('test.email') }}"
                                                    data-title="{{ __('Send Test Mail') }}"
                                                    class="btn  btn-primary send_email">
                                                    {{ __('Send Test Mail') }}
                                                </a>

                                            </div>
                                            <div class="text-end col-6">
                                                <input type="submit" value="{{ __('Save Changes') }}"
                                                    class="btn-submit btn btn-primary">
                                            </div>

                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>

<<<<<<< HEAD
                </div> --}}
                    </div>
                    <!-- [ sample-page ] end -->
                </div>
                <!-- [ Main Content ] end -->
            </div>
        @endsection

        @push('scripts')
            <script src="{{ asset('assets/custom/js/jquery-ui.min.js') }}"></script>
            <script src="{{ asset('assets/custom/js/repeater.js') }}"></script>
            <script src="{{ asset('assets/custom/js/colorPick.js') }}"></script>
            <script>
                var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                    target: '#useradd-sidenav',
                    offset: 300
                })
            </script>
            <script>
                function check_theme(color_val) {
                    $('input[value="' + color_val + '"]').prop('checked', true);
                    $('input[value="' + color_val + '"]').attr('checked', true);
                    $('a[data-value]').removeClass('active_color');
                    $('a[data-value="' + color_val + '"]').addClass('active_color');
                }
                var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                    target: '#useradd-sidenav',
                    offset: 300
                })
            </script>

            <script>
                $(document).on("click", '.send_email', function(e) {
                    e.preventDefault();
                    var title = $(this).attr('data-title');

                    var size = 'md';
                    var url = $(this).attr('data-url');
                    if (typeof url != 'undefined') {
                        $("#commonModal .modal-title").html(title);
                        $("#commonModal .modal-dialog").addClass('modal-' + size);
                        $("#commonModal").modal('show');

                        $.post(url, {
                            mail_driver: $("#mail_driver").val(),
                            mail_host: $("#mail_host").val(),
                            mail_port: $("#mail_port").val(),
                            mail_username: $("#mail_username").val(),
                            mail_password: $("#mail_password").val(),
                            mail_encryption: $("#mail_encryption").val(),
                            mail_from_address: $("#mail_from_address").val(),
                            mail_from_name: $("#mail_from_name").val(),
                        }, function(data) {
                            $('#commonModal .body').html(data);
                        });
                    }
                });
                $(document).on('submit', '#test_email', function(e) {
                    e.preventDefault();
                    $("#email_sending").show();
                    var post = $(this).serialize();
                    var url = $(this).attr('action');
                    $.ajax({
                        type: "post",
                        url: url,
                        data: post,
                        cache: false,
                        beforeSend: function() {
                            $('#test_email .btn-create').attr('disabled', 'disabled');
                        },
                        success: function(data) {
                            if (data.is_success) {
                                show_toastr('Success', data.message, 'success');
                            } else {
                                show_toastr('Error', data.message, 'error');
                            }
                            $("#email_sending").hide();
                        },
                        complete: function() {
                            $('#test_email .btn-create').removeAttr('disabled');
                        },
                    });
                })
            </script>

            <script src="{{ asset('assets/js/pages/wow.min.js') }}"></script>
            <script>
                // Start [ Menu hide/show on scroll ]
                let ost = 0;
                document.addEventListener("scroll", function() {
                    let cOst = document.documentElement.scrollTop;
                    if (cOst == 0) {
                        //   document.querySelector(".navbar").classList.add("top-nav-collapse");
                    } else if (cOst > ost) {
                        document.querySelector(".navbar").classList.add("top-nav-collapse");
                        document.querySelector(".navbar").classList.remove("default");
                    } else {
                        document.querySelector(".navbar").classList.add("default");
                        document
                            .querySelector(".navbar")
                            .classList.remove("top-nav-collapse");
                    }
                    ost = cOst;
                });
                // End [ Menu hide/show on scroll ]
                var wow = new WOW({
                    animateClass: "animate__animated", // animation css class (default is animated)
                });
                wow.init();
                // var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                //   target: "#navbar-example",
                // });
            </script>
            <script>
                $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
                    var template = $("select[name='invoice_template']").val();
                    var color = $("input[name='invoice_color']:checked").val();
                    $('iframe').attr('src', '{{ url($currentWorkspace->slug . '/invoices/preview') }}/' + template + '/' +
                        color);
                });

                $(document).ready(function() {

                    var $dragAndDrop = $("body .task-stages tbody").sortable({
                        handle: '.sort-handler'
                    });

                    var $repeater = $('.task-stages').repeater({
                        initEmpty: true,
                        defaultValues: {},
                        show: function() {
                            $(this).slideDown();
                        },
                        hide: function(deleteElement) {
                            if (confirm('{{ __('Are you sure ?') }}')) {
                                $(this).slideUp(deleteElement);
                            }
                        },
                        ready: function(setIndexes) {
                            $dragAndDrop.on('drop', setIndexes);
                        },
                        isFirstItemUndeletable: true
                    });


                    var value = $(".task-stages").attr('data-value');
                    if (typeof value != 'undefined' && value.length != 0) {
                        value = JSON.parse(value);
                        $repeater.setList(value);
                    }

                    var $dragAndDropBug = $("body .bug-stages tbody").sortable({
                        handle: '.sort-handler'
                    });

                    var $repeaterBug = $('.bug-stages').repeater({
                        initEmpty: true,
                        defaultValues: {},
                        show: function() {
                            $(this).slideDown();
                        },
                        hide: function(deleteElement) {
                            if (confirm('{{ __('Are you sure ?') }}')) {
                                $(this).slideUp(deleteElement);
                            }
                        },
                        ready: function(setIndexes) {
                            $dragAndDropBug.on('drop', setIndexes);
                        },
                        isFirstItemUndeletable: true
                    });


                    var valuebug = $(".bug-stages").attr('data-value');
                    if (typeof valuebug != 'undefined' && valuebug.length != 0) {
                        valuebug = JSON.parse(valuebug);
                        $repeaterBug.setList(valuebug);
                    }
                    $(document).on('click', '.list-group-item', function() {
                        $('.list-group-item').removeClass('active');
                        $('.list-group-item').removeClass('text-primary');
                        setTimeout(() => {
                            $(this).addClass('active').removeClass('text-primary');
                        }, 10);
                    });

                    var type = window.location.hash.substr(1);
                    $('.list-group-item').removeClass('active');
                    $('.list-group-item').removeClass('text-primary');
                    if (type != '') {
                        $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
                    } else {
                        $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
                    }
                });
            </script>


            <script>
                $('#logo').change(function() {

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#dark_logo').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);

                });

                $('#logo_white').change(function() {

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);

                });

                $('#small-favicon').change(function() {

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#favicon').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);

                });
            </script>

            <script>
                $(document).ready(function() {
                    if ($('.gdpr_fulltime').is(':checked')) {
                        $('.fulltime').show();
                    } else {
                        $('.fulltime').hide();
                    }
                    $('#gdpr_cookie').on('change', function() {
                        if ($('.gdpr_fulltime').is(':checked')) {
                            $('.fulltime').show();
                        } else {
                            $('.fulltime').hide();
                        }
                    });
                });

                var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                    target: '#useradd-sidenav',
                    offset: 300
                })

                $('.themes-color-change').on('click', function() {
                    var color_val = $(this).data('value');
                    $('.theme-color').prop('checked', false);
                    $('.themes-color-change').removeClass('active_color');
                    $(this).addClass('active_color');
                    $(`input[value=${color_val}]`).prop('checked', true);

                });

                function check_theme(color_val) {
                    $('.theme-color').prop('checked', false);
                    $('input[value="' + color_val + '"]').prop('checked', true);
                    $('#color_value').val(color_val);
                }
            </script>

            <script>
                $(document).on("click", ".email-template-checkbox", function() {
                    var chbox = $(this);
                    $.ajax({
                        url: chbox.attr('data-url'),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            status: chbox.val()
                        },
                        type: 'POST',
                        success: function(response) {
                            if (response.is_success) {
                                show_toastr('{{ __('Success') }}', response.success, 'success');
                                if (chbox.val() == 1) {
                                    $('#' + chbox.attr('id')).val(0);
                                } else {
                                    $('#' + chbox.attr('id')).val(1);
                                }
                            } else {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', response, 'error');
                            }
                        }
                    })
                });
            </script>
            <script>
                function cust_theme_bg() {
                    var custthemebg = document.querySelector("#cust-theme-bg");

                    if (custthemebg.checked) {
                        document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                        document
                            .querySelector(".dash-header:not(.dash-mob-header)")
                            .classList.add("transprent-bg");
                    } else {
                        document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                        document
                            .querySelector(".dash-header:not(.dash-mob-header)")
                            .classList.remove("transprent-bg");
                    }

                }

                function cust_darklayout() {
                    var custdarklayout = document.querySelector("#cust-darklayout");

                    if (custdarklayout.checked) {
                        document
                            .querySelector(".m-header > .b-brand > .logo-lg")
                            .setAttribute("src", "{{ asset('assets/images/logo.svg') }}");
                        document
                            .querySelector("#main-style-link")
                            .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
                    } else {
                        document
                            .querySelector(".m-header > .b-brand > .logo-lg")
                            .setAttribute("src", "{{ asset('assets/images/logo-dark.svg') }}");
                        document
                            .querySelector("#main-style-link")
                            .setAttribute("href", "{{ asset('assets/css/style.css') }}");
                    }

                }
            </script>
        @endpush
=======
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/custom/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/custom/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/custom/js/colorPick.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
        function check_theme(color_val) {
            $('input[value="' + color_val + '"]').prop('checked', true);
            $('input[value="' + color_val + '"]').attr('checked', true);
            $('a[data-value]').removeClass('active_color');
            $('a[data-value="' + color_val + '"]').addClass('active_color');
        }
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

    <script>
        $(document).on("click", '.send_email', function(e) {
            e.preventDefault();
            var title = $(this).attr('data-title');

            var size = 'md';
            var url = $(this).attr('data-url');
            if (typeof url != 'undefined') {
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);
                $("#commonModal").modal('show');

                $.post(url, {
                    mail_driver: $("#mail_driver").val(),
                    mail_host: $("#mail_host").val(),
                    mail_port: $("#mail_port").val(),
                    mail_username: $("#mail_username").val(),
                    mail_password: $("#mail_password").val(),
                    mail_encryption: $("#mail_encryption").val(),
                    mail_from_address: $("#mail_from_address").val(),
                    mail_from_name: $("#mail_from_name").val(),
                }, function(data) {
                    $('#commonModal .body').html(data);
                });
            }
        });
        $(document).on('submit', '#test_email', function(e) {
            e.preventDefault();
            $("#email_sending").show();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,
                beforeSend: function() {
                    $('#test_email .btn-create').attr('disabled', 'disabled');
                },
                success: function(data) {
                    if (data.is_success) {
                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                    $("#email_sending").hide();
                },
                complete: function() {
                    $('#test_email .btn-create').removeAttr('disabled');
                },
            });
        })
    </script>

    <script src="{{ asset('assets/js/pages/wow.min.js') }}"></script>
    <script>
        // Start [ Menu hide/show on scroll ]
        let ost = 0;
        document.addEventListener("scroll", function() {
            let cOst = document.documentElement.scrollTop;
            if (cOst == 0) {
                //   document.querySelector(".navbar").classList.add("top-nav-collapse");
            } else if (cOst > ost) {
                document.querySelector(".navbar").classList.add("top-nav-collapse");
                document.querySelector(".navbar").classList.remove("default");
            } else {
                document.querySelector(".navbar").classList.add("default");
                document
                    .querySelector(".navbar")
                    .classList.remove("top-nav-collapse");
            }
            ost = cOst;
        });
        // End [ Menu hide/show on scroll ]
        var wow = new WOW({
            animateClass: "animate__animated", // animation css class (default is animated)
        });
        wow.init();
        // var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        //   target: "#navbar-example",
        // });
    </script>
    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('iframe').attr('src', '{{ url($currentWorkspace->slug . '/invoices/preview') }}/' + template + '/' +
                color);
        });

        $(document).ready(function() {

            var $dragAndDrop = $("body .task-stages tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeater = $('.task-stages').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });


            var value = $(".task-stages").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

            var $dragAndDropBug = $("body .bug-stages tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeaterBug = $('.bug-stages').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDropBug.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });


            var valuebug = $(".bug-stages").attr('data-value');
            if (typeof valuebug != 'undefined' && valuebug.length != 0) {
                valuebug = JSON.parse(valuebug);
                $repeaterBug.setList(valuebug);
            }
            $(document).on('click', '.list-group-item', function() {
                $('.list-group-item').removeClass('active');
                $('.list-group-item').removeClass('text-primary');
                setTimeout(() => {
                    $(this).addClass('active').removeClass('text-primary');
                }, 10);
            });

            var type = window.location.hash.substr(1);
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            if (type != '') {
                $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
            } else {
                $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
            }
        });
    </script>


    <script>
        $('#logo').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#dark_logo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });

        $('#logo_white').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });

        $('#small-favicon').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#favicon').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });
    </script>

    <script>
        $(document).ready(function() {
            if ($('.gdpr_fulltime').is(':checked')) {
                $('.fulltime').show();
            } else {
                $('.fulltime').hide();
            }
            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked')) {
                    $('.fulltime').show();
                } else {
                    $('.fulltime').hide();
                }
            });
        });

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })

        $('.themes-color-change').on('click', function() {
            var color_val = $(this).data('value');
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);

        });

        function check_theme(color_val) {
            $('.theme-color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
            $('#color_value').val(color_val);
        }
    </script>

    <script>
        $(document).on("click", ".email-template-checkbox", function() {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: chbox.val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('{{ __('Success') }}', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('{{ __('Error') }}', response.error, 'error');
                    }
                },
                error: function(response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('{{ __('Error') }}', response.error, 'error');
                    } else {
                        show_toastr('{{ __('Error') }}', response, 'error');
                    }
                }
            })
        });
    </script>
    <script>
        function cust_theme_bg() {
            var custthemebg = document.querySelector("#cust-theme-bg");

            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }

        }

        function cust_darklayout() {
            var custdarklayout = document.querySelector("#cust-darklayout");

            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('assets/images/logo.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('assets/images/logo-dark.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }

        }
    </script>
@endpush
>>>>>>> production
