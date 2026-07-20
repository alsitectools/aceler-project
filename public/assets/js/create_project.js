window.initCreateProjectSearch = function () {
    if (window.createProjectSearchCleanup) {
        window.createProjectSearchCleanup();
    }

    let projects = [];
    try {
        const form = document.getElementById('new-project-form');
        if (form && form.dataset.projects) {
            projects = JSON.parse(form.dataset.projects);
        }
    } catch (e) {
        projects = [];
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

        const addPhaseLabel = stageSelect.dataset.addPhaseLabel || 'Add phase';
        const addPhaseOption = document.createElement('option');
        addPhaseOption.value = 'add_phase';
        addPhaseOption.textContent = addPhaseLabel;
        stageSelect.appendChild(addPhaseOption);

        if (typeof window.initMilestoneStageAddPhaseToggle === 'function') {
            window.initMilestoneStageAddPhaseToggle();
        }
    }

    function hideAllLists(exceptList) {
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

    const debounce = (func, delay) => {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    };

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

    $(document).on('input.createProjectSearch', '#searchProject', function () {
        milestoneMoInput.val("");
        handleInputChange($(this), projectList, searchProjectsUrl, 'Sin resultados. El proyecto no ha sido creado.', 'projects');
    });

    $(document).on('input.createProjectSearch', '#searchSalesManager', function () {
        if ($(this).val().trim() === "") {
            salesList.empty().hide();
            return;
        }
        handleInputChange($(this), salesList, searchSalesManagerUrl, 'Sin resultados encontrados', 'salesManagers');
    });

    const handleMoInputChange = debounce(function () {
        clientInput.val("");
        project_nameInput.val("");
        $('#projectId').val('');
        handleInputChange(refMoInput, refMoList, searchMoUrl, 'Sin resultados encontrados', 'mo');
    }, 150);

    $(document).on('input.createProjectSearch', '#searchMo', handleMoInputChange);

    $(document).on('input.createProjectSearch', '#searchClipo', function () {
        handleInputChange($(this), clipoList, searchClipoUrl, 'Sin resultados encontrados', 'clients');
    });

    $(document).on('change.createProjectSearch', '#project_type', function () {
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
                    .data('item', item);
            });
            list.append(listItems);
            currentPage++;
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

    function populateMoList(obras) {
        refMoList.empty().show();

        const obraItems = obras.map(obra => {
            return $('<a href="#" class="list-group-item list-group-item-action stylelist">')
                .text(`${obra.ref_mo} - ${obra.name}`)
                .data('item', obra)
                .data('populate', 'mo');
        });

        refMoList.append(obraItems);
    }

    function populateClientList(selectedClients) {
        clipoList.empty().show();

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
                .data('item', client)
                .data('populate', 'clients');
        });
        clipoList.append(clientItems);
    }

    function setupInfiniteScroll(list, url, itemProcessor, noResultsMessage, type) {
        list.on('scroll.createProjectSearch', function () {
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

    $(document).on('click.createProjectSearch', '.list-group-item.stylelist', function (e) {
        e.preventDefault();
        const $this = $(this);
        const item = $this.data('item');
        const list = $this.closest('.list-group');
        const additionalForm = document.getElementById('visado');

        let type = '';
        if (list.is('#ref_mo_list')) type = 'mo';
        else if (list.is('#clipo_list')) type = 'clients';
        else if (list.is('#projects_list')) type = 'projects';
        else if (list.is('#sales_manager_list')) type = 'salesManagers';

        const populate = $this.data('populate');

        if (populate === 'mo') {
            refMoInput.val(item.ref_mo);
            project_nameInput.val(item.name);
            refMoList.empty().hide();
            return;
        }

        if (populate === 'clients') {
            clientInput.val(item.name);
            clipoList.empty().hide();
            return;
        }

        console.log('handlelistitemclick type::', type);
        console.log('handlelistitemclick item::', item);

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

        if (item.clients && item.clients.length) {
            populateClientList(item.clients);
        }
    });

    window.createProjectSearchCleanup = function () {
        $(document).off('.createProjectSearch');
        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }
        removeAllLoadingSpinners();
    };
};
