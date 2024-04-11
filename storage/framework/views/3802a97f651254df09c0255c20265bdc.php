 <div class="modal-body">
<?php if($currentWorkspace && $milestone): ?>

    <div class="p-2">
        <div class="row mb-4">
            <div class="col-md-4">
                <div>
                    <div class="form-control-label"><?php echo e(__('Milestone Title')); ?></div>
                    <p class="mt-1"><?php echo e($milestone->title); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-control-label"><?php echo e(__('messages.Status')); ?></div>
                <p class="mt-1">
                    <?php if($milestone->status == 'incomplete'): ?>
                        <label class="badge bg-warning p-2 px-3 rounded"><?php echo e(__('Incomplete')); ?></label>
                    <?php endif; ?>
                    <?php if($milestone->status == 'complete'): ?>
                        <label class="badge bg-success p-2 px-3 rounded"><?php echo e(__('Complete')); ?></label>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="col-md-12">
                <div class="form-control-label"><?php echo e(__('Milestone Summary')); ?></div>
                <p class="mt-1"><?php echo e($milestone->summary); ?></p>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="page-error">
                    <div class="page-inner">
                        <h1>404</h1>
                        <div class="page-description">
                            <?php echo e(__('Page Not Found')); ?>

                        </div>
                        <div class="page-search">
                            <p class="text-muted mt-3"><?php echo e(__("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.")); ?></p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="<?php echo e(route('home')); ?>"><i class="fas fa-reply"></i> <?php echo e(__('Return Home')); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/milestoneShow.blade.php ENDPATH**/ ?>