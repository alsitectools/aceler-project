<style>
    #ref_mo_list,
    #clipo_list {
        max-height: 230px;
        overflow: hidden;
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

            <div class="form-group col-md-6" id="clipo" style="display: none">
                <label for="clipo" class="col-form-label">{{ __('Clipo') }}</label>
                <input class="form-control" type="text" name="clipo" id="searchClipo"
                    placeholder="{{ __('Clipo') }}">
                <div class="list-group" id="clipo_list"></div>
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

            // Cancelar la solicitud AJAX anterior si existe
            if (currentRequest) {
                currentRequest.abort();
            }

            loading = true; // Establecer la bandera de carga
            currentRequest = $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (page === 1) {
                        refMoList.empty().show(); // Limpiar la lista al buscar nuevo contenido
                    }

                    // Si hay resultados, los agregamos a la lista
                    if (data.mo && data.mo.length) {
                        data.mo.forEach(item => {
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


                                    // Mostrar clientes si hay más de uno
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

                        currentPage++; // Incrementar la página actual para el próximo scroll
                    } else {
                        if (page === 1) {
                            refMoList.append('<p>No results found</p>');
                        }
                    }

                    loading = false; // Restablecer la bandera de carga
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loading = false; // Restablecer la bandera de carga
                }
            });
        }


        // Buscar Master Obra por referencia al escribir
        refMoInput.on('input', function() {
            searchQuery = $(this).val().trim();
            currentPage = 1;
            refMoList.empty();

            // Limpiar los inputs relacionados al modificar la búsqueda
            nameInput.val('');
            clientInput.val('');


            if (searchQuery) {
                fetchMoData(searchQuery, currentPage);
            }
        });

        // Scroll infinito
        refMoList.on('scroll', function() {

            const scrollTop = refMoList[0].scrollTop;
            const scrollHeight = refMoList[0].scrollHeight;
            const innerHeight = refMoList.innerHeight();

            if (!loading && (scrollTop + innerHeight >= scrollHeight -
                    10)) {
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
                        nameInput.val($(this).data('name'));;
                    }
                });
            } else if (inputId === 'searchClipo') {
                clipoList.find('a').each(function() {
                    if ($(this).data('name') === selectedValue) {
                        clientInput.val($(this).data('name'));
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
