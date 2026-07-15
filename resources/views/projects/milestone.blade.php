<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <style>
        .dropdown-menu {
            max-height: 110px !important;
            overflow-y: auto !important;

        }

        #user-select {
            display: none;
        }

        /* Add styles for drag state */
        .browse-file.dragover {
            border: 2px dashed #ccc !important;
        }

        .browse-file.dragover .dz-message {
            color: #999 !important;
        }

        .browse-file.dragover .dz-message span,
        .browse-file.dragover .dz-message p {
            color: #999 !important;
        }

        .deleteFileButton,
        .deleteFileButton:hover,
        .deleteFileButton:focus {
            padding: 5px;
            background-color: #a11233;
            border-radius: 5px;
            cursor: pointer;

        }

        .iftheordoesnotexist {
            font-size: 13px;
            margin-left: 22px
        }
    </style>
</head>
@php
    $user = Auth::user();
    $actionUrl =
        $project_id == -1
            ? route('projects.milestone.store', [$currentWorkspace->slug, $project_id])
            : route('projects.milestone.store', [$currentWorkspace->slug, $project->id]);
@endphp
<style>
    #user-select {
        display: none;
    }
</style>
@if ($currentWorkspace)
    <div class="modal-body">
        <!-- Toast Notification -->
        <div aria-live="polite" aria-atomic="true"
            class="position-fixed d-flex justify-content-end  top-25 start-50 translate-middle-y" style="z-index: 1080;">
            <div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive"
                aria-atomic="true" id="successToast">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="milestone-tab" data-bs-toggle="tab" href="#milestone" role="tab"
                    aria-controls="milestone" aria-selected="true">
                    <i class="fa-solid fa-file-lines me-2"></i> {{ __('Create Milestone') }}
                </a>
            </li>
            @if (isset($project_id) && $project_id == -1)
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="project-tab" data-bs-toggle="tab" href="#projectForm" role="tab"
                        aria-controls="project" aria-selected="false">
                        <i class="fa-solid fa-diagram-project me-2"></i> {{ __('Create New Project') }}
                    </a>
                </li>
            @endif
        </ul>
        <div class="tab-content mt-3" id="myTabContent">
            <!-- Milestone Form -->
            <div class="tab-pane fade show active" id="milestone" role="tabpanel" aria-labelledby="milestone-tab">
                <form id="milestone-form" method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @if (isset($project_id) && $project_id == -1)
                                    <div id="project">
                                        <label class="col-form-label">{{ __('Search project') }}
                                            <span class="text-muted iftheordoesnotexist">
                                                <i class="bi bi-info-circle me-2"
                                                    style="color: #FFD43B;"></i>{{ __('If the project does not exist, create a new project.') }}
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" id="searchProject"
                                            placeholder="{{ __('Name or reference M.O') }}" autocomplete="off">
                                        <input id="projectId" name="project_id" style="display: none">
                                        <div class="list-group" id="projects_list" style="width:48% !important"></div>
                                    </div>
                                @else
                                    <label class="col-form-label">{{ __('Project') }}</label>
                                    <input class="form-control" type="text" id="projectIdDisabled"
                                        value="{{ $project->name }}" disabled>
                                    <input class="form-control" type="text" id="project_id" name="project_id"
                                        value="{{ $project->id }}" autocomplete="off" style="display: none;">
                                @endif
                            </div>
                        </div>
                        @php
                            $isPhaseProject = isset($project) && in_array((int) $project->type, [3, 5], true);
                        @endphp
                        <div class="col-md-6" id="phase-wrapper" style="{{ $isPhaseProject ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('Stage') }}</label>
                                <select class="form-control form-control-light" name="phase" id="phase">
                                    <option value="">{{ __('Choose one') }}</option>
                                    @foreach ($phases as $phase)
                                        <option value="{{ $phase }}">{{ __(\App\Models\MilestonePhases::translationKey($phase)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6" id="mo-wrapper" style="{{ $isPhaseProject ? 'display:none;' : '' }}">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('MO') }}</label>
                                @if (isset($project_id) && $project_id == -1)
                                    <input type="text" class="form-control form-control-light" id="milestone_mo"
                                        placeholder="{{ __('MO') }}" name="ref_mo" required readonly>
                                @else
                                    <input type="text" class="form-control form-control-light"
                                        placeholder="{{ $project->ref_mo }}" disabled>
                                    <input type="text" name="ref_mo" value="{{ $project->ref_mo }}"
                                        style="display: none;">
                                @endif
                            </div>
                        </div>

                        {{-- Inicio apartado asignado a --}}
                        {{-- <div class="col-md-6" id="requestBy-req">
                        <label class="col-form-label">Asignado a</label>
                        <input type="text" class="form-control" id="search-requested-by"
                            placeholder="{{ __('Search') }}" name="search-requested-by" value="" autocomplete="off">

                        <div id="user-select-req-by" aria-label="Default select example" class="dropdown-menu"
                            style="width: 45% !important;">
                            @foreach ($users as $user)
                            <div class="option list-group-item list-group-item-action stylelist ps-3"
                                collected-data-id="{{ $user->id }}" style="padding: 8px; cursor: pointer;">
                                {{ $user->name }}
                            </div>
                            @endforeach
                            <input type="text" name="req_assing_to" id="req_assing_To" style="display: none;">
                        </div>
                    </div> --}}
                        {{-- Final apartado --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('Title') }}</label>
                                <input type="text" class="form-control form-control-light" id="milestone-title"
                                    placeholder="{{ __('Title') }}" name="title" required>
                            </div>
                        </div>

                        <div class="col-md-6" id="requestBy">
                            <label class="col-form-label">{{ __('Requested by') }}</label>
                            <input type="text" class="form-control form-control-light" id="search"
                                placeholder="{{ __('Search') }}" autocomplete="off">



                            <div id="user-select" aria-label="Default select example" class="dropdown-menu"
                                style="width: 45% !important;">
                                @foreach ($users as $user)
                                    <div class="option list-group-item list-group-item-action stylelist ps-3"
                                        data-id="{{ $user->id }}" style="padding: 8px; cursor: pointer;">
                                        {{ $user->name }}
                                    </div>
                                @endforeach
                                <input type="hidden" name="assing_to" id="assing_To" required>
                            </div>
                        </div>

                    </div>
                    <div class="form-check form-switch mb-3" id="visado" style="display: none">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleFormSwitch">
                        <label class="form-check-label"
                            for="toggleFormSwitch">{{ __('Only in case it is necessary to to
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                carry out a project with a visa.') }}</label>
                    </div>
                    <div id="additionalForm" class="collapse mt-3">
                        <div class="card card-body">
                            <div class="mb-3">
                                <label for="input1"
                                    class="form-label">{{ __('Name of the company that will install the
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        system') }}:</label>
                                <input type="text" class="form-control" name="company" id="company"
                                    placeholder="Ingrese valor">
                            </div>
                            <div class="mb-3">
                                <label for="input2"
                                    class="form-label">{{ __('Name of prime contractor') }}:</label>
                                <input type="text" class="form-control" name="contractor" id="contractor"
                                    placeholder="Ingrese valor">
                            </div>
                            <div class="mb-3">
                                <label for="input2"
                                    class="form-label">{{ __('Address of prime contractor') }}:</label>
                                <input type="text" class="form-control" name="contractorAdress"
                                    id="contractorAdress" placeholder="Ingrese valor">
                            </div>
                            <div class="mb-3">
                                <label for="input2" class="form-label">{{ __('Adress jobsite') }}:</label>
                                <input type="text" class="form-control" name="jobsiteAdress" id="jobsiteAdress"
                                    placeholder="Ingrese valor">
                            </div>
                            <p class="mb-3">
                                <b>{{ __('Note: In order to carry out the project it is necessary to send the quotation of
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                the formwork and falsework system, and the complete assembly drawings and geometrical
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                definition of the structure.') }}
                                </b>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Created date') }}</label>
                                <input type="date" class="form-control form-control-light date" id="start_date"
                                    name="start_date" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Desired delivery date') }}</label>
                                <input onclick="this.showPicker()" type="date"
                                    class="form-control form-control-light date" id="end_date" value=""
                                    placeholder="{{ __('Date') }}" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        {{-- PRIORITY --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Priority') }}</label>
                                <select class="form-control form-control-light" id="priority" name="priority">
                                    <option value="">{{ __('Not defined') }}</option>
                                    <option value="alta">{{ __('High Priority') }}</option>
                                    <option value="media">{{ __('Medium Priority') }}</option>
                                    <option value="baja">{{ __('Low Priority') }}</option>
                                </select>
                            </div>
                        </div>
                        {{-- PHASE (solo visible si proyecto type = 3) --}}
                        @php
                            $typeId = isset($project) ? (int) $project->type : null;
                            $typeName = isset($project) ? $project->typeRel->name ?? '' : '';
                            $showPhase =
                                $typeId === 3 ||
                                in_array($typeName, ['I+D Project', 'Proyecto I+D']) ||
                                $typeId === 5 ||
                                in_array($typeName, ['I+D Development', 'Desarrollo I+D']);
                            $showStage = $showPhase;
                        @endphp

                        <div class="col-md-6" id="stage-wrapper" style="{{ $showStage ? '' : 'display:none;' }}">
                            <div class="form-group">
                                <label class="form-label">{{ __('Phase') }}</label>
                                <select class="form-control form-control-light" name="stage" id="stage"
                                    data-add-phase-label="{{ __('Add phase') }}">
                                    <option value="">{{ __('Choose one') }}</option>
                                    @foreach ($stagesProject ?? [] as $stageName)
                                        <option value="{{ $stageName }}">{{ __($stageName) }}</option>
                                    @endforeach
                                    <option value="add_phase">{{ __('Add phase') }}</option>
                                </select>
                                <div id="new-stage-name-wrapper" class="mt-2" style="display: none;">
                                    <input type="text" name="new_stage_name" id="new_stage_name"
                                        class="form-control form-control-light"
                                        placeholder="{{ __('Enter phase name') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>




                    </div>
                    <div class="col-md-12 mt-3" style="padding-bottom: 10px;">
                        <div class="row">
                            <!-- Sección de Descripción (Izquierda) -->
                            <div class="col-md-6">
                                <label for="description-text" class="form-label" style="margin-bottom: 3px;">
                                    <strong>{{ __('Description') }}</strong>
                                </label>
                                <textarea style="height:82%" class="form-control mt-2" id="description-text" name="description" rows="5"
                                    placeholder="{{ __('Enter description...') }}"></textarea>
                            </div>

                            <!-- Sección de Archivos Adjuntos (Derecha) -->
                            <div class="col-md-6">
                                <label class="form-label"><strong>{{ __('Upload files') }}</strong></label>
                                <div>
                                    <div class="col-md-12 dropzone browse-file" id="dropzonewidgetMilestone">
                                        <div class="dz-message" data-dz-message>
                                            <input type="file" id="file-uploadMilestone" style="display:none"
                                                multiple />
                                            <span> {{ __('Drop files here to upload') }}</span>
                                            <p>
                                                {{ __('You can Also hold click + Control + V to paste the content of the clipboard') }}
                                            </p>
                                            <p class="text-muted" style="font-size:15px; margin:5px;">50MB</p>
                                            <small class="text-muted">.png .gif .pdf .txt .doc .docx .zip .rar .dwg
                                                .dxf</small>

                                        </div>
                                    </div>
                                </div>
                                <div id="file-list" style="padding-top: 5px;"></div>
                                <div id="hidden-file-inputs" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" id="submitMilestoneBtn" class="btn btn-primary">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="projectForm" role="tabpanel" aria-labelledby="project-tab">
                <form class="" id="projectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="col-form-label">{{ __('Project type') }}</label>
                                <select class="form-control form-control-light" name="project_type" id="project_type"
                                    required="">
                                    <option selected disabled>{{ __('Choose one') }}</option>
                                    @foreach ($project_type as $type)
                                        <option style="background-color:white; color:black;"
                                            value="{{ $type->id }}" data-type="{{ $type->name }}">
                                            {{ __($type->name) }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6" id="ref_mo" style="display: none;">
                                <label for="search_mo" class="col-form-label">{{ __('Search MO') }}</label>
                                <input type="text" class="form-control" name="ref_mo" id="searchMo"
                                    placeholder="Masterobras" autocomplete="off">
                                <div class="list-group" id="ref_mo_list" style="width: 48%"></div>

                            </div>

                            <div class="form-group col-md-6" id="clipo"
                                style="display: none; position: relative">
                                <label for="clipo" class="col-form-label">{{ __('Search client') }}</label>
                                <input class="form-control" type="text" name="clipo" id="searchClipo"
                                    placeholder="{{ __('Clipo') }}" autocomplete="off">
                                <div class="list-group" style="display: none;" id="clipo_list"></div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="projectname" class="col-form-label">{{ __('Name') }}</label>
                                <input class="form-control" type="text" id="projectname" name="name" required
                                    placeholder="{{ __('Project Name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <input type="submit" value="{{ __('Add New project') }}" class="btn btn-primary">
                    </div>
                </form>
            </div>

        </div>
    </div>
@else
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="page-error">
                    <div class="page-inner">
                        <h1>404</h1>
                        <div class="page-description">
                            {{ __('Page Not Found') }}
                        </div>
                        <div class="page-search">
                            <p class="text-muted mt-3">
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            best of us. Here's a little tip that might help you get back on track.") }}
                            </p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}">
                                    <i class="fas fa-reply"></i> {{ __('Return Home') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<script src="{{ asset('assets/custom/libs/nicescroll/jquery.nicescroll.min.js') }} "></script>
<!-- Scripts para el dropdown de usuarios -->
<script>
    (function() {
        // Definir valores por defecto del usuario logueado
        var searchInput = document.getElementById('search');
        var optionsList = document.getElementById('user-select');
        var options = optionsList ? optionsList.getElementsByClassName('option') : [];
        var hiddenInput = document.getElementById('assing_To');

        // Nombres para handlers del documento para poder eliminarlos luego
        function _requestedByDocClick(event) {
            if (!event.target.closest('#requestBy') && !event.target.closest('#search') && optionsList) {
                optionsList.style.display = 'none';
            }
        }

        // Limpiar handlers anteriores si existen
        if (window._requestedByCleanup) {
            window._requestedByCleanup();
        }

        // Registrar cleanup
        window._requestedByCleanup = function() {
            document.removeEventListener('click', _requestedByDocClick);
        };

        if (!searchInput || !optionsList || !hiddenInput) {
            // Si no existen elementos, salir (puede ocurrir si se carga mal el modal)
            return;
        }

        function inputMatchesAnyOption(value) {
            const v = (value || '').trim().toLowerCase();
            if (!v) return false;

            for (let i = 0; i < options.length; i++) {
                const name = options[i].innerText.trim().toLowerCase();
                if (name === v) return true; // match exacto
            }
            return false;
        }


        // ✅ Asegurar que arranca vacío
        hiddenInput.value = '';
        searchInput.value = '';

        searchInput.addEventListener('click', function(event) {
            event.stopPropagation();
            optionsList.style.display = 'block';
        });

        // ✅ Si el usuario escribe, invalidamos la selección (hidden vacío)
        searchInput.addEventListener('input', function() {
            hiddenInput.value = '';
            searchInput.classList.remove('is-invalid');

            const filter = searchInput.value.toLowerCase();
            let hasVisibleOption = false;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.innerText.toLowerCase();

                if (text.includes(filter)) {
                    option.style.display = 'block';
                    hasVisibleOption = true;
                } else {
                    option.style.display = 'none';
                }
            }

            optionsList.style.display = hasVisibleOption ? 'block' : 'none';
        });

        for (let i = 0; i < options.length; i++) {
            options[i].addEventListener('click', function() {
                const selectedUserId = this.getAttribute('data-id');
                searchInput.value = this.innerText;
                hiddenInput.value = selectedUserId;
                searchInput.classList.remove('is-invalid');
                optionsList.style.display = 'none';
            });
        }

        document.addEventListener('click', _requestedByDocClick);

        // ✅ Si sales del input sin seleccionar, limpiamos el texto
        searchInput.addEventListener('blur', function() {
            const typed = (searchInput.value || '').trim();

            // Si no hay id seleccionado, o el texto NO coincide exactamente con un usuario, limpiamos
            if (!hiddenInput.value || !inputMatchesAnyOption(typed)) {
                hiddenInput.value = '';
                searchInput.value = '';
                searchInput.classList.add('is-invalid');
            }
        });


        // Add event listener to capitalize the first letter of the milestone title
        const milestoneTitle = document.getElementById('milestone-title');
        if (milestoneTitle) {
            milestoneTitle.addEventListener('input', function() {
                let value = this.value;
                if (value.length > 0) {
                    this.value = value.charAt(0).toUpperCase() + value.slice(1);
                }
                // Quitar el error cuando el usuario escribe en el title
                this.classList.remove('is-invalid');
            });
        }

        // Quitar el error cuando el usuario selecciona una fecha
        const endDate = document.getElementById('end_date');
        if (endDate) {
            endDate.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        }

        // Quitar el error cuando el usuario selecciona una fase
        const phaseField = document.getElementById('phase');
        if (phaseField) {
            phaseField.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        }
    })();
</script>
{{-- // Script para el dropdown de "Asignado a" --}}
{{-- <script>
    // Variables para el apartado "Asignado a"
    var searchInputReq = document.getElementById('search-requested-by');
    var optionsListReq = document.getElementById('user-select-req-by');
    var optionsReq = optionsListReq.getElementsByClassName('option');
    var hiddenInputReq = document.getElementById('req_assing_To');

    // Muestra el menú al hacer clic en el input
    searchInputReq.addEventListener('click', function(event) {
        event.stopPropagation();
        optionsListReq.style.display = 'block';
    });

    // Filtra las opciones conforme se escribe
    searchInputReq.addEventListener('input', function() {
        const filter = searchInputReq.value.toLowerCase();
        let hasVisibleOption = false;
        for (let i = 0; i < optionsReq.length; i++) {
            const option = optionsReq[i];
            const text = option.innerText.toLowerCase();
            if (text.includes(filter)) {
                option.style.display = 'block';
                hasVisibleOption = true;
            } else {
                option.style.display = 'none';
            }
        }
        optionsListReq.style.display = hasVisibleOption ? 'block' : 'none';
    });

    // Asigna el valor seleccionado y oculta el menú
    for (let i = 0; i < optionsReq.length; i++) {
        optionsReq[i].addEventListener('click', function() {
            const selectedUserId = this.getAttribute('collected-data-id');
            searchInputReq.value = this.innerText;
            hiddenInputReq.value = selectedUserId;
            optionsListReq.style.display = 'none';
        });
    }

    // Cierra el menú si se hace clic fuera del contenedor
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#requestBy-req') && !event.target.closest('#search-requested-by')) {
            optionsListReq.style.display = 'none';
        }
    });
</script> --}}


@if (isset($projects))
    <script>
        const projects = @json($projects);
        const milestonePhaseOptions = @json($phases ?? \App\Models\MilestonePhases::PHASES);
        const currentWorkspaceSlug = '{{ $currentWorkspace->slug }}';
        const searchMoUrl = "{{ route('search-mo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
        const searchClipoUrl = "{{ route('search-clipo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
        const searchProjectsUrl = "{{ route('search-project-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
        const searchSalesManagerUrl = "{{ route('search-sales-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    </script>
    <script src="{{ asset('assets/js/create_project.js') }}?v={{ time() }}"></script>
    {{-- staging y produccion 
 <script src="{{ asset('assets/js/create_project.js') }}"></script> --}}
@endif

<script>
    (function() {
        function initMilestoneStageAddPhaseToggle() {
            const stageSelect = document.getElementById('stage');
            const newStageWrapper = document.getElementById('new-stage-name-wrapper');
            const newStageInput = document.getElementById('new_stage_name');

            if (!stageSelect || !newStageWrapper) {
                return;
            }

            const toggleNewStageInput = function() {
                const showInput = stageSelect.value === 'add_phase';
                newStageWrapper.style.display = showInput ? '' : 'none';
                if (!showInput && newStageInput) {
                    newStageInput.value = '';
                    newStageInput.classList.remove('is-invalid');
                }
            };

            stageSelect.removeEventListener('change', stageSelect._toggleNewStageHandler || function() {});
            stageSelect._toggleNewStageHandler = toggleNewStageInput;
            stageSelect.addEventListener('change', toggleNewStageInput);
            toggleNewStageInput();
        }

        window.initMilestoneStageAddPhaseToggle = initMilestoneStageAddPhaseToggle;
        document.addEventListener('DOMContentLoaded', initMilestoneStageAddPhaseToggle);
        initMilestoneStageAddPhaseToggle();
    })();
</script>

<!-- Código para el envío del formulario "Add New project" -->
<script>
    $(document).ready(function() {
        $('#projectForm').on('submit', function(event) {
            event.preventDefault();
            const data = {
                project_type: $('#project_type').val(),
                name: $('#projectname').val(),
                ref_mo: $('#searchMo').val(),
                clipo: $('#searchClipo').val(),
                isReload: false
            };
            const slug = "{{ $currentWorkspace->slug }}";
            const url = "{{ route('project.milestone.store', ['slug' => 'slug']) }}";
            const finalUrl = url.replace('slug', slug);
            $.ajax({
                url: finalUrl,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#projectname').val("");
                    $('#searchMo').val("");
                    $('#searchClipo').val("");
                    let msg = '{{ __('Project Created Successfully!') }}';
                    $('#toastMessage').text(msg);
                    const toast = new bootstrap.Toast(document.getElementById(
                        'successToast'), {
                        delay: 2000
                    });
                    toast.show();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    $('#toastMessage').text('An error occurred.');
                    const toast = new bootstrap.Toast(document.getElementById(
                        'successToast'), {
                        delay: 2000
                    });
                    toast.show();
                }
            });
        });
    });
</script>

<!-- Actualiza el action del formulario de milestone cuando cambia el project_id -->
<script>
    $(document).ready(function() {
        $('#project_id').on('change', function() {
            var selectedProjectId = $(this).val();
            var currentWorkspaceSlug = "{{ $currentWorkspace->slug }}";
            var actionUrl =
                `{{ route('projects.milestone.store', [$currentWorkspace->slug, 'PLACEHOLDER']) }}`;
            actionUrl = actionUrl.replace('PLACEHOLDER', selectedProjectId);
            $('#milestone-form').attr('action', actionUrl);
        });
    });
</script>
<script>
    function shouldShowPhase(typeId, typeName) {
        typeId = parseInt(typeId, 10);
        typeName = (typeName || '').trim().toLowerCase();

        return typeId === 3 ||
            typeName === 'i+d project' ||
            typeName === 'proyecto i+d' ||
            typeId === 5 ||
            typeName === 'i+d development' ||
            typeName === 'desarrollo i+d';
    }

    function togglePhaseWrapper(show) {
        const wrapper = document.getElementById('phase-wrapper');
        const select = document.getElementById('phase');

        if (!wrapper) return;

        wrapper.style.display = show ? '' : 'none';

        // opcional: si ocultas, limpias el valor
        if (!show && select) select.value = '';
    }

    // Caso: cuando cambie el tipo de proyecto (Create New Project)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'project_type') {
            const opt = e.target.options[e.target.selectedIndex];
            const typeId = e.target.value;
            const typeName = opt ? opt.getAttribute('data-type') : '';
            togglePhaseWrapper(shouldShowPhase(typeId, typeName));
        }
    });

    // Ejecutar al cargar por si ya hay algo seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        const projectType = document.getElementById('project_type');
        if (projectType && projectType.value) {
            const opt = projectType.options[projectType.selectedIndex];
            togglePhaseWrapper(shouldShowPhase(projectType.value, opt?.getAttribute('data-type')));
        }
    });
</script>


<!-- Funciones para manejar la carga y listado de archivos -->
<script>
    (function() {
        // Limpiar handlers anteriores si existen (para evitar duplicados al reabrir modal)
        if (window._milestoneHandlersCleanup) {
            window._milestoneHandlersCleanup();
        }

        const dropzoneMilestone = document.getElementById('dropzonewidgetMilestone');
        let fileInputMilestone = document.getElementById('file-uploadMilestone');
        const fileListMilestone = document.getElementById('file-list');
        const hiddenInputsMilestone = document.getElementById('hidden-file-inputs');
        var filesArrayMilestone = [];
        var rejectedFilesMilestone = [];

        // --- Extensiones y tipos MIME permitidos ---
        const allowedMilestone = [{
                ext: 'png',
                mime: 'image/png'
            },
            {
                ext: 'gif',
                mime: 'image/gif'
            },
            {
                ext: 'pdf',
                mime: 'application/pdf'
            },
            {
                ext: 'txt',
                mime: 'text/plain'
            },
            {
                ext: 'doc',
                mime: 'application/msword'
            },
            {
                ext: 'docx',
                mime: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            },
            {
                ext: 'zip',
                mime: 'application/zip'
            },
            {
                ext: 'rar',
                mime: 'application/vnd.rar'
            },
            {
                ext: 'rar',
                mime: 'application/x-rar-compressed'
            },
            {
                ext: 'dwg',
                mime: 'application/acad'
            },
            {
                ext: 'dwg',
                mime: 'application/autocad_dwg'
            },
            {
                ext: 'dxf',
                mime: 'application/dxf'
            }
        ];
        const allowedExtsMilestone = allowedMilestone.map(a => a.ext.toLowerCase());
        const allowedMimesMilestone = allowedMilestone.map(a => a.mime);

        // --- Drag & Drop visual feedback ---
        dropzoneMilestone.addEventListener('dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneMilestone.classList.add('dragover');
        });

        dropzoneMilestone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneMilestone.classList.add('dragover');
        });

        dropzoneMilestone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.target === dropzoneMilestone) {
                dropzoneMilestone.classList.remove('dragover');
            }
        });

        dropzoneMilestone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneMilestone.classList.remove('dragover');

            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                const droppedFiles = Array.from(e.dataTransfer.files);
                handleFilesMilestone(droppedFiles);
                // Los archivos se subirán al hacer submit del formulario, no aquí
                dropzoneMilestone.focus();
            }
        });

        // Prevent default drag behavior on document - usar handlers con nombre
        function _milestoneDocDragover(e) {
            e.preventDefault();
        }

        function _milestoneDocDrop(e) {
            e.preventDefault();
        }
        document.addEventListener('dragover', _milestoneDocDragover);
        document.addEventListener('drop', _milestoneDocDrop);

        // --- Selección manual desde input file ---
        // Click en dropzone abre el selector de archivos
        dropzoneMilestone.addEventListener('click', function(e) {
            // Evitar que se dispare si el click fue en un botón de eliminar dentro del dropzone
            if (e.target.closest('.buttonFiles') || e.target.closest('a')) {
                return;
            }
            fileInputMilestone.value = '';
            fileInputMilestone.click();
        });

        fileInputMilestone.addEventListener('change', function() {
            if (fileInputMilestone.files && fileInputMilestone.files.length) {
                const selectedFiles = Array.from(fileInputMilestone.files);
                // Procesar y listar localmente (los archivos se subirán al hacer submit)
                handleFilesMilestone(selectedFiles);
            }
        });

        // --- Permitimos que la dropzone reciba foco y capture paste ---
        dropzoneMilestone.setAttribute('tabindex', '0'); // hace que se pueda enfocar

        // --- Capturar paste a nivel de document, pero sólo procesar si foco está dentro de dropzone ---
        function _milestonePasteHandler(e) {
            const focused = document.activeElement;
            if (focused !== dropzoneMilestone && !dropzoneMilestone.contains(focused)) {
                return;
            }
            e.preventDefault();
            if (!e.clipboardData || !e.clipboardData.items) {
                return;
            }
            const items = Array.from(e.clipboardData.items);
            const conversionPromises = items.map(item => {
                if (item.kind !== 'file') {
                    return Promise.resolve(null);
                }
                const file = item.getAsFile();
                if (!file) {
                    return Promise.resolve(null);
                }
                const ext = file.name.split('.').pop().toLowerCase().trim();
                const mime = file.type;

                // Si es PNG o GIF → convertir a JPG
                if (
                    mime === 'image/png' || mime === 'image/gif' ||
                    ext === 'png' || ext === 'gif'
                ) {
                    return new Promise(resolve => {
                        convertImageToJPGMilestone(file, function(jpgFile) {
                            resolve(jpgFile);
                        });
                    });
                }

                // Si su extensión O su MIME están permitidos, devolvemos el File
                if (
                    allowedExtsMilestone.includes(ext) ||
                    allowedMimesMilestone.includes(mime)
                ) {
                    return Promise.resolve(file);
                }

                // De lo contrario, no lo tomamos
                return Promise.resolve(null);
            });

            Promise.all(conversionPromises).then(results => {
                const archivosValidos = results.filter(f => f instanceof File);
                if (archivosValidos.length > 0) {
                    handleFilesMilestone(archivosValidos);
                    // Los archivos se subirán al hacer submit del formulario
                } else {
                    alert('El portapapeles no contiene un archivo permitido');
                }
                dropzoneMilestone.focus();
            });
        }
        document.addEventListener('paste', _milestonePasteHandler);

        // --- Función ÚNICA para procesar archivos subidos (pegados, arrastrados o seleccionados) ---
        function handleFilesMilestone(files) {
            const MAX_FILE_SIZE = 52428800; // 50MB en bytes
            const rejectedInThisBatch = [];

            files.forEach(file => {
                // Validar tamaño del archivo
                if (file.size > MAX_FILE_SIZE) {
                    rejectedInThisBatch.push(file.name);
                    rejectedFilesMilestone.push({
                        name: file.name,
                        reason: 'File too big'
                    });
                    return;
                }

                const ext = file.name.split('.').pop().toLowerCase().trim();
                const mime = file.type;

                // Reemplazar espacios con guiones bajos en el nombre (de lo contrario las descargas no funcionaran correctamente)
                const processedFile = new File(
                    [file],
                    file.name.replace(/\s+/g, '_'), {
                        type: file.type
                    }
                );

                // 1) Si el archivo ya es un JPEG (resultado de la conversión), lo añadimos directamente
                if (mime === 'image/jpeg') {
                    addFileToMilestoneArray(processedFile);
                    return;
                }

                // 2) Si es PNG o GIF (arrastrado, pegado o seleccionado manualmente), convertimos a JPG
                if (
                    mime === 'image/png' || mime === 'image/gif' ||
                    ext === 'png' || ext === 'gif'
                ) {
                    convertImageToJPGMilestone(processedFile, function(jpgFile) {
                        addFileToMilestoneArray(jpgFile);
                    });
                    return;
                }

                // 3) Si su extensión o su MIME están permitidos, lo añadimos tal cual
                if (
                    allowedExtsMilestone.includes(ext) ||
                    allowedMimesMilestone.includes(mime)
                ) {
                    addFileToMilestoneArray(processedFile);
                    return;
                }

                // 4) Cualquier otro, se ignora (console.warn para depuración)
                console.warn(
                    `Archivo no permitido: ${processedFile.name} (${mime || 'sin MIME detectado'})`);
            });

            // Mostrar alerta si hay archivos rechazados
            if (rejectedInThisBatch.length > 0) {
                const rejectedList = rejectedInThisBatch.join('\n- ');
                alert('Los siguientes archivos fueron rechazados por exceder el límite de 50MB:\n- ' +
                    rejectedList);
            }

            updateFileListMilestone();
        }

        /**
         * Genera un nombre único para el archivo si ya existe uno con el mismo nombre.
         * Ejemplo: archivo.pdf -> archivo (2).pdf -> archivo (3).pdf
         */
        function generateUniqueFileNameMilestone(fileName) {
            const existingNames = filesArrayMilestone.map(f => f.name);

            if (!existingNames.includes(fileName)) {
                return fileName;
            }

            // Separar nombre base y extensión
            const lastDotIndex = fileName.lastIndexOf('.');
            let baseName, extension;

            if (lastDotIndex > 0) {
                baseName = fileName.substring(0, lastDotIndex);
                extension = fileName.substring(lastDotIndex);
            } else {
                baseName = fileName;
                extension = '';
            }

            // Verificar si ya tiene un sufijo numérico como " (2)"
            const suffixMatch = baseName.match(/^(.+)\s\((\d+)\)$/);
            let originalBaseName = baseName;
            let startCounter = 2;

            if (suffixMatch) {
                originalBaseName = suffixMatch[1];
                startCounter = parseInt(suffixMatch[2]) + 1;
            }

            // Buscar el siguiente número disponible
            let counter = startCounter;
            let newFileName = `${originalBaseName} (${counter})${extension}`;

            while (existingNames.includes(newFileName)) {
                counter++;
                newFileName = `${originalBaseName} (${counter})${extension}`;
            }

            return newFileName;
        }

        function addFileToMilestoneArray(file) {
            // Generar nombre único si el nombre ya existe (permite archivos con mismo nombre)
            const uniqueName = generateUniqueFileNameMilestone(file.name);

            // Si el nombre cambió, crear un nuevo File con el nombre único
            let fileToAdd = file;
            if (uniqueName !== file.name) {
                fileToAdd = new File([file], uniqueName, {
                    type: file.type,
                    lastModified: file.lastModified
                });
                console.info(`Archivo renombrado: ${file.name} -> ${uniqueName}`);
            }

            filesArrayMilestone.push(fileToAdd);
            updateFileListMilestone();
        }

        // --- Conversión de imagen PNG/GIF a JPG usando canvas ---
        function convertImageToJPGMilestone(blobOrFile, callback) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    canvas.toBlob(function(jpgBlob) {
                        const nuevoNombre = (blobOrFile.name || 'clipboard').replace(
                            /\.(png|gif)$/i,
                            '.jpg');
                        const jpgFile = new File([jpgBlob], nuevoNombre, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });
                        callback(jpgFile);
                    }, 'image/jpeg', 0.92);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(blobOrFile);
        }

        function updateFileListMilestone() {
            const assetBasePath = '{{ asset('assets/iconFilesTypes') }}/';
            fileListMilestone.innerHTML = '';
            hiddenInputsMilestone.innerHTML = '';

            // Mostrar archivos válidos
            filesArrayMilestone.forEach((file, index) => {
                const fileContainer = document.createElement('div');
                fileContainer.classList.add('file');

                const icon = document.createElement('img');
                icon.src = getIconPathMilestone(file.name, assetBasePath);
                icon.alt = `${getExtensionMilestone(file.name)} icon`;
                icon.style.width = '20px';
                icon.style.height = '25px';
                fileContainer.appendChild(icon);

                const fileNameContainer = document.createElement('div');
                fileNameContainer.classList.add('file-name');
                fileNameContainer.textContent = file.name;
                fileNameContainer.style.maxWidth = "70%";
                fileContainer.appendChild(fileNameContainer);

                const removeButton = document.createElement('a');
                removeButton.classList.add('buttonFiles');
                removeButton.innerHTML =
                    '<i class="fa-solid fa-trash deleteFileButton" style="color:white"></i>';
                removeButton.addEventListener('click', function() {
                    filesArrayMilestone.splice(index, 1);
                    updateFileListMilestone();
                });

                fileContainer.appendChild(removeButton);
                fileListMilestone.appendChild(fileContainer);

                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'files[]';
                input.style.display = 'none';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                hiddenInputsMilestone.appendChild(input);
            });

            // Mostrar archivos rechazados (tachados)
            rejectedFilesMilestone.forEach((rejectedFile, index) => {
                const fileContainer = document.createElement('div');
                fileContainer.classList.add('file');
                fileContainer.style.opacity = '0.5';
                fileContainer.style.textDecoration = 'line-through';
                fileContainer.title = 'File too big: Exceeds 50MB limit';
                fileContainer.style.cursor = 'not-allowed';

                const icon = document.createElement('img');
                icon.src = getIconPathMilestone(rejectedFile.name, assetBasePath);
                icon.alt = `${getExtensionMilestone(rejectedFile.name)} icon`;
                icon.style.width = '20px';
                icon.style.height = '25px';
                icon.style.opacity = '0.5';
                fileContainer.appendChild(icon);

                const fileNameContainer = document.createElement('div');
                fileNameContainer.classList.add('file-name');
                fileNameContainer.textContent = rejectedFile.name;
                fileNameContainer.style.maxWidth = "70%";
                fileContainer.appendChild(fileNameContainer);

                const removeButton = document.createElement('a');
                removeButton.classList.add('buttonFiles');
                removeButton.innerHTML =
                    '<i class="fa-solid fa-trash deleteFileButton" style="color:white"></i>';
                removeButton.addEventListener('click', function() {
                    rejectedFilesMilestone.splice(index, 1);
                    updateFileListMilestone();
                });

                fileContainer.appendChild(removeButton);
                fileListMilestone.appendChild(fileContainer);
            });
        }

        function getIconPathMilestone(filename, assetBasePath) {
            const extension = getExtensionMilestone(filename);
            const iconPath = `${assetBasePath}${extension}.png`;
            const defaultIcon = `${assetBasePath}default.png`;
            const supportedExtensions = [
                'pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt',
                'dwg', 'dxf', 'img', 'docx', 'zip', 'rar', 'gif', 'jpeg'
            ];
            return supportedExtensions.includes(extension) ? iconPath : defaultIcon;
        }

        function getExtensionMilestone(filename) {
            return filename.split('.').pop().toLowerCase().trim();
        }

        // Manejar click del botón de submit directamente
        let isSubmitting = false;

        function _milestoneSubmitClickHandler(e) {
            const submitButton = e.target.closest('#submitMilestoneBtn');
            if (!submitButton) return;

            // Si ya está guardando, bloquear completamente
            if (isSubmitting) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }

            const milestoneForm = document.getElementById('milestone-form');
            if (!milestoneForm) {
                console.error('Milestone form not found');
                return;
            }

            // ✅ Validación personalizada de campos requeridos
            // Validar Title
            const titleField = document.getElementById('milestone-title');
            const titleValue = (titleField.value || '').trim();
            let validationErrors = [];

            if (!titleValue) {
                titleField.classList.add('is-invalid');
                validationErrors.push('Debes introducir un título');
            } else {
                titleField.classList.remove('is-invalid');
            }

            // Validar End Date
            const endDateField = document.getElementById('end_date');
            const endDateValue = (endDateField.value || '').trim();

            if (!endDateValue) {
                endDateField.classList.add('is-invalid');
                validationErrors.push('Debes seleccionar una fecha de entrega');
            } else {
                endDateField.classList.remove('is-invalid');
            }

            // Validar Requested by (assing_to)
            const hiddenInput = document.getElementById('assing_To');
            const searchInput = document.getElementById('search');

            const requestedById = hiddenInput ? hiddenInput.value : '';
            const typedName = searchInput ? (searchInput.value || '').trim() : '';

            // Helper para validar si el input coincide con alguna opción (copia local para evitar ReferenceError)
            function _localInputMatchesAnyOption(value) {
                const optionsList = document.getElementById('user-select');
                if (!optionsList) return false;
                const options = optionsList.getElementsByClassName('option');
                const v = (value || '').trim().toLowerCase();
                if (!v) return false;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].innerText.trim().toLowerCase() === v) return true;
                }
                return false;
            }

            if (!requestedById || !_localInputMatchesAnyOption(typedName)) {
                if (searchInput) searchInput.classList.add('is-invalid');
                validationErrors.push('Debes seleccionar un usuario existente en "Requested by"');
            } else {
                if (searchInput) searchInput.classList.remove('is-invalid');
            }

            // Validar Phase (solo si la sección está visible)
            const phaseWrapper = document.getElementById('phase-wrapper');
            if (phaseWrapper && phaseWrapper.style.display !== 'none') {
                const phaseField = document.getElementById('phase');
                const phaseValue = (phaseField.value || '').trim();

                if (!phaseValue) {
                    phaseField.classList.add('is-invalid');
                    validationErrors.push('Debes seleccionar una fase');
                } else {
                    phaseField.classList.remove('is-invalid');
                }
            }

            // Validar Stage / nueva phase (solo si la sección está visible)
            const stageWrapper = document.getElementById('stage-wrapper');
            if (stageWrapper && stageWrapper.style.display !== 'none') {
                const stageField = document.getElementById('stage');
                const newStageNameField = document.getElementById('new_stage_name');
                const stageValue = (stageField?.value || '').trim();

                if (stageValue === 'add_phase') {
                    const newStageName = (newStageNameField?.value || '').trim();
                    if (!newStageName) {
                        newStageNameField?.classList.add('is-invalid');
                        validationErrors.push('{{ __('Please enter a phase name.') }}');
                    } else {
                        newStageNameField?.classList.remove('is-invalid');
                    }
                } else {
                    newStageNameField?.classList.remove('is-invalid');
                }
            }

            // Si hay errores, mostrar toast y retornar
            if (validationErrors.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                showToast(validationErrors.join('. '), 'danger');
                isSubmitting = false;
                return;
            }

            // Marcar como en proceso
            e.preventDefault();
            e.stopPropagation();
            isSubmitting = true;

            // Deshabilitar el botón visualmente
            submitButton.disabled = true;
            submitButton.textContent = '{{ __('Saving...') }}';
            submitButton.style.opacity = '0.6';
            submitButton.style.cursor = 'not-allowed';
            submitButton.style.pointerEvents = 'none';

            // Crear FormData del formulario
            const formData = new FormData(milestoneForm);
            const actionUrl = milestoneForm.getAttribute('action');

            console.log('Enviando formulario a:', actionUrl);

            fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta del servidor:', data);

                    // Limpiar arrays de archivos
                    filesArrayMilestone = [];
                    rejectedFilesMilestone = [];

                    if (data.success) {
                        let message = '';
                        if (data.uploaded_count > 0 && data.failed_count > 0) {
                            message = data.uploaded_count + ' archivos subidos, ' + data.failed_count +
                                ' rechazados';
                        } else if (data.uploaded_count > 0) {
                            message = data.uploaded_count + ' archivos subidos exitosamente';
                        } else if (data.failed_count > 0) {
                            message = 'Todos los archivos fueron rechazados';
                        } else {
                            message = 'Encargo creado correctamente';
                        }

                        showToast(message, 'success');

                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.querySelector('.modal'));
                            if (modal) {
                                modal.hide();
                            }
                            window.location.reload();
                        }, 1500);

                    } else {
                        showToast(data.error || 'Error al guardar cambios', 'danger');

                        // Re-habilitar el botón en caso de error
                        submitButton.disabled = false;
                        submitButton.textContent = '{{ __('Save Changes') }}';
                        submitButton.style.opacity = '1';
                        submitButton.style.cursor = 'pointer';
                        submitButton.style.pointerEvents = 'auto';
                        isSubmitting = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error al enviar formulario', 'danger');

                    // Re-habilitar el botón en caso de error
                    submitButton.disabled = false;
                    submitButton.textContent = '{{ __('Save Changes') }}';
                    submitButton.style.opacity = '1';
                    submitButton.style.cursor = 'pointer';
                    submitButton.style.pointerEvents = 'auto';
                    isSubmitting = false;
                });
        }
        document.addEventListener('click', _milestoneSubmitClickHandler, false);

        // Registrar función de limpieza para cuando se cierre/reabra el modal
        window._milestoneHandlersCleanup = function() {
            document.removeEventListener('dragover', _milestoneDocDragover);
            document.removeEventListener('drop', _milestoneDocDrop);
            document.removeEventListener('paste', _milestonePasteHandler);
            document.removeEventListener('click', _milestoneSubmitClickHandler, false);
        };

        // Función para mostrar toast
        function showToast(message, type = 'info') {
            const toastHTML = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : 'info'}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const toastElement = document.createElement('div');
            toastElement.innerHTML = toastHTML;
            toastContainer.appendChild(toastElement.firstElementChild);

            const toast = new bootstrap.Toast(toastContainer.querySelector('.toast:last-child'));
            toast.show();
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }
    })();
</script>

<!-- NUEVO: Función para notificación antes del submit del formulario de milestone -->
<script>
    async function displayNotification(milestoneId) {
        console.log("Enviando notificación con milestone ID:", milestoneId);

        let milestoneTitle = document.getElementById('milestone-title').value;
        let milestoneParent;

        try {
            milestoneParent = document.getElementById('searchProject').value;
        } catch {
            milestoneParent = document.getElementById('projectIdDisabled').value;
        }

        let msg = milestoneTitle + ' en ' + milestoneParent;
        let ntipe = 2;

        try {
            const response = await fetch("{{ route('notifications.add') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    workspace_id: {{ $currentWorkspace->id }},
                    msg: msg,
                    ntipe: ntipe,
                    milestoneAssignedTo: -2,
                    milestone_id: milestoneId // ✅ AQUÍ SE ENVÍA A LARAVEL
                })
            });

            const data = await response.json();
            console.log("Respuesta de notificación:", data);

        } catch (error) {
            console.error("Error al enviar la notificación:", error);
        }
    }
</script>
