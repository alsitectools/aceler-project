<form class="" method="post" action="{{ route('projects.store', $currentWorkspace->slug) }}">
    @csrf
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label class="col-form-label">{{ __('Tipo de proyecto') }}</label>
                <select style="background-color:#AA182C; color:white;" class="form-control form-control-light"
                    name="project_type" id="project_type" required="">
                    <option style="background-color:white; color:black;"></option>
                    @foreach ($project_type as $type)
                        <option style="background-color:white; color:black;" value="{{ $type->id }}"
                            data-type="{{ $type->name }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="ref_mo" class="col-form-label">{{ __('Master Obra') }}</label>
                <input class="form-control" type="text" id="ref_mo" name="ref_mo"
                    placeholder="{{ __('Número master obra') }}">
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
        $('#project_type').change(function() {
            var selectedType = $(this).find('option:selected').data('type');
            var refMoInput = $('#ref_mo');
            var nameInput = $('#projectname');

            if (selectedType === 'Obra') {
                
                nameInput.val("");
                refMoInput.val("");
                refMoInput.prop('required', true);
                refMoInput.prop('disabled', false);
                $('#projectname').prop('readonly', true);

            } else {
                nameInput.val("");
                refMoInput.val("");
                refMoInput.prop('required', false);
                refMoInput.prop('disabled', true);
                $('#projectname').prop('readonly', false);
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
