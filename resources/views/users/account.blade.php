@extends('layouts.admin')

@section('page-title')
    {{ __('User Profile') }}
@endsection
@section('links')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"> {{ __('User Profile') }}</li>
@endsection
@php

    $logo = 'storage/app/public/';
@endphp

@section('content')
    @include('loader.loader')
    @include('saver.saver')

    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#v-pills-home" class="list-group-item list-group-item-action border-0">{{ __('Account') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>

                    <a href="#v-pills-profile"
                        class="list-group-item list-group-item-action border-0">{{ __('Add another workspace') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>

                    <a href="#v-pills-timetable"
                        class="list-group-item list-group-item-action border-0">{{ __('Timetable') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>

                    <a href="#v-pills-HolidayPicker"
                        class="list-group-item list-group-item-action border-0">{{ __('Holiday') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div id="v-pills-home" class="card">
                <div class="card-header buttonColocation">
                    <h5>{{ __('Avatar') }}</h5>
                    <button style="margin-right: 1%;" class="btn btn-sm btn-primary toggle-section buttonColapse"
                        data-target="#avatar-content">-</button>
                </div>
                @php
                    $workspace = $currentWorkspace ? $currentWorkspace->id : 0;
                    $user_id = $user ? $user->id : 0;
                @endphp
                <div class="card-body collapse-section" id="avatar-content">
                    <form method="post"
                        action="@auth('web'){{ route('update.account', [$workspace, $user_id]) }}@elseauth{{ route('client.update.account', [$workspace, $user_id]) }}@endauth"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4 avatar-centrado">
                                <div class="form-group">
                                    <img @if ($user->avatar) src="{{ asset($user->avatar) }}" @else avatar="{{ $user->name }}" @endif
                                        id="myAvatar" alt="user-image" class="rounded-circle img-thumbnail">
                                    {{-- <div class="choose-file">
                                        <label for="avatar">
                                            <div class=" bg-primary "><i
                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                            </div>
                                            <input type="file" class="form-control choose_file_custom" name="avatar"
                                                id="avatar" data-filename="avatar-logo" style="display: none;">
                                        </label>
                                        @error('avatar')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div> --}}
                                </div>
                                {{-- <small
                                    class="text-muted text-center">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</small> --}}
                            </div>
                            <div class="col-lg-8">
                                <div class="d-flex">
                                    <h1> {{ $user->name }}</h1>
                                </div>
                                <div class="d-flex mt-4">
                                    <i class="fa-regular fa-envelope fa-xl mt-4 me-3"></i>
                                    <div style="display: flex; flex-direction: column;" class="col-12">
                                        <strong for="name" class="form-label ">{{ __('Email') }}</strong>
                                        {{ $user->email }}
                                    </div>
                                </div>
                                <div class="d-flex mt-4">
                                    <i class="fa-regular fa-user fa-xl mt-4 me-3"></i>
                                    <div style="display: flex; flex-direction: column;" class="col-12">
                                        <strong for="name" class="form-label ">{{ __('Job title') }}</strong>
                                        {{ $user->jobTitle }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mt-4">
                            <div class=" row">
                                <div class="text-end">
                                    <button type="submit" class="btn-submit btn btn-primary col-sm-auto col-12">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </div> --}}
                    </form>
                    @if ($user->avatar != '')
                        <form
                            action="@auth('web'){{ route('delete.avatar') }}@elseauth{{ route('client.delete.avatar') }}@endauth"
                            method="post" id="delete_avatar">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>

            <div class="card" id="v-pills-profile">
                <div class="card-header buttonColocation">
                    <h5>{{ __('Add another workspace') }}</h5>
                    <button style="margin-right: 1%;" class="btn btn-sm btn-primary toggle-section buttonColapse"
                        data-target="#workspace-content">-</button>
                </div>
                <div class="card-body collapse-section" id="workspace-content">
                    <div class="col-12 d-flex">
                        <div class="col-4">
                            <div class="d-flex mt-4">
                                <h1><i class="ti ti-users text-success card-icon-text-space m-2"></i>
                                </h1>
                                <div style="display: flex; flex-direction: column;" class="col-8">
                                    <strong for="name" class="form-label mb-3">{{ __('Current workspace') }}</strong>
                                    {{ $currentWorkspace->name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-8 mt-4 ms-5">
                            <a href="#" class="d-flex">
                                <h1 class="mt-2 me-1 d-inline"><i class="bi bi-info-circle" style="color: #FFD43B;"></i>
                                </h1>
                                <p class="text-muted infoWorkspace">
                                    {{ __('The selected workspaces indicate which ones you belong to. You can also select others to be displayed in the list of your workspaces.') }}
                                </p>
                            </a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex mt-4">
                            <h1><i class="ti ti-users text-primary card-icon-text-space m-2"></i></h1>
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <strong for="name" class="form-label mb-4">{{ __('New workspace') }}</strong>
                                <div class="container">
                                    <div class="row">
                                        @foreach ($workspaces as $workspace)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="workspaceCheckbox-{{ $workspace->id }}"
                                                        onchange="workspaceManager({{ $workspace->id }})"
                                                        @if (in_array($workspace->id, $anotherWorkspaces)) checked @endif>
                                                    {{ $workspace->name }}
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

        <!-- timetable-->
        <div class="card divTimetable" id="v-pills-timetable">
            <div class="card-header buttonColocation">
                <h5>{{ __('Timetable') }}</h5>
                <button class="btn btn-sm btn-primary toggle-section buttonColapse"
                    data-target="#timetable-content">-</button>
            </div>
            <div class="mb-3 highlight-error" id="timetableErrorMessage" style="display: none;">
                <div class="bg-danger" style="display: inline-block; padding: 8px; margin: 5px; border-radius: 40%;">
                    <i class="fa-solid fa-circle-xmark text-white"></i>
                </div>
                <span
                    class="text-danger">{{ __('Make sure you have a day selected and the hours imputed correctly.') }}</span>
            </div>
            <div class="card-body collapse-section timetable-content" id="timetable-content"
                style="display: flex; flex-direction: row;">
                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="dayToggle">
                        <p>{{ __($day) }}</p>
                        <label class="switch">
                            <input type="checkbox" class="dayCheckbox" id="{{ strtolower($day) }}Checkbox">
                            <span class="slider round"></span>
                        </label>
                        <input id="{{ strtolower($day) }}Input" type="time" class="inputToggle">
                    </div>
                @endforeach
            </div>
            <div class="alignCenterItems">
                <button id="saveTimetable" class="btn btn-sm btn-primary saveButton">{{ _('Save') }}</button>
            </div>
        </div>

        <!-- holiday picker-->
        <div class="card divTimetable" id="v-pills-HolidayPicker">
            <div class="card-header buttonColocation">
                <h5>{{ __('Holiday') }}</h5>
                <button class="btn btn-sm btn-primary toggle-section buttonColapse"
                    data-target="#Holiday-content">-</button>
            </div>
            <div class="collapse-section card-body divHolidayContent" id="Holiday-content">
                <div id="startDayPick">
                    @include('calendar.customCalendar')

                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div class="HolidayToggleDiv">
                        <p class="holidayP">{{ __('Holiday') }}</p>
                        <label class="switch switchHoliday">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                        <p class="holidayPIntenisve">{{ __('Intensive workday') }}</p>
                    </div>
                    <input class="intensiveWorkInput" type="time" id="intesiveWordaykInput"
                        onclick="this.showPicker()" disabled></input>
                </div>
            </div>
            <div class="alignCenterItems">
                <button id="saveHoliday" class="btn btn-sm btn-primary saveButton">{{ __('Save') }}</button>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript">
            function toggleErrorMessage(show) {
                if (show) {
                    $('#timetableErrorMessage').show();
                } else {
                    $('#timetableErrorMessage').hide();
                }
            }
            let days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            //onload get user timetable info
            window.onload = function() {
                document.body.style.overflow = 'hidden';

                operationUrl = '<?php echo url('user/get-timetable'); ?>';
                $.ajax({
                    type: 'GET',
                    url: operationUrl,
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                    },
                    success: function(data) {
                        console.log("success");

                        // Reactivar scroll
                        document.body.style.overflow = 'auto';
                        // Esconder el loader
                        document.getElementById('loader-overlay').style.display = 'none';

                        // Eliminar id y user_id de los datos recibidos
                        delete data[0].id;
                        delete data[0].user_id;

                        console.log("actual data", data);

                        // Contador de checkboxes activos
                        let checkedCount = 0;

                        // Iterar sobre los días y establecer valores en los inputs
                        days.forEach(day => {
                            let value = data[0][day]; // Obtener el valor del día actual
                            let input = document.getElementById(`${day}Input`);
                            let toggle = input.previousElementSibling.querySelector(
                                'input[type="checkbox"]'); // Obtener el toggle asociado

                            if (value) {
                                input.value = value;
                                toggle.checked = true;
                                input.disabled = false;
                                input.style.backgroundColor = 'white';
                                checkedCount++;
                            }
                        });

                        // Si solo hay un checkbox marcado, deshabilitarlo junto con su inputToggle
                        if (checkedCount < 1) {
                            let lastChecked = $('.dayCheckbox:checked').closest('.dayToggle');
                            lastChecked.find('.dayCheckbox').prop('disabled', true);
                            lastChecked.find('.inputToggle').prop('disabled', true)
                                .css('background-color', '#E4DEDE')
                                .css('-webkit-box-shadow', 'rgb(0 0 0 / 10%) 0px 4px 10px 0px');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    }
                });
            };


            let saveButtonHoliday = document.getElementById('saveHoliday');

            saveButtonHoliday.addEventListener('click', function() {

                //when the user save, show the loader
                document.getElementById('saving-overlay').style.display = 'flex';

                // Deshabilitar el scroll
                document.body.style.overflow = 'hidden';

                //get range of the localstorage
                let rangeDate = localStorage.getItem('DateSelectedRange');
                console.log("rangeDate", rangeDate);

                //check if the days will be Holidays or intensive workdays
                let HolidayToggle = document.querySelector('.HolidayToggleDiv .switch input[type="checkbox"]');

                let rangeAndInput = {
                    "rangeDate": rangeDate
                };
                if (HolidayToggle.checked) {
                    //check the value of the input 
                    let intensiveWorkInput = document.getElementById('intesiveWordaykInput');
                    console.log("Holiday toggle is ON (Checked)");

                    rangeAndInput["intensiveWorkday"] = intensiveWorkInput.value;

                }

                console.log("range and Input::", rangeAndInput);
                operationUrl = '<?php echo url('user/specialUpdate-timetable'); ?>';
                $.ajax({
                    type: 'POST',
                    url: operationUrl,
                    data: {
                        "rangeAndInput": JSON.stringify(rangeAndInput),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                    },
                    success: function(data) {
                        console.log("success");
                        document.getElementById('saving-overlay').style.display = 'none';
                        //reactivar scroll
                        document.body.style.overflow = 'auto';
                    },
                    fail: function() {
                        console.log("fail");
                        document.getElementById('saving-overlay').style.display = 'none';
                        //reactivar scroll
                        document.body.style.overflow = 'auto';
                    },
                });
            });

            let saveButton = document.getElementById('saveTimetable');
            saveButton.addEventListener('click', function() {
                // Mostrar el loader cuando el usuario guarda
                document.getElementById('saving-overlay').style.display = 'flex';

                // Deshabilitar el scroll
                document.body.style.overflow = 'hidden';

                // Recoger los valores de los inputs
                let inputs = document.querySelectorAll(".inputToggle");
                let schedule = {}; // Diccionario para almacenar los valores de horarios

                inputs.forEach(input => {
                    let day = input.id.replace("Input", "").toLowerCase();
                    let checkbox = input.previousElementSibling.querySelector('input[type="checkbox"]');
                    let value = input.value && input.value !== '00:00' ? input.value : null;

                    if (checkbox.checked && value !== null) {
                        schedule[day] = value;
                    }
                });


                if (Object.keys(schedule).length < 1) {

                    toggleErrorMessage(true);

                    document.getElementById('saving-overlay').style.display = 'none';
                    document.body.style.overflow = 'auto';

                    return;
                }

                // Llamada AJAX para actualizar el horario
                operationUrl = '<?php echo url('user/update-timetable'); ?>';
                $.ajax({
                    type: 'POST',
                    url: operationUrl,
                    data: {
                        "inputHours": JSON.stringify(schedule),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                    },
                    success: function(data) {
                        console.log("success");
                        window.location.reload();
                    },
                    fail: function() {
                        console.log("fail");
                        document.getElementById('saving-overlay').style.display = 'none';
                        document.body.style.overflow = 'auto';
                    },
                });
            });


            $(document).ready(function() {
                function checkAtLeastOneCheckbox() {
                    let isChecked = false;
                    $('.dayCheckbox').each(function() {
                        if ($(this).is(':checked')) {
                            isChecked = true;
                            return false;
                        }
                    });
                    return isChecked;
                }

                $('.dayToggle .switch input[type="checkbox"]').on('change', function() {
                    var inputToggle = $(this).closest('.dayToggle').find(
                        '.inputToggle'); // Obtiene el input relacionado al checkbox

                    // Si el checkbox está marcado
                    if ($(this).is(':checked')) {
                        // Habilitar solo el input relacionado con el checkbox
                        inputToggle.prop('disabled', false);
                        inputToggle.css('background-color', 'white');
                        inputToggle.css('-webkit-box-shadow', 'rgb(0 0 0 / 20%) 0px 4px 10px 0px');
                    } else {
                        // Si el checkbox está desmarcado
                        inputToggle.css('background-color', '#E4DEDE'); // Cambia el fondo a gris
                        inputToggle.css('-webkit-box-shadow', 'rgb(0 0 0 / 10%) 0px 4px 10px 0px');
                        inputToggle.prop('disabled', true).val(''); // Deshabilita el input y limpia el valor

                        // Verifica si al menos un checkbox está marcado
                        if ($('.dayToggle .switch input[type="checkbox"]:checked').length < 1) {
                            inputToggle.css('background-color', '#E4DEDE'); // Cambia el fondo a gris
                            inputToggle.css('-webkit-box-shadow', 'rgb(0 0 0 / 10%) 0px 4px 10px 0px');
                            inputToggle.prop('disabled', true)
                            toggleErrorMessage(true);
                        } else {
                            toggleErrorMessage(false);
                        }
                    }
                });


                $('.toggle-section').on('click', function() {
                    var target = $(this).data('target');
                    var saveButton = $('#saveTimetable');
                    var saveButtonHoliday = $('#saveHoliday');

                    $(target).slideToggle(function() {
                        if (target === "#timetable-content") {
                            if ($(target).is(':visible')) {
                                $(target).css({
                                    display: 'flex',
                                    justifyContent: 'space-evenly'
                                });
                                saveButton.show();
                            } else {
                                $(target).css({
                                    display: 'none'
                                });
                                saveButton.hide();
                            }
                        } else if (target == "#Holiday-content") {
                            if ($(target).is(':visible')) {
                                saveButtonHoliday.show();
                            } else {
                                saveButtonHoliday.hide();
                            }
                        }
                    });

                    var currentSymbol = $(this).text();
                    $(this).text(currentSymbol === '-' ? '+' : '-');
                });

                $('.inputToggle').prop('disabled', true).css({
                    'background-color': '#E4DEDE',
                    '-webkit-box-shadow': 'rgb(0 0 0 / 10%) 0px 4px 10px 0px'
                });

                $('.HolidayToggleDiv .switch input[type="checkbox"]').on('change', function() {
                    var intensiveWorkInput = $('#intesiveWordaykInput');

                    if ($(this).is(':checked')) {
                        intensiveWorkInput.prop('disabled', false);
                        intensiveWorkInput.css({
                            'background-color': 'white',
                            '-webkit-box-shadow': 'rgb(0 0 0 / 20%) 0px 4px 10px 0px'
                        });
                    } else {
                        intensiveWorkInput.prop('disabled', true).val('');
                        intensiveWorkInput.css({
                            'background-color': '#E4DEDE',
                            '-webkit-box-shadow': 'rgb(0 0 0 / 10%) 0px 4px 10px 0px'
                        });
                    }
                });
            });

            $('#avatar').change(function() {

                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#myAvatar').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            function workspaceManager(workspaceId) {
                let isChecked = document.getElementById(`workspaceCheckbox-${workspaceId}`).checked;

                let url = isChecked ?
                    '{{ route('addWorkspace', ':id') }}'.replace(':id', workspaceId) :
                    '{{ route('leave-workspace', ':id') }}'.replace(':id', workspaceId);

                let method = isChecked ? 'GET' : 'DELETE';

                $.ajax({
                    url: url,
                    type: method,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response) {
                        location.reload();

                    },
                    error: function(response) {
                        location.reload();

                    }
                });
            }
        </script>
        <script>
            $(document).on('click', '.list-group-item', function() {
                $('.list-group-item').removeClass('active');
                $('.list-group-item').removeClass('text-primary');
                setTimeout(() => {
                    $(this).addClass('active').removeClass('text-primary');
                }, 10);
            });

            var type = window.location.hash.substr(1);
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            if (type != '') {
                $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
            } else {
                $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
            }

            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300
            })
        </script>
    @endpush
    <style>
        .icon {
            width: 25px;
            height: 23px;
            margin-right: 10px;
        }
    </style>
