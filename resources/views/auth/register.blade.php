<x-guest-layout>
    <x-auth-card>
        @php
            $setting = \App\Models\Utility::getAdminPaymentSettings();
            $languages = \App\Models\Utility::languages();
            App\models\Utility::setCaptchaConfig();
        @endphp
        @section('page-title')
            {{ __('Register') }}
        @endsection

        @section('language-bar')
            <div href="#" class="lang-dropdown-only-desk">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text"> {{ ucFirst($languages[$lang]) }}
                        </span>
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
                    <h2 class="mb-3 f-w-600">{{ __('Register') }}</h2>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @if (session('statuss'))
                        <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                            {{ __('Email SMTP settings does not configured so please contact to your site admin.') }}
                        </div>
                    @endif
                    @csrf
                    <div class="">
                        <div class="form-group mb-3">
                            <label for="fullname" class="form-label">{{ __('Full Name') }}</label>
                            <input type="text" class="form-control  @error('name') is-invalid @enderror" name="name"
                                id="fullname" value="{{ old('name') }}" required autocomplete="name" autofocus
                                placeholder="{{ __('Enter Your Name') }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- añadido tipo de usuario --}}
                        {{-- <div class="form-group mb-3">
                            <label for="type" class="form-label">{{ __('messages.Type_user') }}</label>
                            <input type="text" class="form-control  @error('type') is-invalid @enderror" name="type"
                                id="type" value="{{ old('type') }}" required autocomplete="type" autofocus
                                placeholder="{{ __('type user') }}">
                            @error('type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}
                        <div class="form-group mb-3">
                            {{-- modificado OK delegaciones españa --}}
                            <label for="currant_workspace" class="form-label">{{ __('dictionary.Delegation') }}</label>
                            <div class="input-group mb-3">
                                <select class="form-select" name="currant_workspace" id="currant_workspace" required>
                                    <option disabled selected></option>
                                    @foreach ($workspaces as $workspace)
                                        <option value={{ $workspace->slug }}>{{ $workspace->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('company')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="emailaddress" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email"
                                id="emailaddress" value="{{ old('email') }}" required autocomplete="email"
                                placeholder="{{ __('Enter Your Email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="new-password" id="password"
                                placeholder="{{ __('Enter Your Password') }}">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation" required autocomplete="new-password" id="password_confirmation"
                                placeholder="{{ __('Confirm Your Password') }}">

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
                                class="btn btn-primary btn-block mt-2">{{ __('Register') }}</button>
                        </div>
                </form>

                <p class="mb-2 mt-2 text-center">{{ __('Already have an account?') }} <a
                        href="{{ route('login', $lang) }}" class="f-w-400 text-primary">{{ __('Login') }}</a></p>
            </div>
        @endsection
        @push('custom-scripts')
            @if ($setting['recaptcha_module'] == 'on')
                {!! NoCaptcha::renderJs() !!}
            @endif
        @endpush
    </x-auth-card>
</x-guest-layout>
