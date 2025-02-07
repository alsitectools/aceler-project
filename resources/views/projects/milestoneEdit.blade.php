<head>
    <link rel="stylesheet" href="{{ asset('assets/css/milestone.css') }}">
    <!--<script src="{{ asset('assets/js/milestone_uploadfiles.js') }}" defer></script>-->
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
   let filesArray = [];

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

        // Evitar archivos con nombres duplicados agregando "_update"
        while (existingFileNames.includes(newFileName)) {
            fileBaseName = fileBaseName.endsWith("_update") ? fileBaseName + "_update" : fileBaseName + "_update";
            newFileName = `${fileBaseName}${fileExtension}`;
        }

        let renamedFile = new File([file], newFileName, { type: file.type, lastModified: file.lastModified });

        let fileKey = `${renamedFile.name}-${renamedFile.size}-${renamedFile.lastModified}`;
        if (!filesArray.some(f => `${f.name}-${f.size}-${f.lastModified}` === fileKey)) {
            filesArray.push(renamedFile);
            existingFileNames.push(newFileName);
        }
    });

    updateFileList();
});

// 🔹 Función para actualizar la lista visual y los inputs ocultos
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

        const fileSize = document.createElement('small');
        fileSize.classList.add('text-muted', 'ms-2');
        fileSize.textContent = `(${formatFileSize(file.size)})`;
        fileNameContainer.appendChild(fileSize);

        const removeButton = document.createElement('button');
        removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2');
        removeButton.textContent = "X";
        removeButton.addEventListener('click', function () {
            filesArray = filesArray.filter(f => `${f.name}-${f.size}-${f.lastModified}` !== fileKey);
            document.getElementById(fileKey).remove();
            updateFileList();
        });
        fileContainer.appendChild(removeButton);

        fileListElement.appendChild(fileContainer);

        // 🔹 Crear un input oculto con ID único para cada archivo
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

// 🔹 Funciones auxiliares para íconos, extensiones y tamaños de archivos
function getIconPath(filename) {
    const extension = getExtension(filename);
    const supportedExtensions = ['pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt', 'dwg', 'dxf', 'img', 'docx', 'zip'];

    return supportedExtensions.includes(extension)
        ? `${assetBasePath}${extension}.png`
        : `${assetBasePath}default.png`;
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