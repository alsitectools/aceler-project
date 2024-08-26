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
<nav style="padding-top: 5px; padding-left: 5px;"
    class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('home') }}" class="b-brand">
                <img class="img-fluid" src="{{ asset('assets/img/acelerProject.png') }}" alt="logo"
                    style="width: 220px; border-radius: 7px !important;" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('home') }}"
                        class="dash-link  {{ Request::route()->getName() == 'home' ||
                        Request::route()->getName() == null ||
                        Request::route()->getName() == 'client.home'
                            ? ' active'
                            : '' }}">

                        <span class="dash-micon"><i class="fa-solid fa-bookmark"></i></span>
                        <span class="dash-mtext">{{ __('Summary') }}</span>
                    </a>
                </li>
                @if (isset($currentWorkspace) && $currentWorkspace)
                    <li
                        class="dash-item {{ Request::route()->getName() == 'projects.index' || Request::segment(2) == 'projects' ? ' active' : '' }}">
                        <a href="{{ route('projects.index', $currentWorkspace->slug) }}" class="dash-link">
                            <span class="dash-micon"><i class="fa-solid fa-diagram-project"></i></span><span
                                class="dash-mtext">{{ __('Projects') }}</span></a>
                    </li>
                    {{-- si mostramos todos los proyectos enviamos -1 o proyecto en especifico --}}
                    <li class="dash-item">
                        <a href="{{ route('projects.milestone.board', [$currentWorkspace->slug, -1]) }}"
                            class="dash-link ">
                            <span class="dash-micon"><i class="fa-solid fa-file-lines"></i></span><span
                                class="dash-mtext">{{ __('Milestones') }}</span></a>
                    </li>
                    <li class="dash-item {{ Request::route()->getName() == 'timesheet.index' ? ' active' : '' }}">
                        <a href="{{ route('timesheet.index', $currentWorkspace->slug) }}" class="dash-link ">
                            <span class="dash-micon"><i class="fas fa-tasks"></i></span><span
                                class="dash-mtext">{{ __('Tasks') }}</span></a>
                    </li>
                    <li class="dash-item {{ Request::route()->getName() == 'calender.index' ? ' active' : '' }}">
                        <a href="{{ route('calender.google.calendar', $currentWorkspace->slug) }}"
                            class="dash-link "><span class="dash-micon"><i
                                    class="fa-regular fa-calendar"></i></span><span
                                class="dash-mtext">{{ __('Calendar') }}</span></a>
                    </li>
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('clients.index', $currentWorkspace->slug) }}"
                            class="dash-link {{ Request::route()->getName() == 'clients.index' ? ' active' : '' }} "><span
                                class="dash-micon"> <i class="fa-solid fa-user-tie"></i></span><span class="dash-mtext">
                                {{ __('Sales managers') }}</span></a>
                    </li>
                    <li
                        class="dash-item {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users_logs.index' ? ' active' : '' }}">
                        <a href="{{ route('users.index', $currentWorkspace->slug) }}" class="dash-link ">
                            <span class="dash-micon"> <i class="fa-solid fa-helmet-safety"></i></span>
                            <span class="dash-mtext">{{ __('Technicians') }}</span>
                        </a>
                    </li>
                @endif
                {{-- colorAlsina #AA182C --}}
                @if (\Auth::user()->type == 'admin')
                    <li class="dash-item {{ Request::route()->getName() == 'workspace.settings' ? ' active' : '' }}">
                        <a href="{{ route('workspace.settings', $currentWorkspace->slug) }}" class="dash-link "><span
                                class="dash-micon"><i data-feather="settings"></i></span><span
                                class="dash-mtext">{{ __('Settings') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
