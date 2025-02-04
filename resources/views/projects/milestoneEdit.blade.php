<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <script src="{{ asset('assets/js/milestone_uploadfiles.js') }}" defer></script>
</head>
@if ($milestone && $currentWorkspace)
    <form method="post" action="{{ route('projects.milestone.update', [$currentWorkspace->slug, $milestone->id]) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <!-- Información general del hito -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="milestone-title" class="col-form-label">{{ __('Milestone Title') }}</label>
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" value="{{ $milestone->title }}" name="title"
                            required disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="start_date" class="col-form-label">{{ __('Created date') }}</label>
                        <input type="date" class="form-control form-control-light date" id="start_date"
                            name="start_date" value="{{ $milestone->start_date }}" disabled>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="end_date" class="col-form-label">{{ __('Desired delivery date') }}</label>
                        <input onclick="this.showPicker()" type="date" class="form-control form-control-light date"
                            id="end_date" name="end_date" value="{{ $milestone->end_date }}" required>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <label for="task-summary" class="col-form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary">{{ $milestone->summary }}</textarea>
                </div>
            </div>
            <!-- Archivos adjuntos existentes -->
            <div class="form-group col-md-12">
                <label for="file-uploadMilestone"class="form-label">
                    <strong>{{ __('Upload files') }}</strong>
                </label>
                <div >
                    <div class="col-md-12 dropzone browse-file" id="dropzonewidgetMilestone">
                        <div class="dz-message" data-dz-message>
                            <input type="file" id="file-uploadMilestone" style="display:none" multiple/>
                            <span> {{ __('Drop files here to upload') }}</span>
                            <p class="text-muted" style="font-size:15px; margin:5px;">200MB</p>
                            <small class="text-muted">.png .gif .pdf .txt .doc .docx .zip .rar .dwg .dxf</small>
                            <div id="file-list"></div>
                            <div style="    display: grid;grid-template-columns: repeat(4, 1fr);gap: 5px;">
                                @foreach ($milestone->files as $file)
                                    @php
                                        $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                                        $iconPath = file_exists(public_path('assets/iconFilesTypes/' . $extension . '.png'))
                                            ? 'assets/iconFilesTypes/' . $extension . '.png'
                                            : 'assets/iconFilesTypes/default.png';
                                    @endphp
                                    <div class="fileMilestoneEdit exist d-flex align-items-center mt-2" data-file-id="{{ $file->id }}">
                                        <img src="{{ asset($iconPath) }}" alt="{{ $extension }} icon"
                                            style="width: 20px; height: 25px;">
                                        <div class="file-name ms-2">{{ $file->name }} <small
                                                class="text-muted">({{ $file->file_size }})</small></div>
                                        <button class="btn btn-danger btn-sm ms-2 btn-delete-file"
                                            data-url="{{ route('milestone.destroy.file', [$currentWorkspace->slug, $milestone->id, $file->id, $milestone->project_id]) }}">
                                            <i class="fa-solid fa-trash-alt"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        </div>
                    </div>
                    <div id="hidden-file-inputs" style="display: none;"></div>
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
    const assetBasePath = "{{ asset('assets/iconFilesTypes') }}/";
</script>

<script>
     document.getElementById('dropzonewidgetMilestone').addEventListener('click', function() {
        document.getElementById('file-uploadMilestone').click();
    });
    
    document.getElementById('file-uploadMilestone').addEventListener('change', function(event) {
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