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

    /* Estado vacío genérico (también lo usan los tableros de encargos: «Encargos ocultos»,
       «Aún no hay encargos»): centrado en su contenedor, en columna. La campana lo ajusta abajo (.acn). */
    .noNotificationsContainer {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* ---------- Campana: panel de notificaciones ---------- */
    .drp-notification .dropdown-menu.acn {
        width: 392px;
        min-width: 0 !important;
        max-width: calc(100vw - 24px);
        padding: 0;
        border: 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 18px 50px rgba(15, 23, 42, .22), 0 0 0 1px rgba(15, 23, 42, .06);
        color: #1f2937;
    }

    .acn .acn-head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 16px 0;
    }

    .acn .acn-head h5 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 700;
        color: #111827;
    }

    .acn .acn-count {
        background: #AA182C;
        color: #fff;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        padding: 0 7px;
        line-height: 18px;
    }

    .acn .acn-sp {
        flex: 1;
    }

    .acn .acn-link-btn {
        border: 0;
        background: none;
        padding: 0;
        font-size: 12.5px;
        font-weight: 600;
        color: #AA182C;
        cursor: pointer;
    }

    .acn .acn-link-btn:hover {
        text-decoration: underline;
    }

    .acn .acn-tabs {
        display: flex;
        gap: 18px;
        padding: 0 16px;
        margin-top: 10px;
        border-bottom: 1px solid #eef0f3;
    }

    .acn .acn-tabs button {
        border: 0;
        background: none;
        padding: 8px 0;
        font-size: 13px;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        cursor: pointer;
    }

    .acn .acn-tabs button b {
        font-weight: 500;
        color: #94a3b8;
        margin-left: 4px;
    }

    .acn .acn-tabs button.is-on {
        color: #AA182C;
        font-weight: 600;
        border-bottom-color: #AA182C;
    }

    .acn .acn-tabs button.is-on b {
        color: #AA182C;
    }

    .drp-notification .acn .noti-body.acn-body {
        position: relative;
        height: auto;
        min-height: 120px;
        max-height: min(470px, calc(100vh - 220px));
        overflow-y: auto;
        padding: 0;
        scrollbar-width: thin;
    }

    .acn .acn-grp {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #94a3b8;
        padding: 12px 16px 4px;
    }

    .acn .acn-item {
        position: relative;
        transition: opacity .2s ease, transform .2s ease;
    }

    .acn .acn-item + .acn-item {
        border-top: 1px solid #f3f4f6;
    }

    .acn .acn-item.is-unread {
        background: #fdf6f7;
    }

    .acn .acn-item:hover {
        background: #f8fafc;
    }

    .acn .acn-item.is-leaving {
        opacity: 0;
        transform: translateX(24px);
    }

    .acn .acn-open {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 10px 40px 10px 16px;
        color: inherit;
        text-decoration: none;
    }

    .acn .acn-ic {
        flex: 0 0 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .acn .acn-ic--review { background: #fff1e0; color: #d9480f; }
    .acn .acn-ic--assign { background: #eef2ff; color: #4f46e5; }
    .acn .acn-ic--created { background: #e0f2fe; color: #0369a1; }
    .acn .acn-ic--project { background: #e0f2fe; color: #0369a1; }
    .acn .acn-ic--finished { background: #e9f8ee; color: #2f9e44; }

    .acn .acn-tx {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .acn .acn-t1 {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .acn .acn-t2 {
        font-size: 13px;
        color: #374151;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .acn .acn-t3 {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }

    .acn .acn-t3 > span:last-child {
        white-space: nowrap;
    }

    .acn .acn-pill {
        font-size: 10.5px;
        font-weight: 600;
        line-height: 17px;
        border-radius: 999px;
        padding: 0 7px;
        background: #f1f5f9;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 170px;
    }

    .acn .acn-dot {
        position: absolute;
        right: 16px;
        top: 16px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #AA182C;
        display: none;
    }

    .acn .acn-item.is-unread .acn-dot {
        display: block;
    }

    .acn .acn-x {
        position: absolute;
        right: 10px;
        top: 9px;
        width: 26px;
        height: 26px;
        border: 0;
        border-radius: 7px;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        opacity: 0;
        cursor: pointer;
        transition: opacity .15s ease;
    }

    .acn .acn-item:hover .acn-x,
    .acn .acn-x:focus-visible {
        opacity: 1;
    }

    .acn .acn-item:hover .acn-dot {
        display: none;
    }

    .acn .acn-x:hover {
        background: #fde2e6;
        color: #AA182C;
    }

    /* En pantallas táctiles no hay hover: la ✕ se ve siempre, discreta */
    @media (hover: none) and (pointer: coarse) {
        .acn .acn-x {
            opacity: .7;
            background: transparent;
        }

        .acn .acn-item.is-unread .acn-dot {
            right: 40px;
        }
    }

    .acn .noNotificationsContainer {
        position: static;
        transform: none;
        width: auto;
        height: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 34px 16px;
        text-align: center;
        color: #94a3b8;
    }

    .acn .noNotificationsContainer i {
        font-size: 34px;
        margin-bottom: 10px;
        color: #cbd5e1;
    }

    .acn .noNotificationsContainer p {
        margin: 0;
        font-size: 13.5px;
    }

    .acn .acn-tab-empty {
        padding: 26px 16px;
        text-align: center;
        font-size: 13px;
        color: #94a3b8;
    }

    .acn .acn-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 16px;
        border-top: 1px solid #eef0f3;
        font-size: 12.5px;
        color: #6b7280;
    }

    .acn [hidden] {
        display: none !important;
    }

    /* ---------- Selector de idioma ---------- */
    .drp-language .dropdown-menu.acl {
        width: 240px;
        min-width: 0 !important;
        padding: 6px;
        border: 0;
        border-radius: 12px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .16), 0 0 0 1px rgba(15, 23, 42, .06);
        font-size: 13.5px;
    }

    .acl .acl-title {
        margin: 0;
        padding: 6px 10px 4px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .acl .acl-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 8px;
        color: #334155;
        text-decoration: none;
        line-height: 1.2;
    }

    .acl .acl-item:hover,
    .acl .acl-item:focus-visible {
        background: #f8fafc;
        color: #111827;
    }

    .acl .acl-code {
        flex: 0 0 30px;
        height: 22px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .acl .acl-tr {
        color: #94a3b8;
        font-size: 12px;
    }

    .acl .acl-check {
        margin-left: auto;
        font-size: 16px;
        color: #AA182C;
    }

    .acl .acl-item.is-on {
        background: #fdf2f4;
        color: #AA182C;
        font-weight: 600;
    }

    .acl .acl-item.is-on .acl-code {
        background: #AA182C;
        color: #fff;
    }

    .acl .acl-sep {
        height: 1px;
        margin: 6px 4px;
        background: #eef0f3;
    }

    .acl .acl-admin {
        color: #64748b;
        font-size: 13px;
    }

    .acl .acl-admin i {
        flex: 0 0 30px;
        text-align: center;
        font-size: 15px;
    }

    /* ---------- Avatar del usuario: iniciales y, si hay foto, la foto encima ---------- */
    .acu-av {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 50%;
        background: #fff;
        color: #AA182C;
        font-weight: 700;
        font-size: 12px;
        line-height: 1;
    }

    .dash-head-link .theme-avtar.acu-av {
        width: 32px;
        height: 32px;
    }

    .acu-av img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    /* ---------- Menú de usuario ---------- */
    .drp-company .dropdown-menu.acu {
        width: 300px;
        min-width: 0 !important;
        padding: 0;
        border: 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 18px 50px rgba(15, 23, 42, .22), 0 0 0 1px rgba(15, 23, 42, .06);
    }

    .acu .acu-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 14px 12px;
        border-bottom: 1px solid #eef0f3;
    }

    .acu .acu-av--big {
        width: 44px;
        height: 44px;
        font-size: 16px;
        background: #fdf2f4;
    }

    .acu .acu-who {
        min-width: 0;
        flex: 1;
    }

    .acu .acu-who b {
        display: block;
        font-size: 14px;
        color: #111827;
        line-height: 1.25;
    }

    .acu .acu-email {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin-top: 1px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .acu .acu-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 6px;
    }

    .acu .acu-tag {
        max-width: 100%;
        font-size: 10.5px;
        font-weight: 600;
        line-height: 18px;
        border-radius: 999px;
        padding: 0 8px;
        background: #f1f5f9;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .acu .acu-tag--role { background: #eef2ff; color: #4338ca; }
    .acu .acu-tag--support { background: #fff7e6; color: #b45309; }

    .acu .acu-sec {
        padding: 6px;
    }

    .acu .acu-sep {
        height: 1px;
        margin: 0 6px;
        background: #eef0f3;
    }

    .acu .dropdown-item.acu-item {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 13.5px;
        color: #334155;
        white-space: normal;
    }

    .acu .dropdown-item.acu-item:hover,
    .acu .dropdown-item.acu-item:focus-visible {
        background: #f8fafc;
        color: #111827;
    }

    .acu .acu-item > i {
        flex: 0 0 20px;
        margin: 0;
        text-align: center;
        font-size: 17px;
        color: #64748b;
    }

    .acu .acu-txt {
        min-width: 0;
        line-height: 1.25;
    }

    .acu .acu-txt small {
        display: block;
        margin-top: 2px;
        font-size: 11.5px;
        color: #94a3b8;
    }

    .acu .dropdown-item.acu-out,
    .acu .dropdown-item.acu-out > i {
        color: #AA182C;
    }

    .acu .dropdown-item.acu-out:hover {
        background: #fdf2f4;
    }

    /* Código del idioma (ES, EN…): solo se ve con el menú lateral plegado, en lugar del globo */
    .drp-language .drp-code {
        display: none;
    }

    /* Selector de delegaciones (píldora) junto a la campana */
    .dash-header .drp-workspace {
        display: flex;
        align-items: center;
    }

    .dash-header .drp-workspace .workspace-dropdown-container {
        width: auto;
    }

    .dash-header .workspace-name-header {
        width: auto;
        min-width: 110px;
        margin-bottom: 0;
        white-space: nowrap;
    }

    /* En la cabecera (móvil) el menú se alinea a la derecha del botón para no salirse */
    .dash-header .workspace-dropdown {
        left: auto;
        right: 0;
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
                    @php
                        // Menú de usuario: iniciales si no hay foto (o si la foto no carga), rol y permisos.
                        // Cada opción describe lo que esa persona tiene de verdad en su perfil.
                        $acuUser = Auth::user();
                        $acuName = trim((string) ($acuUser->name ?? ''));
                        $acuParts = preg_split('/\s+/', $acuName) ?: [];
                        $acuInitials = mb_strtoupper(mb_substr($acuParts[0] ?? '', 0, 1) . mb_substr($acuParts[1] ?? '', 0, 1)) ?: '?';
                        $acuIsWeb = Auth::guard('web')->check();
                        $acuIsExporter = $acuIsWeb && (bool) ($acuUser->is_exporter ?? false);
                        $acuIsSupport = $acuIsWeb && (bool) ($acuUser->is_support ?? false);
                        $acuRole = $acuIsExporter ? __('Axapta exporter') : (($acuUser->type ?? '') === 'admin' ? __('Administrator') : __('User'));
                        $acuProfileHint = $acuIsExporter
                            ? __('Your workspaces, your timetable and the export to Axapta')
                            : __('Your workspaces and your timetable');
                    @endphp
                    <a class="dash-head-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="theme-avtar acu-av" aria-hidden="true" data-name="{{ $acuName }}">
                            <span class="acu-ini">{{ $acuInitials }}</span>
                            @if (!empty($acuUser->avatar))
                                <img src="{{ asset($acuUser->avatar) }}" alt="" onerror="this.remove()">
                            @endif
                        </span>
                        @php
                            // Soporte: banderita en la foto si hay informes de errores nuevos (se gestionan en el perfil)
                            $aprNewReports = $acuIsSupport ? \App\Models\ProblemReport::newCount() : 0;
                        @endphp
                        @if (Auth::guard('web')->check() && (Auth::user()->is_support ?? false))
                            <span class="apr-avatar-flag" @if (!$aprNewReports) hidden @endif
                                title="{{ trans_choice(':count new error report|:count new error reports', $aprNewReports, ['count' => $aprNewReports]) }}">
                                <i class="fa-solid fa-flag" aria-hidden="true"></i>
                            </span>
                        @endif

                        <span class="hide-mob ms-2">{{ __('Hi') }}, {{ Auth::user()->name }} </span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown acu">
                        {{-- Quién ha iniciado sesión: nombre completo, correo, espacio de trabajo y rol --}}
                        <div class="acu-head">
                            <span class="acu-av acu-av--big" aria-hidden="true">
                                <span class="acu-ini">{{ $acuInitials }}</span>
                                @if (!empty($acuUser->avatar))
                                    <img src="{{ asset($acuUser->avatar) }}" alt="" onerror="this.remove()">
                                @endif
                            </span>
                            <div class="acu-who">
                                <b>{{ $acuName }}</b>
                                @if (!empty($acuUser->email))
                                    <span class="acu-email">{{ $acuUser->email }}</span>
                                @endif
                                <div class="acu-tags">
                                    @if (isset($currentWorkspace) && $currentWorkspace)
                                        <span class="acu-tag" title="{{ $currentWorkspace->name }}">{{ $currentWorkspace->name }}</span>
                                    @endif
                                    @if ($acuIsWeb)
                                        <span class="acu-tag acu-tag--role">{{ $acuRole }}</span>
                                    @endif
                                    @if ($acuIsSupport)
                                        <span class="acu-tag acu-tag--support">{{ __('Support') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="acu-sec">
                            <a href="@auth('web'){{ route('users.my.account') }}@elseauth{{ route('client.users.my.account') }}@endauth"
                                class="dropdown-item acu-item">
                                <i class="ti ti-user" aria-hidden="true"></i>
                                <span class="acu-txt">{{ __('My Profile') }}
                                    @if ($acuIsWeb)<small>{{ $acuProfileHint }}</small>@endif
                                </span>
                            </a>
                            @if ($acuIsSupport)
                                <a href="{{ route('users.my.account') }}#reports" class="dropdown-item acu-item">
                                    <i class="ti ti-flag" aria-hidden="true"></i>
                                    <span class="acu-txt">{{ __('Error reports') }}
                                        <small>{{ __('Problems reported by users') }}</small>
                                    </span>
                                    <b class="apr-menu-count" data-reports-new-count @if (!$aprNewReports) hidden @endif>{{ $aprNewReports }}</b>
                                </a>
                            @endif
                            @auth('web')
                                {{-- En móvil no se ve el enlace del pie del menú: va aquí --}}
                                <a href="#" class="dropdown-item acu-item apr-mobile-item" data-problem-report-open>
                                    <i class="ti ti-message-report" aria-hidden="true"></i>
                                    <span class="acu-txt">{{ __('Report a problem') }}</span>
                                </a>
                            @endauth
                        </div>
                        <div class="acu-sep"></div>
                        <div class="acu-sec">
                            <a href="#" class="dropdown-item acu-item acu-out"
                                onclick="event.preventDefault();document.getElementById('logout-form1').submit();">
                                <i class="ti ti-power" aria-hidden="true"></i>
                                <span class="acu-txt">{{ __('Logout') }}</span>
                            </a>
                        </div>
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

                @php
                    // Menú de espacios de trabajo: el actual arriba y el resto por orden alfabético,
                    // con iniciales de color, país y número de proyectos de cada espacio.
                    $wsUserList = Auth::user()->workspaces();
                    $wsIds = $wsUserList->pluck('workspace_id')->map(fn ($v) => (int) $v)->all();
                    $wsCountries = $wsIds ? \App\Models\Workspace::whereIn('id', $wsIds)->pluck('country', 'id') : collect();
                    $wsProjects = $wsIds
                        ? \App\Models\Project::whereIn('workspace', $wsIds)->selectRaw('workspace, COUNT(*) as n')->groupBy('workspace')->pluck('n', 'workspace')
                        : collect();
                    $wsCurrentId = isset($currentWorkspace) && $currentWorkspace ? (int) $currentWorkspace->id : 0;
                    $wsInitials = function ($name) {
                        $words = array_values(array_filter(preg_split('/\s+/u', trim((string) $name)) ?: [], function ($w) {
                            return !in_array(mb_strtolower($w), ['y', 'e', 'de', 'del', 'la', 'el', 'las', 'los', 'and', 'of', 'the', '&', '-'], true);
                        }));
                        if (count($words) >= 2) return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                        return mb_strtoupper(mb_substr($words[0] ?? '?', 0, 2));
                    };
                    $wsEntries = $wsUserList->map(function ($ws) use ($wsCountries, $wsProjects, $wsInitials, $wsCurrentId) {
                        $id = (int) $ws->workspace_id;
                        $display = \App\Models\Workspace::translateName($ws->name);
                        return [
                            'id' => $id,
                            'name' => $ws->name,
                            'display' => $display,
                            'initials' => $wsInitials($display),
                            'tone' => 0,
                            'country' => trim((string) ($wsCountries[$id] ?? '')),
                            'projects' => (int) ($wsProjects[$id] ?? 0),
                            'current' => $id === $wsCurrentId,
                            'url' => route('change-workspace', $id),
                        ];
                    });
                    // Color por orden alfabético entre los espacios del usuario: no cambia al cambiar de
                    // espacio y, hasta 5 espacios, no se repite ninguno
                    $wsEntries = $wsEntries->sortBy(fn ($e) => mb_strtolower($e['display']))->values()
                        ->map(function ($e, $i) { $e['tone'] = $i % 5; return $e; });
                    $wsCurrent = $wsEntries->firstWhere('current', true);
                    $wsOthers = $wsEntries->where('current', false)->sortBy(fn ($e) => mb_strtolower($e['display']))->values();
                @endphp
                @if ($wsUserList->isNotEmpty())
                    <li class="dash-h-item drp-workspace">
                        <div class="workspace-dropdown-container">
                            <button class="workspace-name-header" id="workspaceButton" type="button"
                                aria-haspopup="listbox" aria-expanded="false">
                                <span class="ws-btn-text">
                                    <span id="workspaceName">{{ $currentWorkspace?->display_name ?? __('Select workspace') }}</span>
                                    @if (!empty($wsCurrent['country']))
                                        <small class="ws-btn-country">{{ $wsCurrent['country'] }}</small>
                                    @endif
                                </span>
                                <i class="fa-solid fa-chevron-down workspace-dropdown-icon" aria-hidden="true"></i>
                            </button>
                            <div class="workspace-dropdown" id="workspaceDropdown">
                                <div class="workspace-search">
                                    <h6 class="ws-title">{{ __('Switch workspace') }}</h6>
                                    <label class="ws-search-box">
                                        <i class="ti ti-search" aria-hidden="true"></i>
                                        <input type="text" id="workspaceSearchInput" autocomplete="off"
                                            placeholder="{{ __('Search workspace or country…') }}"
                                            aria-label="{{ __('Search workspace or country…') }}">
                                        <kbd aria-hidden="true">↑↓ ↵</kbd>
                                    </label>
                                </div>
                                <div class="workspace-list" id="workspaceList" role="listbox">
                                    @php
                                        $wsSections = [];
                                        if ($wsCurrent) $wsSections[] = ['key' => 'current', 'label' => __('Current'), 'items' => [$wsCurrent]];
                                        if ($wsOthers->isNotEmpty()) $wsSections[] = ['key' => 'others', 'label' => __('Other workspaces'), 'items' => $wsOthers->all()];
                                    @endphp
                                    @foreach ($wsSections as $sec)
                                        <div class="ws-grp" data-ws-group="{{ $sec['key'] }}">{{ $sec['label'] }}</div>
                                        @foreach ($sec['items'] as $e)
                                            <div class="workspace-item @if ($e['current']) active @endif"
                                                role="option" aria-selected="{{ $e['current'] ? 'true' : 'false' }}"
                                                data-ws-group="{{ $sec['key'] }}"
                                                data-workspace-id="{{ $e['id'] }}"
                                                data-workspace-name="{{ $e['name'] }}"
                                                data-workspace-display-name="{{ $e['display'] }}"
                                                data-workspace-country="{{ $e['country'] }}"
                                                data-workspace-url="{{ $e['url'] }}">
                                                <span class="ws-badge ws-tone-{{ $e['tone'] }}" aria-hidden="true">{{ $e['initials'] }}</span>
                                                <span class="ws-text">
                                                    <span class="ws-name">{{ $e['display'] }}</span>
                                                    <small>@if ($e['country'] !== ''){{ $e['country'] }} · @endif{{ trans_choice(':count project|:count projects', $e['projects'], ['count' => $e['projects']]) }}</small>
                                                </span>
                                                @if ($e['current'])
                                                    <i class="ti ti-check ws-check" aria-hidden="true"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endforeach
                                    <div class="ws-empty" id="workspaceEmpty" hidden>{{ __('No workspaces match your search') }}</div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                <li class="dropdown dash-h-item drp-notification">
                    @if (isset($currentWorkspace) && $currentWorkspace)
                        @auth('web')
                            @php
                                // Avisos del usuario en el espacio de trabajo actual, más los de tipo 4 y 5 (globales).
                                $notifications = \App\Models\Notification::where('user_id', Auth::user()->id)
                                    ->where(function ($query) use ($currentWorkspace) {
                                        $query
                                            ->where('workspace_id', $currentWorkspace->id)
                                            ->orWhere('type', 4)
                                            ->orWhere('type', 5);
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->get();

                                // Agrupados por fecha (Hoy, Ayer, Esta semana, Anteriores); los repetidos del mismo
                                // grupo (mismo tipo y texto) se pintan una sola vez con «×N».
                                $notiToday = \Carbon\Carbon::today();
                                $notiYesterday = $notiToday->copy()->subDay();
                                $notiWeek = $notiToday->copy()->subDays(6);
                                $notiGroupLabels = [
                                    'today' => __('Today'),
                                    'yesterday' => __('Yesterday'),
                                    'week' => __('This week'),
                                    'older' => __('Earlier'),
                                ];
                                $notiKinds = [
                                    'review' => ['icon' => 'ti-flag', 'label' => __('Pending your review')],
                                    'assign' => ['icon' => 'ti-user-check', 'label' => __('You were assigned an order form')],
                                    'created' => ['icon' => 'ti-file-plus', 'label' => __('New order form')],
                                    'finished' => ['icon' => 'ti-circle-check', 'label' => __('Order form finished')],
                                    'project' => ['icon' => 'ti-briefcase', 'label' => __('New project')],
                                ];
                                $notiItems = [];
                                foreach ($notifications as $n) {
                                    $c = $n->created_at;
                                    $group = !$c ? 'older' : ($c->gte($notiToday) ? 'today' : ($c->gte($notiYesterday) ? 'yesterday' : ($c->gte($notiWeek) ? 'week' : 'older')));
                                    $key = $group . '|' . $n->type . '|' . $n->data;
                                    if (isset($notiItems[$key])) {
                                        $notiItems[$key]['ids'][] = $n->id;
                                        $notiItems[$key]['unread'] = $notiItems[$key]['unread'] || !$n->is_read;
                                        continue;
                                    }
                                    $notiItems[$key] = ['n' => $n, 'group' => $group, 'ids' => [$n->id], 'unread' => !$n->is_read, 'parts' => $n->parts()];
                                }
                                // Se cuentan los avisos tal como se ven (los repetidos agrupados cuentan uno)
                                $notiUnread = count(array_filter($notiItems, fn ($it) => $it['unread']));
                                $notiCount = ['all' => count($notiItems), 'review' => 0, 'assign' => 0];
                                foreach ($notiItems as $it) {
                                    if (isset($notiCount[$it['parts']['kind']])) $notiCount[$it['parts']['kind']]++;
                                }
                            @endphp
                            <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="ti ti-bell"></i>
                                <span id="notificationBadge"
                                    class="@if ($notiUnread > 0) bg-danger dash-h-badge dots @endif"><span
                                        class="sr-only"></span></span>
                            </a>
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end notification_menu_all acn">
                                <div class="acn-head">
                                    <h5>{{ __('Notifications') }}</h5>
                                    <span class="acn-count" id="acnUnread" @if ($notiUnread === 0) hidden @endif>{{ $notiUnread }}</span>
                                    <span class="acn-sp"></span>
                                    <button type="button" class="acn-link-btn" id="acnReadAll"
                                        data-url="{{ route('notifications.read_all') }}"
                                        @if ($notiUnread === 0) hidden @endif>{{ __('Mark all as read') }}</button>
                                </div>
                                <nav class="acn-tabs" id="acnTabs" @if ($notiCount['all'] === 0) hidden @endif>
                                    <button type="button" class="is-on" data-kind="all">{{ __('All notifications') }}<b>{{ $notiCount['all'] }}</b></button>
                                    <button type="button" data-kind="review">{{ __('Review') }}<b>{{ $notiCount['review'] }}</b></button>
                                    <button type="button" data-kind="assign">{{ __('Assigned to me') }}<b>{{ $notiCount['assign'] }}</b></button>
                                </nav>
                                <div class="noti-body acn-body">
                                    <div class="limited">
                                        @if (empty($notiItems))
                                            <div class="noNotificationsContainer">
                                                <i class="ti ti-bell-off" aria-hidden="true"></i>
                                                <p>{{ __("You're up to date! No notifications") }}</p>
                                            </div>
                                        @else
                                            @php $notiLastGroup = null; @endphp
                                            @foreach ($notiItems as $it)
                                                @php
                                                    $n = $it['n'];
                                                    $p = $it['parts'];
                                                    $k = $notiKinds[$p['kind']] ?? $notiKinds['created'];
                                                    $ids = implode(',', $it['ids']);
                                                @endphp
                                                @if ($it['group'] !== $notiLastGroup)
                                                    <div class="acn-grp" data-group="{{ $it['group'] }}">{{ $notiGroupLabels[$it['group']] }}</div>
                                                    @php $notiLastGroup = $it['group']; @endphp
                                                @endif
                                                <div class="acn-item notificationSTL {{ $it['unread'] ? 'is-unread' : '' }}"
                                                    data-notification-id="{{ $n->id }}" data-ids="{{ $ids }}"
                                                    data-kind="{{ $p['kind'] }}" data-group="{{ $it['group'] }}">
                                                    <a class="acn-open" href="{{ route('notifications.open', $n->id) }}?ids={{ $ids }}">
                                                        <span class="acn-ic acn-ic--{{ $p['kind'] }}"><i class="ti {{ $k['icon'] }}" aria-hidden="true"></i></span>
                                                        <span class="acn-tx">
                                                            <span class="acn-t1">{{ $k['label'] }}@if (count($it['ids']) > 1)<span class="acn-pill">×{{ count($it['ids']) }}</span>@endif</span>
                                                            <span class="acn-t2">{{ $p['title'] }}</span>
                                                            <span class="acn-t3">
                                                                @if ($p['project'] !== '')<span class="acn-pill" title="{{ $p['project'] }}">{{ $p['project'] }}</span>@endif
                                                                <span>@if ($p['due'] !== ''){{ __('due :date', ['date' => $p['due']]) }} · @endif{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</span>
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <span class="acn-dot" aria-hidden="true"></span>
                                                    <button type="button" class="acn-x" aria-label="{{ __('Delete notification') }}" title="{{ __('Delete notification') }}"><i class="ti ti-x" aria-hidden="true"></i></button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="acn-tab-empty" id="acnTabEmpty" hidden>{{ __('No notifications in this tab') }}</div>
                                </div>
                                <div class="acn-foot" id="acnFoot" @if ($notiCount['all'] === 0) hidden @endif>
                                    <span>{{ __('Click one to open its order form') }}</span>
                                    <button type="button" class="acn-link-btn" id="acnClearAll"
                                        data-url="{{ route('delete_all.notifications', $currentWorkspace->slug) }}">{{ __('Clear all') }}</button>
                                </div>
                            </div>
                        @endauth
                    @endif
                </li>

                {{-- Nieve de Navidad: solo se ve en temporada (1 dic – 6 ene); el script del sidebar la muestra y la gestiona --}}
                <li class="dash-h-item drp-snow" id="acelerSnowItem" hidden>
                    <button type="button" class="dash-head-link arrow-none me-0" id="acelerSnowToggle" aria-pressed="true"
                        data-off-text="{{ __('Turn off snow') }}" data-on-text="{{ __('Turn on snow') }}"
                        title="{{ __('Turn off snow') }}" aria-label="{{ __('Turn off snow') }}">
                        <i class="ti ti-snowflake nocolor" aria-hidden="true"></i>
                    </button>
                </li>

                @php
                    // Selector de idioma: código (ES, EN…), nombre en su propio idioma (tabla languages)
                    // y, en gris, el nombre en el idioma actual de la aplicación si es distinto.
                    $langEnglishNames = [
                        'es' => 'Spanish', 'en' => 'English', 'it' => 'Italian', 'fr' => 'French',
                        'de' => 'German', 'pt' => 'Portuguese', 'pt-br' => 'Portuguese (Brazil)', 'pl' => 'Polish',
                        'ro' => 'Romanian', 'nl' => 'Dutch', 'da' => 'Danish', 'ru' => 'Russian',
                        'tr' => 'Turkish', 'ar' => 'Arabic', 'he' => 'Hebrew', 'ja' => 'Japanese', 'zh' => 'Chinese',
                    ];
                    $langIsClient = \Auth::guard('client')->check();
                    $langIsAdmin = !$langIsClient && \Auth::user() && \Auth::user()->type == 'admin';
                    $langHasWorkspace = isset($currentWorkspace) && $currentWorkspace;
                    $langOptions = [];
                    foreach ($languages as $languageCode => $languageFullName) {
                        if ($langIsClient && $langHasWorkspace) {
                            $href = route('change_lang_workspace1', [$currentWorkspace->id, $languageCode]);
                        } elseif ($langIsAdmin) {
                            $href = route('change_lang_admin', $languageCode);
                        } elseif ($langHasWorkspace && \Auth::guard('web')->check()) {
                            $href = route('change_lang_workspace', [$currentWorkspace->id, $languageCode]);
                        } else {
                            continue;
                        }
                        $native = ucfirst(trim($languageFullName));
                        $translated = isset($langEnglishNames[$languageCode]) ? __($langEnglishNames[$languageCode]) : '';
                        if (mb_strtolower($translated) === mb_strtolower($native)) $translated = '';
                        $langOptions[] = [
                            'code' => strtoupper(explode('-', $languageCode)[0]),
                            'native' => $native,
                            'translated' => $translated,
                            'href' => $href,
                            'active' => $currantLang == $languageCode,
                        ];
                    }
                    $langCurrentCode = strtoupper(explode('-', (string) $currantLang)[0]);
                @endphp
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-code" aria-hidden="true">{{ $langCurrentCode }}</span>
                        <span id="selectedlenguage"
                            class="drp-text hide-mob">{{ ucfirst(\App\Models\Utility::getlang_fullname($currantLang)) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end acl">
                        <h6 class="acl-title">{{ __('App language') }}</h6>
                        @foreach ($langOptions as $opt)
                            <a href="{{ $opt['href'] }}" class="acl-item {{ $opt['active'] ? 'is-on' : '' }}"
                                @if ($opt['active']) aria-current="true" @endif lang="{{ strtolower($opt['code']) }}">
                                <span class="acl-code">{{ $opt['code'] }}</span>
                                <span class="acl-name">{{ $opt['native'] }}</span>
                                @if ($opt['translated'] !== '')
                                    <small class="acl-tr">{{ $opt['translated'] }}</small>
                                @endif
                                @if ($opt['active'])
                                    <i class="ti ti-check acl-check" aria-hidden="true"></i>
                                @endif
                            </a>
                        @endforeach
                        @if ($langIsAdmin)
                            <div class="acl-sep"></div>
                            <a href="#" class="acl-item acl-admin" data-ajax-popup="true"
                                data-size="md" data-title="{{ __('Create Language') }}"
                                data-url="{{ route('create_lang_workspace') }}">
                                <i class="ti ti-plus" aria-hidden="true"></i><span class="acl-name">{{ __('Create Language') }}</span></a>
                            <a href="{{ route('lang_workspace') }}" class="acl-item acl-admin">
                                <i class="ti ti-settings" aria-hidden="true"></i><span class="acl-name">{{ __('Manage Language') }}</span></a>
                        @endif
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
{{-- <script>
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
                    ntipe: ntipe,
                    milestoneAssignedTo: -2
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let notificationList = document.querySelector('.limited');
                    let newNotification = document.createElement('div');
                    newNotification.classList.add('notificationSTL');
                    const span1 = document.createElement('span');
                    span1.className = 'textRepo';
                    span1.textContent = data.data.msg;
                    const span2 = document.createElement('span');
                    span2.className = 'textRepo';
                    span2.textContent = data.data.type;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn-close repoIcon';
                    btn.setAttribute('aria-label', 'Close');
                    newNotification.appendChild(span1);
                    newNotification.appendChild(span2);
                    newNotification.appendChild(btn);
                    notificationList.prepend(newNotification);
                    checkEmptyState()
                }
            })
            .catch(error => console.error("Error al agregar notificación:", error));
    });
</script> --}}
@if (isset($currentWorkspace) && $currentWorkspace)
<script>
    // Campana: pestañas, marcar como leídas, borrar uno (con sus repetidos) o todos.
    // Pulsar un aviso va a notifications.open, que lo marca como leído y abre su encargo.
    (function() {
        const menu = document.querySelector('.notification_menu_all.acn');
        if (!menu) return;
        const list = menu.querySelector('.limited');
        const tabs = document.getElementById('acnTabs');
        const tabEmpty = document.getElementById('acnTabEmpty');
        const foot = document.getElementById('acnFoot');
        const readAll = document.getElementById('acnReadAll');
        const unreadBadge = document.getElementById('acnUnread');
        const csrf = @json(csrf_token());
        const deleteUrl = @json(route('notifications.delete', [$currentWorkspace->slug, '__id']));
        let kind = 'all';

        const post = function(url) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            }).then(function(r) { return r.json(); });
        };
        const items = function() { return Array.from(list.querySelectorAll('.acn-item')); };

        // Contadores de pestañas y de no leídas, cabeceras de grupo vacías y mensaje de pestaña vacía
        const refresh = function() {
            const all = items();
            const counts = { all: all.length, review: 0, assign: 0 };
            all.forEach(function(el) { if (counts[el.dataset.kind] !== undefined) counts[el.dataset.kind]++; });
            if (tabs) tabs.querySelectorAll('button').forEach(function(b) { b.querySelector('b').textContent = counts[b.dataset.kind]; });

            let shown = 0;
            all.forEach(function(el) {
                const ok = kind === 'all' || el.dataset.kind === kind;
                el.hidden = !ok;
                if (ok) shown++;
            });
            list.querySelectorAll('.acn-grp').forEach(function(g) {
                g.hidden = !all.some(function(el) { return !el.hidden && el.dataset.group === g.dataset.group; });
            });
            if (tabEmpty) tabEmpty.hidden = !(all.length && !shown);

            const unread = list.querySelectorAll('.acn-item.is-unread').length;
            if (unreadBadge) { unreadBadge.textContent = unread; unreadBadge.hidden = unread === 0; }
            if (readAll) readAll.hidden = unread === 0;
            const badge = document.getElementById('notificationBadge');
            if (badge) badge.className = unread > 0 ? 'bg-danger dash-h-badge dots' : '';
            if (tabs) tabs.hidden = all.length === 0;
            if (foot) foot.hidden = all.length === 0;
            checkEmptyState();
        };
        window.acelerNotiRefresh = refresh;

        if (tabs) tabs.addEventListener('click', function(e) {
            const b = e.target.closest('button[data-kind]');
            if (!b) return;
            kind = b.dataset.kind;
            tabs.querySelectorAll('button').forEach(function(x) { x.classList.toggle('is-on', x === b); });
            refresh();
        });

        if (readAll) readAll.addEventListener('click', function() {
            post(readAll.dataset.url).then(function(d) {
                if (!d.is_success) return;
                list.querySelectorAll('.acn-item.is-unread').forEach(function(el) { el.classList.remove('is-unread'); });
                refresh();
            }).catch(function(err) { console.error('Error al marcar como leídas:', err); });
        });

        const removeItem = function(el) {
            el.classList.add('is-leaving');
            setTimeout(function() { el.remove(); refresh(); }, 220);
        };

        // ✕ de un aviso: se borran todos los ids que agrupa («×2»)
        list.addEventListener('click', function(e) {
            const x = e.target.closest('.acn-x');
            if (!x) return;
            e.preventDefault();
            const el = x.closest('.acn-item');
            const ids = (el.dataset.ids || el.dataset.notificationId || '').split(',').filter(Boolean);
            x.disabled = true;
            Promise.all(ids.map(function(id) { return post(deleteUrl.replace('__id', id)); }))
                .then(function(res) {
                    if (res.every(function(d) { return d.is_success; })) removeItem(el);
                    else x.disabled = false;
                })
                .catch(function(err) { x.disabled = false; console.error('Error al eliminar notificación:', err); });
        });

        const clearAll = document.getElementById('acnClearAll');
        if (clearAll) clearAll.addEventListener('click', function() {
            post(clearAll.dataset.url).then(function(d) {
                if (!d.is_success) return;
                items().forEach(function(el, i) { setTimeout(function() { el.classList.add('is-leaving'); }, Math.min(i, 8) * 40); });
                setTimeout(function() { items().forEach(function(el) { el.remove(); }); refresh(); }, 600);
            }).catch(function(err) { console.error('Error al eliminar todas las notificaciones:', err); });
        });
    })();
</script>
@endif
{{-- Estado vacío de la campana (también lo llaman otras pantallas) --}}
<script>
    function checkEmptyState() {
        const notificationContainer = document.querySelector('.limited');
        if (!notificationContainer) return;
        const hasNotifications = notificationContainer.querySelector('.notificationSTL') !== null;
        const emptyState = notificationContainer.querySelector('.noNotificationsContainer');
        if (!hasNotifications) {
            notificationContainer.querySelectorAll('.acn-grp').forEach(function(g) { g.remove(); });
            if (!emptyState) {
                const box = document.createElement('div');
                box.className = 'noNotificationsContainer';
                box.innerHTML = '<i class="ti ti-bell-off" aria-hidden="true"></i><p></p>';
                box.querySelector('p').textContent = @json(__("You're up to date! No notifications"));
                notificationContainer.appendChild(box);
            }
        } else if (emptyState) {
            emptyState.remove();
        }
    }
</script>
