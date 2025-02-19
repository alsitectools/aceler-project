@php
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $logo = 'storage/app/public/';
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

    .notificationSTL {
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
        /* background-color: #a5222f; */
        height: 77px;
        border-radius: 10px;
        box-shadow: 0 6px 30px rgba(182, 186, 203, 0.3);
        font-size: 16px;
        transition: transform 0.5s ease-out, opacity 0.5s ease-out;
    }

    .repoIcon {
        position: absolute;
        right: 10%;
        /* filter: invert(1); */
    }

    .noNotificationsContainer {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .textRepo {
        color: black;
        position: absolute;
        left: 10%;
        font-size: 15px;
        max-width: 75%;
        margin-bottom: 19px;
    }

    .MC {
        background-color: #c7c7c74f;
        border-left: 6px solid #595959;
        border-top: 2px solid #c7c7c74f;
        border-bottom: 2px solid #c7c7c74f;
        border-right: 2px solid #c7c7c74f;
    }

    .PC {
        background-color: #03c8ff36;
        border-left: 6px solid #0794bb;
        border-top: 2px solid #03c8ff36;
        border-bottom: 2px solid #03c8ff36;
        border-right: 2px solid #03c8ff36;
    }

    .MF {
        background-color: #25c74336;
        border-left: 6px solid #25C743;
        border-top: 2px solid #25c74336;
        border-bottom: 2px solid #25c74336;
        border-right: 2px solid #25c74336;
    }

    .AP {
        background-color: #ffff0038;
        border-left: 6px solid #dfdf00;
        border-top: 2px solid #ffff0038;
        border-bottom: 2px solid #ffff0038;
        border-right: 2px solid #ffff0038;
    }

    .notification-slide-out {
        transform: translateX(100%);
        opacity: 0;
    }

    .btn-close:focus {
        outline: none;
        box-shadow: none;
        animation: pulse 0.5s ease-in-out;
    }

    .animate-pulse {
        animation: pulse 0.5s ease-in-out;
    }

    .smallDate {
        position: relative;
        top: 30%;
        width: 100%;
        left: 6%;
        font-style: italic;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.3);
        }

        100% {
            transform: scale(1);
        }
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
                            @if (\Auth::user()->avatar) src="{{ asset(Auth::user()->avatar) }}" @else avatar="{{ Auth::user()->name }}" @endif
                            alt="{{ Auth::user()->name }}">

                        <span class="hide-mob ms-2">{{ __('Hi') }}, {{ Auth::user()->name }} </span>
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
                    {{-- <button id="addNotificationBtn" class="btn btn-primary">Añadir Notificación</button> --}}

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

                                        {{-- <div style="margin-bottom: 5px;">
                                            <div class="notificationSTL"><span class="textRepo">Se ha creado el proyecto
                                                    merequetengue</span> <button type="button" class="btn-close repoIcon"
                                                    aria-label="Close"></button></div>
                                        </div> --}}
                                        @if ($notifications->isEmpty())
                                            <div class="noNotificationsContainer"> <i
                                                    class="fa-duotone fa-solid fa-bell-slash" aria-hidden="true"
                                                    style="font-size: 48px; margin-bottom: 30px; color: #d1d1d1;"></i>
                                                <p style="font-size: 15px; color: #d1d1d1;">¡Estás al día! No hay
                                                    notificaciones</p>
                                            </div>
                                        @else
                                            @foreach ($notifications as $notification)
                                                <div style="margin-bottom: 10px;">

                                                    @if ($notification->type == '1')
                                                        <div class="notificationSTL PC"
                                                            data-notification-id="{{ $notification->id }}">
                                                            <span class="textRepo">Se ha creado el proyecto
                                                                {{ $notification->data }}</span>
                                                            <small
                                                                class="text-muted smallDate">{{ ucfirst($notification->created_at->diffForHumans()) }}</small>
                                                            <button type="button" class="btn-close repoIcon"
                                                                aria-label="Close"></button>
                                                        @elseif($notification->type == '2')
                                                            <div class="notificationSTL MC"
                                                                data-notification-id="{{ $notification->id }}">
                                                                <span class="textRepo">Se ha creado el encargo
                                                                    {{ $notification->data }}</span>
                                                                <small
                                                                    class="text-muted smallDate">{{ ucfirst($notification->created_at->diffForHumans()) }}</small>
                                                                <button type="button" class="btn-close repoIcon"
                                                                    aria-label="Close"></button>
                                                            @elseif($notification->type == '3')
                                                                <div class="notificationSTL MF"
                                                                    data-notification-id="{{ $notification->id }}">
                                                                    <span class="textRepo">Se ha acabado el encargo
                                                                        {{ $notification->data }}</span>
                                                                    <small
                                                                        class="text-muted smallDate">{{ ucfirst($notification->created_at->diffForHumans()) }}</small>
                                                                    <button type="button" class="btn-close repoIcon"
                                                                        aria-label="Close"></button>
                                                                @elseif($notification->type == '4')
                                                                    <div class="notificationSTL AP"
                                                                        data-notification-id="{{ $notification->id }}">
                                                                        <span class="textRepo">Se te ha añadido al proyecto
                                                                            {{ $notification->data }}</span>
                                                                        <small
                                                                            class="text-muted smallDate">{{ ucfirst($notification->created_at->diffForHumans()) }}</small>
                                                                        <button type="button" class="btn-close repoIcon"
                                                                            aria-label="Close"></button>
                                                    @endif

                                                </div>
                                    </div>
                        @endforeach
                        @endif
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
            <span class="drp-text hide-mob">{{ ucfirst(\App\Models\Utility::getlang_fullname($currantLang)) }}</span>
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
                <a href="#" class="dropdown-item text-primary" data-ajax-popup="true" data-size="md"
                    data-title="{{ __('Create Language') }}" data-toggle="tooltip"
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
<script>
    document.getElementById('addNotificationBtn').addEventListener('click', function() {
        let msg = prompt("Escribe tu notificación:");
        let ntipe = 4
        if (!msg) return;

        fetch("{{ route('notifications.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    workspace_id: {{ $currentWorkspace->id }},
                    msg: msg,
                    ntipe: ntipe
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let notificationList = document.querySelector('.limited');
                    let newNotification = document.createElement('div');
                    newNotification.classList.add('notificationSTL');
                    newNotification.innerHTML = `
                    <span class="textRepo">${data.data.msg}</span>
                    <span class="textRepo">${data.data.type}</span>
                    <button type="button" class="btn-close repoIcon" aria-label="Close"></button>
                `;
                    notificationList.prepend(newNotification);
                }
            })
            .catch(error => console.error("Error al agregar notificación:", error));
    });
</script>
<script>
    // Asocia el evento click a cada botón de cierre dentro de las notificaciones
    document.querySelectorAll('.notificationSTL .btn-close').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.stopPropagation(); // Evita que se cierre el pop-up

            // Se busca el contenedor de la notificación (con data-notification-id)
            var notificationElement = this.closest('.notificationSTL');
            var notificationId = notificationElement.getAttribute('data-notification-id');

            // Envía la petición para eliminar la notificación individual
            fetch("{{ route('notifications.delete', [$currentWorkspace->slug, '__id']) }}".replace(
                    '__id', notificationId), {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.is_success) {
                        // Añade la clase para la animación
                        notificationElement.classList.add('notification-slide-out');
                        // Una vez finalizada la transición, elimina el elemento del DOM
                        notificationElement.addEventListener('transitionend', function() {
                            notificationElement.remove();
                        });
                    } else {
                        console.error("Error:", data.error);
                    }
                })
                .catch(error => console.error("Error al eliminar notificación:", error));
        });
    });
</script>
<script>
    // Manejador para "Clear all" (eliminar todas las notificaciones)
    document.querySelector('.clear_all_notifications').addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation(); // Evita que el click cierre el dropdown

        // Obtén la URL desde el atributo data-url del enlace
        let clearUrl = this.getAttribute('data-url');

        // Envía la petición para eliminar todas las notificaciones
        fetch(clearUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.is_success) {
                    // Selecciona todas las notificaciones dentro del contenedor ".limited"
                    let notifications = document.querySelectorAll('.limited .notificationSTL');
                    notifications.forEach(function(notificationElement, index) {
                        setTimeout(function() {
                            // Añade la clase para la animación
                            notificationElement.classList.add('notification-slide-out');
                            // Una vez finalizada la transición, elimina el elemento
                            notificationElement.addEventListener('transitionend',
                                function() {
                                    notificationElement.remove();
                                });
                        }, index * 300); // 300ms de delay entre cada eliminación
                    });
                } else {
                    console.error("Error:", data.error);
                }
            })
            .catch(error => console.error("Error al eliminar todas las notificaciones:", error));
    });
</script>
