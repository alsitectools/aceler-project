@php
    $logo = \App\Models\Utility::get_file('logo/');
    if (Auth::user()->type == 'admin') {
        $setting = App\Models\Utility::getAdminPaymentSettings();
        //añadido
        $currentWorkspace = Auth::user()->currentWorkspace;
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = $setting['site_rtl'];
        $company_logo = App\Models\Utility::get_logo();
    } else {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
        $adminSetting = App\Models\Utility::getAdminPaymentSettings();
        $company_logo = App\Models\Utility::getcompanylogo($currentWorkspace->id);
        if ($company_logo == '' || $company_logo == null) {
            $company_logo = App\Models\Utility::get_logo();
        }
    }

    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSettings();
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $company_logo = App\Models\Utility::get_logo();
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }
@endphp

<nav class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('home') }}" class="b-brand">
                <img class="img-fluid rounded" src="{{ asset('assets/img/sidebar.jpg') }}" alt="logo"
                    style="width: 200px ; heigth: 200px !important" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('home') }}"
                        class="dash-link  {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }}">

                        <span class="dash-micon"><i class="ti ti-user"></i></span>
                        <span class="dash-mtext">{{ trans('messages.Company') }}</span>
                    </a>
                </li>

                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('clients.index', $currentWorkspace->slug) }}"
                        class="dash-link {{ Request::route()->getName() == 'clients.index' ? ' active' : '' }} "><span
                            class="dash-micon"> <i class="ti ti-brand-python"></i></span><span class="dash-mtext">
                            {{ trans('messages.Sales_manager') }}</span></a>
                </li>

                <li
                    class="dash-item {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users_logs.index' ? ' active' : '' }}">
                    <a href="{{ route('users.index', $currentWorkspace->slug) }}" class="dash-link ">
                        <span class="dash-micon"> <i data-feather="user"></i></span>
                        <span class="dash-mtext">{{ trans('messages.Technicians') }}</span>
                    </a>
                </li>

                @if (isset($currentWorkspace) && $currentWorkspace)
                    @auth('web')
                        <li
                            class="dash-item {{ Request::route()->getName() == 'projects.index' || Request::segment(2) == 'projects' ? ' active' : '' }}">
                            <a href="{{ route('projects.index', $currentWorkspace->slug) }}" class="dash-link">
                                <span class="dash-micon"> <i data-feather="briefcase"></i></span><span
                                    class="dash-mtext">{{ __('Projects') }}</span></a>
                        </li>
                        <li class="dash-item {{ Request::route()->getName() == 'tasks.index' ? ' active' : '' }}">
                            <a href="{{ route('tasks.index', $currentWorkspace->slug) }}" class="dash-link ">
                                <span class="dash-micon"><i data-feather="list"></i></span><span
                                    class="dash-mtext">{{ trans('messages.Tasks') }}</span></a>
                        </li>

                        <li class="dash-item {{ Request::route()->getName() == 'timesheet.index' ? ' active' : '' }}">
                            <a href="{{ route('timesheet.index', $currentWorkspace->slug) }}" class="dash-link ">
                                <span class="dash-micon"><i data-feather="clock"></i></span><span
                                    class="dash-mtext">{{ trans('messages.Timesheets') }}</span></a>
                        </li>
                        

                        @if (isset($currentWorkspace) &&
                                $currentWorkspace &&
                                $currentWorkspace->creater->id == Auth::user()->id &&
                                Auth::user()->type == 'admin')
                            <li
                                class="dash-item dash-hasmenu {{ Request::route()->getName() == 'contracts.index' || Request::route()->getName() == 'contracts.show' ? ' active' : '' }}">
                                <a href="#" class="dash-link"><span class="dash-micon"><i
                                            class="ti ti-device-floppy"></i></span><span
                                        class="dash-mtext">{{ __('Contratos') }}</span><span class="dash-arrow"><i
                                            data-feather="chevron-right"></i></span></a>
                                <ul
                                    class="dash-submenu collapse  {{ Request::route()->getName() == 'contracts.index' ? ' active' : '' }}">

                                    <li
                                        class="dash-item {{ Request::route()->getName() == 'contracts.index' || Request::route()->getName() == 'contracts.show' ? 'active' : '' }}">
                                        <a class="dash-link"
                                            href="{{ route('contracts.index', $currentWorkspace->slug) }}">{{ __('Contratos') }}</a>
                                    </li>

                                    <li class="dash-item ">
                                        <a class="dash-link"
                                            href="{{ route('contract_type.index', $currentWorkspace->slug) }}">{{ __('Tipo de contratos') }}</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <li class="dash-item {{ Request::route()->getName() == 'calender.index' ? ' active' : '' }}">
                            <a href="{{ route('calender.google.calendar', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="calendar"></i></span><span
                                    class="dash-mtext">{{ __('Calendar') }}</span></a>
                        </li>

                        @elseauth
                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.projects.index' || Request::segment(3) == 'projects' ? ' active' : '' }}">
                            <a href="{{ route('client.projects.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="briefcase"></i></span><span
                                    class="dash-mtext">{{ __('Projects') }}</span></a>
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.timesheet.index' ? ' active' : '' }}">
                            <a href="{{ route('client.timesheet.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="clock"></i></span><span
                                    class="dash-mtext">{{ __('Timesheet') }}</span></a>
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.contracts.index' || Request::route()->getName() == 'client.contracts.show' ? 'active' : '' }}">
                            <a href="{{ route('client.contracts.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i class="ti ti-device-floppy"></i></span><span
                                    class="dash-mtext">{{ __('Contracts') }}</span></a>
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.project_report.index' || Request::segment(3) == 'project_report' ? ' active' : '' }}">
                            <a href="{{ route('client.project_report.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i class="ti ti-chart-line"></i></span><span
                                    class="dash-mtext">{{ __('Project Report') }}</span></a>
                        </li>

                        <li
                            class="dash-item {{ Request::route()->getName() == 'client.calender.index' ? ' active' : '' }}">
                            <a href="{{ route('client.calender.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i data-feather="calendar"></i></span><span
                                    class="dash-mtext">{{ __('Calendar') }}</span></a>
                        </li>
                    @endauth
                @endif
                @if (isset($currentWorkspace) && $currentWorkspace)
                    @auth('web')
                        <li
                            class="dash-item {{ Request::route()->getName() == 'project_report.index' || Request::segment(2) == 'project_report' ? ' active' : '' }}">
                            <a href="{{ route('project_report.index', $currentWorkspace->slug) }}"
                                class="dash-link "><span class="dash-micon"><i class="ti ti-chart-line"></i></span><span
                                    class="dash-mtext">{{ __('Project Report') }}</span></a>
                        </li>
                    @endauth
                @endif
                {{-- colorAlsina #AA182C --}}
                {{-- @if (\Auth::user()->type == 'admin') --}}
                    <li class="dash-item {{ Request::route()->getName() == 'workspace.settings' ? ' active' : '' }}">
                        <a href="{{ route('workspace.settings', $currentWorkspace->slug) }}" class="dash-link "><span
                                class="dash-micon"><i data-feather="settings"></i></span><span
                                class="dash-mtext">{{ __('Settings') }}</span></a>

                    </li>
                {{-- @endif --}}

        </div>
    </div>
</nav>
