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

    function hideAllLists(exceptList) {
        // Oculta todas las listas excepto la especificada
        if (exceptList !== projectList) projectList.hide();
        if (exceptList !== refMoList) refMoList.hide();
        if (exceptList !== clipoList) clipoList.hide();
        if (exceptList !== salesList) salesList.hide();
    }

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
        hideAllLists(list);
        searchQuery = input.val().trim();
        currentPage = 1;

        if (searchQuery == '') {
            if (currentRequest) {
                currentRequest.abort();
            }
            $('#loading-spinner-container').remove();
            list.empty().hide();
            return;
        }

        fetchData(`${url}/${encodeURIComponent(searchQuery)}?page=${currentPage}`, list, data => data[type].data, noResultsMessage, type);
    }, 300);

    // Manejadores de entrada para los campos de búsqueda
    projectInput.on('input', function () {
        milestoneMoInput.val("");
        handleInputChange($(this), projectList, searchProjectsUrl, 'Sin resultados. El proyecto no ha sido creado.', 'projects');
    });

    salesManagerInput.on('input', function () {
        if (salesManagerInput.val().trim() === "") {
            salesList.empty().hide();
            return;
        }
        handleInputChange($(this), salesList, searchSalesManagerUrl, 'Sin resultados encontrados', 'salesManagers');
    });

    refMoInput.on('input', function () {
        clientInput.val("");
        project_nameInput.val("");
        $('#projectId').val('');
        handleInputChange($(this), refMoList, searchMoUrl, 'Sin resultados encontrados', 'mo');
    });

    clientInput.on('input', function () {
        handleInputChange($(this), clipoList, searchClipoUrl, 'Sin resultados encontrados', 'clients');
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
                console.log('Tipo desconocido para el spinner:', type);

        }
        // Realizar la solicitud AJAX
        currentRequest = $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                loading = false;
                const itemData = itemProcessor(data);
                console.log('obras', itemData);

                if (!itemData.length && searchQuery.length >= 3) {
                    list.append(`<p class="text-danger">${noResultsMessage}</p>`);
                }

                handleDataList(itemData, list, noResultsMessage, type);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                loading = false;
                showAlert(type);
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
            // Inicializar NiceScroll después de agregar los elementos a la lista
            list.niceScroll({
                cursorcolor: "grey",
                cursorwidth: "8px",
                background: "transparent",
                autohidemode: true,
                cursorborder: "1px solid #ccc",
                cursorborderradius: "5px",
            });

        } else if (currentPage === 1) {
            let errorMessage = $(`<div class="text-danger list-group-item m-0">${noResultsMessage}</div>`);
            list.append(errorMessage);

            setTimeout(() => {
                errorMessage.fadeOut(1000, function () {
                    $(this).remove();
                });
            }, 5000);
        }
    }

    function handleListItemClick(item, list, type) {
        return function (e) {
            e.preventDefault();

            const additionalForm = document.getElementById('visado');
            console.log('tipo de busqueda', type);

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

                if (item.obras && item.obras.length > 0) {
                    populateMoList(item.obras);
                }

            } else if (type === 'projects') {

                $('#projectId').val(item.id);
                projectInput.val(item.name);

                if (!item.ref_mo) {

                    milestoneMoInput.prop('disabled', true);
                    milestoneMoInput.prop('required', false);
                } else {
                    milestoneMoInput.val('');
                    milestoneMoInput.prop('disabled', false);
                    milestoneMoInput.val(item.ref_mo).prop('readonly', true);
                    additionalForm.style.display = 'block';
                }


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

    // Función para mostrar la lista de obras en ref_mo_list
    function populateMoList(obras) {
        refMoList.empty().show();

        const obraItems = obras.map(obra => {
            return $('<a href="#" class="list-group-item list-group-item-action stylelist">')
                .text(`${obra.ref_mo} - ${obra.name}`)
                .data('item', obra)
                .on('click', function (e) {
                    e.preventDefault();
                    refMoInput.val(obra.ref_mo);
                    project_nameInput.val(obra.name);
                    refMoList.empty().hide();
                });
        });

        refMoList.append(obraItems);
    }
    function populateClientList(selectedClients) {
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
        'Sin resultados. El proyecto no ha sido creado.', 'projects');
    setupInfiniteScroll(refMoList, searchMoUrl, data => data.mo.data, 'Sin proyectos encontrados', 'ref_mo');
    setupInfiniteScroll(clipoList, searchClipoUrl, data => data.clients.data, 'Sin clientes encontrados', 'clipo');
});