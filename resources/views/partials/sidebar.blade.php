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
<nav style="padding-top: 5px; padding-left: 5px;"
    class="vistaPortatil dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="mt-2">
                <img class="ajustarImg" src="{{ asset('assets/img/acerlerRemastered.png') }}" alt="logo" />
            </a>

        </div>
        <div class="navbar-content">
            <ul class="dash-navbar ">
                @if ($userWorkspaces->isNotEmpty())
                    <div class="workspace-dropdown-container mt-2">
                        <button class="workspace-name-header" id="workspaceButton">
                            <span id="workspaceName">{{ $currentWorkspace?->name ?? __('Select workspace') }}</span>
                            <i class="fa-solid fa-chevron-down workspace-dropdown-icon"></i>
                        </button>
                        <div class="workspace-dropdown" id="workspaceDropdown">
                            <div class="workspace-search">
                                <input type="text" id="workspaceSearchInput"
                                    placeholder="{{ __('Search workspace...') }}">
                            </div>
                            <div class="workspace-list" id="workspaceList">
                                @forelse(Auth::user()->workspaces() as $ws)
                                    <div class="workspace-item @if ($ws->workspace_id == $currentWorkspace->id) active @endif"
                                        data-workspace-id="{{ $ws->workspace_id }}"
                                        data-workspace-name="{{ $ws->name }}"
                                        data-workspace-url="{{ route('change-workspace', $ws->workspace_id) }}">
                                        {{ $ws->name }}
                                    </div>
                                @empty
                                    <div style="padding: 12px 16px; color: #999; font-size: 13px;">
                                        {{ __('No workspaces available') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
                <li
                    class="dash-item  dash-hasmenu {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="dash-link menu-element">
                        <span class="dash-micon"><i class="fa-solid fa-bookmark"></i></span>
                        <span class="dash-mtext">{{ __('Resume') }}</span>
                    </a>
                </li>
                @if (isset($currentWorkspace) && $currentWorkspace)
                    <li
                        class="dash-item  {{ (Request::route()->getName() == 'projects.index' || Request::segment(2) == 'projects') && Request::route()->getName() != 'projects.milestone.board' ? 'active' : '' }}">
                        <a href="{{ route('projects.index', $currentWorkspace->slug) }}"
                            class="dash-link  menu-element">
                            <span class="dash-micon"><i class="fa-solid fa-diagram-project"></i></span><span
                                class="dash-mtext">{{ __('Projects') }}</span></a>
                    </li>

                    </li>
                    <li
                        class="dash-item {{ Request::route()->getName() == 'projects.milestone.board' ? 'active' : '' }}">
                        <a href="{{ route('projects.milestone.board', [$currentWorkspace->slug, -1]) }}"
                            class="dash-link  menu-element">
                            <span class="dash-micon"><i class="fa-solid fa-file-lines"></i></span><span
                                class="dash-mtext">{{ __('Milestones') }}</span></a>
                    </li>
                    {{-- si mostramos todos los proyectos enviamos -1 o proyecto en especifico --}}
                    <li class="dash-item {{ Request::route()->getName() == 'my_projects' ? 'active' : '' }}">
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
                    <li class="dash-item ">
                        <a href="{{ route('projects.my_milestone_board') }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fa-solid fa-file-lines"></i>
                                <i class="fa-solid fa-user user-badge-sub"
                                    style="position: absolute; bottom: -2px; right: -1px; font-size: 15px; color: #525a69; background: #fcf9f9; border-radius: 50%; padding: 2px;"></i>
                            </span>
                            <span class="dash-mtext">{{ __('My Milestones') }}</span>
                        </a>
                    </li>
                    <li class="dash-item ">
                        <a href="{{ route('projects.my_tasks') }}" class="dash-link menu-element">
                            <span class="dash-micon"
                                style="position: relative; display: inline-flex; align-items: center; justify-content: center; overflow:hidden;">
                                <i class="fas fa-tasks"></i>
                            </span>
                            <span class="dash-mtext">{{ __('My Tasks') }}</span>
                        </a>
                    </li>
                    <li class="dash-item  {{ Request::route()->getName() == 'timesheet.index' ? 'active' : '' }}">
                        <a href="{{ route('timesheet.index', $currentWorkspace->slug) }}"
                            class="dash-link  menu-element">
                            <span class="dash-micon"><i class="fa-solid fa-clock"></i></span><span
                                class="dash-mtext">{{ __('My Timesheet') }}</span></a>
                    </li>
                    <li class="dash-item  {{ Request::route()->getName() == 'calender.index' ? 'active' : '' }}">
                        <a href="{{ route('calender.google.calendar', $currentWorkspace->slug) }}"
                            class="dash-link  menu-element"><span class="dash-micon"><i
                                    class="fa-regular fa-calendar"></i></span><span
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
                                data-feather="settings"></i></span><span class="dash-mtext">{{ __('Settings') }}</span>
                    </a>
                </li>
            @endif
            </ul>
        </div>
    </div>
</nav>
<!-- Agrega jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
                const workspaceName = this.getAttribute('data-workspace-name');
                const workspaceUrl = this.getAttribute('data-workspace-url');

                // Actualizar el nombre mostrado
                document.getElementById('workspaceName').textContent = workspaceName;

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
                const workspaceName = item.getAttribute('data-workspace-name');
                if (!workspaceName) {
                    item.classList.add('hidden');
                    return;
                }

                const name = workspaceName.toLowerCase();
                if (name.includes(searchTerm)) {
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

    //when the user clicks on create timetable redirect it to timetable creation
    /*$(document).ready(function() {
        $('.buttonAlertCreate').click(function() {
            let currentUrl = window.location.href;
            let nuevaUrl = url.replace(/\/[^/]*$/, "/my-account#v-pills-timetable");

            window.location.href = nuevaUrl;
        });
    });*/
</script>
