<form class="" method="post" action="{{ route('projects.store', $currentWorkspace->slug) }}">
    @csrf
    <div class="modal-body">
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
                <label for="searchMo" class="col-form-label">{{ __('Reference M.O') }}</label>
                <input type="text" class="form-control" name="ref_mo" id="searchMo"
                    placeholder="{{ __('Reference') }}" list="ref_mo_list">
                <datalist id="ref_mo_list"></datalist>
                <span class="text-danger"></span>
            </div>

            <div class="form-group col-md-6" id="clipo" style="display: none">
                <label for="clipo" class="col-form-label">{{ __('Clipo') }}</label>
                <input class="form-control" type="text" name="clipo" placeholder="{{ __('Clipo') }}">
                <span class="text-danger"></span>
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
        $('#searchMo').on('input', function() {
            const query = $(this).val().trim();

            // Si el input está vacío, limpiar el datalist y salir
            if (query === '') {
                $('#ref_mo_list').empty();
                return;
            }

            // Construir la URL para hacer la búsqueda
            const url = '{{ route('search-mo-json', '__slug') }}'.replace('__slug', '{{ $currentWorkspace->slug }}') + '/' + encodeURIComponent(query);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    const refMoList = $('#ref_mo_list');
                    refMoList.empty(); 

                    if (data.mo && data.mo.length > 0) {
                        data.mo.forEach(item => {
                            const option = $('<option></option>');
                            option.attr('value', item.ref_mo);
                            option.data('name', item.name);

                            refMoList.append(option);
                        });
                    } else {
                        console.log("No se encontraron resultados.");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error en la solicitud:', textStatus, errorThrown); 
                }
            });
        });

        $('#searchMo').on('change', function() {
            const selectedRefMo = $(this).val(); 

            $('#ref_mo_list option').each(function() {
                if ($(this).val() === selectedRefMo) {
                    $('#projectname').val($(this).data('name'));
                }
            });
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchMo').length) {
                $('#ref_mo_list').empty(); 
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#project_type').change(function() {
            let selectedType = $(this).find('option:selected').data('type');
            let refMoInput = $('#ref_mo input');
            let nameInput = $('#projectname');
            let clientInput = $('#clipo input');

            nameInput.val("");
            refMoInput.val("");
            clientInput.val("");

            if (selectedType === 'Jobsite') {
                refMoInput.prop('required', true);
                refMoInput.prop('disabled', false);
                nameInput.prop('readonly', true);

                $('#ref_mo').show();
                $('#clipo').show();
            } else {
                refMoInput.prop('required', false);
                refMoInput.prop('disabled', true);
                nameInput.prop('readonly', false);

                $('#ref_mo').hide();
                $('#clipo').hide();
            }
        });

        $('#ref_mo').change(function() {
            var refMo = $(this).val();
            var masterObras = @json($masterObras);
            var projects = @json($projects);

            var existingMasterObra = masterObras.find(function(masterObra) {
                return masterObra.ref_mo === refMo;
            });
            var existingProject = projects.find(function(project) {
                return project.ref_mo === refMo;
            });

            if (!existingProject) {
                if (existingMasterObra) {
                    $('.text-danger').text("");
                    $('#projectname').prop('readonly', true);
                    $('#projectType').val();
                    $('#projectname').val(existingMasterObra.name);
                    $('#name').val(existingMasterObra.name);
                    $('#ref_mo').val(refMo);
                }
            } else {
                $('.text-danger').text('El número de referencia ya existe.');
                setTimeout(function() {
                    $('.text-danger').text("");
                }, 5000);
            }
        });
    });
</script>
