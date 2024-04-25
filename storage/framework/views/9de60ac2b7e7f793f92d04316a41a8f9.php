<form class="" method="post" action="<?php echo e(route('projects.store',$currentWorkspace->slug)); ?>">
    <?php echo csrf_field(); ?>
     <div class="modal-body">
    <div class="row">
        <?php if($currentWorkspace->is_chagpt_enable()): ?>
        <div class="text-end col-12">
            <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary" data-url="<?php echo e(route('generate',['project'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Generate with AI')); ?>" data-title="<?php echo e(__('Generate Project Name & Description')); ?>">
                <i class="fas fa-robot px-1"></i><?php echo e(__('Generate with AI')); ?></a>
        </div>
        <?php endif; ?>
        <div class="form-group col-md-12">
            <label for="projectname" class="col-form-label"><?php echo e(__('Name')); ?></label>
            <input class="form-control" type="text" id="projectname" name="name" required="" placeholder="<?php echo e(__('Project Name')); ?>">
        </div>
        <div class="form-group col-md-12">
            <label for="description" class="col-form-label"><?php echo e(__('Description')); ?></label>
            <textarea rows="3" class="form-control" id="description" name="description" required="" placeholder="<?php echo e(__('Add Description')); ?>"></textarea>
        </div>
        <div class="col-md-12">
            <label for="users_list" class="col-form-label"><?php echo e(__('Users')); ?></label>
            <select class=" multi-select" id="users_list" name="users_list[]" data-toggle="select2" multiple="multiple" data-placeholder="<?php echo e(__('Select Users ...')); ?>">
                <?php $__currentLoopData = $currentWorkspace->users($currentWorkspace->created_by); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->email); ?>"><?php echo e($user->name); ?> - <?php echo e($user->email); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
</div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
            <input type="submit" value="<?php echo e(__('Add New project')); ?>" class="btn  btn-primary">
        </div>
    
</form>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/create.blade.php ENDPATH**/ ?>