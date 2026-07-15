<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <!--<script src="{{ asset('assets/js/milestone_uploadfiles.js') }}" defer></script>-->
</head>
<style>
    /* Contenedor en grid para los archivos */
    .custom-file-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 5px;
        width: 100%;
    }

    /* Estilo uniforme para cada "tarjeta" de archivo */
    .custom-file {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
        width: 220px;
        background: #f4f5ff;
        padding: 10px;
        margin: 5px;
        box-shadow: -3px 3px 0px 0px rgb(239, 239, 239);
        border-radius: 6px;
        text-align: center;
    }

    /* Ajusta el párrafo para evitar que el texto se desborde */
    .custom-file p {
        margin: 0;
        font-size: 14px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
    }

    /* Estilos para el drag & drop */
    .dropzone.dragover {
        border-color: #ccc !important;
        background-color: #f8f9fa !important;
    }

    .dropzone.dragover .dz-message {
        opacity: 0.5;
    }

    .fixingRowMargin {
        margin-right: 0 !important;
        padding-right: 0 !important;
    }

    .paddingRight0 {
        padding-right: 0 !important;
    }
</style>

@if ($milestone && $currentWorkspace)
    <form method="post" action="{{ route('projects.milestone.update', [$currentWorkspace->slug, $milestone->id]) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <!-- Información general del hito -->
            <div class="row">
                <div class="@if($project && in_array((int) $project->type, [3, 5], true)) fixingRowMargin row @else col-md-12 @endif">
                    <div class=" @if($project && in_array((int) $project->type, [3, 5], true)) paddingRight0 col-md-6 @endif">
                        <label for="milestone-title" class="col-form-label">{{ __('Milestone Title') }}</label>
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" value="{{ $milestone->title }}" name="title"
                            required>
                    </div>
                    @if ($project && in_array((int) $project->type, [3, 5], true))
                        <div class="paddingRight0 col-md-6">
                            <label for="phase" class="col-form-label">{{ __('Stage') }}</label>
                            <select class="form-control form-control-light" id="phase" name="phase">
                                <option value="">{{ __('Select a stage') }}</option>
                                @foreach ($phases as $phase)
                                    <option value="{{ $phase }}"
                                        {{ trim((string) $currentPhase) === trim((string) $phase) ? 'selected' : '' }}>
                                        {{ __(\App\Models\MilestonePhases::translationKey($phase)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="fixingRowMargin row">
                    <div class="paddingRight0  col-md-6">
                        <label for="start_date" class="col-form-label">{{ __('Created date') }}</label>
                        <input type="date" class="form-control form-control-light date" id="start_date"
                            name="start_date" value="{{ $milestone->start_date }}" disabled>
                    </div>
                    <div class="paddingRight0  col-md-6">
                        <label for="end_date" class="col-form-label">{{ __('Desired delivery date') }}</label>
                        <input onclick="this.showPicker()" type="date" class="form-control form-control-light date"
                            id="end_date" name="end_date" value="{{ $milestone->end_date }}" required>
                    </div>
                </div>
                <div class="fixingRowMargin row">
                    <div class=" paddingRight0  col-md-6">
                        <label for="priority" class="col-form-label">{{ __('Priority') }}</label>
                        <select class="form-control form-control-light" id="priority" name="priority">
                            <option value="">{{ __('Not defined') }}</option>
                            <option value="alta" {{ $milestone->priority === 'alta' ? 'selected' : '' }}>
                                {{ __('High Priority') }}</option>
                            <option value="media" {{ $milestone->priority === 'media' ? 'selected' : '' }}>
                                {{ __('Medium Priority') }}</option>
                            <option value="baja" {{ $milestone->priority === 'baja' ? 'selected' : '' }}>
                                {{ __('Low Priority') }}</option>
                        </select>
                    </div>



                    {{-- Phase field para proyectos tipo 3 y 5 --}}
                    @if ($project && in_array((int) $project->type, [3, 5], true))
                        <div class="paddingRight0  col-md-6">
                            <label for="stage" class="col-form-label">{{ __('Phase') }}</label>
                            <select class="form-control form-control-light" id="stage" name="stage"
                                data-add-phase-label="{{ __('Add phase') }}">
                                <option value="">{{ __('Select a phase') }}</option>
                                @foreach ($stagesProject as $stageName)
                                    <option value="{{ $stageName }}"
                                        {{ trim((string) $currentStage) === trim((string) $stageName) ? 'selected' : '' }}>
                                        {{ $stageName }}
                                    </option>
                                @endforeach
                                <option value="add_phase">{{ __('Add phase') }}</option>
                            </select>
                            <div id="new-stage-name-wrapper" class="mt-2" style="display: none;">
                                <input type="text" name="new_stage_name" id="new_stage_name"
                                    class="form-control form-control-light"
                                    placeholder="{{ __('Enter phase name') }}" autocomplete="off">
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Inputs hidden para mantener los usuarios asignados -->
                <input type="hidden" name="assign_to" value="{{ $milestone->assign_to }}">
                <input type="hidden" name="milestone_assigned_to_user"
                    value="{{ $milestone->milestone_assigned_to_user }}">
                <div class=" col-md-12">
                    <label for="task-summary" class="col-form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary">{{ $milestone->summary }}</textarea>
                </div>
            </div>
            <!-- Archivos adjuntos existentes -->
            <div class=" col-md-12">
                <label class="form-label">
                    <strong>{{ __('Upload files') }}</strong>
                </label>
                <div>
                    <div class="col-md-12 dropzone browse-file" id="dropzonewidgetMilestone">
                        <div class="dz-message" data-dz-message>
                            <input type="file" id="file-uploadMilestone" style="display:none" multiple />
                            <span>{{ __('Drop files here to upload') }}</span>
                            <p>
                                {{ __('You can Also hold click + Control + V to paste the content of the clipboard') }}
                            </p>
                            <p class="text-muted" style="font-size:15px; margin:5px;">50MB</p>
                            <small class="text-muted">.png .gif .pdf .txt .doc .docx .zip .rar .dwg .dxf</small>
                        </div>
                    </div>
                    <div id="file-list"></div>
                    <p style="margin-top: 1%;"><b>{{ __('Actual milestone files') }}</b></p>
                    <!-- Aplicamos las clases para que se muestren 3 columnas -->
                    <div class="actualMilestoneFiles custom-file-container">
                        @foreach ($milestone->files as $file)
                            @php
                                $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                                $iconPath = file_exists(public_path('assets/iconFilesTypes/' . $extension . '.png'))
                                    ? 'assets/iconFilesTypes/' . $extension . '.png'
                                    : 'assets/iconFilesTypes/default.png';
                            @endphp
                            <div class="fileMilestoneEdit exist d-flex align-items-center mt-2 custom-file"
                                data-file-id="{{ $file->id }}">
                                <div class="d-flex align-items-center flex-grow-1" style="cursor: pointer;"
                                    onclick="previewFile({{ $milestone->project_id }}, '{{ $milestone->title }}', '{{ $file->file }}', '{{ $extension }}')">
                                    <img src="{{ asset($iconPath) }}" alt="{{ $extension }} icon"
                                        style="width: 20px; height: 25px;">
                                    <div class="file-name ms-2">{{ $file->name }} </div>
                                </div>
                                <a class="buttonFiles btn btn-sm"
                                    onclick="deleteFile({{ $milestone->project_id }}, '{{ $milestone->id }}', '{{ $file->id }}')">
                                    <i class="fa-solid fa-trash-alt"
                                        style="color:white; background-color:#aa182c; padding:7px; border-radius:6px;"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="hidden-file-inputs" style="display: none;"></div>
            </div>
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
    (function() {
        const stageSelect = document.getElementById('stage');
        const newStageWrapper = document.getElementById('new-stage-name-wrapper');
        const newStageInput = document.getElementById('new_stage_name');
        const milestoneForm = stageSelect ? stageSelect.closest('form') : null;

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

        stageSelect.addEventListener('change', toggleNewStageInput);
        toggleNewStageInput();

        if (milestoneForm) {
            milestoneForm.addEventListener('submit', function(e) {
                if (stageSelect.value !== 'add_phase') {
                    return;
                }

                const newStageName = (newStageInput?.value || '').trim();
                if (!newStageName) {
                    e.preventDefault();
                    newStageInput?.classList.add('is-invalid');
                    newStageInput?.focus();
                }
            });
        }
    })();
</script>

<script>
    // Función para eliminar archivos existentes del servidor
    function deleteFile(idProject, milestoneId, fileId) {
        event.preventDefault();
        const deleteUrl = "{{ route('milestone.destroy.file') }}";

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: '{{ __('Are You Sure?') }}',
            text: '{{ __('This action can not be undone. Do you want to continue?') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: appLocale == 'es' ? 'Si' : 'Yes',
            cancelButtonText: 'No',
            reverseButtons: false,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        "idProject": idProject,
                        "milestoneId": milestoneId,
                        "fileID": fileId,
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        const fileElement = document.querySelector(
                            `.fileMilestoneEdit[data-file-id="${fileId}"]`);
                        if (fileElement) {
                            fileElement.remove();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    }
</script>
<script>
    (function() {
        // Cleanup de listeners anteriores
        if (window._milestoneEditCleanup) {
            window._milestoneEditCleanup();
        }

        // --- Dropzone lógica igual que milestone.blade.php ---
        const dropzoneMilestone = document.getElementById('dropzonewidgetMilestone');
        let fileInputMilestone = document.getElementById('file-uploadMilestone');
        const fileListMilestone = document.getElementById('file-list');
        const hiddenInputsMilestone = document.getElementById('hidden-file-inputs');
        var filesArrayMilestone = [];

        // Drag & Drop visual feedback
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

        ['dragleave', 'dragend'].forEach(eventName => {
            dropzoneMilestone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneMilestone.classList.remove('dragover');
            });
        });

        dropzoneMilestone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneMilestone.classList.remove('dragover');

            const dt = e.dataTransfer;
            if (dt.files && dt.files.length) {
                const files = Array.from(dt.files);
                handleFilesMilestone(files);
                dropzoneMilestone.focus();
            }
        });

        // Asegurarse de que el dropzone mantenga el foco después de cualquier operación
        function refocusDropzone() {
            setTimeout(() => {
                dropzoneMilestone.focus();
            }, 10);
        }

        // Click para seleccionar archivos
        dropzoneMilestone.setAttribute('tabindex', '0');
        dropzoneMilestone.addEventListener('click', function(e) {
            // Evitar que se dispare si el click fue en un botón de eliminar
            if (e.target.closest('.buttonFiles') || e.target.closest('a')) {
                return;
            }
            fileInputMilestone.value = '';
            fileInputMilestone.click();
        });

        // Selección manual desde input file
        fileInputMilestone.addEventListener('change', function() {
            if (fileInputMilestone.files && fileInputMilestone.files.length) {
                handleFilesMilestone(Array.from(fileInputMilestone.files));
            }
        });

        // Ctrl+V para pegar archivos
        function _pasteHandler(e) {
            const focused = document.activeElement;
            if (focused !== dropzoneMilestone && !dropzoneMilestone.contains(focused)) {
                return;
            }
            e.preventDefault();
            if (!e.clipboardData || !e.clipboardData.items) {
                return;
            }
            const items = Array.from(e.clipboardData.items);
            const files = items
                .filter(item => item.kind === 'file')
                .map(item => item.getAsFile())
                .filter(file => file !== null);
            if (files.length > 0) {
                handleFilesMilestone(files);
            }
            dropzoneMilestone.focus();
        }
        document.addEventListener('paste', _pasteHandler);

        window._milestoneEditCleanup = function() {
            document.removeEventListener('paste', _pasteHandler);
        };

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

                // Reemplazar espacios con guiones bajos en el nombre
                const processedFile = new File(
                    [file],
                    file.name.replace(/\s+/g, '_'), {
                        type: file.type,
                        lastModified: file.lastModified
                    }
                );

                addFileToMilestoneArray(processedFile);
            });

            // Mostrar alerta si hay archivos rechazados
            if (rejectedInThisBatch.length > 0) {
                const rejectedList = rejectedInThisBatch.join('\n- ');
                alert('Los siguientes archivos fueron rechazados por exceder el límite de 50MB:\n- ' +
                    rejectedList);
            }
        }

        /**
         * Genera un nombre único para el archivo si ya existe uno con el mismo nombre.
         * Ejemplo: archivo.pdf -> archivo (2).pdf -> archivo (3).pdf
         */
        function generateUniqueFileNameMilestone(fileName) {
            // Incluir tanto archivos en el array como archivos existentes del servidor
            const existingNamesFromArray = filesArrayMilestone.map(f => f.name);

            // Para archivos del servidor, extraer solo el nombre sin el tamaño
            const existingNamesFromDOM = Array.from(document.querySelectorAll(
                    '.fileMilestoneEdit .file-name, #file-list .file-name'))
                .map(el => {
                    // Si tiene un <small> dentro (tamaño del archivo), obtener solo el texto antes
                    const smallElement = el.querySelector('small');
                    if (smallElement) {
                        // Obtener solo el primer nodo de texto (el nombre del archivo)
                        const textNodes = Array.from(el.childNodes).filter(node => node.nodeType === Node
                            .TEXT_NODE);
                        return textNodes.length > 0 ? textNodes[0].textContent.trim() : el.textContent.trim();
                    }
                    return el.textContent.trim();
                });

            const existingNames = [...new Set([...existingNamesFromArray, ...existingNamesFromDOM])];

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

        function updateFileListMilestone() {
            fileListMilestone.innerHTML = '';
            hiddenInputsMilestone.innerHTML = '';

            // Mostrar archivos válidos
            filesArrayMilestone.forEach((file, index) => {
                const fileContainer = document.createElement('div');
                fileContainer.classList.add('custom-file');

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

                const removeButton = document.createElement('a');
                removeButton.classList.add('buttonFiles');
                removeButton.innerHTML =
                    '<i class="fa-solid fa-trash" style="color:white; background-color:#aa182c; padding:7px; border-radius:6px;"></i>';
                removeButton.addEventListener('click', function() {
                    filesArrayMilestone.splice(index, 1);
                    updateFileListMilestone();
                });

                fileContainer.appendChild(removeButton);
                fileListMilestone.appendChild(fileContainer);

                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'new_files[]';
                input.style.display = 'none';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                hiddenInputsMilestone.appendChild(input);
            });

            // Mostrar archivos rechazados (tachados)
            rejectedFilesMilestone.forEach((rejectedFile, index) => {
                const fileContainer = document.createElement('div');
                fileContainer.classList.add('custom-file');
                fileContainer.style.opacity = '0.5';
                fileContainer.style.textDecoration = 'line-through';
                fileContainer.title = 'File too big: Exceeds 50MB limit';
                fileContainer.style.cursor = 'not-allowed';

                const icon = document.createElement('img');
                icon.src = getIconPath(rejectedFile.name);
                icon.alt = `${getExtension(rejectedFile.name)} icon`;
                icon.style.width = '20px';
                icon.style.height = '25px';
                icon.style.opacity = '0.5';
                fileContainer.appendChild(icon);

                const fileNameContainer = document.createElement('div');
                fileNameContainer.classList.add('file-name');
                fileNameContainer.textContent = rejectedFile.name;
                fileContainer.appendChild(fileNameContainer);

                const removeButton = document.createElement('a');
                removeButton.classList.add('buttonFiles');
                removeButton.innerHTML =
                    '<i class="fa-solid fa-trash" style="color:white; background-color:#aa182c; padding:7px; border-radius:6px;"></i>';
                removeButton.addEventListener('click', function() {
                    rejectedFilesMilestone.splice(index, 1);
                    updateFileListMilestone();
                });
                fileContainer.appendChild(removeButton);
                fileListMilestone.appendChild(fileContainer);
            });
        }

        function getIconPath(filename) {
            const extension = getExtension(filename);
            const supportedExtensions = ['pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt', 'dwg', 'dxf', 'img', 'docx',
                'zip',
                'rar', 'gif', 'jpeg'
            ];
            return supportedExtensions.includes(extension) ?
                `${assetBasePath}${extension}.png` :
                `${assetBasePath}default.png`;
        }

        function getExtension(filename) {
            return filename.split('.').pop().toLowerCase();
        }

        // Manejar submit del formulario para capturar respuesta JSON
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="post"]');
            if (form) {
                const submitButton = form.querySelector('input[type="submit"], button[type="submit"]');
                // Usar propiedad del formulario para evitar conflictos globales
                form._isSubmitting = false;

                form.addEventListener('submit', function(e) {
                    if (form._isSubmitting) {
                        e.preventDefault();
                        return false;
                    }

                    // SIEMPRE prevenir submit tradicional y usar AJAX
                    e.preventDefault();
                    form._isSubmitting = true;
                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    const hasFiles = filesArrayMilestone && filesArrayMilestone.length > 0;
                    const formData = new FormData(this);

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Limpiar arrays de archivos
                            filesArrayMilestone = [];
                            rejectedFilesMilestone = [];

                            if (data.success) {
                                // Mostrar toast con resumen de carga
                                let message = '';
                                if (data.uploaded_count > 0 && data.failed_count > 0) {
                                    message = data.uploaded_count + ' archivos subidos, ' + data
                                        .failed_count + ' rechazados';
                                } else if (data.uploaded_count > 0) {
                                    message = data.uploaded_count +
                                        ' archivos subidos exitosamente';
                                } else if (data.failed_count > 0) {
                                    message = 'Todos los archivos fueron rechazados';
                                } else {
                                    message = 'Cambios guardados correctamente';
                                }

                                // Mostrar toast
                                showToast(message, 'success');

                                // Cerrar modal después de 1.5 segundos
                                setTimeout(() => {
                                    const modal = bootstrap.Modal.getInstance(document
                                        .querySelector('.modal'));
                                    if (modal) {
                                        modal.hide();
                                    }
                                    // Redirigir para refrescar la página
                                    window.location.reload();
                                }, 1500);
                            } else {
                                showToast('Error al guardar cambios', 'danger');
                                form._isSubmitting = false;
                                if (submitButton) {
                                    submitButton.disabled = false;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Error al enviar formulario', 'danger');
                            form._isSubmitting = false;
                            if (submitButton) {
                                submitButton.disabled = false;
                            }
                        });
                });
            }
        });

        // Función para mostrar toast (usa la misma del sitio)
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
