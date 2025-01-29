<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
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
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="project-tab" data-bs-toggle="tab" href="#projectForm" role="tab"
                    aria-controls="project" aria-selected="false">
                    <i class="fa-solid fa-diagram-project me-2"></i> {{ __('Create New Project') }}
                </a>
            </li>
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
                                        <label class="col-form-label">{{ __('Search project') }}</label>
                                        <input type="text" class="form-control" id="searchProject"
                                            placeholder="{{ __('Name or reference M.O') }}" autocomplete="off">
                                        <input id="projectId" name="project_id" style="display: none">
                                        <div class="list-group" id="projects_list"></div>
                                    </div>
                                @else
                                    <label class="col-form-label">{{ __('Project') }}</label>
                                    <input class="form-control" type="text" value="{{ $project->name }}" disabled>
                                    <input class="form-control" type="text" id="project_id" name="project_id"
                                        value="{{ $project->id }}" autocomplete="off" style="display: none;">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">{{ __('MO') }}</label>
                                <input type="text" class="form-control form-control-light" id="milestone_mo"
                                    placeholder="{{ __('MO') }}" name="ref_mo" required readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="text-muted">
                                <i class="bi bi-info-circle me-2"
                                    style="color: #FFD43B;"></i>{{ __('If the project with MO reference exists in the database, it will be created automatically. Otherwise, it will be requested later.') }}
                            </p>
                        </div>
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
                                placeholder="{{ __('Search') }}" value="" autocomplete="off">

                            <div id="user-select" aria-label="Default select example" class="dropdown-menu"
                                style="width: 45% !important;">
                                @foreach ($users as $user)
                                    <div class="option list-group-item list-group-item-action stylelist ps-3"
                                        data-id="{{ $user->id }}" style="padding: 8px; cursor: pointer;">
                                        {{ $user->name }}
                                    </div>
                                @endforeach
                                <input type="text" name="assing_to" id="assing_To" style="display: none;">
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3" id="visado" style="display: none">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleFormSwitch">
                        <label class="form-check-label"
                            for="toggleFormSwitch">{{ __('Only in case it is necessary to to carry out a project with a visa.') }}</label>
                    </div>
                    <div id="additionalForm" class="collapse mt-3">
                        <div class="card card-body">
                            <div class="mb-3">
                                <label for="input1"
                                    class="form-label">{{ __('Name of the company that will install the system') }}:</label>
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
                                <b>{{ __('Note: In order to carry out the project it is necessary to send the quotation of the formwork and falsework system, and the complete assembly drawings and geometrical definition of the structure.') }}
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
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <!-- Sección de Descripción (Izquierda) -->
                            <div class="col-md-6">
                                <label for="description-text"
                                    class="form-label"><strong>{{ __('Description') }}</strong></label>
                                <textarea class="form-control mt-2" id="description-text" name="description" rows="5"
                                    placeholder="{{ __('Enter description...') }}"></textarea>
                            </div>

                            <!-- Sección de Archivos Adjuntos (Derecha) -->
                            <div class="col-md-6">
                                <label for="file-upload"
                                    class="form-label"><strong>{{ __('Upload files') }}</strong></label>
                                <div class="form-group browser-file mt-2">
                                    <input type="file" id="file-upload" multiple style="display: none;" />
                                    <div id="file-list" class="border p-3 rounded bg-light"
                                        style="min-height: 120px;">
                                        <!-- Aquí se listarán los archivos adjuntos -->
                                    </div>
                                </div>
                                <div class="text-start mt-2">
                                    <button type="button" id="file-select-button" class="btn btn-primary">
                                        <i class="fa-solid fa-paperclip"></i> {{ __('Attach files') }}
                                    </button>
                                </div>
                                <div id="hidden-file-inputs" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
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
                                    placeholder="{{ __('Reference') }}" autocomplete="off">
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
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
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
<script>
    const searchInput = document.getElementById('search');
    const optionsList = document.getElementById('user-select');
    const options = optionsList.getElementsByClassName('option');
    const hiddenInput = document.getElementById('assing_To');

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
</script>
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
                    // Limpiar campos
                    $('#projectname').val("");
                    $('#searchMo').val("");
                    $('#searchClipo').val("");

                    msg = '{{ __('Project Created Successfully!') }}'
                    // Mostrar el mensaje en el *toast*
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

    const assetBasePath = '{{ asset('assets/iconFilesTypes') }}/';
    let filesArray = [];

    document.getElementById('file-select-button').addEventListener('click', function() {
        document.getElementById('file-upload').click();
    });

    document.getElementById('file-upload').addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);

        newFiles.forEach((file) => {
            if (!filesArray.some((f) => f.name === file.name && f.size === file.size)) {
                filesArray.push(file);
            } else {
                console.warn(`Archivo duplicado ignorado: ${file.name}`);
            }
        });

        updateFileList();
    });

    function updateFileList() {
        const fileListElement = document.getElementById('file-list');
        const hiddenInputsContainer = document.getElementById('hidden-file-inputs');

        fileListElement.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        filesArray.forEach((file, index) => {
            const fileContainer = document.createElement('div');
            fileContainer.classList.add('file');

            // Ícono de la extensión
            const icon = document.createElement('img');
            icon.src = getIconPath(file.name);
            icon.alt = `${getExtension(file.name)} icon`;
            icon.style.width = '20px';
            icon.style.height = '25px';
            fileContainer.appendChild(icon);

            const fileNameContainer = document.createElement('div');
            fileNameContainer.classList.add('file-name');
            fileNameContainer.textContent = file.name;
            fileContainer.appendChild(fileNameContainer);

            const fileDetailsSmall = document.createElement('small');
            fileDetailsSmall.classList.add('text-muted', 'ms-1');
            fileDetailsSmall.innerHTML = ' · ' + `(${formatFileSize(file.size)})`;

            const removeButton = document.createElement('button');
            removeButton.classList.add('btn', 'btn-link', 'text-danger', 'p-0');
            removeButton.innerHTML = '<i class="ps-2 fa-solid fa-xmark"></i>';
            removeButton.addEventListener('click', function() {
                filesArray.splice(index, 1);
                updateFileList();
            });
            fileNameContainer.appendChild(fileDetailsSmall);
            fileContainer.appendChild(removeButton);

            // Añadir a la lista visual
            fileListElement.appendChild(fileContainer);

            // Crear un input oculto para cada archivo
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'files[]';
            input.style.display = 'none';

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            // Agregar el input al contenedor oculto
            hiddenInputsContainer.appendChild(input);
        });
    }

    // Función para obtener la ruta del ícono basado en la extensión del archivo
    function getIconPath(filename) {
        const extension = getExtension(filename);
        const iconPath = `${assetBasePath}${extension}.png`;
        const defaultIcon = `${assetBasePath}default.png`;

        const supportedExtensions = ['pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt', 'dwg', 'dxf', 'img', 'docx', 'zip'];
        return supportedExtensions.includes(extension) ? iconPath : defaultIcon;
    }

    // Función para obtener la extensión del archivo
    function getExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    // Función para formatear el tamaño del archivo (bytes a KB/MB)
    function formatFileSize(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(2)} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
    }
</script>
