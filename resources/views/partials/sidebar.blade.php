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

    $week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $workHoursWeek = []; 

    //userTimetable is an array of arrays, we need to take the first element of the array
    $timetable = $userTimetable[0]; 

    //get the days and the hours 
    foreach ($timetable as $key => $value) {
            
            if(in_array(strtolower($key), $week)){
                $workHoursWeek[$key] = $value;
            }
        }
    //check if the user implemented his timetable 
    $emptyTimetable = empty(array_filter($workHoursWeek, fn($value) => !is_null($value)));
@endphp

<style>
    .ajustarImg {
        width: 270px;
    }

    .alertDiv{
        background-color: #ffae6a;
        width: 86%;
        height: 18%;
        margin-left: 7%;
        border-radius: 12px;
    }
    .alertTitle{
        color: #aa182c;
        display: flex;
        font-size: 33px;
        align-items: center;
        justify-content: center;
    }
    .alertSubstitle{
        color: white;
        font-size: 12px;
        padding-bottom: 10px;
    }
    .alertCenter{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    
    .buttonAlertCreate{
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
.screenAlert-icon{border-radius:50%;border:4px solid gray;box-sizing:content-box;height:80px;margin:20px auto;padding:0;position:relative;width:80px}
.screenAlert-icon + .screenAlert-icon {margin-top:50px}

/* CSS WARNING */
.screenAlert-icon.screenAlert-warning{border-color:#F8BB86}
.screenAlert-icon.screenAlert-warning:before{-webkit-animation:pulseWarning 2s linear infinite;animation:pulseWarning 2s linear infinite;border-radius:50%;content:"";display:inline-block;height:100%;opacity:0;position:absolute;width:100%}
.screenAlert-icon.screenAlert-warning:after{border-radius:50%;content:'';display:block;height:100%;position:absolute;width:100%;z-index:1}
.screenAlert-icon.screenAlert-warning .screenAlert-body{background-color:#F8BB86;border-radius:2px;height:47px;left:50%;margin-left:-2px;position:absolute;top:10px;width:5px;z-index:2}
.screenAlert-icon.screenAlert-warning .screenAlert-dot{background-color:#F8BB86;border-radius:50%;bottom:10px;height:7px;left:50%;margin-left:-3px;position:absolute;width:7px;z-index:2}
.scaleWarning{-webkit-animation:scaleWarning .75s infinite alternate;animation:scaleWarning .75s infinite alternate}
.pulseWarningIns{-webkit-animation:pulseWarningIns .75s infinite alternate;animation:pulseWarningIns .75s infinite alternate}
/* ANIMATE WARNING */
@-webkit-keyframes scaleWarning{0%{-webkit-transform:scale(1);transform:scale(1)}30%{-webkit-transform:scale(1.02);transform:scale(1.02)}100%{-webkit-transform:scale(1);transform:scale(1)}}
@keyframes scaleWarning{0%{-webkit-transform:scale(1);transform:scale(1)}30%{-webkit-transform:scale(1.02);transform:scale(1.02)}100%{-webkit-transform:scale(1);transform:scale(1)}}
@-webkit-keyframes pulseWarning{0%{background-color:#fff;-webkit-transform:scale(1);transform:scale(1);opacity:.5}30%{background-color:#fff;-webkit-transform:scale(1);transform:scale(1);opacity:.5}100%{background-color:#F8BB86;-webkit-transform:scale(2);transform:scale(2);opacity:0}}
@keyframes pulseWarning{0%{background-color:#fff;-webkit-transform:scale(1);transform:scale(1);opacity:.5}30%{background-color:#fff;-webkit-transform:scale(1);transform:scale(1);opacity:.5}100%{background-color:#F8BB86;-webkit-transform:scale(2);transform:scale(2);opacity:0}}
@-webkit-keyframes pulseWarningIns{0%{background-color:#F8D486}100%{background-color:#F8BB86}}
@keyframes pulseWarningIns{0%{background-color:#F8D486}100%{background-color:#F8BB86}}

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
                <img class="ajustarImg" src="{{ asset('assets/img/logoRed1.png') }}" alt="logo" />
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
                                    class="dash-mtext">{{ __('Order forms') }}</span></a>
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
                        <li class="dash-item {{ Request::route()->getName() == 'tutorialHome' ? ' active' : '' }}">
                            <a href="{{ route('home.showTutorial', [$currentWorkspace->slug]) }}"
                                class="dash-link "><span class="dash-micon"><i class="fas fa-book"></i>
                                </span>
                                <span
                                    class="dash-mtext">Tutorial</span></a>
                        </li>
                        @if($emptyTimetable == 1 && $userType != 'client')
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
                        @endif
                    <!-- <li class="dash-item dash-hasmenu">
                        <a href="{{ route('clients.index', $currentWorkspace->slug) }}"
                            class="dash-link {{ Request::route()->getName() == 'clients.index' ? ' active' : '' }} "><span
                                class="dash-micon"> <img class="img-fluid"
                                    src="{{ asset('assets/img/salesManager.png') }}" alt="logo" /> </span><span
                                class="dash-mtext"> {{ __('Sales managers') }}</span></a>
                    </li>
                    <li
                        class="dash-item {{ Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users_logs.index' ? ' active' : '' }}">
                        <a href="{{ route('users.index', $currentWorkspace->slug) }}" class="dash-link ">
                            <span
                            class="dash-micon"> <img class="img-fluid"
                                src="{{ asset('assets/img/technicians.png') }}" alt="logo" /> </span>
                            <span class="dash-mtext">{{ __('Technicians') }}</span>
                        </a>
                    </li> -->
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
<!-- Agrega jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    //when the user clicks on create timetable redirect it to timetable creation
    $(document).ready(function() {
        $('.buttonAlertCreate').click(function() {
            let currentUrl = window.location.href;
            let nuevaUrl = url.replace(/\/[^/]*$/, "/my-account#v-pills-timetable");

            window.location.href = nuevaUrl;
        });
    });
</script>

