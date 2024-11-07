<style>
    html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        /* Estilo para la imagen de fondo */
        .backgroundImageLogin {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('assets/img/login/imagenes cuadrados fondo filtred.png') }}');
            background-size: cover;  /* Hace que la imagen ocupe toda la pantalla sin deformarse */
            background-position: center; /* Centra la imagen */
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            z-index: -1; /* Envía la imagen al fondo */
        }

        .divLogin{
            box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);
            -webkit-box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);
            -moz-box-shadow: 0px 4px 4px 0px rgb(0 0 0 / 25%);
            background-color: white;
            width: 30%;
            height: 60%;
            margin-left: 33%;
            margin-top: 9%;
            border-radius: 15px;
        }

        .imgDiv{
            width: 90%;
            padding-top: 5%;
            margin-left: 12%;
        }

        .loginText{
            display: flex;
            align-content: center;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loginTitle{
            font-size: 50px;
            padding-top: 4%;
            padding-bottom: 5%;
        }

        .loginExplanation{
            width: 79%;
            font-size: 18px;
            padding-left: 5%;
        }

        .azureLogo{
            width: 26px;
        }

        .copyText{
            color: #AA182C;
            padding-left: 41%;
        }
        @media screen and (max-width: 1669px) and (min-width: 1555px){

            .buttonLogin{
                font-size: 17px;
            }
            .azureLogo{
                width: 20px;
            }
        }

        @media screen and (max-width: 1553px) and (min-width: 1479px){

            .buttonLogin{
                font-size: 15px;
            }
            
            .loginExplanation{
                font-size: 16px;
            }

            .loginTitle{
                font-size: 48px;
            }

            .imgDiv{
                padding-top: 8%;
            }
        }

        @media screen and (max-width: 1479px) and (min-width: 1240px ){
            
            .azureLogo {
                width: 20px;
            }

            .divLogin{
                margin-top: 10%;
            }
            .buttonLogin{
                font-size: 15px;
            }
        }

        @media screen and (max-width: 1240px) and (min-width: 800px ){
            
            .azureLogo {
                width: 20px;
            }

            .loginTitle{
                font-size: 40px;
            }
            .loginExplanation{
                width: 75%;
                font-size: 15px;
            }
            .divLogin{
                margin-top: 10%;
            }
            .copyText{
                padding-left: 20%;
            }
            .buttonLogin{
                font-size: 12px;
            }
            .imgDiv{
                padding-top: 11%;
                padding-bottom: 8%;
            }
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
                    <a style="color:white;" class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text">{{ isset($languages[$lang]) ? ucfirst($languages[$lang]) : 'es' }}</span>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach ($languages as $code => $language)
                            <a style="border-radius: 10px;" href="{{ route('login', $code) }}" tabindex="0"
                                class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                                <span>{{ ucFirst($language) }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
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
<div class="backgroundImageLogin">
    <div>
        <div class="divLogin">
            @if (session()->has('error'))
                <div>
                     <p >{{ session('error') }}</p>
                </div>
            @endif
                <div class="imgDiv">
                    <img width="100%" src="{{ asset('assets/img/logoRed1.png') }}" alt="Logo Alsina Project">
                </div>
                <div class="loginText">
                    <h2 class="loginTitle">{{ __('Login') }}</h2>
                    <p class="loginExplanation">
                        {{ __('To access aCeler Project, you will be automatically redirected to the Azure login page.') }}
                    </p>
                </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('azureLoginButton').addEventListener('click', function() {
        window.location.href = "{{ route('azure.login') }}";
    });
</script>