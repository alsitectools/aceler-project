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
                        <li><button class="dropdown-item"><?php echo e($type->name); ?></button></li>
                        <li><button class="type" style="display:none"></button></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label"><?php echo e(__('Name')); ?></label>
                <input class="form-control" type="text" id="projectname" name="name" required=""
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
        $('#ref_mo').on('input', function() {
            var moValue = $(this).val().trim();

            if (moValue === '') {
                $('#opcionBtn').text('Seleccionar Opción');
                $('#opcionBtn').prop('disabled', false);
                $('#projectname').val('');
                $('#projectname').prop('disabled', false);
                $('#name').val('');

            } else {
                $('#projectType .dropdown-menu .dropdown-item:first').trigger('click');
                $('#opcionBtn').prop('disabled', true);

                for (var ref_mo in masterObras) {
                    if (moValue == ref_mo) {

                        $('#projectname').val(masterObras[ref_mo]);
                        $('#name').val(masterObras[ref_mo]);
                        $('#ref_mo').val(ref_mo);
                        $('#projectname').prop('disabled', true);
                        break;
                    }
                }
            }
        });

        //================================ NO GUARDA EL PROYECTO CTM  los que son require no los recibe :(========================//

        // Evento para actualizar el texto del botón cuando se selecciona una opción del dropdown
        $('.dropdown-item').on('click', function() {
            var opcionSeleccionada = $(this).text();
            $('#opcionBtn').text(opcionSeleccionada);


            if (opcionSeleccionada === 'Obra') {
                var selectedOption = $(this).data('name');
                $('#ref_mo').prop('required', true);

            } else {
                $('#opcionBtn').prop('disabled', false);
                $('#ref_mo').prop('required', false);
                $('#projectname').val('');
                $('#type').val(opcionSeleccionada);
            }

        });
    });
</script>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/create.blade.php ENDPATH**/ ?>