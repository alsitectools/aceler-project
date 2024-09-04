<x-guest-layout>
    <x-auth-card>
        @php
            $languages = \App\Models\Utility::languages();
            $setting = \App\Models\Utility::getAdminPaymentSettings();
            App\models\Utility::setCaptchaConfig();
        @endphp
        @section('page-title')
            {{ __('Login') }}
        @endsection

        @section('language-bar')
            <div href="#" class="lang-dropdown-only-desk">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text">{{ isset($languages[$lang]) ? ucfirst($languages[$lang]) : 'es' }}</span>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach ($languages as $code => $language)
                            <a href="{{ route('login', $code) }}" tabindex="0"
                                class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                                <span>{{ ucFirst($language) }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
            </div>
        @endsection

        @section('content')
            <div class="card-body">

                @if (session()->has('error'))
                    <div>
                        <p class="text-danger">{{ session('error') }}</p>
                    </div>
                @endif
                {{-- LOGO PROJECT --}}
                <div class="navbar-brand d-none d-md-block text-center mt-5 mb-5">
                    <img width="100%" src="{{ asset('assets/img/logoRed.png') }}" alt="Logo Alsina Project">
                </div>

                <div class="text-center">
                    <h2 class="text-muted m-1">{{ __('Login') }}</h2>
                    <p class="text-muted m-2">{{__('To access aCeler Project, you will be automatically redirected to the Azure login page.')}}</p>
                </div>
                <div class="d-grid mt-5 mb-5">
                    <a href="{{ route('azure.login') }}" class="btn btn-primary text-white" title="{{ __('Login azure') }}">
                        <img class="me-2 azureIcon" src="{{ asset('assets/img/azureIcon.png') }}"
                            alt="login with microsoft">{{ __('Sign in') }}</a>
                </div>
                <div class="text-center text-primary mt-5">
                    <a href="#"> <span>
                            &copy; {{ date('Y') }}
                            {{ 'aCeler Project' }} ~@yield('page-title')
                        </span></a>
                </div>
            </div>
        @endsection
        @push('custom-scripts')
            <script src="{{ asset('assets/custom/libs/jquery/dist/jquery.min.js') }}"></script>
            <script>
                function mostrarMensaje(event) {
                    event.preventDefault();
                    alert("This option is not aviable.");
                }
                $(document).ready(function() {
                    $("#form_data").submit(function(e) {
                        $("#login_button").attr("disabled", true);
                        return true;
                    });
                });
            </script>
        @endpush
    </x-auth-card>
</x-guest-layout>
<style>
    .azureIcon {
        width: 20px;
        filter: drop-shadow(0px 0px 1.2px #ffffff);
    }

    .login-deafult {
        width: 139px !important;
    }
</style>
