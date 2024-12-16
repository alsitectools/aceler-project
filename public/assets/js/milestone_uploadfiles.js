let filesArray = [];

// Eliminar archivos existentes
$(document).on('click', '.btn-delete-file', function () {
    event.preventDefault();

    const url = $(this).data('url');
    const button = $(this);

    $.ajax({
        url: url,
        type: 'DELETE',
        success: function (response) {
            button.closest('.file').remove();
        },
        error: function (xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
            alert('Ocurrió un error al intentar eliminar el archivo.');
        }
    });
});

// Manejar la selección de nuevos archivos
document.getElementById('file-upload').addEventListener('change', function (event) {
    const newFiles = Array.from(event.target.files);

    newFiles.forEach(file => {
        const fileKey = `${file.name}-${file.size}-${file.lastModified}`;

        if (!filesArray.some(f => `${f.name}-${f.size}-${f.lastModified}` === fileKey)) {
            filesArray.push(file);
        } else {
            console.warn(`Archivo ya existe en filesArray: ${fileKey}`);
        }
    });

    updateFileList();
});

// Función para actualizar la lista de archivos visualmente
function updateFileList() {

    const fileListElement = document.getElementById('file-list');
    const hiddenInputsContainer = document.getElementById('hidden-file-inputs');

    // Eliminar solo los archivos nuevos (que no tienen la clase "exist")
    const newFiles = fileListElement.querySelectorAll('.file:not(.exist)');
    newFiles.forEach(file => file.remove());

    // Mantener inputs ocultos solo para los archivos que permanecen en filesArray
    const existingInputs = hiddenInputsContainer.querySelectorAll('input[type="file"]');
    existingInputs.forEach(input => {
        const fileFromInput = input.files[0];
        if (!filesArray.some(f => f.name === fileFromInput.name && f.size === fileFromInput.size && f.lastModified === fileFromInput.lastModified)) {
            input.remove();
        }
    });

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

        // Crear un input oculto con ID único para el archivo
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
        } else {
            console.warn(`Input oculto ya existe para archivo: ${fileKey}`);
        }
    });
}

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

