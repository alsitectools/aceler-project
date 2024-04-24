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
                <div class="">
                    <h2 class="mb-3 f-w-600">{{ __('Login') }}</h2>
                </div>
                @if (session()->has('error'))
                    <div>
                        <p class="text-danger">{{ session('error') }}</p>
                    </div>
                @endif
                <form method="POST" id="form_data" action="{{ route('login') }}">
                    @csrf
                    <div class="">
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
                        <div class="form-group mb-3 text-start">
                            <span>
                                <a href="{{ route('password.request', $lang) }}"
                                    tabindex="0">{{ trans('dictionary.Forgot_Password?') }}</a>
                            </span>
                        </div>

                        @if ($setting['recaptcha_module'] == 'on')
                            <div class="form-group col-lg-12 col-md-12 mt-3">
                                {!! NoCaptcha::display($setting['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []) !!}
                                @error('g-recaptcha-response')
                                    <span class="small text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endif

                        <div class="d-grid">
                            <button type="submit" id="login_button"
                                class="btn btn-primary btn-block mt-2">{{ __('Login') }}</button>
                        </div>
                        <p class="my-4 text-center">{{ __('dictionary.DontAccount?') }}
                            <a href="{{ route('register', $lang) }}" class="my-4 text-center text-primary">
                                {{ __('dictionary.SignUp') }}</a>
                        </p>
                    </div>
                </form>
                {{-- <div class="d-grid mt-3">
                    <button type="button" id="" class="btn btn-primary btn-block  "><a
                            href="{{ route('client.login', $lang) }}" class="" style="color:#fff">
                            {{ __('Client Login') }}</a></button>
                 --}}
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
            @if ($setting['recaptcha_module'] == 'on')
                {!! NoCaptcha::renderJs() !!}
            @endif
        @endpush
    </x-auth-card>
</x-guest-layout>
<style>
    .login-deafult {
        width: 139px !important;
    }
</style>
