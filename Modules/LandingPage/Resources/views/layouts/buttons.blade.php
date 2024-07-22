@php
    $settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
@endphp
@if ($settings['menubar_status'] == 'on')
    {{-- @dd($settings); --}}
    @if (is_array(json_decode($settings['menubar_page'])) || is_object(json_decode($settings['menubar_page'])))
        <li class="nav-item">
            <a class="nav-link" target="_blank"
                href='https://www.alsina.com/es/sobre-alsina/'>{{ __('dictionary.about') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" target="_blank"
                href='https://www.alsina.com/es/aviso-legal/'>{{ __('dictionary.terms') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" target="_blank"
                href='https://www.alsina.com/es/politica-de-privacidad/'>{{ __('dictionary.privacy') }}</a>
        </li>
    @endif
@endif
