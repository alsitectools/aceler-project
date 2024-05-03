<form class="" method="post" action="<?php echo e(route('projects.store', $currentWorkspace->slug)); ?>">
    <?php echo csrf_field(); ?>
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label class="col-form-label"><?php echo e(__('Tipo de proyecto')); ?></label>
                <select style="background-color:#AA182C; color:white;" class="form-control form-control-light"
                    name="project_type" id="project_type" required="">
                    <option style="background-color:white; color:black;"></option>
                    <?php $__currentLoopData = $project_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option style="background-color:white; color:black;" value="<?php echo e($type->id); ?>"
                            data-type="<?php echo e($type->name); ?>"><?php echo e($type->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="ref_mo" class="col-form-label"><?php echo e(__('Master Obra')); ?></label>
                <input class="form-control" type="text" id="ref_mo" name="ref_mo"
                    placeholder="<?php echo e(__('Número master obra')); ?>">
                <span class="text-danger"></span>
            </div>
            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label"><?php echo e(__('Name')); ?></label>

                <input class="form-control" type="text" id="projectname" name="name" required=""
                    placeholder="<?php echo e(__('Project Name')); ?>">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
        <input type="submit" value="<?php echo e(__('Add New project')); ?>" class="btn btn-primary">
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
            var masterObras = <?php echo json_encode($masterObras, 15, 512) ?>;
            var projects = <?php echo json_encode($projects, 15, 512) ?>;

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
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/create.blade.php ENDPATH**/ ?>