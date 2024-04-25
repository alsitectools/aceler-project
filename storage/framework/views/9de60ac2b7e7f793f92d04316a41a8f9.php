<form class="" method="post" action="<?php echo e(route('projects.store', $currentWorkspace->slug)); ?>">
    <?php echo csrf_field(); ?>
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="ref_mo" class="col-form-label"><?php echo e(__('Master Obra')); ?></label>
                <input class="form-control" type="text" id="ref_mo" name="ref_mo"
                    placeholder="<?php echo e(__('Número master obra')); ?>">
            </div>

            <div id="projectType" class="form-group col-md-6">
                <label for="projectType" class="col-form-label"><?php echo e(__('Tipo de proyecto')); ?></label>
                <button id="opcionBtn" class="btn btn-primary dropdown-toggle col-md-12" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">Seleccionar Opción</button>
                <ul class="dropdown-menu">
                    <?php $__currentLoopData = $project_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="dropdown-item" id="type" name="type" data-id="<?php echo e($type->id); ?>">
                            <?php echo e($type->name); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label"><?php echo e(__('Name')); ?></label>
                <input class="form-control" type="text" id="projectname" name="projectname" required=""
                    placeholder="<?php echo e(__('Project Name')); ?>">
                <input class="form-control" type="text" id="name" name="name" required=""
                    placeholder="<?php echo e(__('Project Name')); ?>" style="display:none">
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
        <input type="submit" value="<?php echo e(__('Add New project')); ?>" class="btn btn-primary">
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var masterObras = {};
    <?php $__currentLoopData = $masterObras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterObra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        masterObras['<?php echo e($masterObra->ref_mo); ?>'] = '<?php echo e($masterObra->name); ?>';
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/create.blade.php ENDPATH**/ ?>