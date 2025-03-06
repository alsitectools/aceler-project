
// let filesArray = [];

// // Eliminar archivos existentes
// $(document).on('click', '.btn-delete-file', function () {
//     const url = $(this).data('url');
//     const button = $(this);

//     $.ajax({
//         url: url,
//         type: 'DELETE',
//         success: function (response) {
//             button.closest('.file').remove();
//             console.log('Archivo eliminado con éxito.');
//         },
//         error: function (xhr, status, error) {
//             console.error('Error en la solicitud AJAX:', error);
//             alert('Ocurrió un error al intentar eliminar el archivo.');
//         }
//     });
// });

// // Manejar la selección de nuevos archivos
// document.getElementById('file-upload').addEventListener('change', function (event) {
//     const newFiles = Array.from(event.target.files);

//     newFiles.forEach(file => {
//         if (!filesArray.some(f => f.name === file.name && f.size === file.size)) {
//             filesArray.push(file);
//         }
//     });

//     updateFileList();
// });

// // Función para actualizar la lista de archivos visualmente
// function updateFileList() {
//     const fileListElement = document.getElementById('file-list');
//     const hiddenInputsContainer = document.getElementById('hidden-file-inputs');

//     // Eliminar solo los archivos nuevos (que no tienen la clase "exist")
//     const newFiles = fileListElement.querySelectorAll('.file:not(.exist)');
//     newFiles.forEach(file => file.remove());

//     hiddenInputsContainer.innerHTML = '';

//     // Mantenemos los archivos ya existentes y agregamos los nuevos
//     filesArray.forEach((file, index) => {
//         const fileContainer = document.createElement('div');
//         fileContainer.classList.add('file', 'd-flex', 'align-items-center', 'mt-2');

//         const icon = document.createElement('img');
//         icon.src = getIconPath(file.name);
//         icon.alt = `${getExtension(file.name)} icon`;
//         icon.style.width = '20px';
//         icon.style.height = '25px';
//         fileContainer.appendChild(icon);

//         const fileNameContainer = document.createElement('div');
//         fileNameContainer.classList.add('file-name', 'ms-2');
//         fileNameContainer.textContent = file.name;
//         fileContainer.appendChild(fileNameContainer);

//         // Tamaño del archivo
//         const fileSize = document.createElement('small');
//         fileSize.classList.add('text-muted', 'ms-2'); // Añade una clase para estilizar el tamaño
//         fileSize.textContent = `(${formatFileSize(file.size)})`;
        
//         fileNameContainer.appendChild(fileSize);
//         // Botón para eliminar archivo de la lista visual
//         const removeButton = document.createElement('button');
//         removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2');
//         removeButton.textContent = "X";
//         removeButton.addEventListener('click', function () {
//             filesArray.splice(index, 1);
//             updateFileList();
//         });
//         fileContainer.appendChild(removeButton);

//         fileListElement.appendChild(fileContainer);

//         // Crear un input oculto para cada archivo que será enviado al servidor
//         const input = document.createElement('input');
//         input.type = 'file';
//         input.name = 'new_files[]';
//         input.style.display = 'none';

//         const dataTransfer = new DataTransfer();
//         dataTransfer.items.add(file);
//         input.files = dataTransfer.files;

//         hiddenInputsContainer.appendChild(input);
//     });
// }

// // Obtener el ícono del archivo basado en su extensión
// function getIconPath(filename) {
//     const extension = getExtension(filename);
//     const supportedExtensions = ['pdf', 'doc', 'jpg', 'png', 'xlsx', 'txt', 'dwg', 'dxf', 'img', 'docx', 'zip'];

//     // Si se pasa la URL del ícono desde PHP como $iconPath, esto ya está listo
//     return supportedExtensions.includes(extension)
//         ? `${assetBasePath}${extension}.png`
//         : `${assetBasePath}default.png`;
// }

// function getExtension(filename) {
//     return filename.split('.').pop().toLowerCase();
// }

// // Función para formatear el tamaño del archivo (bytes a KB/MB)
// function formatFileSize(bytes) {
//     if (bytes < 1024) return `${bytes} B`;
//     if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(2)} KB`;
//     return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
// }