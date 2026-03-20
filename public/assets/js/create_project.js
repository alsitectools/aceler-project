$(document).ready(function () {
    if (window.createProjectSearchCleanup) {
        window.createProjectSearchCleanup();
    }

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
            <div class="d-inline-flex ms-2 loading-spinner-container" style="display: none;">
                <div class="spinner-grow spinner-grow-sm me-1" style="width: 5px; height: 5px; animation-delay: 0s;" role="status"></div>
                <div class="spinner-grow spinner-grow-sm me-1" style="width: 5px; height: 5px; animation-delay: 0.2s;" role="status"></div>
                <div class="spinner-grow spinner-grow-sm" style="width: 5px; height: 5px; animation-delay: 0.4s;" role="status"></div>
            </div>
        `);

    let currentPage = 1;
    let currentRequest = null;
    let loading = false;
    let searchQuery = '';

    function removeAllLoadingSpinners() {
        $('.loading-spinner-container').remove();
    }

    function getSpinnerTarget(type) {
        switch (type) {
            case 'clients':
                return $('#clipo > label').first();
            case 'salesManagers':
                return $('#sales_manager > label').first();
            case 'mo':
                return $('#ref_mo > label').first();
            case 'projects':
                return $('#project > label').first();
            default:
                return $();
        }
    }

    function showLoadingSpinner(type) {
        const spinnerTarget = getSpinnerTarget(type);

        if (!spinnerTarget.length) {
            return;
        }

        spinnerTarget.siblings('.loading-spinner-container').remove();
        spinnerTarget.after(loadingSpinner.clone().show());
    }

    function isProjectWithStagesAndPhases(item) {
        const typeId = parseInt(item?.type, 10);
        const typeName = (
            item?.type_rel?.name ||
            item?.typeRel?.name ||
            ''
        ).trim().toLowerCase();

        return item?.is_phase_project === true ||
            typeId === 3 ||
            typeId === 5 ||
            typeName === 'i+d project' ||
            typeName === 'proyecto i+d' ||
            typeName === 'i+d development' ||
            typeName === 'desarrollo i+d';
    }

    function populatePhaseOptions(phaseOptions = null) {
        const phaseSelect = document.getElementById('phase');

        if (!phaseSelect) {
            return;
        }

        const availablePhaseOptions = Array.isArray(phaseOptions)
            ? phaseOptions
            : Array.isArray(window.milestonePhaseOptions)
                ? window.milestonePhaseOptions
                : [];

        phaseSelect.innerHTML = '<option value="">Choose one</option>';

        availablePhaseOptions.forEach((phaseName) => {
            const option = document.createElement('option');
            option.value = phaseName;
            option.textContent = phaseName;
            phaseSelect.appendChild(option);
        });
    }

    function populateStageOptions(stageOptions = null) {
        const stageSelect = document.getElementById('stage');

        if (!stageSelect) {
            return;
        }

        const availableStageOptions = Array.isArray(stageOptions)
            ? stageOptions
            : [];

        stageSelect.innerHTML = '<option value="">Choose one</option>';

        availableStageOptions.forEach((stageName) => {
            const option = document.createElement('option');
            option.value = stageName;
            option.textContent = stageName;
            stageSelect.appendChild(option);
        });
    }

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
            removeAllLoadingSpinners();
            list.empty().hide();
            return;
        }

        fetchData(`${url}/${encodeURIComponent(searchQuery)}?page=${currentPage}`, list, data => data[type].data, noResultsMessage, type);
    }, 300);

    // Manejadores de entrada para los campos de búsqueda
    projectInput.off('.createProjectSearch').on('input.createProjectSearch', function () {
        milestoneMoInput.val("");
        handleInputChange($(this), projectList, searchProjectsUrl, 'Sin resultados. El proyecto no ha sido creado.', 'projects');
    });

    salesManagerInput.off('.createProjectSearch').on('input.createProjectSearch', function () {
        if (salesManagerInput.val().trim() === "") {
            salesList.empty().hide();
            return;
        }
        handleInputChange($(this), salesList, searchSalesManagerUrl, 'Sin resultados encontrados', 'salesManagers');
    });

    // Debounce más agresivo para M.O: 150ms en lugar de 300ms para búsqueda más rápida
    const handleMoInputChange = debounce(function () {
        clientInput.val("");
        project_nameInput.val("");
        $('#projectId').val('');
        handleInputChange(refMoInput, refMoList, searchMoUrl, 'Sin resultados encontrados', 'mo');
    }, 150);

    refMoInput.off('.createProjectSearch').on('input.createProjectSearch', handleMoInputChange);

    clientInput.off('.createProjectSearch').on('input.createProjectSearch', function () {
        handleInputChange($(this), clipoList, searchClipoUrl, 'Sin resultados encontrados', 'clients');
    });

    $('#project_type').off('.createProjectSearch').on('change.createProjectSearch', function () {
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

        if (type) {
            showLoadingSpinner(type);
        } else {
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
                removeAllLoadingSpinners();
            }
        });
    }

    function handleDataList(dataList, list, noResultsMessage, type) {
        if (currentPage === 1) {
            list.empty().show();
        }

        if (dataList && dataList.length) {
            const listItems = dataList.map(item => {
                console.log(item);
                let displayText = item.potential_customer_id
                    ? `${item.potential_customer_id} - ${item.name}`
                    : (item.name);
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
            console.log('handlelistitemclick type::', type);
            console.log('handlelistitemclick item::', item);
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
                const hasPhaseOptions = Array.isArray(item?.phases) && item.phases.length > 0;
                const hasStageOptions = Array.isArray(item?.stages) && item.stages.length > 0;
                const isPhaseProject = hasPhaseOptions || hasStageOptions || isProjectWithStagesAndPhases(item);
                const phaseWrapper = document.getElementById('phase-wrapper');
                const moWrapper = document.getElementById('mo-wrapper');
                const phaseSelect = document.getElementById('phase');
                const stageWrapper = document.getElementById('stage-wrapper');
                const stageSelect = document.getElementById('stage');

                if (!item.ref_mo) {

                    milestoneMoInput.prop('disabled', true);
                    milestoneMoInput.prop('required', false);
                } else {
                    milestoneMoInput.val('');
                    milestoneMoInput.prop('disabled', false);
                    milestoneMoInput.val(item.ref_mo).prop('readonly', true);
                    additionalForm.style.display = 'block';
                }

                if (phaseWrapper) {
                    phaseWrapper.style.display = isPhaseProject ? '' : 'none';
                }

                if (moWrapper) {
                    moWrapper.style.display = isPhaseProject ? 'none' : 'block';
                }

                if (phaseSelect) {
                    phaseSelect.required = isPhaseProject;
                    if (isPhaseProject) {
                        populatePhaseOptions(item.phases);
                    } else {
                        phaseSelect.innerHTML = '<option value="">Choose one</option>';
                        phaseSelect.value = '';
                    }
                }

                if (stageWrapper) {
                    stageWrapper.style.display = isPhaseProject ? '' : 'none';
                }

                if (stageSelect) {
                    if (isPhaseProject) {
                        populateStageOptions(item.stages);
                    } else {
                        stageSelect.innerHTML = '<option value="">Choose one</option>';
                    }
                }

                if (isPhaseProject) {
                    milestoneMoInput.val('');
                    milestoneMoInput.prop('required', false);
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

        // Deduplicar clientes basándose en el nombre único
        const uniqueClientsMap = new Map();
        selectedClients.forEach(client => {
            if (!uniqueClientsMap.has(client.name)) {
                uniqueClientsMap.set(client.name, client);
            }
        });

        const uniqueClients = Array.from(uniqueClientsMap.values());

        const clientItems = uniqueClients.map(client => {
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
        list.off('.createProjectSearch').on('scroll.createProjectSearch', function () {
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

    window.createProjectSearchCleanup = function () {
        projectInput.off('.createProjectSearch');
        projectList.off('.createProjectSearch');
        refMoInput.off('.createProjectSearch');
        refMoList.off('.createProjectSearch');
        clientInput.off('.createProjectSearch');
        clipoList.off('.createProjectSearch');
        salesManagerInput.off('.createProjectSearch');
        salesList.off('.createProjectSearch');
        $('#project_type').off('.createProjectSearch');

        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }

        removeAllLoadingSpinners();
    };
});