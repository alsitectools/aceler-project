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

    <style>
        /* styles for searchable workspace list */
        .workspace-search-wrapper .workspace-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .workspace-search-wrapper .workspace-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background-color: #d3d3d378;
            border-radius: 15px;
            font-size: 14px;
            border: 1px solid lightgray;
            font-weight: bold;
        }

        .workspace-search-wrapper .workspace-chip-close {
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }

        .workspace-search-wrapper .workspace-list {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .workspace-search-wrapper .workspace-item {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .workspace-search-wrapper .workspace-item.selected {
            background-color: #aa182c3b;
            border-color: #aa182c;
        }
    </style>
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">

                    <a href="#v-pills-profile"
                        class="list-group-item list-group-item-action border-0">{{ __('Add another workspace') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>

                    <a href="#v-pills-timetable"
                        class="list-group-item list-group-item-action border-0">{{ __('Timetable') }}
                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                    </a>

                    @if ($user->is_exporter === 1)
                        <a href="#v-pills-axapta"
                            class="list-group-item list-group-item-action border-0">{{ __('Export to Axapta') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-9">

            <div class="card" id="v-pills-profile">
                <div class="card-header buttonColocation">
                    <h5>{{ __('Add another workspace') }}</h5>
                    <button style="margin-right: 1%;" class="btn btn-sm btn-primary toggle-section buttonColapse"
                        data-target="#workspace-content">-</button>
                </div>
                <div class="card-body collapse-section" id="workspace-content">
                    <div class="col-12 d-flex">
                    <div class="col-12">
                        <div class="d-flex mt-4">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <strong for="name" class="form-label mb-4">{{ __('Add new workspace') }}</strong>
                                <div class="workspace-search-wrapper">
                                    <input type="text" id="workspaceSearch"
                                        placeholder="{{ __('Search workspaces...') }}" class="form-control mb-3">
                                    <div class="workspace-chips" id="workspaceChips">
                                        @foreach ($workspaces as $workspace)
                                            @if (in_array($workspace->id, $anotherWorkspaces))
                                                <div class="workspace-chip" data-workspace-id="{{ $workspace->id }}">
                                                    {{ $workspace->name }}
                                                    <span class="workspace-chip-close">✕</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="workspace-list">
                                        @foreach ($workspaces as $workspace)
                                            <div class="workspace-item"
                                                style="{{ in_array($workspace->id, $anotherWorkspaces) ? 'display:none;' : '' }}"
                                                data-name="{{ strtolower($workspace->name) }}"
                                                data-workspace-id="{{ $workspace->id }}">
                                                {{ $workspace->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="alignCenterItems">
                                        <button id="saveWorkspaces" class="btn btn-sm btn-primary saveButton" style="margin-top: 1%;">
                                            {{ __('Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>

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
                        <input id="{{ strtolower($day) }}Input" type="time" lang="es-ES" step="60"
                            class="inputToggle">
                    </div>
                @endforeach
            </div>
            <div class="alignCenterItems">
                <button id="saveTimetable" class="btn btn-sm btn-primary saveButton">{{ __('Save') }}</button>
            </div>
        </div>

        <!-- Axapta Exporter-->
        @if ($user->is_exporter === 1)
            <div class="card divTimetable" id="v-pills-axapta">
                <div class="card-header buttonColocation">
                    <h5>{{ __('Export to Axapta') }}</h5>
                    <button class="btn btn-sm btn-primary toggle-section buttonColapse"
                        data-target="#axapta-content">-</button>
                </div>

                <div class="collapse-section card-body" id="axapta-content">
                    <div style="display: flex;justify-content: flex-start;">
                        <i class="bi bi-info-circle"
                            style="color: #FFD43B;font-size: 30px;margin-left: 15px;margin-right: -63px;"></i>
                        <p class="text-muted infoWorkspace">
                            {{ __('Click the following button to export all Jobside projects to Axapta') }}
                        </p>
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <button id="exportAxapta" class="btn btn-sm btn-primary" style="width: 15%">
                            {{ __('Export') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
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

            let saveButton = document.getElementById('saveTimetable');
            if (saveButton) {
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
            }


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

            // search/filter behavior and chip-based selection for workspace list
            document.addEventListener('DOMContentLoaded', function() {
                const workspaceSection = document.querySelector('#workspace-content .workspace-search-wrapper');
                if (!workspaceSection) {
                    return;
                }

                const searchInput = workspaceSection.querySelector('#workspaceSearch');
                const chipsContainer = workspaceSection.querySelector('#workspaceChips');
                const saveBtn = workspaceSection.querySelector('#saveWorkspaces');
                const workspaceItems = workspaceSection.querySelectorAll('.workspace-item');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                // Initialize selected workspace IDs from existing chips
                let selectedWorkspaceIds = new Set();
                workspaceSection.querySelectorAll('.workspace-chip').forEach(function(chip) {
                    selectedWorkspaceIds.add(parseInt(chip.dataset.workspaceId));
                });
                const initialWorkspaceIds = new Set(selectedWorkspaceIds);

                function renderWorkspaceList() {
                    const filter = (searchInput?.value || '').toLowerCase();

                    workspaceItems.forEach(function(item) {
                        const workspaceId = parseInt(item.dataset.workspaceId);
                        const workspaceName = (item.dataset.name || item.textContent || '').trim().toLowerCase();
                        const matchesFilter = workspaceName.includes(filter);
                        const isSelected = selectedWorkspaceIds.has(workspaceId);

                        item.style.display = !isSelected && matchesFilter ? '' : 'none';
                    });
                }

                function createChip(workspaceId, workspaceName) {
                    const chip = document.createElement('div');
                    chip.className = 'workspace-chip';
                    chip.dataset.workspaceId = workspaceId;
                    chip.innerHTML = workspaceName + ' <span class="workspace-chip-close">✕</span>';
                    chipsContainer.appendChild(chip);

                    chip.querySelector('.workspace-chip-close').addEventListener('click', function(e) {
                        e.stopPropagation();
                        removeChip(chip, workspaceId);
                    });
                }

                // Filter list by search input
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        renderWorkspaceList();
                    });
                }

                // Handle workspace item click - add chip and hide item
                workspaceItems.forEach(function(item) {
                    item.addEventListener('click', function() {
                        const workspaceId = parseInt(this.dataset.workspaceId);
                        const workspaceName = this.textContent.trim();

                        if (selectedWorkspaceIds.has(workspaceId)) {
                            return;
                        }

                        selectedWorkspaceIds.add(workspaceId);
                        createChip(workspaceId, workspaceName);
                        renderWorkspaceList();
                    });
                });

                // Handle chip close click
                function removeChip(chipElement, workspaceId) {
                    selectedWorkspaceIds.delete(workspaceId);
                    chipElement.remove();
                    renderWorkspaceList();
                }

                // Handle save button
                if (saveBtn) {
                    saveBtn.addEventListener('click', async function() {
                        let selectedIds = Array.from(selectedWorkspaceIds);
                        const initialIds = Array.from(initialWorkspaceIds);
                        const currentWorkspaceId = {{ (int) $currentWorkspace->id }};

                        if (selectedIds.length === 0) {
                            selectedIds = [currentWorkspaceId];
                        }

                        const selectedIdSet = new Set(selectedIds);
                        const idsToAdd = selectedIds.filter(id => !initialWorkspaceIds.has(id));
                        const idsToRemove = initialIds.filter(id => !selectedIdSet.has(id));

                        saveBtn.disabled = true;
                        document.getElementById('saving-overlay').style.display = 'flex';
                        document.body.style.overflow = 'hidden';

                        const doAddWorkspace = function(workspaceId) {
                            return $.ajax({
                                url: '{{ route('addWorkspace', ':id') }}'.replace(':id', workspaceId),
                                type: 'GET',
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                                }
                            });
                        };

                        const doRemoveWorkspace = function(workspaceId) {
                            return $.ajax({
                                url: '{{ route('leave-workspace', ':id') }}'.replace(':id', workspaceId),
                                type: 'DELETE',
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                                }
                            });
                        };

                        try {
                            for (const workspaceId of idsToAdd) {
                                await doAddWorkspace(workspaceId);
                            }

                            for (const workspaceId of idsToRemove) {
                                await doRemoveWorkspace(workspaceId);
                            }
                        } catch (error) {
                            console.error('Error saving selected workspaces:', error);
                        } finally {
                            window.location.reload();
                        }
                    });
                }

                // Add event listeners to close buttons on existing chips
                workspaceSection.querySelectorAll('.workspace-chip-close').forEach(function(closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const chip = this.closest('.workspace-chip');
                        const workspaceId = parseInt(chip.dataset.workspaceId);
                        removeChip(chip, workspaceId);
                    });
                });

                renderWorkspaceList();
            });
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

        <script>
            $('#exportAxapta').on('click', function() {
                operationUrl = '<?php echo url('/projects/export-axapta'); ?>';
                $.ajax({
                    type: 'POST',
                    url: operationUrl,
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Decodificar el contenido del archivo
                            const fileContent = atob(response.fileContent);
                            const blob = new Blob([fileContent], {
                                type: 'text/plain'
                            });

                            // Crear un link de descarga
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = response.fileName;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(link.href);
                        } else {
                            console.error("Error en la respuesta:", response.error);
                            alert('Error: ' + (response.error || 'Error desconocido'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error AJAX:", error);
                        console.error("Response:", xhr.responseText);
                        alert('Error al descargar el archivo: ' + error);
                    }
                });
            });
        </script>
    @endpush
    <style>
        .icon {
            width: 25px;
            height: 23px;
            margin-right: 10px;
        }
    </style>
