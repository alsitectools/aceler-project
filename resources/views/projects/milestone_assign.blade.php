@php
    $user = $milestone->milestone_assigned_to_user
        ? \App\Models\User::find($milestone->milestone_assigned_to_user)
        : null;
@endphp

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <!--<script src="{{ asset('assets/js/milestone_uploadfiles.js') }}" defer></script>-->
</head>
<style>
    .ctr {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modalMod {
        width: 50%;
    }
</style>
@if ($milestone && $currentWorkspace)
    <form id="asignMilestoneForm" method="post"
        action="{{ route('projects.milestone.update', [$currentWorkspace->slug, $milestone->id]) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <!-- Información general del hito -->

            <div class="row" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" value="{{ $milestone->title }}" name="title"
                            required disabled style="display: none;">

                        <input type="text" class="form-control form-control-light" id="milestone-secret-input"
                            placeholder="{{ __('Enter Title') }}" value="{{ $milestone->project_id }}" name="title"
                            required disabled style="display: none;" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <input type="date" class="form-control form-control-light date" id="start_date"
                            name="start_date" value="{{ $milestone->project_id }}" disabled style="display: none;">
                    </div>
                    <div class="form-group col-md-6">
                        <input onclick="this.showPicker()" type="date" class="form-control form-control-light date"
                            id="end_date" name="end_date" value="{{ $milestone->end_date }}" required
                            style="display: none;">
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary"
                        value="{{ $milestone->project_id }}" style="display: none;">{{ $milestone->summary }}</textarea>
                </div>
            </div>
            <!-- Apartado "Asignado a" -->
            <div class="row mt-3 ctr">
                <div id="requestBy-req">
                    <label class="col-form-label">{{ __('Assigned to') }}</label>
                    <input type="text" class="form-control" id="search-requested-by"
                        placeholder="{{ __('Search') }}" name="search-requested-by" value="{{ $user->name ?? '' }}"
                        autocomplete="off">
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
                </div>
            </div>
            <!-- Archivos adjuntos existentes -->
            <div class="form-group col-md-12 mt-3" style="display: none;">
                <label for="file-uploadMilestone" class="form-label">
                    <strong>{{ __('Upload files') }}</strong>
                </label>
                <div>
                    <div class="col-md-12 dropzone browse-file" id="dropzonewidgetMilestone">
                        <div class="dz-message" data-dz-message>
                            <input type="file" id="file-uploadMilestone" style="display:none" multiple />
                            <span> {{ __('Drop files here to upload') }}</span>
                            <p class="text-muted" style="font-size:15px; margin:5px;">200MB</p>
                            <small class="text-muted">.png .gif .pdf .txt .doc .docx .zip .rar .dwg .dxf</small>
                        </div>
                    </div>
                    <div id="file-list"></div>
                    <p style="margin-top: 1%;">
                        <b>{{ __('Actual milestone files') }}</b>
                    </p>
                    <div class="actualMilestoneFiles">
                        @foreach ($milestone->files as $file)
                            @php
                                $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                                $iconPath = file_exists(public_path('assets/iconFilesTypes/' . $extension . '.png'))
                                    ? 'assets/iconFilesTypes/' . $extension . '.png'
                                    : 'assets/iconFilesTypes/default.png';
                            @endphp
                            <div class="fileMilestoneEdit exist d-flex align-items-center mt-2"
                                data-file-id="{{ $file->id }}">
                                <img src="{{ asset($iconPath) }}" alt="{{ $extension }} icon"
                                    style="width: 20px; height: 25px;">
                                <div class="file-name ms-2">{{ $file->name }} <small
                                        class="text-muted">({{ $file->file_size }})</small></div>
                                <a class="buttonFiles"
                                    onclick="deleteFile({{ $milestone->project_id }}, '{{ $milestone->id }}', '{{ $file->name }}')">
                                    <i class="fa-solid fa-trash-alt" style="color:white"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="hidden-file-inputs" style="display: flex;"></div>
            </div>
            <div id="hidden-file-inputs" style="display: flex;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
        </div>
    </form>
@else
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <h1>404</h1>
                <p>{{ __('Page Not Found') }}</p>
            </div>
        </div>
    </div>
@endif

<script>
    var assetBasePath = "{{ asset('assets/iconFilesTypes') }}/";
</script>

<script>
    var filesArray = [];

    function deleteFile(idProject, milestoneId, file) {
        const deleteUrl = '<?php echo url('/milestone/delete_file'); ?>';

        $.ajax({
            url: deleteUrl,
            method: 'POST',
            data: {
                "idProject": idProject,
                "milestoneId": milestoneId,
                "fileName": file,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log("file succefully deleted")
            },
            error: function(xhr) {
                alert("An error occurred while downloading the file.");
                console.error(xhr.responseText);
            }
        });
    }

    document.getElementById('dropzonewidgetMilestone').addEventListener('click', function() {
        document.getElementById('file-uploadMilestone').click();
    });

    document.getElementById('file-uploadMilestone').addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);
        const existingFileNames = Array.from(document.querySelectorAll('.file-name'))
            .map(fileNameElement => fileNameElement.textContent.trim().split(" (")[0]);

        newFiles.forEach(file => {
            let fileName = file.name;
            let fileBaseName = fileName.substring(0, fileName.lastIndexOf(".")) || fileName;
            let fileExtension = fileName.substring(fileName.lastIndexOf(".")) || "";
            let newFileName = fileName;

            while (existingFileNames.includes(newFileName)) {
                fileBaseName = fileBaseName.endsWith("_update") ? fileBaseName + "_update" :
                    fileBaseName + "_update";
                newFileName = `${fileBaseName}${fileExtension}`;
            }

            let renamedFile = new File([file], newFileName, {
                type: file.type,
                lastModified: file.lastModified
            });

            let fileKey = `${renamedFile.name}-${renamedFile.size}-${renamedFile.lastModified}`;
            if (!filesArray.some(f => `${f.name}-${f.size}-${f.lastModified}` === fileKey)) {
                filesArray.push(renamedFile);
                existingFileNames.push(newFileName);
            }
        });

        updateFileList();
    });

    function updateFileList() {
        const fileListElement = document.getElementById('file-list');
        const hiddenInputsContainer = document.getElementById('hidden-file-inputs');

        fileListElement.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        filesArray.forEach(file => {
            const fileKey = `${file.name}-${file.size}-${file.lastModified}`;

            const fileContainer = document.createElement('div');
            fileContainer.classList.add('file', 'd-flex', 'align-items-center', 'mt-2');

            const icon = document.createElement('img');
            icon.src = getIconPath(file.name);
            icon.alt = `${getExtension(file.name)} icon`;
            icon.style.width = '20px';
            icon.style.height = '25px';
            fileContainer.appendChild(icon);

            const fileNameContainer = document.createElement('div');
            fileNameContainer.classList.add('file-name', 'ms-2');
            fileNameContainer.textContent = file.name;
            fileContainer.appendChild(fileNameContainer);

            const removeButton = document.createElement('a');
            removeButton.classList.add('buttonFiles');
            removeButton.innerHTML = '<i class="fa-solid fa-trash" style="color:white"></i>';
            removeButton.addEventListener('click', function() {
                filesArray = filesArray.filter(f => `${f.name}-${f.size}-${f.lastModified}` !==
                    fileKey);
                document.getElementById(fileKey).remove();
                updateFileList();
            });
            fileContainer.appendChild(removeButton);

            fileListElement.appendChild(fileContainer);

            if (!document.getElementById(fileKey)) {
                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'new_files[]';
                input.id = fileKey;
                input.style.display = 'none';

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;

                hiddenInputsContainer.appendChild(input);
            }
        });
    }

    function getIconPath(filename) {
        const extension = getExtension(filename);
        const supportedExtensions = ['pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt', 'dwg', 'dxf', 'img', 'docx', 'zip'];
        return supportedExtensions.includes(extension) ?
            `${assetBasePath}${extension}.png` :
            `${assetBasePath}default.png`;
    }

    function getExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(2)} KB`;
        return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
    }
</script>

<script>
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
</script>
<script>
    async function displayNotification() {
        console.log('Generando notificacion de encargo creado');
        let milestoneTitle = document.getElementById('milestone-title').value;
        let milestoneParent;
        let milestoneAssignedTo = document.getElementById('req_assing_To').value;
        if (milestoneAssignedTo === '') {
            milestoneAssignedTo = -2;
        }
        try {
            milestoneParent = document.getElementById('searchProject').value;
            console.log("Milestone parent:", milestoneParent);
        } catch (error) {
            // No redeclaramos la variable, solo la asignamos
            milestoneParent = document.getElementById('milestone-secret-input').value;
            console.log("Milestone parent pero en el catch:", milestoneParent);

            // Genera la URL con un placeholder y reemplázalo con el id obtenido
            const projectNameUrl = "{{ route('projects.milestone.getNameByID', ['id' => 'ID_PLACEHOLDER']) }}";
            const url = projectNameUrl.replace('ID_PLACEHOLDER', milestoneParent);

            try {
                const response = await fetch(url);
                const projectName = await response.text();
                console.log("Nombre del proyecto:", projectName);
                milestoneParent = projectName;
            } catch (err) {
                console.error("Error al obtener el nombre del proyecto:", err);
            }
        }

        // Aquí ya se tiene el valor correcto en milestoneParent
        let msg = milestoneTitle + ' en ' + milestoneParent;
        let ntipe = (milestoneAssignedTo !== '') ? 4 : 2;
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

    document.getElementById('asignMilestoneForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevenir el envío inmediato
        await displayNotification(); // Esperar a que se complete la notificación
        this.submit(); // Enviar el formulario
    });
</script>
<script>
    const observer = new MutationObserver(() => {
        const form = document.getElementById(
            "asignMilestoneForm"); // Asegúrate de usar un selector más preciso si hay más de un form
        if (form) {
            const grandParent = form.parentElement?.parentElement;
            const greatGrandParent = grandParent?.parentElement;
            if (grandParent) {
                grandParent.classList.add("modalMod");
                greatGrandParent.classList.add("ctr");
                observer.disconnect(); // Deja de observar una vez encontrado
            }
        }
    });

    // Observamos todo el body en busca de cambios en el DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
</script>
