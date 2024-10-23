<style>
    #ref_mo_list,
    #clipo_list {
        max-height: 230px;
        overflow-y: auto;
        position: absolute;
        max-width: 215px;

    }

    #ref_mo_list::-webkit-scrollbar,
    #clipo_list::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }
</style>
<form class="" method="post" action="{{ route('projects.store', $currentWorkspace->slug) }}">
    @csrf
    <div class="modal-body modal-xl">
        <div class="row">

            <div class="form-group col-md-12">
                <label class="col-form-label">{{ __('Project type') }}</label>
                <select class="form-control form-control-light" name="project_type" id="project_type" required="">
                    <option selected disabled>{{ __('Choose one') }}</option>
                    @foreach ($project_type as $type)
                        <option style="background-color:white; color:black;" value="{{ $type->id }}"
                            data-type="{{ $type->name }}"> {{ __($type->name) }} </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6" id="ref_mo" style="display: none;">
                <label for="search_mo" class="col-form-label">{{ __('Search MO') }}</label>
                <input type="text" class="form-control" name="ref_mo" id="searchMo"
                    placeholder="{{ __('Reference') }}">
                <div class="list-group" id="ref_mo_list"></div>
                <span class="text-danger"></span>
            </div>

            <div class="form-group col-md-6" id="clipo" style="display: none;">
                <label for="searchClipo" class="col-form-label">{{ __('Clipo') }}</label>
                <input class="form-control" type="text" name="clipo" id="searchClipo"
                    placeholder="{{ __('Clipo') }}">
                <div class="list-group" style="display: none;" id="clipo_list"></div>
            </div>


            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label">{{ __('Name') }}</label>
                <input class="form-control" type="text" id="projectname" name="name" required=""
                    placeholder="{{ __('Project Name') }}">
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <input type="submit" value="{{ __('Add New project') }}" class="btn btn-primary">
    </div>
</form>
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

        function fetchData(url, list, itemProcessor, noResultsMessage, isClipo = false) {
            if (currentRequest) {
                currentRequest.abort();
            }

            loading = true;

            // Crear y mostrar el icono de carga
            let loadingSpinner;

            if (isClipo) {
                loadingSpinner = $(
                    '<div class="spinner-grow spinner-grow-sm me-2 ms-2" style="width: 15px; height: 15px; color: rgb(152, 151, 151);" role="status"></div>'
                );
                $('#clipo label').after(loadingSpinner);
            } else {
                loadingSpinner = $(
                    '<div class="spinner-grow spinner-grow-sm me-2 ms-2" style="width: 15px; height: 15px; color: rgb(152, 151, 151);" role="status"></div>'
                );
                $('#ref_mo label').after(loadingSpinner);
            }

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
                },
                complete: function() {

                    // Ocultar y eliminar el icono de carga
                    loadingSpinner.remove();
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

                const projects = @json($projects);
                const existingProject = projects.find(project => project.ref_mo === item.ref_mo);

                if (existingProject) {
                    showAlert('El número de referencia ya existe.');
                    resetSearchFields();
                    return;
                }

                if (item.ref_mo) {
                    refMoInput.val(item.ref_mo);
                    nameInput.val(item.name);
                } else {
                    clientInput.val(item.name);
                }

                list.empty().hide();

                const selectedClients = item.clients;

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

            // Si el campo está vacío, ocultar la lista
            if (!searchQuery) {
                refMoList.empty().hide();
            } else {
                const url = '{{ route('search-mo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchQuery) +
                    '?page=' + currentPage;
                fetchData(url, refMoList, data => data.mo.data, 'No results found');
            }
        });

        // Buscar Cliente al escribir
        clientInput.on('input', function() {
            const searchClient = $(this).val().trim();
            currentPage = 1;

            // Si el campo está vacío, ocultar la lista de clientes y limpiar
            if (!searchClient) {
                clipoList.empty().hide();
            } else {
                const url = '{{ route('search-clipo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchClient) +
                    '?page=' + currentPage;
                fetchData(url, clipoList, data => data.clients.data, 'No results found',
                    true);
            }
        });

        function resetSearchFields() {
            nameInput.val('');
            clientInput.val('');
            clipoList.empty();
            refMoList.empty();
        }

        // Scroll infinito mo
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

        // Scroll infinito clients
        clipoList.on('scroll', function() {
            const scrollTop = clipoList[0].scrollTop;
            const scrollHeight = clipoList[0].scrollHeight;
            const innerHeight = clipoList.innerHeight();

            if (!loading && (scrollTop + innerHeight >= scrollHeight - 10)) {
                console.log('Al final del scroll, cargando más resultados...');
                fetchData('{{ route('search-clipo-json', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(searchQuery) +
                    '?page=' + currentPage, clipoList, data => data.clients.data, 'No results found'
                );
            }
        });

        // Manejar selección de MO o Cliente
        $('#searchMo, #searchClipo').on('change', function() {
            const inputId = $(this).attr('id');
            const selectedValue = $(this).val();

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
                if (selectedValue) {
                    clientInput.val(item.name);
                } else {

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
