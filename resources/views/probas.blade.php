{{------------- en funcionamiento ------- --}}

<script>
    $(document).ready(function() {

        const refMoInput = $('#searchMo');
        const nameInput = $('#projectname');
        const clientInput = $('#searchClipo');
        const refMoList = $('#ref_mo_list');
        const clipoList = $('#clipo_list');

        let currentPage = 1;
        let currentRequest = null;
        let loading = false;
        let searchQuery = '';

        // Maneja el cambio de tipo de proyecto
        $('#project_type').change(function() {
            const selectedType = $(this).find('option:selected').data('type');
            const isJobsite = (selectedType === 'Jobsite');

            console.log('Tipo de proyecto seleccionado:', selectedType); // Depuración

            // Reset inputs
            resetInputs(isJobsite);
            $('#ref_mo, #clipo').toggle(isJobsite);
        });

        function resetInputs(isJobsite) {
            nameInput.val("");
            refMoInput.val("");
            clientInput.val("");
            refMoInput.prop('required', isJobsite).prop('disabled', !isJobsite);
            nameInput.prop('readonly', isJobsite);
        }

        // Función para realizar la búsqueda con paginación
        function fetchData(url, list, itemProcessor, noResultsMessage) {

            if (currentRequest) {
                currentRequest.abort();
            }

            loading = true;
            currentRequest = $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    loading = false;
                    const itemData = itemProcessor(data);
                    handleDataList(itemData, list, noResultsMessage);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loading = false;
                    console.error('Error en la solicitud:', textStatus, errorThrown);
                }
            });
        }

        function handleDataList(dataList, list, noResultsMessage) {
            if (currentPage === 1) {
                list.empty().show();
            }

            if (dataList && dataList.length) {
                dataList.forEach(item => {
                    $('<a href="#" class="list-group-item list-group-item-action">')
                        .text(`${item.ref_mo || item.potential_customer_id} - ${item.name}`)
                        .data('item', item)
                        .appendTo(list)
                        .on('click', handleListItemClick(item, list));
                });
                currentPage++;
            } else if (currentPage === 1) {
                list.append(`<p class="text-danger">${noResultsMessage}</p>`);
            }
        }

        function handleListItemClick(item, list) {
            return function(e) {
                e.preventDefault();
                const selectedClients = item.clients;

                if (item.ref_mo) {
                    refMoInput.val(item.ref_mo);
                    nameInput.val(item.name);
                } else {
                    clientInput.val(item.name);
                }
                list.empty().hide();

                if (selectedClients && selectedClients.length) {
                    populateClientList(selectedClients);
                }
            };
        }

        function populateClientList(selectedClients) {
            clipoList.empty();
            selectedClients.forEach(client => {
                $('<a href="#" class="list-group-item list-group-item-action">')
                    .text(client.name)
                    .data('name', client.name)
                    .appendTo(clipoList)
                    .on('click', function(e) {
                        e.preventDefault();
                        clientInput.val(client.name);
                        clipoList.empty().hide();
                    });
            });
            clipoList.show();
        }

        // Buscar Master Obra por referencia al escribir
        refMoInput.on('input', function() {
            resetSearchFields();
            searchQuery = $(this).val().trim();
            currentPage = 1;

            if (searchQuery) {
                const url = '{{ route('search-mo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchQuery) +
                    '?page=' + currentPage;
                fetchData(url, refMoList, data => data.mo.data, 'No results found');
            }
        });

        clientInput.on('input', function() {
            const searchClient = $(this).val().trim();
            currentPage = 1;
            clipoList.empty();

            if (searchClient) {
                const url = '{{ route('search-clipo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchClient) +
                    '?page=' + currentPage;
                fetchData(url, clipoList, data => data.clients.data, 'No results found');
            }
        });

        function resetSearchFields() {
            nameInput.val('');
            clientInput.val('');
            clipoList.empty();
            refMoList.empty();
        }

        // Scroll infinito
        refMoList.on('scroll', function() {
            const scrollTop = refMoList[0].scrollTop;
            const scrollHeight = refMoList[0].scrollHeight;
            const innerHeight = refMoList.innerHeight();

            if (!loading && (scrollTop + innerHeight >= scrollHeight - 10)) {
                console.log('Al final del scroll, cargando más resultados...');
                fetchData('{{ route('search-mo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchQuery) +
                    '?page=' + currentPage, refMoList, data => data.mo.data, 'No results found');
            }
        });

        // Manejar selección de MO o Cliente
        $('#searchMo, #searchClipo').on('change', function() {
            const inputId = $(this).attr('id');
            const selectedValue = $(this).val();
            const projects = @json($projects);

            const existingProject = projects.find(project => project.ref_mo === selectedValue);

            if (existingProject) {
                showAlert('El número de referencia ya existe.');
                resetSearchFields();
                return;
            }

            if (inputId === 'searchMo') {
                updateNameInputFromList(refMoList, selectedValue);
            } else if (inputId === 'searchClipo') {
                updateClientInputFromList(clipoList, selectedValue);
            }
        });

        function showAlert(message) {
            $('.text-danger').text(message);
            setTimeout(() => {
                $('.text-danger').text("");
            }, 5000);
        }

        function updateNameInputFromList(list, selectedValue) {
            list.find('a').each(function() {
                const item = $(this).data('item');
                if (item.ref_mo === selectedValue) {
                    nameInput.val(item.name);
                }
            });
        }

        function updateClientInputFromList(list, selectedValue) {
            list.find('a').each(function() {
                const item = $(this).data('item');
                if (item.name === selectedValue) {
                    clientInput.val(item.name);
                } else {
                    clientInput.val('');
                    clipoList.empty().hide();
                }
            });
        }

        // Cerrar listas cuando se hace click fuera de las mismas
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchMo').length) {
                refMoList.empty().hide();
            }

        });
    });
</script>

{{-- --------------------------------------------------------------------- --}}
<script>
    $(document).ready(function() {
        const refMoInput = $('#searchMo');
        const nameInput = $('#projectname');
        const clientInput = $('#searchClipo');
        const refMoList = $('#ref_mo_list');
        const clipoList = $('#clipo_list');

        let currentPage = 1;
        let currentRequest = null;
        let loading = false;
        let searchQuery = '';

        // Maneja el cambio de tipo de proyecto
        $('#project_type').change(function() {
            const selectedType = $(this).find('option:selected').data('type');
            const isJobsite = (selectedType === 'Jobsite');

            // Reset inputs
            resetInputs(isJobsite);
            $('#ref_mo, #clipo').toggle(isJobsite);
        });

        function resetInputs(isJobsite) {
            nameInput.val("");
            refMoInput.val("");
            clientInput.val("");
            refMoInput.prop('required', isJobsite).prop('disabled', !isJobsite);
            nameInput.prop('readonly', isJobsite);
        }

        // Función para realizar la búsqueda con paginación
        function fetchData(url, list, itemProcessor, noResultsMessage) {
            if (currentRequest) {
                currentRequest.abort();
            }

            loading = true;
            currentRequest = $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    loading = false;
                    const itemData = itemProcessor(data);
                    handleDataList(itemData, list, noResultsMessage);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loading = false;
                    console.error('Error en la solicitud:', textStatus, errorThrown);
                }
            });
        }

        function handleDataList(dataList, list, noResultsMessage) {
            resetSearchFields();
            if (currentPage === 1) {
                list.empty().show();
            }

            if (dataList && dataList.length) {
                dataList.forEach(item => {
                    $('<a href="#" class="list-group-item list-group-item-action">')
                        .text(`${item.ref_mo || item.potential_customer_id} - ${item.name}`)
                        .data('item', item)
                        .appendTo(list)
                        .on('click', handleListItemClick(item, list));
                });
                currentPage++;
            } else if (currentPage === 1) {
                list.append(`<p class="text-danger">${noResultsMessage}</p>`);
            }
        }

        function handleListItemClick(item, list) {
            return function(e) {
                e.preventDefault();
                const selectedClients = item.clients;

                if (item.ref_mo) {
                    refMoInput.val(item.ref_mo);
                    nameInput.val(item.name);
                } else {
                    clientInput.val(item.name);
                }
                list.empty().hide();

                if (selectedClients && selectedClients.length) {
                    populateClientList(selectedClients);
                }
            };
        }

        function populateClientList(selectedClients) {
            clipoList.empty();
            selectedClients.forEach(client => {
                $('<a href="#" class="list-group-item list-group-item-action">')
                    .text(client.name)
                    .data('name', client.name)
                    .appendTo(clipoList)
                    .on('click', function(e) {
                        e.preventDefault();
                        clientInput.val(client.name);
                        clipoList.empty().hide();
                    });
            });
            clipoList.show();
        }

        // Buscar Master Obra por referencia al escribir
        refMoInput.on('input', function() {

            resetSearchFields();
            searchQuery = $(this).val().trim();
            currentPage = 1;

            if (!searchQuery) {
                refMoList.empty().hide();
                return;
            }

            const url = '{{ route('search-mo-json', '__slug') }}'.replace('__slug',
                    '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchQuery) +
                '?page=' + currentPage;
            fetchData(url, refMoList, data => data.mo.data, 'No results found');
        });

        clientInput.on('input', function() {
            const searchClient = $(this).val().trim();
            currentPage = 1;

            // Si el campo está vacío, ocultar la lista de clientes y limpiar
            if (!searchClient) {
                clipoList.empty().hide(); // Ocultar la lista
                return;
            }

            clipoList.empty();
            const url = '{{ route('search-clipo-json', '__slug') }}'.replace('__slug',
                    '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchClient) +
                '?page=' + currentPage;
            fetchData(url, clipoList, data => data.clients.data, 'No results found');
        });

        function resetSearchFields() {
            nameInput.val('');
            clientInput.val('');
            clipoList.empty();
            refMoList.empty();
        }

        // Scroll infinito
        refMoList.on('scroll', function() {
            const scrollTop = refMoList[0].scrollTop;
            const scrollHeight = refMoList[0].scrollHeight;
            const innerHeight = refMoList.innerHeight();

            if (!loading && (scrollTop + innerHeight >= scrollHeight - 10)) {
                console.log('Al final del scroll, cargando más resultados...');
                fetchData('{{ route('search-mo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchQuery) +
                    '?page=' + currentPage, refMoList, data => data.mo.data, 'No results found');
            }
        });

        // Manejar selección de MO o Cliente
        $('#searchMo, #searchClipo').on('change', function() {
            const inputId = $(this).attr('id');
            const selectedValue = $(this).val();
            const projects = @json($projects);

            const existingProject = projects.find(project => project.ref_mo === selectedValue);

            if (existingProject) {
                showAlert('El número de referencia ya existe.');
                resetSearchFields();
                return;
            }

            if (inputId === 'searchMo') {
                updateNameInputFromList(refMoList, selectedValue);
            } else if (inputId === 'searchClipo') {
                updateClientInputFromList(clipoList, selectedValue);
            }
        });

        function showAlert(message) {
            $('.text-danger').text(message);
            setTimeout(() => {
                $('.text-danger').text("");
            }, 5000);
        }

        function updateNameInputFromList(list, selectedValue) {
            list.find('a').each(function() {
                const item = $(this).data('item');
                if (item.ref_mo === selectedValue) {
                    nameInput.val(item.name);
                }
            });
        }

        function updateClientInputFromList(list, selectedValue) {
            list.find('a').each(function() {
                const item = $(this).data('item');
                if (item.name === selectedValue) {
                    clientInput.val(item.name);
                } else {
                    clientInput.val('');
                    clipoList.empty().hide();
                }
            });
        }

        // Cerrar listas cuando se hace click fuera de las mismas
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchMo').length) {
                refMoList.empty().hide();
            }
        });
    });
</script>


{{-- -------------------------------------------------------------------------------------------------- --}}

<script>
    $(document).ready(function() {
        const refMoInput = $('#searchMo');
        const nameInput = $('#projectname');
        const clientInput = $('#searchClipo');
        const refMoList = $('#ref_mo_list');
        const clipoList = $('#clipo_list');
        let currentPage = 1;
        let currentRequest = null;
        let loading = false;
        let searchQuery = '';

        // Maneja el cambio de tipo de proyecto
        $('#project_type').change(function() {
            const selectedType = $(this).find('option:selected').data('type');
            const isJobsite = (selectedType === 'Jobsite');

            console.log('Tipo de proyecto seleccionado:', selectedType); // Depuración

            // Reset inputs
            nameInput.val("");
            refMoInput.val("");
            clientInput.val("");

            refMoInput.prop('required', isJobsite).prop('disabled', !isJobsite);
            nameInput.prop('readonly', isJobsite);
            $('#ref_mo, #clipo').toggle(isJobsite);
        });

        // Función para realizar la búsqueda con paginación
        function fetchMoData(query, page = 1) {
            const url = '{{ route('search-mo-json', '__slug') }}'.replace('__slug',
                '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(query) + '?page=' + page;

            if (currentRequest) {
                currentRequest.abort();
            }

            loading = true;
            currentRequest = $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {

                    const moData = data.mo.data;
                    if (page === 1) {
                        refMoList.empty().show();
                    }
                    if (moData && moData.length) {
                        moData.forEach(item => {
                            $('<a href="#" class="list-group-item list-group-item-action">')
                                .text(`${item.ref_mo} - ${item.name}`)
                                .data('ref_mo', item.ref_mo)
                                .data('clients', item.clients)
                                .appendTo(refMoList)
                                .on('click', function(e) {
                                    e.preventDefault();
                                    const selectedClients = $(this).data('clients');

                                    refMoInput.val(item.ref_mo);
                                    nameInput.val(item.name);
                                    refMoList.empty().hide();

                                    clipoList.empty();
                                    if (selectedClients.length > 1) {
                                        selectedClients.forEach(client => {
                                            $('<a href="#" class="list-group-item list-group-item-action">')
                                                .text(client.name)
                                                .data('name', client.name)
                                                .appendTo(clipoList)
                                                .on('click', function(e) {
                                                    e.preventDefault();
                                                    clientInput.val(client
                                                        .name);
                                                    clipoList.empty()
                                                        .hide();
                                                });
                                        });
                                        clipoList.show();
                                    } else if (selectedClients.length === 1) {
                                        clientInput.val(selectedClients[0].name);
                                        clipoList.hide();
                                    }
                                });
                        });

                        currentPage++;
                    } else {
                        if (page === 1) {
                            refMoList.append('<p>No results found</p>');
                        }
                    }

                    loading = false;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loading = false;
                }
            });
        }

        // Función para realizar la búsqueda con paginación
        function fetchClipoData(query, page = 1) {
            const url = '{{ route('search-clipo-json', '__slug') }}'.replace('__slug',
                '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(query) + '?page=' + page;

            if (currentRequest) {
                currentRequest.abort();
            }

            loading = true;
            currentRequest = $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {

                    const clientData = data.clients.data;
                    if (page === 1) {
                        clipoList.empty().show();
                    }

                    if (clientData && clientData.length) {
                        clientData.forEach(item => {
                            $('<a href="#" class="list-group-item list-group-item-action">')
                                .text(`${item.potential_customer_id} - ${item.name}`)
                                .data('cli_po', item.potential_customer_id)
                                .appendTo(clipoList)
                                .on('click', function(e) {
                                    e.preventDefault();
                                    const selectedClients = $(this).data('clients');

                                    clientInput.val(item.name);
                                    clipoList.empty().hide();

                                    clipoList.empty();
                                });
                        });

                        currentPage++;
                    } else {
                        if (page === 1) {
                            refMoList.append('<p>No results found</p>');
                        }
                    }

                    loading = false;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loading = false;
                }
            });
        }

        // Buscar Master Obra por referencia al escribir
        refMoInput.on('input', function() {
            nameInput.val('');
            clientInput.val('');

            clipoList.empty();
            refMoList.empty();

            searchQuery = $(this).val().trim();
            currentPage = 1;


            if (searchQuery) {
                fetchMoData(searchQuery, currentPage);
            }
        });

        clientInput.on('input', function() {
            searchClient = $(this).val().trim();
            currentPage = 1;
            clipoList.empty();

            fetchClipoData(searchClient, currentPage);
        });

        // Scroll infinito
        refMoList.on('scroll', function() {
            // Comprobar si el scroll está al final de la lista
            const scrollTop = refMoList[0].scrollTop;
            const scrollHeight = refMoList[0].scrollHeight;
            const innerHeight = refMoList.innerHeight();

            if (!loading && (scrollTop + innerHeight >= scrollHeight -
                    10)) { // Un pequeño margen de 10px
                console.log('Al final del scroll, cargando más resultados...');
                fetchMoData(searchQuery, currentPage);
            }
        });


        // Manejar selección de MO o Cliente
        $('#searchMo, #searchClipo').on('change', function() {
            const inputId = $(this).attr('id');
            const selectedValue = $(this).val();
            const projects = @json($projects);

            const existingProject = projects.find(function(project) {
                return project.ref_mo === selectedValue;
            });

            // Si el proyecto ya existe, mostrar alerta y limpiar campos
            if (existingProject) {
                $('.text-danger').text('El número de referencia ya existe.');
                setTimeout(function() {
                    $('.text-danger').text("");
                }, 5000);

                // Limpiar los inputs y las listas
                nameInput.val('');
                clientInput.val('');
                refMoList.empty().hide();
                clipoList.empty().hide();

                return;
            }

            // Si es searchMo, buscar el nombre correspondiente en la lista
            if (inputId === 'searchMo') {
                refMoList.find('a').each(function() {
                    if ($(this).data('ref_mo') === selectedValue) {
                        nameInput.val($(this).data('name'));
                    }
                });
            } else if (inputId === 'searchClipo') {
                clipoList.find('a').each(function() {
                    if ($(this).data('name') === selectedValue) {
                        clientInput.val($(this).data('name'));
                    } else {
                        clientInput.val('');
                        clipoList.empty().hide();
                    }
                });
            }
        });

        // Cerrar listas cuando se hace click fuera de las mismas
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchMo').length) {
                refMoList.empty().hide();
            }

        });
    });
</script>
