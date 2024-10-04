<style>
    .buttonLogin {
        width: 100% !important;
        height: 50px;
    }

    .azureIcon {
        width: 20px;
        filter: drop-shadow(0px 0px 1.2px #ffffff);
    }

    .login-deafult {
        width: 139px !important;
    }
</style>
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
                        <span class="drp-text">{{ isset($languages[$lang]) ? ucfirst($languages[$lang]) : 'en' }}</span>
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
            <div class="card-body p-3">
                @if (session()->has('error'))
                    <div>
                        <p class="text-danger">{{ session('error') }}</p>
                    </div>
                @endif
                <div class="navbar-brand d-none d-md-block text-center mt-5 mb-5 ms-4">
                    <img width="100%" src="{{ asset('assets/img/logoSidebar.png') }}" alt="Logo Alsina Project">
                </div>
                <div class="text-center">
                    <h2 class="text-muted m-1">{{ __('Login') }}</h2>
                    <p class="text-muted m-2">
                        {{ __('To access aCeler Project, you will be automatically redirected to the Azure login page.') }}
                    </p>
                </div>
                <div class="text-center mt-4 me-4 ms-4">
                    <a href="{{ route('azure.login') }}" class="btn btn-primary text-white buttonLogin"
                        title="{{ __('Login azure') }}">
                        <img class="me-2 azureIcon" src="{{ asset('assets/img/azureIcon.svg') }}"
                            alt="login with microsoft">
                        <p class="d-inline" style="font-size: 18px;">{{ __('Sign in') }}
                        </p>
                    </a>
                </div>
                <a href="#" class="mt-3 text-center mb-5 m-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    {{ __('Login whit admin') }}
                </a>
            </div>
            <div class="text-center text-primary mt-5 rounded" style="background-color: white">
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
<form method="POST" id="form_data" action="{{ route('login', config::get('app.locale')) }}">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Iniciar sesión</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email"
                            id="emailaddress" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="{{ __('Enter Your Email') }}">
                        @error('email')
                            <span class="error invalid-email text-danger" role="alert">
                                <small>{{ $message }}</small>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password" id="password"
                            placeholder="{{ __('Enter Your Password') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="login_button" class="btn btn-primary btn-block mt-2">{{ __('Login') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
