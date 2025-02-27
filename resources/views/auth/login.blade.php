@php
    use App\Models\Workspace;
    $workspaces = Workspace::all();
@endphp
<style>
    .backgroundImageLogin {
        background-image: url("{{ asset('assets/img/login/imagenes cuadrados fondo filtred.png') }}");
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('assets/css/custom-login.css') }}">
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
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            <div class="imgDiv">
                <img width="100%" src="{{ asset('assets/img/logoRed1.png') }}" alt="Logo Alsina Project">
            </div>
            <div class="loginText">
                <h2 class="loginTitle">{{ __('Login') }}</h2>
                <p class="loginExplanation text-center">
                    {{ __('To access aCeler Project, you will be automatically redirected to the Azure login page.') }}
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('showModal'))
            var modal = new bootstrap.Modal(document.getElementById('registerModal'));
            modal.show();
        @endif
    });

    document.getElementById('azureLoginButton').addEventListener('click', function() {
        window.location.href = "{{ route('azure.login') }}";
    });
</script>

@if (session('showModal'))
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel"
        data-bs-backdrop="static" data-bs-keyboard="false"
        style="top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">{{ __('User information') }}</h5>
                </div>
                <div class="modal-body">
                    <div class="animacion">
                        <!-- Language dropdown -->
                        <div class="lengua-bar d-flex justify-content-end">
                            @php
                                $languages = \App\Models\Utility::languages();
                                $setting = \App\Models\Utility::getAdminPaymentSettings();
                                // dump(session('userProfile'));
                            @endphp
                            <ul class="nav">
                                <li class="dropdown dash-h-item drp-language">
                                    <a class="btn btn-primary dash-head-link dropdown-toggle" href="#"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="drp-text">{{ ucfirst($languages[$lang] ?? 'es') }}</span>
                                    </a>
                                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                                        @foreach ($languages as $code => $language)
                                            <a href="{{ route('login', $code) }}" tabindex="0"
                                                class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                                                <span>{{ ucfirst($language) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Profile selection cards -->
                        <div class="d-flex justify-content-center">
                            <div class="col-md-2 text-center m-4">
                                <div class="card" id="technicianCard">
                                    <img class="card-img-top" src="{{ asset('assets/img/technicians_cardImg.png') }}"
                                        alt="Alsina Project logo">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Sales technician') }}</h5>
                                        <p class="card-text">{{ __('Business profile') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-center m-4">
                                <div class="card" id="comercialCard">
                                    <img class="card-img-top" src="{{ asset('assets/img/salesManager_cardImg.png') }}"
                                        alt="Sales manager logo">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Another') }}</h5>
                                        <p class="card-text">{{ __('Technical profile') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job title selection -->
                        <div class="row mt-4">
                            <div class="text-center"
                                style="display: flex; align-items: center; flex-direction: column;">
                                <h2>{{ __('Choose your job title') }}</h2>
                                <p class="col-5 text-center">
                                    {{ __('To complete the user information according to your role, choose the profile that best represents you. Other (R&D, engineering, administration or other support areas)') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Technician Form -->
                    <form action="{{ route('register.azure.post') }}" method="POST" id="technicianForm"
                        style="display: none;">
                        @csrf
                        <button type="button" class="btn btn-primary mb-4 backButton"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <input type="hidden" name="mail" value="{{ session('userProfile.mail') }}">
                        <input type="hidden" name="userPrincipalName"
                            value="{{ session('userProfile.userPrincipalName') }}">
                        <input type="hidden" name="name" value="{{ session('userProfile.displayName') }}">
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" name="jobTitle" value="{{ session('userProfile.jobTitle') }}">
                        <input type="hidden" name="department" value="{{ session('userProfile.department') }}">
                        <input type="hidden" name="officeLocation"
                            value="{{ session('userProfile.officeLocation') }}">
                        <input type="hidden" name="city" value="{{ session('userProfile.city') }}">
                        <input type="hidden" name="country" value="{{ session('userProfile.country') }}">
                        <input type="hidden" name="companyName" value="{{ session('userProfile.companyName') }}">
                        <input type="hidden" name="photo_path" value="{{ session('userProfile.photo_path') }}">


                        <div class="row">
                            <div class="form-group col-md-8">
                                <fieldset class="custom-fieldset disabled">
                                    <legend class="custom-legend">{{ __('Name') }}</legend>
                                    <input type="text" class="custom-input"
                                        value="{{ session('userProfile.displayName') }}" readonly>
                                </fieldset>
                            </div>
                            <div class="form-group col-md-4">
                                <fieldset class="custom-fieldset">
                                    <legend class="custom-legend">{{ __('Department') }}</legend>
                                    <input type="text" class="custom-input"
                                        value="{{ session('userProfile.department') }}" readonly>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Workspace Selection -->
                        <div class="form-group col-md-12">
                            <fieldset class="custom-fieldset delegationFieldset">
                                <legend class="custom-legend">{{ __('Company') }}</legend>
                                <input type="text" class="custom-input mt-2 searchWorkspace"
                                    placeholder="{{ __('Search') }}" autocomplete="off" value="">
                                <div class="workspace-select dropdown-menu" style="max-height: 200px; display: none;">
                                    @foreach ($workspaces as $workspace)
                                        <div class="option list-group-item list-group-item-action"
                                            data-id="{{ $workspace->id }}" style="padding: 8px; cursor: pointer;">
                                            {{ $workspace->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                            <div class="selectedWorkspacesContainer d-flex flex-wrap gap-2 p-2 rounded"></div>
                            <input type="hidden" class="selectedWorkspaceIds" name="selectedWorkspaceIds"
                                value="">
                        </div>

                        <!-- Workday Information -->
                        <div class="form-group col-md-12">
                            <fieldset class="custom-fieldset">
                                <legend class="custom-legend">{{ __('Complete your workday information') }}</legend>
                                <div class="m-3 divTimetable" id="v-pills-timetable">
                                    <div class="card-body collapse-section timetable-content" id="timetable-content"
                                        style="display: flex; flex-direction: row;">
                                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                            <div class="dayToggle">
                                                <p>{{ __($day) }}</p>
                                                <label class="switch">
                                                    <input type="checkbox" class="dayCheckbox"
                                                        id="{{ strtolower($day) }}Checkbox">
                                                    <span class="slider round"></span>
                                                </label>
                                                <input id="{{ strtolower($day) }}Input" type="time"
                                                    class="inputToggle" onclick="this.showPicker()">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </fieldset>


                            <!-- Input oculto para los días seleccionados -->
                            <input type="hidden" name="workday" id="workdayInput">
                            <p class="text-muted text-end mt-3">
                                {{ __('Once logged in, you can modify your information from the My profile section.') }}
                            </p>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>

                    <form action="{{ route('register.azure.post') }}" method="POST" style="display: none;"
                        id="comercialForm">
                        @csrf
                        <button type="button" class="btn btn-primary mb-4 backButton"><i
                                class="fa-solid fa-chevron-left"></i></button>
                        <input type="hidden" name="mail" value="{{ session('userProfile.mail') }}">
                        <input type="hidden" name="email" value="{{ session('userProfile.userPrincipalName') }}">
                        <input type="hidden" name="name" value="{{ session('userProfile.displayName') }}">
                        <input type="hidden" name="type" value="client">
                        <input type="hidden" name="jobTitle" value="{{ session('userProfile.jobTitle') }}">
                        <input type="hidden" name="department" value="{{ session('userProfile.department') }}">
                        <input type="hidden" name="officeLocation"
                            value="{{ session('userProfile.officeLocation') }}">
                        <input type="hidden" name="city" value="{{ session('userProfile.city') }}">
                        <input type="hidden" name="country" value="{{ session('userProfile.country') }}">
                        <input type="hidden" name="companyName" value="{{ session('userProfile.companyName') }}">
                        <input type="hidden" name="photo_path" value="{{ session('userProfile.photo_path') }}">

                        <div class="row">
                            <div class="form-group col-md-8">
                                <fieldset class="custom-fieldset disabled">
                                    <legend class="custom-legend">{{ __('Name ') }}:</legend>
                                    <input type="text" class="custom-input"
                                        value="{{ session('userProfile.displayName') }}" readonly>
                                </fieldset>
                            </div>
                            <div class="form-group col-md-4">
                                <fieldset class="custom-fieldset">
                                    <legend class="custom-legend">{{ __('Department') }}</legend>
                                    <input type="text" class="custom-input"
                                        value="session('userProfile.department') " readonly>
                                </fieldset>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <fieldset class="custom-fieldset delegationFieldset">
                                <legend class="custom-legend">{{ __('Delegation') }}</legend>
                                <input type="text" class="custom-input mt-2 searchWorkspace"
                                    placeholder="{{ __('Search') }}" autocomplete="off" value="">
                                <div class="workspace-select dropdown-menu" style="max-height: 200px; display: none;">
                                    @foreach ($workspaces as $workspace)
                                        <div class="option list-group-item list-group-item-action"
                                            data-id="{{ $workspace->id }}" style="padding: 8px; cursor: pointer;">
                                            {{ $workspace->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>
                            <div class="selectedWorkspacesContainer d-flex flex-wrap gap-2 p-2 rounded"></div>
                            <input type="hidden" class="selectedWorkspaceIds" name="selectedWorkspaceIds"
                                value="">
                        </div>
                        <p class="text-muted text-end mt-3">
                            {{ __('Once logged in, you can modify your information from the My profile section.') }}
                        </p>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll(".card");
            const technicianForm = document.getElementById("technicianForm");
            const comercialForm = document.getElementById("comercialForm");
            const animacion = document.querySelector(".animacion");
            const backButtons = document.querySelectorAll(".backButton");

            if (!cards.length || !technicianForm || !comercialForm || !animacion || !backButtons.length) {
                console.error("Algunos elementos no se han encontrado en el DOM.");
                return;
            }

            const toggleForms = (formToShow) => {
                technicianForm.style.display = "none";
                comercialForm.style.display = "none";
                formToShow.style.display = "block";
            };

            const toggleBackButton = (state) => {
                backButtons.forEach(button => button.style.display = state ? "block" : "none");
            };

            cards.forEach((card, index) => {
                card.addEventListener("click", function() {
                    animacion.style.display = "none";
                    toggleForms(index === 0 ? comercialForm : technicianForm);
                    toggleBackButton(true);
                });
            });

            backButtons.forEach(button => {
                button.addEventListener("click", function() {
                    technicianForm.style.display = "none";
                    comercialForm.style.display = "none";
                    animacion.style.display = "block";
                    toggleBackButton(false);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const searchInputs = document.querySelectorAll('.searchWorkspace');
            const optionsLists = document.querySelectorAll('.workspace-select');
            const selectedContainers = document.querySelectorAll('.selectedWorkspacesContainer');
            const hiddenInputs = document.querySelectorAll('.selectedWorkspaceIds');

            $(document).ready(function() {
                $(".workspace-select").niceScroll({
                    cursorcolor: "grey",
                    cursorwidth: "8px",
                    cursorborderradius: "4px",
                    autohidemode: false
                });
            });

            const handleSearchInput = (searchInput, optionsList, selectedContainer, hiddenInput) => {
                const options = optionsList.getElementsByClassName('option');
                let selectedWorkspaces = [];

                searchInput.addEventListener('click', function(event) {
                    event.stopPropagation();
                    optionsList.style.display = 'block';
                });

                searchInput.addEventListener('input', function() {
                    const filter = searchInput.value.toLowerCase();
                    Array.from(options).forEach(option => {
                        const text = option.innerText.toLowerCase();
                        option.style.display = text.includes(filter) ? 'block' : 'none';
                    });
                    optionsList.style.display = Array.from(options).some(option => option.style
                        .display === 'block') ? 'block' : 'none';
                });

                Array.from(options).forEach(option => {
                    option.addEventListener('click', function() {
                        const selectedId = this.getAttribute('data-id');
                        const selectedText = this.innerText;

                        if (!selectedWorkspaces.includes(selectedId)) {
                            selectedWorkspaces.push(selectedId);

                            const tag = document.createElement('span');
                            tag.className = 'textWorkspace p-2 d-flex align-items-center';
                            tag.innerHTML =
                                `${selectedText} <button type="button" class="closeBut ms-2 text-danger" aria-label="Close">×</button>`;

                            tag.querySelector('.closeBut').addEventListener('click',
                                function() {
                                    selectedContainer.removeChild(tag);
                                    selectedWorkspaces = selectedWorkspaces.filter(id =>
                                        id !== selectedId);
                                    hiddenInput.value = selectedWorkspaces.join(',');
                                });

                            selectedContainer.appendChild(tag);
                            hiddenInput.value = selectedWorkspaces.join(',');
                        }

                        searchInput.value = '';
                        optionsList.style.display = 'none';
                    });
                });

                document.addEventListener('click', function(event) {
                    if (!searchInput.contains(event.target) && !optionsList.contains(event.target)) {
                        optionsList.style.display = 'none';
                    }
                });
            };

            searchInputs.forEach((searchInput, index) => {
                handleSearchInput(searchInput, optionsLists[index], selectedContainers[index], hiddenInputs[
                    index]);
            });
        });
        document.addEventListener('DOMContentLoaded', function() {

            function getSelectedDaysWithTime() {
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                const selectedDays = {};

                days.forEach(function(day) {
                    const checkbox = document.getElementById(day + 'Checkbox');
                    const timeInput = document.getElementById(day + 'Input');
                    if (checkbox && checkbox.checked && timeInput.value) {
                        selectedDays[day] = timeInput.value;
                    }
                });

                return selectedDays;
            }

            function updateWorkdayInput() {
                const selectedDaysWithTime = getSelectedDaysWithTime();
                const workdayInput = document.getElementById('workdayInput');
                workdayInput.value = JSON.stringify(selectedDaysWithTime);
            }

            const checkboxes = document.querySelectorAll('.dayCheckbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const dayId = checkbox.id.replace('Checkbox', '');
                    const timeInput = document.getElementById(dayId + 'Input');

                    if (checkbox.checked) {
                        timeInput.disabled = false;
                    } else {
                        timeInput.disabled = true;
                        timeInput.value = '';
                    }

                    updateWorkdayInput();
                });
            });

            const timeInputs = document.querySelectorAll('.inputToggle');
            timeInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    updateWorkdayInput();
                });
            });

            updateWorkdayInput();
        });
    </script>

@endif
