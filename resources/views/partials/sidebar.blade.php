@php
    $logo = \App\Models\Utility::get_file('logo/');
    $userWorkspaces = \App\Models\UserWorkspace::query()
        ->join('workspaces', 'user_workspaces.workspace_id', '=', 'workspaces.id')
        ->where('user_workspaces.user_id', Auth::user()->id)
        ->select('user_workspaces.workspace_id', 'workspaces.name')
        ->orderBy('workspaces.name')
        ->get();
    $currentWorkspace = $currentWorkspace ?? Auth::user()->currentWorkspace;
    $currentWorkspaceId = (int) (Auth::user()->currant_workspace ?? 0);

    if (!$currentWorkspace && $userWorkspaces->isNotEmpty()) {
        $currentWorkspace = App\Models\Workspace::find($userWorkspaces->first()->workspace_id);
    }

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
        $company_logo = App\Models\Utility::get_logo();
    } else {
        if ($currentWorkspace) {
            $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
            $color = $setting->theme_color;
            $dark_mode = $setting->cust_darklayout;
            $SITE_RTL = $setting->site_rtl;
            $cust_theme_bg = $setting->cust_theme_bg;
        } else {
            $setting = App\Models\Utility::getAdminPaymentSettings();
            $color = $setting['color'] ?? 'theme-3';
            $dark_mode = $setting['cust_darklayout'] ?? 'off';
            $SITE_RTL = $setting['site_rtl'] ?? env('SITE_RTL');
            $cust_theme_bg = $setting['cust_theme_bg'] ?? 'off';
        }
        $adminSetting = App\Models\Utility::getAdminPaymentSettings();
        $company_logo = $currentWorkspace
            ? App\Models\Utility::getcompanylogo($currentWorkspace->id)
            : App\Models\Utility::get_logo();
        if ($company_logo == '' || $company_logo == null) {
            $company_logo = App\Models\Utility::get_logo();
        }
    }

    $userName = Auth::user()->name;
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
    // NOT NEEDED NOW
    // $week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    // $workHoursWeek = [];

    // //userTimetable is an array of arrays, we need to take the first element of the array
    // $timetable = $userTimetable[0];

    // //get the days and the hours
    // foreach ($timetable as $key => $value) {

    //         if(in_array(strtolower($key), $week)){
    //             $workHoursWeek[$key] = $value;
    //         }
    //     }
    // //check if the user implemented his timetable
    // $emptyTimetable = empty(array_filter($workHoursWeek, fn($value) => !is_null($value)));
@endphp

<style>
    .workspace-name-header {
        font-weight: 700;
        border-radius: 11px;
        background: linear-gradient(180deg, rgba(170, 24, 44, 1) 0%, rgb(174 0 24) 100%);
        font-size: 14px;
        color: #ffffff;
        padding: 8px 10px;
        margin-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        border: none;
        width: 100%;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .workspace-name-header:hover {
        background: linear-gradient(180deg, rgba(190, 44, 64, 1) 0%, rgb(194 20 44) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .workspace-dropdown-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    /* Espacio que ocupaba la píldora de delegaciones (mt-2 8 + botón 38 + margen 15) */
    .workspace-pill-spacer {
        height: 61px;
    }

    .workspace-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: #ffffff;
        min-width: 100%;
        max-width: 95vw;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        z-index: 1000;
        margin-top: -8px;
        border: 1px solid #e0e0e0;
        box-sizing: border-box;
    }

    .workspace-dropdown.active {
        display: block;
    }

    .workspace-search {
        padding: 12px;
        border-bottom: 1px solid #e0e0e0;
    }

    .workspace-search input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        box-sizing: border-box;
    }

    .workspace-search input:focus {
        outline: none;
        border-color: #aa182c;
        box-shadow: 0 0 0 3px rgba(170, 24, 44, 0.1);
    }

    .workspace-list {
        max-height: 300px;
        overflow-y: auto;
        padding: 8px 0;
    }

    .workspace-item {
        padding: 10px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        border-left: 3px solid transparent;
        color: #333;
        font-size: 13px;
    }

    .workspace-item:hover {
        background-color: #f5f5f5;
        border-left-color: #aa182c;
    }

    .workspace-item.active {
        background-color: #f0f0f0;
        border-left-color: #aa182c;
        font-weight: 600;
        color: #aa182c;
    }

    .workspace-item.hidden {
        display: none;
    }

    .workspace-dropdown-icon {
        transition: transform 0.3s ease;
        font-size: 12px;
    }

    .workspace-dropdown-icon.rotate {
        transform: rotate(180deg);
    }

    .menu-element {
        background-color: #fcf9f9;
        filter: drop-shadow(-4px 3px 0px #d8d8d8);
    }

    .ajustarImg {
        width: 270px;
    }

    .alertDiv {
        background-color: #ffae6a;
        width: 86%;
        height: 18%;
        margin-left: 7%;
        border-radius: 12px;
    }

    .alertTitle {
        color: #aa182c;
        display: flex;
        font-size: 33px;
        align-items: center;
        justify-content: center;
    }

    .alertSubstitle {
        color: white;
        font-size: 12px;
        padding-bottom: 10px;
    }

    .alertCenter {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .buttonAlertCreate {
        margin-left: 20%;
        height: 33px;
        display: flex;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    #container-alert {
        position: relative;
        width: 100%;
        height: 250px;
    }

    .h1Alert {
        font-size: 0.9em;
        font-weight: 100;
        letter-spacing: 3px;
        padding-top: 5px;
        color: #FCFCFC;
        padding-bottom: 5px;
        text-transform: uppercase;
    }

    .green {
        color: #38836C;
    }

    .red {
        color: #D66B7A;
    }

    .alert {
        font-size: 26px;
        letter-spacing: 5px;
        padding: 0;
        margin-bottom: 7px;
    }

    .pAlert {
        font-size: 11px;
        color: white;
        letter-spacing: 1px;
        font-weight: bold;
    }




    #error-boxAlert {
        width: 90%;
        height: 100%;
        margin-left: 5%;
        background-color: #e06c71;
        border-radius: 20px;
        box-shadow: 5px 5px 20px rgba(203, 205, 211, 0.1);
    }

    .dot-alert {
        width: 8px;
        height: 8px;
        background: #FCFCFC;
        border-radius: 50%;
        position: absolute;
        top: 4%;
        right: 6%;
    }

    .dot-alert:hover {
        background: #CCCCCC;
    }

    .two {
        right: 12%;
        opacity: 1;
    }


    .message-alert {
        width: 100%;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    .button-box-alert {
        position: absolute;
        background: #FCFCFC;
        width: 50%;
        height: 15%;
        border-radius: 20px;
        top: 73%;
        left: 25%;
        outline: 0;
        border: none;
        box-shadow: 2px 2px 10px rgba(119, 119, 119, 0.5);
        transition: all 0.5s ease-in-out;
    }

    .button-box-alert:hover {
        background: #F0F0F0;
        transform: scale(1.05);
        transition: all 0.3s ease-in-out;
    }



    /* CSS ICON ALERT GENERAL */
    .screenAlert-icon {
        border-radius: 50%;
        border: 4px solid gray;
        box-sizing: content-box;
        height: 80px;
        margin: 20px auto;
        padding: 0;
        position: relative;
        width: 80px
    }

    .screenAlert-icon+.screenAlert-icon {
        margin-top: 50px
    }

    /* CSS WARNING */
    .screenAlert-icon.screenAlert-warning {
        border-color: #F8BB86
    }

    .screenAlert-icon.screenAlert-warning:before {
        -webkit-animation: pulseWarning 2s linear infinite;
        animation: pulseWarning 2s linear infinite;
        border-radius: 50%;
        content: "";
        display: inline-block;
        height: 100%;
        opacity: 0;
        position: absolute;
        width: 100%
    }

    .screenAlert-icon.screenAlert-warning:after {
        border-radius: 50%;
        content: '';
        display: block;
        height: 100%;
        position: absolute;
        width: 100%;
        z-index: 1
    }

    .screenAlert-icon.screenAlert-warning .screenAlert-body {
        background-color: #F8BB86;
        border-radius: 2px;
        height: 47px;
        left: 50%;
        margin-left: -2px;
        position: absolute;
        top: 10px;
        width: 5px;
        z-index: 2
    }

    .screenAlert-icon.screenAlert-warning .screenAlert-dot {
        background-color: #F8BB86;
        border-radius: 50%;
        bottom: 10px;
        height: 7px;
        left: 50%;
        margin-left: -3px;
        position: absolute;
        width: 7px;
        z-index: 2
    }

    .scaleWarning {
        -webkit-animation: scaleWarning .75s infinite alternate;
        animation: scaleWarning .75s infinite alternate
    }

    .pulseWarningIns {
        -webkit-animation: pulseWarningIns .75s infinite alternate;
        animation: pulseWarningIns .75s infinite alternate
    }

    /* ANIMATE WARNING */
    @-webkit-keyframes scaleWarning {
        0% {
            -webkit-transform: scale(1);
            transform: scale(1)
        }

        30% {
            -webkit-transform: scale(1.02);
            transform: scale(1.02)
        }

        100% {
            -webkit-transform: scale(1);
            transform: scale(1)
        }
    }

    @keyframes scaleWarning {
        0% {
            -webkit-transform: scale(1);
            transform: scale(1)
        }

        30% {
            -webkit-transform: scale(1.02);
            transform: scale(1.02)
        }

        100% {
            -webkit-transform: scale(1);
            transform: scale(1)
        }
    }

    @-webkit-keyframes pulseWarning {
        0% {
            background-color: #fff;
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: .5
        }

        30% {
            background-color: #fff;
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: .5
        }

        100% {
            background-color: #F8BB86;
            -webkit-transform: scale(2);
            transform: scale(2);
            opacity: 0
        }
    }

    @keyframes pulseWarning {
        0% {
            background-color: #fff;
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: .5
        }

        30% {
            background-color: #fff;
            -webkit-transform: scale(1);
            transform: scale(1);
            opacity: .5
        }

        100% {
            background-color: #F8BB86;
            -webkit-transform: scale(2);
            transform: scale(2);
            opacity: 0
        }
    }

    @-webkit-keyframes pulseWarningIns {
        0% {
            background-color: #F8D486
        }

        100% {
            background-color: #F8BB86
        }
    }

    @keyframes pulseWarningIns {
        0% {
            background-color: #F8D486
        }

        100% {
            background-color: #F8BB86
        }
    }

    /* Sub icono de usuario en My Projects / My Milestones */


    .dash-item:hover .user-badge-sub {
        color: #aa182c !important;
    }

    .dash-item.active .user-badge-sub {
        color: #aa182c !important;
    }

    @media screen and (max-width:1200px) and (min-width:1100px) {
        .vistaPortatil {
            width: 18%;
        }

        .ajustarImg {
            width: 200px;
        }
    }
</style>
@php
    // Pestañas Equipo / Mi actividad: grupo de la página actual ('' si no es de ninguno)
    $sbRoute = optional(Request::route())->getName();
    $sbScopePage = in_array($sbRoute, ['my_summary', 'my_projects', 'projects.my_milestone_board', 'projects.my_tasks', 'timesheet.index', 'calender.index', 'calender.google.calendar'], true)
        ? 'mine'
        : (in_array($sbRoute, ['home', 'client.home', 'projects.index', 'projects.show', 'projects.milestone.board', 'gantt.diagram'], true) ? 'team' : '');
@endphp
<nav style="padding-top: 5px; padding-left: 5px;" data-scope-page="{{ $sbScopePage }}"
    class="vistaPortatil dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="mt-2">
                <img class="ajustarImg" src="{{ asset('assets/img/acerlerRemastered.png') }}" alt="logo" />
                <img class="logo-mini" src="{{ asset('assets/img/alsina-a-mark.png') }}" alt="aCeler" />
                <img class="logo-mini-text" src="{{ asset('assets/img/alsina-aceler-mark.png') }}" alt="" />
            </a>
            {{-- Detalle oculto: gorro navideño sobre la "A" al escribir "navidad" en el buscador --}}
            <span class="sb-xmas-hat" aria-hidden="true">
                <svg viewBox="0 0 64 56" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 44 C14 22 30 6 50 8 C44 16 46 26 52 34 C40 36 22 40 8 44 Z" fill="#E11D2E"/>
                    <path d="M18 34 C26 24 36 16 46 13" stroke="#B3141F" stroke-width="2.5" fill="none" stroke-linecap="round" opacity=".55"/>
                    <rect x="2" y="40" width="54" height="12" rx="6" fill="#FFFFFF"/>
                    <circle cx="52" cy="8" r="7" fill="#FFFFFF"/>
                </svg>
            </span>
        </div>
        {{-- Escritorio: aquí se coloca el selector de espacio de trabajo de la cabecera
             (lo mueve el script del final; en móvil se queda en la cabecera) --}}
        <div class="sb-slot sb-slot-workspace" id="sbSlotWorkspace"></div>
        {{-- Pestañas: qué opciones del menú se ven (script del final; se recuerda en el navegador) --}}
        {{-- Textos de los tooltips (title) de lo que se mueve de la cabecera al sidebar --}}
        <span id="sbTooltipText" hidden data-workspace="{{ __('Workspace') }}" data-notifications="{{ __('Notifications') }}"
            data-language="{{ __('Language') }}" data-switch="{{ __('Switch to') }}" data-home="{{ __('Home') }}"></span>
        <div class="sb-scope-tabs" role="tablist">
            <button type="button" class="sb-scope-tab" role="tab" data-scope="team">
                <i class="fa-solid fa-users"></i><span>{{ __('Team') }}</span>
            </button>
            <button type="button" class="sb-scope-tab" role="tab" data-scope="mine">
                <i class="fa-solid fa-user"></i><span>{{ __('My activity') }}</span>
                <span class="sb-scope-dot" aria-hidden="true"></span>
            </button>
            {{-- Sidebar colapsado: un solo botón (flechas de ida y vuelta) que alterna el grupo;
                 la insignia indica el grupo actual --}}
            <button type="button" class="sb-scope-swap" data-label-team="{{ __('Team') }}"
                data-label-mine="{{ __('My activity') }}" aria-label="{{ __('Team') }} / {{ __('My activity') }}">
                <i class="fa-solid fa-right-left sb-swap-arrows"></i>
                <span class="sb-swap-badge" aria-hidden="true">
                    <i class="fa-solid fa-users" data-scope-icon="team"></i>
                    <i class="fa-solid fa-user" data-scope-icon="mine"></i>
                </span>
                <span class="sb-scope-dot" aria-hidden="true"></span>
            </button>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar ">
                <li data-scope="team"
                    class="dash-item icons-collapsible dash-hasmenu {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="dash-link menu-element">
                        <span class="dash-micon"><i class="fa-solid fa-bookmark"></i></span>
                        <span class="dash-mtext">{{ __('Resume') }}</span>
                    </a>
                </li>
                @if (isset($currentWorkspace) && $currentWorkspace)
                    <li data-scope="team"
                        class="dash-item icons-collapsible {{ (Request::route()->getName() == 'projects.index' || Request::segment(2) == 'projects') && Request::route()->getName() != 'projects.milestone.board' ? 'active' : '' }}">
                        <a href="{{ route('projects.index', $currentWorkspace->slug) }}"
                            class="dash-link  menu-element">
                            <span class="dash-micon"><i class="fa-solid fa-diagram-project"></i></span><span
                                class="dash-mtext">{{ __('Projects') }}</span></a>
                    </li>

                    </li>
                    <li data-scope="team"
                        class="dash-item icons-collapsible {{ Request::route()->getName() == 'projects.milestone.board' ? 'active' : '' }}">
                        <a href="{{ route('projects.milestone.board', [$currentWorkspace->slug, -1]) }}"
                            class="dash-link  menu-element">
                            <span class="dash-micon"><i class="fa-solid fa-file-lines"></i></span><span
                                class="dash-mtext">{{ __('Order Forms') }}</span></a>
                    </li>
                    <li data-scope="team" class="dash-item icons-collapsible {{ Request::route()->getName() == 'gantt.diagram' ? 'active' : '' }}">
                        <a href="{{ route('gantt.diagram') }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fa-solid fa-chart-gantt"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Gantt Diagram') }}</span>
                        </a>
                    </li>
                    <li data-scope="mine" class="dash-item icons-collapsible {{ Request::route()->getName() == 'my_summary' ? 'active' : '' }}">
                        <a href="{{ route('my_summary') }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fa-solid fa-bookmark"></i>
                                <i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i>
                            </span>
                            <span class="dash-mtext">{{ __('My Summary') }}</span>
                        </a>
                    </li>

                    {{-- si mostramos todos los proyectos enviamos -1 o proyecto en especifico --}}
                    <li data-scope="mine" class="dash-item icons-collapsible {{ Request::route()->getName() == 'my_projects' ? 'active' : '' }}">
                        <a href="{{ route('my_projects', $currentWorkspace->slug) }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fa-solid fa-diagram-project"></i>
                                <i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i>
                            </span>
                            <span class="dash-mtext">{{ __('My Projects') }}</span>
                        </a>
                    </li>
                    <li data-scope="mine" class="dash-item icons-collapsible {{ Request::route()->getName() == 'projects.my_milestone_board' ? 'active' : '' }}">
                        <a href="{{ route('projects.my_milestone_board') }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fa-solid fa-file-lines"></i>
                                <i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i>
                            </span>
                            <span class="dash-mtext">{{ __('My Order Forms') }}</span>
                        </a>
                    </li>
                    <li data-scope="mine" class="dash-item icons-collapsible {{ Request::route()->getName() == 'projects.my_tasks' ? 'active' : '' }}">
                        <a href="{{ route('projects.my_tasks') }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fas fa-tasks"></i>
                                <i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i>
                            </span>
                            <span class="dash-mtext">{{ __('My Tasks') }}</span>
                        </a>
                    </li>
                    <li data-scope="mine" class="dash-item icons-collapsible {{ Request::route()->getName() == 'timesheet.index' ? 'active' : '' }}">
                        <a href="{{ route('timesheet.index', $currentWorkspace->slug) }}"
                            class="dash-link  menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fa-solid fa-clock"></i>
                                <i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i>
                            </span><span class="dash-mtext">{{ __('My Timesheet') }}</span></a>
                    </li>
                    <li data-scope="mine" class="dash-item icons-collapsible {{ Request::route()->getName() == 'calender.index' ? 'active' : '' }}">
                        <a href="{{ route('calender.google.calendar', $currentWorkspace->slug) }}"
                            class="dash-link  menu-element"><span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;"><i
                                    class="fa-regular fa-calendar"></i><i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i></span><span
                                class="dash-mtext">{{ __('My Calendar') }}</span></a>
                    </li>
            </ul>
            <!-- Grupo: Otros -->
            {{-- <li class="dash-item  dash-label" data-group="otros">
                        <small><i class="fa-solid fa-ellipsis" style="margin-right: 8px;"></i>{{ __('Otros') }}</small>
                        <i class="fa-solid fa-chevron-down toggle-icon"></i>
                    </li>
                    <li class="dash-item  {{ Request::route()->getName() == 'tutorialHome' ? 'active' : '' }}" data-group-content="otros">
                        <a href="{{ route('home.showTutorial', [$currentWorkspace->slug]) }}" class="dash-link  "><span
                                class="dash-micon"><i class="fas fa-book"></i>
                            </span>
                            <span class="dash-mtext">Tutorial</span></a>
                    </li> --}}
            {{-- @if ($emptyTimetable == 1 && $userType != 'client')
                            <div id="container-alert">
                                <div id="error-boxAlert">
                                    <div style="padding-bottom: 1px;"></div>
                                    <div class="screenAlert-icon screenAlert-warning scaleWarning">
                                        <span class="screenAlert-body pulseWarningIns"></span>
                                        <span class="screenAlert-dot pulseWarningIns"></span>
                                    </div>
                                    <div class="message-alert">
                                        <h1 class="alert h1Alert">{{__('ALERT!')}}</h1>
                                        <p class="pAlert">{{__('You should input your timetable')}}</p>
                                    </div>
                                    <button class="btn btn-primary buttonAlertCreate">
                                        {{__('Create timetable')}}
                                    </button>
                                </div>
                            </div>
                        @endif --}}
            <!-- <li class="dash-item  dash-hasmenu">
                        <a href="{{ route('clients.index', $currentWorkspace->slug) }}"
                            class="dash-link  {{ Request::route()->getName() == 'clients.index' ? ' active' : '' }} "><span
                                class="dash-micon"> <img class="img-fluid"
                                    src="{{ asset('assets/img/salesManager.png') }}" alt="logo" /> </span><span
                                class="dash-mtext"> {{ __('Sales managers') }}</span></a>
                    </li>
                    <li
                        class="dash-item  {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users_logs.index' ? ' active' : '' }}">
                        <a href="{{ route('users.index', $currentWorkspace->slug) }}" class="dash-link  ">
                            <span
                            class="dash-micon"> <img class="img-fluid"
                                src="{{ asset('assets/img/technicians.png') }}" alt="logo" /> </span>
                            <span class="dash-mtext">{{ __('Technicians') }}</span>
                        </a>
                    </li> -->
            @endif
            {{-- colorAlsina #AA182C --}}
            @if (\Auth::user()->type == 'admin')
                <li class="dash-item  {{ Request::route()->getName() == 'workspace.settings' ? ' active' : '' }}">
                    <a href="{{ route('workspace.settings', $currentWorkspace->slug) }}"
                        class="dash-link menu-element "><span class="dash-micon"><i
                                data-feather="settings"></i></span><span
                            class="dash-mtext">{{ __('Settings') }}</span>
                    </a>
                </li>
            @endif
            </ul>
        </div>
        {{-- «Reportar un problema»: enlace sobre la línea del pie y su formulario --}}
        @include('partials.problem_report')
        {{-- Escritorio: notificaciones, idioma (y chat) + menú de usuario de la cabecera --}}
        <div class="sb-bottom">
            <ul class="sb-slot sb-slot-tools list-unstyled" id="sbSlotTools"></ul>
            <ul class="sb-slot sb-slot-user list-unstyled" id="sbSlotUser"></ul>
        </div>
        <div class="menu-icons-row">
            <button type="button" id="aCeler-menu-toggle" class="menu-icons-btn"
                title="Colapsar menú / Expandir menú" aria-label="Colapsar menú / Expandir menú"
                aria-expanded="true">
                <i class="fa-solid fa-angles-left"></i>
            </button>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const workspaceButton = document.getElementById('workspaceButton');
        const workspaceDropdown = document.getElementById('workspaceDropdown');
        const workspaceSearchInput = document.getElementById('workspaceSearchInput');
        const workspaceList = document.getElementById('workspaceList');

        if (!workspaceButton || !workspaceDropdown || !workspaceSearchInput || !workspaceList) {
            return;
        }

        const workspaceItems = workspaceList.querySelectorAll('.workspace-item');
        const dropdownIcon = workspaceButton.querySelector('.workspace-dropdown-icon');

        // Toggle dropdown cuando se hace click en el botón
        workspaceButton.addEventListener('click', function(e) {
            e.preventDefault();
            workspaceDropdown.classList.toggle('active');
            dropdownIcon.classList.toggle('rotate');
        });

        // Cerrar dropdown cuando se hace click fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.workspace-dropdown-container')) {
                workspaceDropdown.classList.remove('active');
                dropdownIcon.classList.remove('rotate');
                workspaceSearchInput.value = '';
                filterWorkspaces('');
            }
        });

        // Búsqueda de workspaces
        workspaceSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterWorkspaces(searchTerm);
        });

        // Cambiar workspace cuando se selecciona uno
        workspaceItems.forEach(item => {
            item.addEventListener('click', function() {
                const workspaceDisplayName = this.getAttribute('data-workspace-display-name');
                const workspaceUrl = this.getAttribute('data-workspace-url');

                // Actualizar el nombre mostrado
                document.getElementById('workspaceName').textContent = workspaceDisplayName;

                // Cerrar el dropdown
                workspaceDropdown.classList.remove('active');
                dropdownIcon.classList.remove('rotate');
                workspaceSearchInput.value = '';
                filterWorkspaces('');

                // Navegar al workspace usando la URL correcta generada con route()
                if (workspaceUrl) {
                    window.location.href = workspaceUrl;
                }
            });
        });

        // Función para filtrar workspaces
        function filterWorkspaces(searchTerm) {
            const items = workspaceList.querySelectorAll('.workspace-item');
            items.forEach(item => {
                const originalName = item.getAttribute('data-workspace-name') || '';
                const displayName = item.getAttribute('data-workspace-display-name') || '';

                if (!originalName && !displayName) {
                    item.classList.add('hidden');
                    return;
                }

                const matches = originalName.toLowerCase().includes(searchTerm) ||
                                displayName.toLowerCase().includes(searchTerm);

                if (matches) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        // Permitir navegación con Enter cuando se busca
        workspaceSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const visibleItems = Array.from(document.querySelectorAll(
                    '.workspace-item:not(.hidden)'));
                if (visibleItems.length === 1) {
                    visibleItems[0].click();
                }
            }
        });
    });

    // ------------------------------------------------------------------
    // Cabecera dentro del sidebar (escritorio, >= 1025px).
    // Se mueven al sidebar los MISMOS elementos de la cabecera (topnav): selector de espacio
    // de trabajo, chat, notificaciones, idioma y menú de usuario. Al moverlos conservan sus
    // eventos (cambiar espacio, borrar notificaciones, idioma, cerrar sesión...).
    // En móvil vuelven a la cabecera, que sigue teniendo el botón del menú.
    // Se ejecuta justo después de la cabecera (layouts/admin.blade.php), antes de que el
    // navegador pinte, para que no se vean aparecer; DOMContentLoaded queda de respaldo.
    // ------------------------------------------------------------------
    window.acelerPlaceHeader = function() {
        if (window.__sbHeaderInit) return;
        var header = document.querySelector('.dash-header');
        var slotWs = document.getElementById('sbSlotWorkspace');
        var slotTools = document.getElementById('sbSlotTools');
        var slotUser = document.getElementById('sbSlotUser');
        if (!header || !slotWs || !slotTools || !slotUser) return;
        window.__sbHeaderInit = true;

        var parts = [];
        var add = function(el, slot, extraClass) {
            if (!el) return;
            var marker = document.createComment('sb-origin');
            el.parentNode.insertBefore(marker, el);
            parts.push({ el: el, slot: slot, marker: marker, extraClass: extraClass || '' });
        };

        add(header.querySelector('.drp-workspace'), slotWs);
        header.querySelectorAll('.ms-auto .dash-h-item:not(.drp-workspace)').forEach(function(li) {
            // Chat, notificaciones e idioma: sus menús se abren a la derecha del sidebar
            add(li, slotTools, 'dropend');
        });
        add(header.querySelector('.drp-company'), slotUser, 'dropend');

        var desktop = window.matchMedia('(min-width: 1025px)');
        var place = function() {
            var inSidebar = desktop.matches;
            parts.forEach(function(p) {
                if (inSidebar) {
                    p.slot.appendChild(p.el);
                } else {
                    p.marker.parentNode.insertBefore(p.el, p.marker.nextSibling);
                }
                if (p.extraClass) p.el.classList.toggle(p.extraClass, inSidebar);
                // En la cabecera los menús se alinean a la derecha; en el sidebar, no
                p.el.querySelectorAll(':scope > .dropdown-menu').forEach(function(menu) {
                    if (inSidebar && menu.classList.contains('dropdown-menu-end')) {
                        menu.classList.remove('dropdown-menu-end');
                        menu.setAttribute('data-sb-was-end', '1');
                    } else if (!inSidebar && menu.getAttribute('data-sb-was-end') === '1') {
                        menu.classList.add('dropdown-menu-end');
                    }
                });
            });
            document.body.classList.toggle('aCeler-header-in-sidebar', inSidebar);
            userNameOnly(inSidebar);
            fitWorkspaceName();
        };

        // En el sidebar solo el nombre del usuario (sin "Hola,"); en la cabecera, el saludo original
        var userNameOnly = function(inSidebar) {
            var span = document.querySelector('.drp-company .dash-head-link .hide-mob');
            var img = document.querySelector('.drp-company .theme-avtar');
            if (!span) return;
            if (!span.hasAttribute('data-sb-greeting')) span.setAttribute('data-sb-greeting', span.textContent);
            var name = (img && img.getAttribute('alt')) || span.getAttribute('data-sb-greeting');
            span.textContent = inSidebar ? name.trim() : span.getAttribute('data-sb-greeting');
        };

        // Nombre del espacio de trabajo: si no cabe, la letra baja hasta 10px; si aun así
        // no cabe, pasa a dos líneas a 11px (y solo entonces "…")
        var fitWorkspaceName = function() {
            var nameEl = document.querySelector('.sb-slot-workspace #workspaceName');
            if (!nameEl) return;
            nameEl.style.fontSize = '';
            nameEl.classList.remove('is-two-lines');
            if (!document.body.classList.contains('aCeler-header-in-sidebar') ||
                document.body.classList.contains('aCeler-menu-icons')) return;
            var size = parseFloat(window.getComputedStyle(nameEl).fontSize) || 12.5;
            while (nameEl.scrollWidth > nameEl.clientWidth + 1 && size > 10) {
                size -= 0.5;
                nameEl.style.fontSize = size + 'px';
            }
            if (nameEl.scrollWidth > nameEl.clientWidth + 1) {
                nameEl.style.fontSize = '11px';
                nameEl.classList.add('is-two-lines');
            }
            nameEl.setAttribute('title', nameEl.textContent.trim());
        };
        window.addEventListener('resize', fitWorkspaceName);
        // Al expandir el sidebar (cambia la clase del body) se vuelve a ajustar cuando
        // termina la animación de ancho (0.2s), para medir con el ancho final
        // Al colapsar/expandir, notificaciones e idioma cambian de fila a columna: solo
        // entonces aparecen con un fundido (no en cada carga de página)
        var wasCollapsed = document.body.classList.contains('aCeler-menu-icons');
        new MutationObserver(function() {
            var collapsed = document.body.classList.contains('aCeler-menu-icons');
            if (collapsed !== wasCollapsed) {
                wasCollapsed = collapsed;
                slotTools.classList.remove('sb-fade');
                void slotTools.offsetWidth;
                slotTools.classList.add('sb-fade');
            }
            window.setTimeout(fitWorkspaceName, 260);
            window.acelerRailTitles();
        }).observe(document.body, { attributes: true, attributeFilter: ['class'] });

        place();
        if (desktop.addEventListener) desktop.addEventListener('change', place);
        else if (desktop.addListener) desktop.addListener(place);

        // Tooltips (title) de lo que viene de la cabecera: en el raíl solo se ve el icono
        var tipText = document.getElementById('sbTooltipText');
        // El texto va a data-rail-title: acelerRailTitles() lo pone como title solo con el
        // sidebar colapsado (abierto ya se lee el texto). Un title propio del HTML (p. ej. el
        // aviso de horas sin imputar) se respeta y se ve siempre.
        var setTitle = function(el, text) {
            if (el && text && !el.getAttribute('title')) el.setAttribute('data-rail-title', text.trim());
        };
        if (tipText) {
            var wsName = document.getElementById('workspaceName');
            setTitle(document.getElementById('workspaceButton'),
                tipText.dataset.workspace + (wsName ? ': ' + wsName.textContent.trim() : ''));
            setTitle(document.querySelector('.drp-notification > .dash-head-link'), tipText.dataset.notifications);
            var lang = document.querySelector('.drp-language .drp-text');
            setTitle(document.querySelector('.drp-language > .dash-head-link'),
                tipText.dataset.language + (lang ? ': ' + lang.textContent.trim() : ''));
            var avatar = document.querySelector('.drp-company .theme-avtar');
            setTitle(document.querySelector('.drp-company > .dash-head-link'), avatar && avatar.getAttribute('alt'));
            var chat = document.querySelector('.sb-slot-tools a[href*="chats"]');
            setTitle(chat, 'Chat');
            // Logo (lleva al inicio) y opciones del menú: el layout también lo hace, pero así
            // no depende del orden de los scripts
            setTitle(document.querySelector('.dash-sidebar .m-header > a'), 'aCeler · ' + tipText.dataset.home);
            document.querySelectorAll('.dash-sidebar .dash-navbar .dash-link').forEach(function(a) {
                var label = a.querySelector('.dash-mtext');
                setTitle(a, label && label.textContent);
            });
            // El botón de colapsar trae su title del HTML: también solo en el raíl
            var toggle = document.getElementById('aCeler-menu-toggle');
            if (toggle && toggle.getAttribute('title')) {
                toggle.setAttribute('data-rail-title', toggle.getAttribute('title'));
                toggle.removeAttribute('title');
            }
        }
        window.acelerRailTitles();

        // Los menús (dropend) se colocan junto a su botón, que puede ocupar media fila:
        // se separan lo justo para que empiecen 10px a la derecha del borde del sidebar.
        document.addEventListener('show.bs.dropdown', function(e) {
            var li = e.target && e.target.closest ? e.target.closest('.sb-slot > li') : null;
            if (!li) return;
            var menu = li.querySelector(':scope > .dropdown-menu');
            var sidebar = document.querySelector('.dash-sidebar');
            if (!menu || !sidebar) return;
            var gap = sidebar.getBoundingClientRect().right - e.target.getBoundingClientRect().right + 10;
            menu.style.setProperty('--sb-gap', Math.max(8, Math.round(gap)) + 'px');
        });
    };
    // Tooltips solo con el sidebar colapsado (escritorio): los elementos guardan su texto en
    // data-rail-title y se enseña un tooltip negro propio a la derecha del icono (el title del
    // navegador no se puede pintar de negro). Abierto, no hay tooltip: el texto ya se lee.
    window.acelerRailTitles = function() {
        var rail = document.body.classList.contains('aCeler-menu-icons') &&
            window.matchMedia('(min-width: 1025px)').matches;
        document.body.classList.toggle('sb-rail-tips', rail);
        document.querySelectorAll('[data-rail-title]').forEach(function(el) {
            el.removeAttribute('title');
        });
        if (!rail && window.acelerRailTipHide) window.acelerRailTipHide();
    };

    (function() {
        if (window.acelerRailTipHide) return;
        var tip = null, current = null, timer = null;
        var hide = function() {
            clearTimeout(timer);
            current = null;
            if (tip) tip.classList.remove('is-on');
        };
        // Pequeña espera: al pasar el ratón de largo por el menú no van saltando tooltips
        var show = function(el) {
            clearTimeout(timer);
            current = el;
            timer = setTimeout(function() { if (current === el) reveal(el); }, 250);
        };
        // Dos fuentes: data-rail-title (menú colapsado, a la derecha) y data-aceler-tip (cualquier
        // pantalla que lo quiera, p. ej. el Gantt; debajo, o a la derecha con data-aceler-tip-pos="right")
        var reveal = function(el) {
            var rail = el.hasAttribute('data-rail-title') && !el.hasAttribute('data-aceler-tip');
            var text = rail ? el.getAttribute('data-rail-title') : el.getAttribute('data-aceler-tip');
            if (!text) return hide();
            if (!tip) {
                tip = document.createElement('div');
                tip.className = 'sb-rail-tip';
                tip.setAttribute('role', 'tooltip');
                document.body.appendChild(tip);
            }
            tip.textContent = text;
            var r = el.getBoundingClientRect();
            var below = !rail && el.getAttribute('data-aceler-tip-pos') !== 'right';
            tip.classList.toggle('is-below', below);
            if (below) {
                tip.style.top = Math.round(r.bottom + 6) + 'px';
                tip.style.left = Math.round(r.left + r.width / 2) + 'px';
                tip.classList.add('is-on');
                // Que no se salga por los lados de la ventana
                var tr = tip.getBoundingClientRect();
                var shift = tr.right > window.innerWidth - 8 ? window.innerWidth - 8 - tr.right : (tr.left < 8 ? 8 - tr.left : 0);
                if (shift) tip.style.left = Math.round(r.left + r.width / 2 + shift) + 'px';
            } else {
                tip.style.left = Math.round(r.right + 8) + 'px';
                tip.style.top = Math.round(r.top + r.height / 2) + 'px';
                tip.classList.add('is-on');
            }
        };
        var target = function(e) {
            var t = e.target && e.target.closest ? e.target : null;
            if (!t) return null;
            var generic = t.closest('[data-aceler-tip]');
            if (generic) return generic;
            if (!document.body.classList.contains('sb-rail-tips')) return null;
            var el = t.closest('[data-rail-title]');
            // Solo los del menú lateral (y su cabecera movida dentro)
            return el && el.closest('.dash-sidebar') ? el : null;
        };
        document.addEventListener('mouseover', function(e) {
            var el = target(e);
            if (el && el !== current) show(el);
            else if (!el && current) hide();
        });
        document.addEventListener('focusin', function(e) {
            var el = target(e);
            if (el) show(el);
        });
        document.addEventListener('focusout', hide);
        document.addEventListener('mousedown', hide);
        window.addEventListener('scroll', hide, true);
        window.acelerRailTipHide = hide;

        // Toda la aplicación usa estos tooltips: acelerTipify(contenedor) pasa sus title a
        // data-aceler-tip, también los que se pongan después por JS. Deja en paz lo que tiene
        // su propio tooltip o lo necesita así: tooltips/popovers de Bootstrap ya creados,
        // data-toggle="tooltip", tooltipCus, SVG, las opciones del menú (data-rail-title, solo con
        // el menú plegado), opciones de un select, iframes y campos con pattern (el navegador usa
        // el title en el aviso de validación). Para excluir algo a mano: data-no-aceler-tip.
        var skipTip = function(el) {
            var tag = el.tagName;
            if (tag === 'OPTION' || tag === 'OPTGROUP' || tag === 'IFRAME' || tag === 'LINK' || tag === 'STYLE') return true;
            if (el.hasAttribute('pattern') || el.hasAttribute('data-rail-title') || el.hasAttribute('data-bs-original-title')) return true;
            if ((el.getAttribute('data-bs-toggle') || el.getAttribute('data-toggle')) === 'tooltip') return true;
            if (el.classList && el.classList.contains('tooltipCus')) return true;
            return !!el.closest('svg, [data-no-aceler-tip]');
        };
        window.acelerTipify = function(root) {
            if (!root || root.__acelerTips) return;
            for (var p = root.parentElement; p; p = p.parentElement) if (p.__acelerTips) return; // ya cubierto
            root.__acelerTips = true;
            var convert = function(el) {
                if (!el.getAttribute || !el.hasAttribute('title')) return;
                if (skipTip(el)) return;
                var t = (el.getAttribute('title') || '').trim();
                el.removeAttribute('title');
                if (!t) { el.removeAttribute('data-aceler-tip'); return; }
                el.setAttribute('data-aceler-tip', t);
                if (!el.getAttribute('aria-label') && !el.textContent.trim()) el.setAttribute('aria-label', t);
            };
            convert(root);
            root.querySelectorAll('[title]').forEach(convert);
            new MutationObserver(function(list) {
                list.forEach(function(m) {
                    if (m.type === 'attributes') convert(m.target);
                    else m.addedNodes.forEach(function(n) {
                        if (n.nodeType !== 1) return;
                        convert(n);
                        if (n.querySelectorAll) n.querySelectorAll('[title]').forEach(convert);
                    });
                });
            }).observe(root, { subtree: true, childList: true, attributes: true, attributeFilter: ['title'] });
        };
        var tipifyPage = function() { window.acelerTipify(document.body); };
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', tipifyPage);
        else tipifyPage();
    })();

    // ------------------------------------------------------------------
    // Pestañas "Equipo" / "Mi actividad": solo se ven las opciones del grupo elegido
    // (las que no son de ningún grupo, como Configuración, se ven siempre).
    // - Si la página actual es de un grupo, se abre en ese grupo.
    // - Si no, se usa el último elegido (se recuerda en el navegador).
    // Se aplica aquí, antes de pintar, para que no se vea el cambio.
    // ------------------------------------------------------------------
    (function() {
        var nav = document.querySelector('.dash-sidebar');
        var tabs = document.querySelectorAll('.sb-scope-tab');
        if (!nav || !tabs.length) return;
        var KEY = 'aceler.sidebar.scope';

        var apply = function(scope, remember) {
            nav.setAttribute('data-scope', scope);
            tabs.forEach(function(t) {
                var on = t.getAttribute('data-scope') === scope;
                t.classList.toggle('active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            if (remember) {
                try { window.localStorage.setItem(KEY, scope); } catch (e) {}
            }
            // Tooltip del botón de cambio (raíl): grupo actual y a cuál cambia
            var sw = document.querySelector('.sb-scope-swap');
            var tip = document.getElementById('sbTooltipText');
            if (sw) {
                var labelTeam = sw.getAttribute('data-label-team');
                var labelMine = sw.getAttribute('data-label-mine');
                var current = scope === 'mine' ? labelMine : labelTeam;
                var other = scope === 'mine' ? labelTeam : labelMine;
                var text = current + ' · ' + ((tip && tip.dataset.switch) || '→') + ' ' + other;
                sw.setAttribute('data-rail-title', text);
                sw.setAttribute('aria-label', text);
                window.acelerRailTitles();
            }
        };

        var saved = null;
        try { saved = window.localStorage.getItem(KEY); } catch (e) {}
        var page = nav.getAttribute('data-scope-page');
        apply(page || (saved === 'mine' ? 'mine' : 'team'), !!page);

        tabs.forEach(function(t) {
            t.addEventListener('click', function() { apply(t.getAttribute('data-scope'), true); });
        });

        // Sidebar colapsado: botón de flechas que alterna el grupo, con animación
        // (giro de las flechas, rebote de la insignia, fundido del menú y tooltip breve)
        var swap = document.querySelector('.sb-scope-swap');
        if (swap) {
            var menu = document.querySelector('.dash-navbar');
            var restart = function(el, cls) {
                if (!el) return;
                el.classList.remove(cls);
                void el.offsetWidth;
                el.classList.add(cls);
            };
            swap.addEventListener('click', function() {
                var next = nav.getAttribute('data-scope') === 'mine' ? 'team' : 'mine';
                apply(next, true);
                restart(swap, 'is-swapping');
                restart(menu, 'sb-list-swap');

                var old = swap.querySelector('.sb-swap-tip');
                if (old) old.remove();
                var tip = document.createElement('span');
                tip.className = 'sb-swap-tip';
                tip.textContent = swap.getAttribute(next === 'mine' ? 'data-label-mine' : 'data-label-team');
                swap.appendChild(tip);
                window.setTimeout(function() { tip.remove(); }, 1500);
            });
            swap.addEventListener('animationend', function(e) {
                if (e.target === swap.querySelector('.sb-swap-arrows')) swap.classList.remove('is-swapping');
            });
        }
    })();

    // La cabecera se incluye ANTES que el sidebar (layouts/admin.blade.php): ya está en el
    // DOM, así que se coloca aquí mismo, sin que el navegador llegue a pintar los huecos vacíos.
    window.acelerPlaceHeader();
    document.addEventListener('DOMContentLoaded', window.acelerPlaceHeader);

    // Detalle oculto: "navidad" en el buscador de proyectos pone un gorro al logo y hace nevar
    (function() {
        var snow = null;
        var makeSnow = function() {
            snow = document.createElement('div');
            snow.className = 'aceler-snow';
            snow.setAttribute('aria-hidden', 'true');
            var flakes = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 60;
            for (var i = 0; i < flakes; i++) {
                var f = document.createElement('span');
                f.className = 'aceler-snowflake';
                f.textContent = Math.random() < 0.35 ? '❄' : '•';
                f.style.left = (Math.random() * 100) + 'vw';
                f.style.fontSize = (8 + Math.random() * 12).toFixed(1) + 'px';
                f.style.opacity = (0.55 + Math.random() * 0.45).toFixed(2);
                f.style.setProperty('--fall', (7 + Math.random() * 8).toFixed(1) + 's');
                f.style.setProperty('--sway', (2.5 + Math.random() * 3).toFixed(1) + 's');
                f.style.setProperty('--drift', (Math.random() * 60 - 30).toFixed(0) + 'px');
                // Retraso negativo: la nieve ya está repartida por la pantalla al aparecer
                f.style.animationDelay = '-' + (Math.random() * 15).toFixed(1) + 's, -' + (Math.random() * 5).toFixed(1) + 's';
                snow.appendChild(f);
            }
            document.body.appendChild(snow);
        };

        // Del 1 de diciembre al 6 de enero (Reyes), ambos incluidos, se activa solo, sin escribir nada
        var today = new Date();
        var month = today.getMonth(); // 0 = enero, 11 = diciembre
        var isSeason = month === 11 || (month === 0 && today.getDate() <= 6);

        // El usuario puede quitar la nieve (el gorro se queda). Se recuerda en este navegador
        // y por temporada: quien la quite este año la vuelve a ver la Navidad siguiente.
        var seasonYear = month === 0 ? today.getFullYear() - 1 : today.getFullYear();
        var snowKey = 'aceler.snow.off.' + seasonYear;
        var snowOff = false;
        try { snowOff = localStorage.getItem(snowKey) === '1'; } catch (e) {}

        var setXmas = function(on) {
            document.body.classList.toggle('aceler-xmas', on);
            document.body.classList.toggle('aceler-nosnow', snowOff);
            if (on && !snowOff && !snow) makeSnow();
        };

        var syncSnowButton = function() {
            var btn = document.getElementById('acelerSnowToggle');
            if (!btn) return;
            var text = snowOff ? btn.dataset.onText : btn.dataset.offText;
            btn.setAttribute('aria-pressed', snowOff ? 'false' : 'true');
            btn.setAttribute('title', text);
            btn.setAttribute('aria-label', text);
        };

        var initSnowButton = function() {
            var item = document.getElementById('acelerSnowItem');
            if (!item || !isSeason) return;
            item.hidden = false;
            syncSnowButton();
        };

        document.addEventListener('click', function(e) {
            var btn = e.target.closest && e.target.closest('#acelerSnowToggle');
            if (!btn) return;
            snowOff = !snowOff;
            try {
                if (snowOff) localStorage.setItem(snowKey, '1');
                else localStorage.removeItem(snowKey);
            } catch (err) {}
            setXmas(document.body.classList.contains('aceler-xmas'));
            syncSnowButton();
        });

        if (isSeason) setXmas(true);
        // El botón está en la cabecera, que puede pintarse después de este script
        initSnowButton();
        document.addEventListener('DOMContentLoaded', initSnowButton);

        document.addEventListener('input', function(e) {
            if (!e.target || e.target.id !== 'searchInput') return;
            // En temporada sigue activo aunque se borre el texto; fuera de ella, el truco de siempre
            setXmas(isSeason || /navidad/i.test(e.target.value || ''));
        });
    })();

    //when the user clicks on create timetable redirect it to timetable creation
    /*$(document).ready(function() {
        $('.buttonAlertCreate').click(function() {
            let currentUrl = window.location.href;
            let nuevaUrl = url.replace(/\/[^/]*$/, "/my-account#v-pills-timetable");

            window.location.href = nuevaUrl;
        });
    });*/
</script>
<!-- Agrega jQuery (al final: un script externo antes de colocar la cabecera dejaba pintar el sidebar a medias) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
