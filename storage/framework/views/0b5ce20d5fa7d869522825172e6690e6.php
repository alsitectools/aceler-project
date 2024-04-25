<div class="modal-body">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-4 text-center">
                            <h6><?php echo e('Todos los espacios de trabajo'); ?></h6>
                            <p class=" text-sm mb-0">
                                <i class="ti ti-users text-warning card-icon-text-space fs-5 mx-1"></i><span
                                    class="total_workspace fs-5">
                                    <?php echo e($workspce_data['total_workspace']); ?></span>
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <h6><?php echo e('Espacio de trabajo activo'); ?></h6>
                            <p class=" text-sm mb-0">
                                <i class="ti ti-users text-primary card-icon-text-space fs-5 mx-1"></i><span
                                    class="active_workspace fs-5"><?php echo e($workspce_data['active_workspace']); ?></span>
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <h6><?php echo e('Espacio de trabajo desactivado'); ?></h6>
                            <p class=" text-sm mb-0">
                                <i class="ti ti-users text-danger card-icon-text-space fs-5 mx-1"></i><span
                                    class="disable_workspace fs-5"><?php echo e($workspce_data['disable_workspace']); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-10 col-xxl-12 col-md-12">
                <div class="p-3 card m-0">
                    <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                        <?php $__currentLoopData = $users_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $workspace = \App\Models\Workspace::where('id', $user_data['workspace_id'])->first();
                            ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-capitalize <?php echo e($loop->index == 0 ? 'active' : ''); ?>"
                                    id="pills-<?php echo e(strtolower($workspace->id)); ?>-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-<?php echo e(strtolower($workspace->id)); ?>"
                                    type="button"><?php echo e($workspace->name); ?></button>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="px-0 card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <?php $__currentLoopData = $users_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php
                                $users = \App\Models\UserWorkspace::where('workspace_id', $user_data['workspace_id'])
                                    ->where('permission', '!=', 'Owner')
                                    ->get();
                                $workspace = \App\Models\Workspace::where('id', $user_data['workspace_id'])->first();
                            ?>
                            <div class="tab-pane text-capitalize fade show <?php echo e($loop->index == 0 ? 'active' : ''); ?>"
                                id="pills-<?php echo e(strtolower($workspace->id)); ?>" role="tabpanel"
                                aria-labelledby="pills-<?php echo e(strtolower($workspace->id)); ?>-tab">

                                <div class="row">
                                    <div class="col-lg-11 col-md-10 col-sm-10 mt-3 text-end">
                                        <small
                                            class="text-danger my-3"><?php echo e(__('* Asegúrese de que si desactiva el espacio de trabajo, todos los usuarios de este espacio de trabajo también estén desactivados.')); ?></small>

                                    </div>
                                    <div class="col-lg-1 col-md-2 col-sm-2 text-end">
                                        <div class="text-end">
                                            <div class="form-check form-switch custom-switch-v1 mt-3">
                                                <input type="checkbox" name="workspace_disable"
                                                    class="form-check-input input-primary is_disable" value="1"
                                                    data-id="<?php echo e($user_data['workspace_id']); ?>"
                                                    data-company="<?php echo e($id); ?>"
                                                    data-name="<?php echo e(__('workspace')); ?>"
                                                    <?php echo e($workspace->is_active == 1 ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="workspace_disable"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row workspace" data-workspace-id=<?php echo e($workspace->id); ?>>
                                    <div class="col-4 text-center">
                                        <p class="text-sm mb-0" data-toggle="tooltip"
                                            data-bs-original-title="<?php echo e(__('Total tecnicos')); ?>"><i
                                                class="ti ti-users text-warning card-icon-text-space fs-5 mx-1"></i><span
                                                class="total_users fs-5"><?php echo e($user_data['total_users']); ?></span>

                                        </p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <p class="text-sm mb-0" data-toggle="tooltip"
                                            data-bs-original-title="<?php echo e(__('Tecnicos activos')); ?>"><i
                                                class="ti ti-users text-primary card-icon-text-space fs-5 mx-1"></i><span
                                                class="active_users fs-5"><?php echo e($user_data['active_users']); ?></span>
                                        </p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <p class="text-sm mb-0" data-toggle="tooltip"
                                            data-bs-original-title="<?php echo e(__('Tecnicos desactivados')); ?>"><i
                                                class="ti ti-users text-danger card-icon-text-space fs-5 mx-1"></i><span
                                                class="disable_users fs-5"><?php echo e($user_data['disable_users']); ?></span>
                                        </p>
                                    </div>
                                </div>

                                <?php if($user_data['total_users'] == 0): ?>
                                    <div class="card mt-4">
                                        <div class="card-body d-flex align-items-center justify-content-center">
                                            <p class="text-danger">Users Not Found!!!</p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="row my-2 " id="user_section_<?php echo e($workspace->id); ?>">
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        
                                        
                                        <?php
                                            $userInfo = \App\Models\User::where('id', $user->user_id)->first();
                                        ?>
                                        <div class="col-md-6 my-2 ">
                                            <div
                                                class="d-flex align-items-center justify-content-between list_colume_notifi pb-2">
                                                <div class="mb-3 mb-sm-0">
                                                    <h6>
                                                        <img <?php if($userInfo->avatar): ?> src="<?php echo e(asset(Storage::url('users-avatar/' . $userInfo->avatar))); ?>" <?php else: ?> avatar="<?php echo e($userInfo->name); ?>" <?php endif; ?>
                                                            id="myAvatar" alt="user-image" height="30"
                                                            class=" wid-30 rounded-circle mx-2" alt="image">
                                                        <label for="user"
                                                            class="form-label"><?php echo e($userInfo->name); ?></label>
                                                    </h6>
                                                </div>
                                                <div class="text-end ">
                                                    <div class="form-check form-switch custom-switch-v1 mb-2">
                                                        <input type="checkbox" name="user_disable"
                                                            class="form-check-input input-primary is_disable"
                                                            value="1" data-id='<?php echo e($user->id); ?>'
                                                            data-company="<?php echo e($id); ?>"
                                                            user-id="<?php echo e($userInfo->id); ?>"
                                                            data-workspace-id="<?php echo e($workspace->id); ?>"
                                                            data-name="<?php echo e(__('user')); ?>"
                                                            <?php echo e($user->is_active == 1 ? 'checked' : ''); ?>

                                                            <?php echo e($workspace->is_active == 1 ? '' : 'disabled'); ?>>
                                                        <label class="form-check-label" for="user_disable"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on("click", ".is_disable", function() {
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var company_id = $(this).attr('data-company');
        var user_id = $(this).attr('user-id');
        var workspace_id = $(this).attr('data-workspace-id');
        var is_disable = ($(this).is(':checked')) ? $(this).val() : 0;

        $.ajax({
            url: '<?php echo e(route('user.unable')); ?>',
            type: 'POST',
            data: {
                "is_disable": is_disable,
                "id": id,
                "user_id": user_id,
                "name": name,
                "company_id": company_id,
                "workspace_id": workspace_id,
                "_token": "<?php echo e(csrf_token()); ?>",
            },
            success: function(data) {
                if (data.success) {
                    if (name == 'workspace') {
                        var container = document.getElementById('user_section_' + id);
                        var checkboxes = container.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(function(checkbox) {
                            if (is_disable == 0) {
                                checkbox.disabled = true;
                                checkbox.checked = false;
                            } else {
                                checkbox.disabled = false;
                                checkbox.checked = true;
                            }
                        });

                    }
                    $('.active_workspace').text(data.workspce_data.active_workspace);
                    $('.disable_workspace').text(data.workspce_data.disable_workspace);
                    $('.total_workspace').text(data.workspce_data.total_workspace);
                    $.each(data.users_data, function(workspaceName, userData) {
                        var $workspaceElements = $('.workspace[data-workspace-id="' +
                            userData.workspace_id + '"]');
                        // Update total_users, active_users, and disable_users for each workspace
                        $workspaceElements.find('.total_users').text(userData.total_users);
                        $workspaceElements.find('.active_users').text(userData
                            .active_users);
                        $workspaceElements.find('.disable_users').text(userData
                            .disable_users);
                    });

                    show_toastr('success', data.success, 'success');
                } else {
                    show_toastr('error', data.error, 'error');

                }

            }
        });
    });
</script>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/users/companyinfo.blade.php ENDPATH**/ ?>