<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <style>
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
                                            <label class="text-muted iftheordoesnotexist">
                                                <i class="bi bi-info-circle me-2"
                                                    style="color: #FFD43B;"></i>{{ __('If the project does not exist, create a new project.') }}
                                            </label>
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
                        <div class="col-md-6">
                            @if (isset($project_id) && $project_id == -1)
                                <div class="form-group">
                                    <label class="col-form-label">{{ __('MO') }}</label>
                                    <input type="text" class="form-control form-control-light" id="milestone_mo"
                                        placeholder="{{ __('MO') }}" name="ref_mo" required readonly>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="col-form-label">{{ __('MO') }}</label>
                                    <input type="text" class="form-control form-control-light"
                                        placeholder="{{ $project->ref_mo }}" disabled>
                                    <input type="text" name="ref_mo" value="{{ $project->ref_mo }}"
                                        style="display: none;">
                                </div>
                            @endif
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
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ __('Search') }}" value="{{ Auth::user()->name }}"
                                autocomplete="off" required>

                            <div id="user-select" aria-label="Default select example" class="dropdown-menu"
                                style="width: 45% !important;">
                                @foreach ($users as $user)
                                    <div class="option list-group-item list-group-item-action stylelist ps-3"
                                        data-id="{{ $user->id }}" style="padding: 8px; cursor: pointer;">
                                        {{ $user->name }}
                                    </div>
                                @endforeach
                                <input type="hidden" name="assing_to" id="assing_To"
                                    value="{{ Auth::user()->id }}">
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
                                <label for="file-uploadMilestone"
                                    class="form-label"><strong>{{ __('Upload files') }}</strong></label>
                                <div>
                                    <div class="col-md-12 dropzone browse-file" id="dropzonewidgetMilestone">
                                        <div class="dz-message" data-dz-message>
                                            <input type="file" id="file-uploadMilestone" style="display:none"
                                                multiple />
                                            <span> {{ __('Drop files here to upload') }}</span>
                                            <p>
                                                {{ __('You can Also hold click + Control + V to paste the content of the clipboard') }}
                                            </p>
                                            <p class="text-muted" style="font-size:15px; margin:5px;">200MB</p>
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
<input type="submit" id="submitMilestoneBtn" value="{{ __('Save Changes') }}" class="btn btn-primary">
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
    let isSubmitting = false; // 🔒 bandera para prevenir múltiples envíos

    document.getElementById('milestone-form').addEventListener('submit', async function(event) {
        event.preventDefault();

        if (isSubmitting) return; // ⛔ si ya se está enviando, no hacer nada
        isSubmitting = true;

        const submitBtn = document.getElementById('submitMilestoneBtn');
        submitBtn.disabled = true;
        submitBtn.value = 'Guardando...';

        try {
            await displayNotification(); // 👈 Notificación previa (si es necesaria)
            this.submit(); // ✅ envío real solo una vez
        } catch (error) {
            console.error('Error al enviar el formulario:', error);
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.value = '{{ __("Save Changes") }}';
        }
    });
</script>

<script>
    
    // Definir valores por defecto del usuario logueado
    var defaultUserId = '{{ Auth::user()->id }}';
    var defaultUserName = '{{ Auth::user()->name }}';

    var searchInput = document.getElementById('search');
    var optionsList = document.getElementById('user-select');
    var options = optionsList.getElementsByClassName('option');
    var hiddenInput = document.getElementById('assing_To');

    // Establecer por defecto el nombre y el ID del usuario logueado
    hiddenInput.value = defaultUserId;
    searchInput.value = defaultUserName;

    searchInput.addEventListener('click', function(event) {
        event.stopPropagation();
        optionsList.style.display = 'block';
    });

    searchInput.addEventListener('input', function() {
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
            optionsList.style.display = 'none';
        });
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('#requestBy') && !event.target.closest('#search')) {
            optionsList.style.display = 'none';
        }
    });

    $(document).ready(function() {
        $('#toggleFormSwitch').change(function() {
            if ($(this).is(':checked')) {
                $('#additionalForm').collapse('show');
            } else {
                $('#additionalForm').collapse('hide');
            }
        });
    });

    // Add event listener to capitalize the first letter of the milestone title
    document.getElementById('milestone-title').addEventListener('input', function() {
        let value = this.value;
        if (value.length > 0) {
            this.value = value.charAt(0).toUpperCase() + value.slice(1);
        }
    });
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
        const currentWorkspaceSlug = '{{ $currentWorkspace->slug }}';
        const searchMoUrl = "{{ route('search-mo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
        const searchClipoUrl = "{{ route('search-clipo-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
        const searchProjectsUrl = "{{ route('search-project-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
        const searchSalesManagerUrl = "{{ route('search-sales-json', '__slug') }}".replace('__slug', currentWorkspaceSlug);
    </script>
    <script src="{{ asset('assets/js/create_project.js') }}"></script>
@endif

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


<!-- Funciones para manejar la carga y listado de archivos -->
<!-- Funciones para manejar la carga y listado de archivos -->
<script>
    const dropzoneMilestone = document.getElementById('dropzonewidgetMilestone');
    let fileInputMilestone = document.getElementById('file-uploadMilestone');
    const fileListMilestone = document.getElementById('file-list');
    const hiddenInputsMilestone = document.getElementById('hidden-file-inputs');
    var filesArrayMilestone = [];

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
            uploadFilesMilestone(droppedFiles);
            dropzoneMilestone.focus();
        }
    });

    // Prevent default drag behavior on document
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    document.addEventListener('drop', function(e) {
        e.preventDefault();
    });

    // --- Selección manual desde input file ---
    dropzoneMilestone.addEventListener('dblclick', function() {
        fileInputMilestone.value = '';
        fileInputMilestone.click();
    });
    fileInputMilestone.addEventListener('change', function() {
        if (fileInputMilestone.files && fileInputMilestone.files.length) {
            const selectedFiles = Array.from(fileInputMilestone.files);
            // 1) Procesar y listar localmente
            handleFilesMilestone(selectedFiles);
            // 2) Subir automáticamente los archivos al servidor
            uploadFilesMilestone(selectedFiles);
            dropzoneMilestone.focus();
        }
    });

    // --- Permitimos que la dropzone reciba foco y capture paste ---
    dropzoneMilestone.setAttribute('tabindex', '0'); // hace que se pueda enfocar
    dropzoneMilestone.addEventListener('click', () => {
        dropzoneMilestone.focus();
    });

    // --- Capturar paste a nivel de document, pero sólo procesar si foco está dentro de dropzone ---
    document.addEventListener('paste', function(e) {
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
                uploadFilesMilestone(archivosValidos);
            } else {
                alert('El portapapeles no contiene un archivo permitido');
            }
            dropzoneMilestone.focus();
        });
    });

    // --- Función ÚNICA para procesar archivos subidos (pegados, arrastrados o seleccionados) ---
    function handleFilesMilestone(files) {
        files.forEach(file => {
            const ext = file.name.split('.').pop().toLowerCase().trim();
            const mime = file.type;

            // 1) Si el archivo ya es un JPEG (resultado de la conversión), lo añadimos directamente
            if (mime === 'image/jpeg') {
                addFileToMilestoneArray(file);
                return;
            }

            // 2) Si es PNG o GIF (arrastrado, pegado o seleccionado manualmente), convertimos a JPG
            if (
                mime === 'image/png' || mime === 'image/gif' ||
                ext === 'png' || ext === 'gif'
            ) {
                convertImageToJPGMilestone(file, function(jpgFile) {
                    addFileToMilestoneArray(jpgFile);
                });
                return;
            }

            // 3) Si su extensión o su MIME están permitidos, lo añadimos tal cual
            if (
                allowedExtsMilestone.includes(ext) ||
                allowedMimesMilestone.includes(mime)
            ) {
                addFileToMilestoneArray(file);
                return;
            }

            // 4) Cualquier otro, se ignora (console.warn para depuración)
            console.warn(`Archivo no permitido: ${file.name} (${mime || 'sin MIME detectado'})`);
        });
    }

    function addFileToMilestoneArray(file) {
        if (!filesArrayMilestone.some(f => f.name === file.name && f.size === file.size)) {
            filesArrayMilestone.push(file);
            updateFileListMilestone();
        } else {
            console.warn(`Archivo duplicado ignorado: ${file.name}`);
        }
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
                    const nuevoNombre = (blobOrFile.name || 'clipboard').replace(/\.(png|gif)$/i,
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
            removeButton.innerHTML = '<i class="fa-solid fa-trash deleteFileButton" style="color:white"></i>';
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

    // --- NUEVA FUNCIÓN: subir archivos vía AJAX al servidor ---
    function uploadFilesMilestone(files) {
        if (!files.length) {
            return;
        }

        // Obtenemos el token CSRF que pusimos en <meta name="csrf-token" ...>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Creamos un FormData y añadimos cada archivo bajo "files[]"
        const formData = new FormData();
        files.forEach(file => {
            formData.append('files[]', file);
        });

        // También pasamos otros campos que el servidor podría necesitar.
        // Por ejemplo, si el formulario #milestone-form tiene campos adicionales, los incluimos:
        const milestoneForm = document.getElementById('milestone-form');
        if (milestoneForm) {
            // Ej: título, asignado a, end_date, etc.
            const extraInputs = milestoneForm.querySelectorAll('input, select, textarea');
            extraInputs.forEach(input => {
                if (!['files[]', '_token'].includes(input.name) && input.value) {
                    formData.append(input.name, input.value);
                }
            });
        }

        // Hacemos el POST a la URL de acción del formulario
        const actionUrl = (milestoneForm && milestoneForm.getAttribute('action')) ?
            milestoneForm.getAttribute('action') :
            window.location.href; // fallback

        fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    // Si algo falla en el servidor, podemos leer el JSON o texto de error
                    return response.json().then(err => {
                        console.error('Error en subida de archivos:', err);
                        // alert('Error al subir archivos: ' + (err.message || response.statusText));
                        throw new Error('Upload failed');
                    });
                }
                return response.json();
            })
            .then(data => {
                // Aquí puedes manejar la respuesta exitosa (p. ej. mostrar mensaje, refrescar lista en servidor, etc.)
                console.log('Archivos subidos correctamente:', data);
                // Si quieres, puedes vaciar el arreglo local o actualizarlo según la respuesta:
                // filesArrayMilestone = [];
                // updateFileListMilestone();
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    }
</script>

<!-- NUEVO: Función para notificación antes del submit del formulario de milestone -->
<script>
    async function displayNotification() {
        console.log('Generando notificacion de encargo creado');
        let milestoneTitle = document.getElementById('milestone-title').value;
        let milestoneParent;
        milestoneAssignedTo = -2;
        // let milestoneAssignedTo = document.getElementById('req_assing_To').value
        // if (milestoneAssignedTo == '') {
        //     milestoneAssignedTo = -2;
        // }
        try {
            milestoneParent = document.getElementById('searchProject').value;
            console.log("Milestone parent:", milestoneParent);
        } catch (error) {
            milestoneParent = document.getElementById('projectIdDisabled').value;
            console.log("Milestone parent pero en el catch:", milestoneParent);
        }



        let msg = milestoneTitle + ' en ' + milestoneParent;
        let ntipe = 2;

        if (!msg) return;
        try {
            const response = await fetch("{{ route('notifications.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    workspace_id: {{ $currentWorkspace->id }},
                    msg: msg,
                    ntipe: ntipe,
                    milestoneAssignedTo: milestoneAssignedTo
                })
            });
            const data = await response.json();
            if (data.success) {
                let notificationList = document.querySelector('.limited');
                let newNotification = document.createElement('div');
                newNotification.classList.add('notificationSTL');
                newNotification.innerHTML = `
                    <span class="textRepo">${data.data.msg}</span>
                    <span class="textRepo">${data.data.type}</span>
                    <button type="button" class="btn-close repoIcon" aria-label="Close"></button>
                `;
                notificationList.prepend(newNotification);
            }
        } catch (error) {
            console.error("Error al agregar notificación:", error);
        }
    }


</script>
