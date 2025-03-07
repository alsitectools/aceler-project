/** Script que gestiona la creacion de proyectos y hojas de encargos desde las vistas de create y milestone */
$(document).ready(function () {
    const projectInput = $('#searchProject');
    const projectList = $('#projects_list');
    const refMoInput = $('#searchMo');
    const milestoneMoInput = $('#milestone_mo');
    const project_nameInput = $('#projectname');
    const clientInput = $('#searchClipo');
    const refMoList = $('#ref_mo_list');
    const clipoList = $('#clipo_list');
    const salesList = $('#sales_manager_list');
    const salesManagerInput = $('#searchSalesManager');

    let loadingSpinner = $(`
            <div class="d-inline-flex ms-2" id="loading-spinner-container" style="display: none;">
                <div class="spinner-grow spinner-grow-sm me-1" style="width: 5px; height: 5px; animation-delay: 0s;" role="status"></div>
                <div class="spinner-grow spinner-grow-sm me-1" style="width: 5px; height: 5px; animation-delay: 0.2s;" role="status"></div>
                <div class="spinner-grow spinner-grow-sm" style="width: 5px; height: 5px; animation-delay: 0.4s;" role="status"></div>
            </div>
        `);

    let currentPage = 1;
    let currentRequest = null;
    let loading = false;
    let searchQuery = '';

    function resetSearchFields() {
        project_nameInput.val('');
        clientInput.val('');
        clipoList.empty();
        refMoList.empty();
    }

    function resetInputs(isJobsite) {
        project_nameInput.val("");
        refMoInput.val("").prop('required', isJobsite).prop('disabled', !isJobsite);
        clientInput.val("");
        project_nameInput.prop('readonly', isJobsite);
    }

    // Función de debounce
    const debounce = (func, delay) => {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    };

    // Función de manejo de entrada
    const handleInputChange = debounce(function (input, list, url, noResultsMessage, type) {
        searchQuery = input.val().trim();
        currentPage = 1;

        if (!searchQuery) {
            list.empty().hide();
            return;
        }

        fetchData(`${url}/${encodeURIComponent(searchQuery)}?page=${currentPage}`, list, data => data[type].data, noResultsMessage, type);
    }, 300);

    // Manejadores de entrada para los campos de búsqueda
    projectInput.on('input', function () {

        milestoneMoInput.val("");
        if ($(this).val().trim() === "") {
            projectList.empty().hide();
            resetSearchFields();
            return;
        }
        handleInputChange($(this), projectList, searchProjectsUrl,
            'No results found. The project is not yet created, you must create it.', 'projects');
    });

    salesManagerInput.on('input', function () {

        if ($(this).val().trim() === "") {
            salesList.empty().hide();
            return;
        }
        handleInputChange($(this), salesList, searchSalesManagerUrl, 'No results found', 'salesManagers');
    });

    refMoInput.on('input', function () {

        if ($(this).val().trim() === "") {
            refMoList.empty().hide();
            resetSearchFields();
            return;
        }
        handleInputChange($(this), refMoList, searchMoUrl, 'No results found', 'mo');
    });
    clientInput.on('input', function () {

        clipoList.empty().hide();
        handleInputChange($(this), clipoList, searchClipoUrl, 'No results found', 'clients');
    });

    $('#project_type').change(function () {
        const isJobsite = $(this).find('option:selected').data('type') === 'Jobsite';
        resetInputs(isJobsite);
        $('#ref_mo, #clipo').toggle(isJobsite);
    });

    function showAlert(message, type) {
        alertSpan = $('<span class="text-danger"></span>');

        switch (type) {
            case 'clipo':
                $('#clipo_list').after(alertSpan);
                break;
            case 'salesManagers':
                $('#sales_manager_list').after(alertSpan);
                break;
            case 'mo':
                $('#ref_mo_list').after(alertSpan);
                break;
            case 'projects':
                $('#projects_list').after(alertSpan);
                break;
            default:
                console.warn('Tipo desconocido para el alert:', type);
        }
        alertSpan.text(message);

        setTimeout(() => alertSpan.text(""), 5000);
    }


    function fetchData(url, list, itemProcessor, noResultsMessage, type = '') {
        if (currentRequest) {
            currentRequest.abort();
        }
        loading = true;

        // Añadir el spinner según el tipo
        switch (type) {
            case 'clients':
                $('#clipo label').after(loadingSpinner);
                break;
            case 'salesManagers':
                $('#sales_manager label').after(loadingSpinner);
                break;
            case 'mo':
                $('#ref_mo label').after(loadingSpinner);
                break;
            case 'projects':
                $('#project label').after(loadingSpinner);
                break;
            default:
                console.warn('Tipo desconocido para el spinner:', type);
        }
        // Realizar la solicitud AJAX
        currentRequest = $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                loading = false;
                const itemData = itemProcessor(data);

                handleDataList(itemData, list, noResultsMessage, type);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loading = false;
                showAlert("Error en la solicitud. Inténtalo de nuevo.", type);
            },
            complete: function () {
                $('#loading-spinner-container').remove();
            }
        });
    }

    function handleDataList(dataList, list, noResultsMessage, type) {
        if (currentPage === 1) {
            list.empty().show();
        }

        if (dataList && dataList.length) {
            const listItems = dataList.map(item => {
                let displayText = item.ref_mo ? `${item.ref_mo} - ${item.name}` : item.name;
                return $('<a href="#" class="list-group-item list-group-item-action stylelist">')
                    .text(displayText.trim())
                    .data('item', item)
                    .on('click', handleListItemClick(item, list, type));
            });
            list.append(listItems);
            currentPage++;
        } else if (currentPage === 1) {
            list.append(`<p class="text-danger">${noResultsMessage}</p>`);
        }
    }

    function handleListItemClick(item, list, type) {
        return function (e) {
            e.preventDefault();
            console.log('Item seleccionado:', item);
            console.log('Tipo en handleListItemClick:', type);

            // Verifica si el proyecto ya existe cuando el tipo es 'mo'
            if (type === 'mo') {
                let existingProject = projects.find(project => project.ref_mo === item.ref_mo);
                if (existingProject) {
                    showAlert('El número de referencia ya existe.', type);
                    resetSearchFields();
                    return;
                }
                refMoInput.val(item.ref_mo);
                project_nameInput.val(item.name);

            } else if (type === 'clients') {
                clientInput.val(item.name);

            } else if (type === 'projects') {

                $('#projectId').val(item.id);
                projectInput.val(item.name);
                milestoneMoInput.val(item.ref_mo).prop('readonly', true);

            } else if (type === 'salesManagers') {
                salesManagerInput.val(item.name);
            }

            list.empty().hide();

            // Si el item contiene una lista de clientes, llamamos a `populateClientList`
            if (item.clients && item.clients.length) {
                populateClientList(item.clients);
            }
        };
    }

    function populateClientList(selectedClients) {
        console.log('Clientes seleccionados:', selectedClients);

        clipoList.empty().show();
        const clientItems = selectedClients.map(client => {
            return $('<a href="#" class="list-group-item list-group-item-action stylelist">')
                .text(client.name)
                .data('name', client.name)
                .on('click', function (e) {
                    e.preventDefault();
                    clientInput.val(client.name);
                    clipoList.empty().hide();
                });
        });
        clipoList.append(clientItems);
    }

    // Configurar el scroll infinito
    function setupInfiniteScroll(list, url, itemProcessor, noResultsMessage, type) {
        list.on('scroll', function () {
            const scrollTop = list[0].scrollTop;
            const scrollHeight = list[0].scrollHeight;
            const innerHeight = list.innerHeight();

            if (!loading && (scrollTop + innerHeight >= scrollHeight - 10)) {
                fetchData(`${url}/${encodeURIComponent(searchQuery)}?page=${currentPage}`, list,
                    itemProcessor, noResultsMessage, type);
            }
        });
    }

    setupInfiniteScroll(projectList, searchMoUrl, data => data.projects.data,
        'No results found. The project is not yet created, you must create it.', 'project');
    setupInfiniteScroll(refMoList, searchMoUrl, data => data.mo.data, 'Projects no results found', 'ref_mo');
    setupInfiniteScroll(clipoList, searchClipoUrl, data => data.clients.data, 'Clients no results found', 'clipo');

    // Cerrar listas cuando se hace click fuera de las mismas
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#projects_list').length) {
            projectList.empty().hide();
        }
        if (!$(e.target).closest('#ref_mo_list').length) {
            refMoList.empty().hide();
        }
        if (!$(e.target).closest('#sales_manager_list').length) {
            salesList.empty().hide();
        }

    });
});
