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
@include('calendar.customCalendar')
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
                    <button style="margin-right: 1%;" class="btn btn-sm btn-primary toggle-section buttonColapse" data-target="#avatar-content">-</button>
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
                                    <img @if (\Auth::user()->avatar) src="{{ asset(Auth::user()->avatar) }}" @else avatar="{{ Auth::user()->name }}" @endif
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
                    <button style="margin-right: 1%;" class="btn btn-sm btn-primary toggle-section buttonColapse" data-target="#workspace-content">-</button>
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
                <button class="btn btn-sm btn-primary toggle-section buttonColapse" data-target="#timetable-content">-</button>
            </div>

            <div class="card-body collapse-section" id="timetable-content" style="display: flex; flex-direction: row;">
                <div class="dayToggle">
                    <p>{{__('Monday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="mondayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>

                <div class="dayToggle">
                    <p>{{__('Tuesday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="tuesdayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>

                <div class="dayToggle">
                    <p>{{__('Wednesday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="wednesdayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>

                <div class="dayToggle">
                    <p>{{__('Thursday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="thursdayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>

                <div class="dayToggle">
                    <p>{{__('Friday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="fridayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>

                <div class="dayToggle">
                    <p>{{__('Saturday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="saturdayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>

                <div class="dayToggle">
                    <p>{{__('Sunday')}}</p>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    <input id="sundayInput" type="time" class="inputToggle" onclick="this.showPicker()"></input> 
                </div>
            </div>

            <div class="alignCenterItems" >
                <button id="saveTimetable" class="btn btn-sm btn-primary saveButton">{{__('Save')}}</button>
            </div>       
        </div>

        <!-- timetable-->
        <div class="card divTimetable" id="v-pills-HolidayPicker">
            <div class="card-header buttonColocation">
                <h5>{{ __('Holiday') }}</h5>
                <button class="btn btn-sm btn-primary toggle-section buttonColapse" data-target="#Holiday-content">-</button>
            </div>
            <div class="collapse-section card-body divHolidayContent" id="Holiday-content">
                <div id="startDayPick" class="inputDatePicker">
                    <i class="fa-solid fa-calendar HolidayIcon"></i>
                </div>
                <div style="display: contents;">
                    <div class="HolidayToggleDiv">
                        <p>{{__('Holiday')}}</p>
                        <label class="switch" style="width: 23%;margin-bottom: 10px;">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                        <p>{{__('Intensive workday')}}</p>
                    </div>
                    <input class="intensiveWorkInput" type="time" id="intesiveWordaykInput" onclick="this.showPicker()" disabled></input>
                </div>
            </div>
            <div class="alignCenterItems" >
                <button id="saveHoliday" class="btn btn-sm btn-primary saveButton">{{_('Save')}}</button>
            </div>   
        </div>

    @endsection
    @push('scripts')
     
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript">

            let days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            //onload get user timetable info
            window.onload = function() {

                // Deshabilitar el scroll
                document.body.style.overflow = 'hidden';

                operationUrl = '<?php echo url("user/get-timetable"); ?>';
                $.ajax({
                    type: 'GET',
                    url: operationUrl,
                            headers: {
                                "Content-Type": "application/json",
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                            },
                            success: function(data) {
                                console.log("success");

                                //reactivar scroll
                                document.body.style.overflow = 'auto';
                                //esconder el loader
                                document.getElementById('loader-overlay').style.display = 'none';

                               //deleting the id and user_id from the data

                                delete data[0].id;
                                delete data[0].user_id;

                               console.log("actual data", data);

                               //Iterate over the days and set the value of the inputs
                               days.forEach(day => {
                                let value = data[0][day]; // Obtener el valor del día actual
                                let input = document.getElementById(`${day}Input`);
                                let toggle = input.previousElementSibling.querySelector('input[type="checkbox"]'); // Obtener el toggle asociado

                                   if(value){
                                       input.value = value;
                                       toggle.checked = true;
                                   }
                               });
                               
                            },
                            error: function(xhr, status, error) {
                                console.error("Error:", error);
                            }
                });
            };

            //show/hide custom calendar
            let showCustomCalendar = document.getElementById('startDayPick');

            showCustomCalendar.addEventListener('click', function() {

                let customCalendar = document.getElementById('customCalendarParent');

                if(customCalendar){

                    if(customCalendar.style.display == 'none'){
                        customCalendar.style.display = 'block';
                    }else{
                        customCalendar.style.display = 'none';
                    }
                }

            });

            let saveButtonHoliday = document.getElementById('saveHoliday');

            saveButtonHoliday.addEventListener('click', function(){

                //get range of the localstorage
                let rangeDate = localStorage.getItem('DateSelectedRange');
                console.log("rangeDate", rangeDate);

                //check if the days will be Holidays or intensive workdays
                let HolidayToggle = document.querySelector('.HolidayToggleDiv .switch input[type="checkbox"]');
                
                let rangeAndInput ={ "rangeDate": rangeDate};
                if (HolidayToggle.checked) {
                    //check the value of the input 
                    let intensiveWorkInput = document.getElementById('intesiveWordaykInput');
                    console.log("Holiday toggle is ON (Checked)");

                    rangeAndInput["intensiveWorkday"] = intensiveWorkInput.value;

                }

                console.log("range and Input::",rangeAndInput);
                operationUrl = '<?php echo url("user/specialUpdate-timetable"); ?>';
                $.ajax({
                    type: 'POST',
                    url: operationUrl,
                            data: {
                                "rangeAndInput" : JSON.stringify (rangeAndInput),
                            }, 
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                            },
                            success: function(data) {
                               console.log("success");
                            },
                            fail:function() {
                                console.log("fail");
                            },
                });
            });

            let saveButton = document.getElementById('saveTimetable');

            saveButton.addEventListener('click', function(){
                //check the value of the inputs

                let inputs = document.querySelectorAll(".inputToggle");
                     // Diccionario para almacenar los días y sus valores
                    let schedule = {};
                    inputs.forEach(input => {
                        // Extraer el día de la semana desde el ID del input
                        let day = input.id.replace("Input", "").toLowerCase(); // Quitar "Input" del ID

                        // Obtener el valor del input o asignar null si está vacío
                        let value = input.value ? input.value : null;

                        // Agregar el día y el valor al diccionario
                        schedule[day] = value;
                    });

                    console.log(schedule);

                //llamada ajax al backend
                
                operationUrl = '<?php echo url("user/update-timetable"); ?>';
                $.ajax({
                    type: 'POST',
                    url: operationUrl,
                            data: {
                                "inputHours" : JSON.stringify (schedule),
                            }, 
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluye el token CSRF
                            },
                            success: function(data) {
                               console.log("success")
                            },
                            fail:function() {
                                console.log("fail")
                            },
                });
            });

            $(document).ready(function () {
        // Manejar el cambio en el checkbox de la sección de Holiday
                $('.HolidayToggleDiv .switch input[type="checkbox"]').on('change', function () {
                    var intensiveWorkInput = $('#intesiveWordaykInput');

                    if ($(this).is(':checked')) {
                        // Habilitar el input cuando el checkbox esté marcado
                        intensiveWorkInput.prop('disabled', false);
                        intensiveWorkInput.css({
                            'background-color': 'white',
                            '-webkit-box-shadow': 'rgb(0 0 0 / 20%) 0px 4px 10px 0px'
                        });
                    } else {
                        // Deshabilitar el input cuando el checkbox no esté marcado
                        intensiveWorkInput.prop('disabled', true).val('');
                        intensiveWorkInput.css({
                            'background-color': '#E4DEDE',
                            '-webkit-box-shadow': 'rgb(0 0 0 / 10%) 0px 4px 10px 0px'
                        });
                    }
                });

                $('.toggle-section').on('click', function () {
                    var target = $(this).data('target');
                    var saveButton = $('#saveTimetable');
                    var saveButtonHoliday = $('#saveHoliday');

                    $(target).slideToggle(function () {
                        // Si el objetivo es la sección Timetable
                        if (target === "#timetable-content") {
                            if ($(target).is(':visible')) {
                                $(target).css({
                                    display: 'flex',
                                    justifyContent: 'space-evenly'
                                });
                                saveButton.show(); // Mostrar el botón Save si la sección está visible
                            } else {
                                $(target).css({
                                    display: 'none'
                                });
                                saveButton.hide(); // Ocultar el botón Save si la sección está oculta
                            }
                        }else if(target == "#Holiday-content"){
                            if ($(target).is(':visible')) {
                                saveButtonHoliday.show();
                            }else{
                                saveButtonHoliday.hide();
                            }
                        }
                    });

                    // Cambia el símbolo del botón entre "+" y "-"
                    var currentSymbol = $(this).text();
                    $(this).text(currentSymbol === '-' ? '+' : '-');
                });

                $('.inputToggle').prop('disabled', true).css({
                    'background-color': '#E4DEDE',
                    '-webkit-box-shadow': 'rgb(0 0 0 / 10%) 0px 4px 10px 0px'
                });

                $('.dayToggle .switch input[type="checkbox"]').on('change', function () {
                    // Encuentra el input más cercano al toggle actual
                    var inputToggle = $(this).closest('.dayToggle').find('.inputToggle');

                    if ($(this).is(':checked')) {
                        // Si el toggle está activo, cambia el fondo del input
                        inputToggle.css('background-color', 'white');
                        inputToggle.css('-webkit-box-shadow', 'rgb(0 0 0 / 20%) 0px 4px 10px 0px ');
                        inputToggle.prop('disabled', false);
                    } else {
                        // Si el toggle está desactivado, restablece el color del fondo
                        inputToggle.css('background-color', '#E4DEDE');
                        inputToggle.css('-webkit-box-shadow', 'rgb(0 0 0 / 10%) 0px 4px 10px 0px');
                        inputToggle.prop('disabled', true).val('');;
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
