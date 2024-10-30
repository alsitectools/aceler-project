@php
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp
@php
    $languages = \App\Models\Utility::languages();
    if (\Auth::user()->type == 'admin') {
        //añadido
        $currentWorkspace = Auth::user()->currentWorkspace;

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
        $adminSetting = App\Models\Utility::getAdminPaymentSettings();
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
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
    $currantLang = basename(App::getLocale());
    if ($currantLang == '') {
        $currantLang = 'es';
    }
@endphp


<style type="text/css">
    .top_header {
        left: auto !important;
        top: 60px !important;
    }

    .noti-body {
        height: 300px;
        overflow: auto;
    }
</style>
<header class="dash-header {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="header-wrapper p-0 me-1">
        <div class="dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="dropdown dash-h-item drp-company ms-4">
                    <a class="dash-head-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img class="theme-avtar"
                            @if (Auth::user()->avatar) 
                                src="{{ url('storage/app/public/' . Auth::user()->avatar) }}" 
                            @else 
                                avatar="{{ Auth::user()->name }}" 
                            @endif
                            alt="{{ Auth::user()->name }}">
                        <span class="hide-mob ms-2">{{ __('Hi') }},{{ Auth::user()->name }} !</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        @foreach (Auth::user()->workspaces() as $workspace)
                            @if (Auth::user()->id == $workspace->user_id)
                                <a href="{{ route('change-workspace', $workspace->workspace_id) }}"
                                    id="change-workspace" class="dropdown-item">
                                    <span>{{ $workspace->name }}</span>
                                    @if ($currentWorkspace->id == $workspace->workspace_id)
                                        <i class="ti ti-checks text-success ms-3"></i>
                                    @endif
                                </a>
                            @else
                                <a href="#" class="dropdown-item" title="{{ __('Locked') }}">
                                    <i class="ti ti-lock"></i>
                                    <span>{{ $workspace->name }}</span>
                                </a>
                            @endif
                        @endforeach

                        <a href="@auth('web'){{ route('users.my.account') }}@elseauth{{ route('client.users.my.account') }}@endauth"
                            class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('My Profile') }}</span>
                        </a>
                        <a href="#" class="dropdown-item "
                            onclick="event.preventDefault();document.getElementById('logout-form1').submit();">
                            <i class="ti ti-power"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="logout-form1"
                            action="@auth('web'){{ route('logout') }}@elseauth{{ route('client.logout') }}@endauth"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Brand + Toggler (for mobile devices) -->

        <div class="ms-auto">
            <ul class="list-unstyled" style="padding-right: 15px;">

                @if ($adminSetting['enable_chat'] == 'on')
                    <li class="dash-h-item">
                        <a class="dash-head-link me-0" href="{{ url('chats') }}">
                            <i class="ti ti-message-circle"></i>
                            <span
                                class="bg-danger dash-h-badge message-counter custom_messanger_counter">{{ $unseenCounter }}<span
                                    class="sr-only"></span>
                            </span></a>
                    </li>
                @endif

                <li class="dropdown dash-h-item drp-notification">
                    @if (isset($currentWorkspace) && $currentWorkspace)
                        @auth('web')
                            @php
                                $notifications = Auth::user()->notifications($currentWorkspace->id);
                            @endphp
                            <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">

                                <i class="ti ti-bell"></i>
                                <span class="@if (count($notifications) > 0) bg-danger dash-h-badge dots @endif"><span
                                        class="sr-only"></span></span>
                            </a>
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end notification_menu_all">
                                <div class="noti-header">
                                    <h5 class="m-0">{{ __('Notification') }}</h5>
                                    <a href="#"
                                        data-url="{{ route('delete_all.notifications', $currentWorkspace->slug) }}"
                                        class="dash-head-link clear_all_notifications">{{ __('Clear all') }}</a>
                                </div>
                                <div class="noti-body">
                                    <div class="limited">
                                        @foreach ($notifications as $notification)
                                            @php
                                                $project = $notification->project;
                                                $task = $notification->task;
                                                $notifyingUser = $notification->user;

                                                // Define variables for the notification data
                                                $projectTitle = $project ? $project->title : '';
                                                $taskTitle = $task ? $task->title : '';
                                                $notifyingUserName = $notifyingUser->name;

                                                // Define other variables you need for HTML
                                                $link = ''; // Replace with the actual link
                                                $name = ''; // Replace with the notification icon or name
                                                $text = ''; // Replace with the notification text
                                                $date = $notification->created_at->diffForHumans();
                                                $data = json_decode($notification->data);
                                            @endphp
                                            @if ($notification->user && trim($notification->user->name) != '')
                                                @php
                                                    $name = '';
                                                    $nameParts = explode(' ', $notification->user->name);
                                                @endphp

                                                @foreach ($nameParts as $word)
                                                    @php
                                                        $name .= strtoupper($word[0]);
                                                    @endphp
                                                @endforeach
                                            @endif

                                            @if ($notification->type == 'task_assign')
                                                @php
                                                    if ($project) {
                                                        $link = route('projects.task.board', [
                                                            $notification->workspace_id,
                                                            $notification->project_id,
                                                        ]);
                                                        $text =
                                                            __('New task assign') .
                                                            ' <b>' .
                                                            $data->title .
                                                            '</b> ' .
                                                            __('in project') .
                                                            ' <b>' .
                                                            $project->name .
                                                            '</b>';
                                                        $icon = 'fa fa-clock-o';
                                                    } else {
                                                        return '';
                                                    }
                                                @endphp
                                            @elseif($notification->type == 'project_assign')
                                                @php
                                                    $link = route('projects.show', [
                                                        $notification->workspace_id,
                                                        $notification->data->id,
                                                    ]);
                                                    $text = __('New project assign') . ' <b>' . $data->title . '</b>';
                                                    $icon = 'fa fa-suitcase';
                                                @endphp
                                            @elseif($notification->type == 'bug_assign')
                                                @php
                                                    if ($project) {
                                                        $link = route('projects.bug.report', [
                                                            $notification->workspace_id,
                                                            $notification->project_id,
                                                        ]);
                                                        $text =
                                                            __('New bug assign') .
                                                            ' <b>' .
                                                            $data->title .
                                                            '</b> ' .
                                                            __('in project') .
                                                            ' <b>' .
                                                            $project->name .
                                                            '</b>';
                                                        $icon = 'fa fa-bug';
                                                    }
                                                @endphp
                                            @endif
                                            <a href="{{ $link }}" class="list-group-item list-group-item-action">
                                                <div class="d-flex align-items-center" data-toggle="tooltip"
                                                    data-placement="right" data-title="{{ $date }}">
                                                    <div class="notification_icon_size">
                                                        <span
                                                            class="avatar bg-primary text-white rounded-circle px-2 py-1">{{ $name }}</span>
                                                    </div>
                                                    <div class="flex-fill ml-3">
                                                        <div class="h6 text-sm mb-0">
                                                            {{ $notification->user->name }}
                                                            <small class="float-end text-muted">{{ $date }}</small>
                                                        </div>
                                                        <p class="text-sm lh-140 mb-0">
                                                            {!! $text !!}
                                                        </p>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>

                                    <div class="all_notification">
                                    </div>

                                </div>
                            </div>
                        @endauth
                    @endif
                </li>

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span
                            class="drp-text hide-mob">{{ ucfirst(\App\Models\Utility::getlang_fullname($currantLang)) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @if (\Auth::guard('client')->check())
                            @foreach ($languages as $languageCode => $languageFullName)
                                <a href="{{ route('change_lang_workspace1', [$currentWorkspace->id, $languageCode]) }}"
                                    class="dropdown-item {{ $currantLang == $languageCode ? 'text-danger' : '' }}">
                                    <span>{{ $languageFullName }}</span>
                                </a>
                            @endforeach
                        @endif
                        @if (\Auth::user()->type == 'admin')
                            @foreach ($languages as $languageCode => $languageFullName)
                                <a href="{{ route('change_lang_admin', $languageCode) }}"
                                    class="dropdown-item {{ $currantLang == $languageCode ? 'text-danger' : '' }}">
                                    <span>{{ $languageFullName }}</span>
                                </a>
                            @endforeach
                            <div class="dropdown-divider m-0"></div>
                            <a href="#" class="dropdown-item text-primary" data-ajax-popup="true"
                                data-size="md" data-title="{{ __('Create Language') }}" data-toggle="tooltip"
                                title="{{ __('Create Language') }}" data-url="{{ route('create_lang_workspace') }}">
                                <span class="dash-mtext">{{ __('Create Language') }}</span></a>
                            <div class="dropdown-divider m-0"></div>
                            <a href="{{ route('lang_workspace') }}" class="dropdown-item text-primary"><span
                                    class="dash-mtext">{{ __('Manage Language') }}</span></a>
                        @elseif(isset($currentWorkspace) && $currentWorkspace && \Auth::guard('web')->check())
                            @foreach ($languages as $languageCode => $languageFullName)
                                <a href="{{ route('change_lang_workspace', [$currentWorkspace->id, $languageCode]) }}"
                                    class="dropdown-item {{ $currantLang == $languageCode ? 'text-danger' : '' }}">
                                    <span>{{ $languageFullName }}</span>
                                </a>
                            @endforeach
                        @endif
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
