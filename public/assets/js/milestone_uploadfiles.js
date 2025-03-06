
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



