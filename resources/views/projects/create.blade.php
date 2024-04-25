<form class="" method="post" action="{{ route('projects.store', $currentWorkspace->slug) }}">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="ref_mo" class="col-form-label">{{ __('Master Obra') }}</label>
                <input class="form-control" type="text" id="ref_mo" name="ref_mo"
                    placeholder="{{ __('Número master obra') }}">
            </div>

            <div class="form-group col-md-6">
                <label for="projectType" class="col-form-label">{{ __('Tipo de proyecto') }}</label>
                <button id="opcionBtn" class="btn btn-primary dropdown-toggle col-md-12" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">Seleccionar Opción</button>

                <ul class="dropdown-menu">

                    @foreach ($project_type as $type)
                        <input class="dropdown-item" id="type" name="type" data-id="{{ $type->id }}">
                        {{ $type->name }}

                        >
                    @endforeach
                </ul>
            </div>

            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label">{{ __('Name') }}</label>
                <input class="form-control" type="text" id="projectname" name="projectname" required=""
                    placeholder="{{ __('Project Name') }}">
                <input class="form-control" type="text" id="name" name="name" required=""
                    placeholder="{{ __('Project Name') }}" style="display:none">
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <input type="submit" value="{{ __('Add New project') }}" class="btn btn-primary">
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var masterObras = {};
    @foreach ($masterObras as $masterObra)
        masterObras['{{ $masterObra->ref_mo }}'] = '{{ $masterObra->name }}';
    @endforeach

    $(document).ready(function() {
        let typeId = $('#type').data('id');

        $('#ref_mo').on('input', function() {
            let moValue = $(this).val().trim();

            if (masterObras[moValue]) {
                $('#projectType .dropdown-menu .dropdown-item:first').trigger('click');
                $('#opcionBtn').prop('disabled', true);
                $('#projectname').val(masterObras[moValue]);
                $('#name').val(masterObras[moValue]);
                $('#ref_mo').val(moValue);
                $('#type').val(typeId);
                $('#projectname').prop('disabled', true);

            } else {
                $('#opcionBtn').text('Seleccionar Opción');
                $('#opcionBtn').prop('disabled', false);
                $('#projectname').prop('disabled', false);
                $('#name').val('');
            }
        });

        //un evento que solo mira el funcionamiento de menu deplegable
        $('.dropdown-item').on('click', function() {

            let opcionSeleccionada = $(this).text();
            $('#opcionBtn').text(opcionSeleccionada);

            if (opcionSeleccionada === 'Obra') {
                $('#ref_mo').prop('required', true);
                $('#projectname').val('');
                $('#name').val('');
                $('#optionBtn').val(opcionSeleccionada);
                $('#ref_mo').prop('disabled', false);
            } else {
                $('#optionBtn').val(opcionSeleccionada);
                $('#opcionBtn').prop('disabled', false);
                $('#opcionBtn').prop('disabled', false);
                $('#ref_mo').prop('disabled', true);
            }
        });
    });
</script>
