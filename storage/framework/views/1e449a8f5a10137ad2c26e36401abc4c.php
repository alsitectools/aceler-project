

<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Project Detail')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('links'); ?>
    
    
    <li class="breadcrumb-item"><a href="<?php echo e(route('projects.index', $currentWorkspace->slug)); ?>"><?php echo e(__('Project')); ?></a>
    </li>
    <li class="breadcrumb-item"><?php echo e($project->name); ?></li>
<?php $__env->stopSection(); ?>
<?php
    // $permissions = Auth::user()->getPermission($project->id);
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
    // $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
?>

<?php $__env->startSection('multiple-action-button'); ?>
    <?php if(isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'): ?>
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12" data-toggle="popover"
                title="<?php echo e(trans('messages.Shared_Project_Settings')); ?>" data-ajax-popup="true" data-size="md"
                data-title="<?php echo e(trans('messages.Shared_Project_Settings')); ?>"
                data-url="<?php echo e(route('projects.copylink.setting.create', [$currentWorkspace->slug, $project->id])); ?>"
                data-toggle="tooltip" title="<?php echo e(__('Add Project')); ?>">
                <i class="ti ti-settings"></i>
            </a>
        </div>
    <?php endif; ?>
    <?php if(
        (isset($currentWorkspace) && $currentWorkspace->permission == 'Member') ||
            (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')): ?>
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="<?php echo e(route($client_keyword . 'projects.timesheet.index', [$currentWorkspace->slug, $project->id])); ?>"
                class="btn btn-xs btn-primary btn-icon-only col-12 "><?php echo e(trans('messages.Timesheet')); ?></a>
        </div>
    <?php endif; ?>

    <?php if(
        (isset($currentWorkspace) && $currentWorkspace->permission == 'Member') ||
            (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')): ?>
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="<?php echo e(route($client_keyword . 'projects.task.board', [$currentWorkspace->slug, $project->id])); ?>"
                class="btn btn-xs btn-primary btn-icon-only col-12 "><?php echo e(trans('messages.Task_Board')); ?></a>
        </div>
    <?php endif; ?>

    <div class="col-md-auto col-sm-6 pb-3">
        <a href="<?php echo e(route($client_keyword . 'projecttime.tracker', [$currentWorkspace->slug, $project->id])); ?>"
            class="btn btn-xs btn-primary btn-icon-only col-12 "><?php echo e(trans('messages.Tracker')); ?></a>
    </div>
<?php $__env->stopSection(); ?>

<style type="text/css">
    .fix_img {
        width: 40px !important;
        border-radius: 50%;
    }

    @media (max-width: 1300px) {
        .header_breadcrumb {
            width: 100% !important;
        }

        .row1 {
            display: flex;
            flex-wrap: wrap;
        }
    }
</style>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="d-block d-sm-flex align-items-center justify-content-between">
                                <h4 class="text-white"> <?php echo e($project->name); ?></h4>
                                <div class="d-flex  align-items-center row1">
                                    <div class="px-3">
                                        <span class="text-white text-sm"><?php echo e(__('[REF] MasterObras')); ?>:</span>
                                        <h5 class="text-white text-nowrap">
                                            <?php echo e('M1234234654 '); ?></h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm"><?php echo e(trans('messages.Start_Date')); ?>:</span>
                                        <h5 class="text-white text-nowrap">
                                            <?php echo e(App\Models\Utility::dateFormat($project->start_date)); ?></h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm"><?php echo e(__('Due Date')); ?>:</span>
                                        <h5 class="text-white"><?php echo e(App\Models\Utility::dateFormat($project->end_date)); ?>

                                        </h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm"><?php echo e(trans('messages.Total_Members')); ?>:</span>
                                        <h5 class="text-white text-nowrap">
                                            <?php echo e((int) $project->users->count() + (int) $project->clients->count()); ?></h5>
                                    </div>
                                    <div class="px-3">

                                        <?php if($project->status == 'Finished'): ?>
                                            <div class="badge  bg-success p-2 px-3 rounded"> <?php echo e(__('Finished')); ?>

                                            </div>
                                        <?php elseif($project->status == 'Ongoing'): ?>
                                            <div class="badge  bg-secondary p-2 px-3 rounded"><?php echo e(__('Ongoing')); ?></div>
                                        <?php else: ?>
                                            <div class="badge bg-warning p-2 px-3 rounded"><?php echo e(__('OnHold')); ?></div>
                                        <?php endif; ?>

                                    </div>
                                </div>

                                <?php if(!$project->is_active): ?>
                                    <button class="btn btn-light d"> <a href="#" class=""
                                            title="<?php echo e(__('Locked')); ?>">
                                            <i data-feather="lock"> </i>
                                        </a></button>
                                <?php else: ?>
                                    <?php if(auth()->guard('web')->check()): ?>
                                        
                                        <?php if(\Auth::user()->type == 'admin'): ?>
                                            <div class="d-flex align-items-center ">

                                                <a href="#" class=""
                                                    data-url="<?php echo e(route('projects.edit', [$currentWorkspace->slug, $project->id])); ?>"
                                                    data-ajax-popup="true" data-title="<?php echo e(__('Edit Project')); ?>"
                                                    data-toggle="popover" title="<?php echo e(__('Edit')); ?>">
                                                    <button class="btn btn-light d-flex align-items-between me-3">
                                                        <i class="ti ti-edit"> </i>
                                                    </button>
                                                </a>
                                                <a href="#" class="bs-pass-para" data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                    data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                    data-confirm-yes="delete-form-<?php echo e($project->id); ?>" data-toggle="popover"
                                                    title="<?php echo e(__('Delete')); ?>">
                                                    <button class="btn btn-light d">
                                                        <i class="ti ti-trash"> </i>
                                                    </button>
                                                </a>
                                            </div>
                                            <form id="delete-form-<?php echo e($project->id); ?>"
                                                action="<?php echo e(route('projects.destroy', [$currentWorkspace->slug, $project->id])); ?>"
                                                method="POST" style="display: none;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                            </form>

                                            <form id="leave-form-<?php echo e($project->id); ?>"
                                                action="<?php echo e(route('projects.leave', [$currentWorkspace->slug, $project->id])); ?>"
                                                method="POST" style="display: none;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-primary">
                                            <i class="fas fas fa-calendar-day"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1"><?php echo e(__('Days left')); ?></h6>
                                            <span class="h6 font-weight-bold mb-0 "><?php echo e($daysleft); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1"><?php echo e(trans('messages.Total_Task')); ?></h6>
                                            <span class="h6 font-weight-bold mb-0 "><?php echo e($project->countTask()); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-success">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1"><?php echo e(__('Comment')); ?></h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 "><?php echo e($project->countTaskComments()); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <div class="col-lg-12">
                        <?php if($currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner'): ?>
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"><?php echo e(__('Milestones')); ?> (<?php echo e(count($project->milestones)); ?>)
                                            </h5>
                                        </div>
                                        <div class="float-end">
                                            <?php if($currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner'): ?>
                                                <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true"
                                                    data-title="<?php echo e(__('Create Milestone')); ?>"
                                                    data-url="<?php echo e(route($client_keyword . 'projects.milestone', [$currentWorkspace->slug, $project->id])); ?>"
                                                    data-toggle="popover" title="<?php echo e(__('Create')); ?>"><i
                                                        class="ti ti-plus"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th><?php echo e(__('Name')); ?></th>
                                                    <th><?php echo e(trans('messages.Status')); ?></th>
                                                    <th><?php echo e(__('messages.Created_date')); ?></th>
                                                    <th><?php echo e(__('messages.Desired_delivery_date')); ?></th>
                                                    <th><?php echo e(__('Fecha de inicio')); ?></th>
                                                    <th><?php echo e(__('Fecha final')); ?></th>
                                                    <th><?php echo e(__('Action')); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $project->milestones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $milestone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><a href="#" class="d-block font-weight-500 mb-0"
                                                                data-ajax-popup="true"
                                                                data-title="<?php echo e(__('Milestone Details')); ?>"
                                                                data-url="<?php echo e(route($client_keyword . 'projects.milestone.show', [$currentWorkspace->slug, $milestone->id])); ?>">
                                                                <h5 class="m-0"> <?php echo e($milestone->title); ?> </h5>
                                                            </a></td>
                                                        <td>
                                                            <?php if($milestone->status == 'complete'): ?>
                                                                <label
                                                                    class="badge bg-success p-2 px-3 rounded"><?php echo e(__('Complete')); ?></label>
                                                            <?php else: ?>
                                                                <label
                                                                    class="badge bg-warning p-2 px-3 rounded"><?php echo e(__('Incomplete')); ?></label>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($milestone->start_date); ?></td>
                                                        <td><?php echo e($milestone->end_date); ?></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-right">
                                                            <div class="col-auto">
                                                                <?php if($currentWorkspace->permission == 'Owner'): ?>
                                                                    <a href="#"
                                                                        class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="<?php echo e(__('Edit')); ?>"
                                                                        data-title="<?php echo e(__('Edit Milestone')); ?>"
                                                                        data-url="<?php echo e(route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id])); ?>"><i
                                                                            class="ti ti-edit"></i></a>
                                                                    <a href="#"
                                                                        class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                                        data-toggle="popover" title="<?php echo e(__('Delete')); ?>"
                                                                        data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                                        data-confirm-yes="delete-form1-<?php echo e($milestone->id); ?>"><i
                                                                            class="ti ti-trash"></i></a>
                                                                    <form id="delete-form1-<?php echo e($milestone->id); ?>"
                                                                        action="<?php echo e(route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id])); ?>"
                                                                        method="POST" style="display: none;">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                    </form>
                                                                <?php elseif($currentWorkspace->permission == 'Member' || $currentWorkspace->permission == 'Owner'): ?>
                                                                    <a href="#"
                                                                        class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="<?php echo e(__('Edit Milestone')); ?>"
                                                                        data-toggle="popover" title="<?php echo e(__('Edit')); ?>"
                                                                        data-url="<?php echo e(route($client_keyword . 'projects.milestone.edit', [$currentWorkspace->slug, $milestone->id])); ?>"><i
                                                                            class="ti ti-edit"></i></a>

                                                                    <?php if($currentWorkspace->permission == 'Owner'): ?>
                                                                        <a href="#"
                                                                            class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                            data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                                            data-toggle="popover"
                                                                            title="<?php echo e(__('Delete')); ?>"
                                                                            data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                                            data-confirm-yes="delete-form1-<?php echo e($milestone->id); ?>"><i
                                                                                class="ti ti-trash"></i></a>
                                                                        <form id="delete-form1-<?php echo e($milestone->id); ?>"
                                                                            action="<?php echo e(route($client_keyword . 'projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id])); ?>"
                                                                            method="POST" style="display: none;">
                                                                            <?php echo csrf_field(); ?>
                                                                            <?php echo method_field('DELETE'); ?>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        <?php endif; ?>
                    </div>

                    

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card ">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            
                                            <h5 class="mb-0"><?php echo e(trans('messages.Team_Members')); ?>

                                                (<?php echo e(count($project->users)); ?>)
                                            </h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-sm-flex align-items-center mb-0">
                                                <?php if(\Auth::user()->type == 'admin'): ?>
                                                    <a href="#" class="btn btn-sm btn-primary "
                                                        data-ajax-popup="true" data-title="<?php echo e(__('Invite')); ?>"
                                                        data-toggle="popover" title="<?php echo e(__('Invite')); ?>"
                                                        data-url="<?php echo e(route('projects.invite.popup', [$currentWorkspace->slug, $project->id])); ?>"><i
                                                            class="ti ti-brand-telegram"></i></a>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        <?php $__currentLoopData = $project->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        <?php if($user->avatar): ?> src="<?php echo e(asset($logo . $user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($user->name); ?>" <?php endif; ?>>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0"><?php echo e($user->name); ?></h5>
                                                                    <small class="text-muted"><?php echo e($user->email); ?><span
                                                                            class="text-primary "> -
                                                                            <?php echo e((int) count($project->user_done_tasks($user->id))); ?>/<?php echo e((int) count($project->user_tasks($user->id))); ?></span></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            <?php if(auth()->guard('web')->check()): ?>
                                                                
                                                                <?php if(\Auth::user()->type == 'admin'): ?>
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="<?php echo e(__('Permission')); ?>"
                                                                        data-title="<?php echo e(__('Edit Permission')); ?>"
                                                                        data-url="<?php echo e(route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id])); ?>"><i
                                                                            class="ti ti-lock"></i></a>

                                                                    <a href="#"
                                                                        class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                                        data-toggle="popover" title="<?php echo e(__('Delete')); ?>"
                                                                        data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                                        data-confirm-yes="delete-user-<?php echo e($user->id); ?>"><i
                                                                            class="ti ti-trash ml-1"></i></a>
                                                                    <form id="delete-user-<?php echo e($user->id); ?>"
                                                                        action="<?php echo e(route('projects.user.delete', [$currentWorkspace->slug, $project->id, $user->id])); ?>"
                                                                        method="POST" style="display: none;">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                    </form>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">

                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>

                                            <h5 class="mb-0"><?php echo e(trans('messages.Sales_manager')); ?>

                                                (<?php echo e(count($project->clients)); ?>)
                                            </h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-none d-sm-flex align-items-center mb-0">
                                                
                                                <?php if(\Auth::user()->type == 'admin'): ?>
                                                    <a href="#" class="btn btn-sm btn-primary"
                                                        data-ajax-popup="true" data-title="<?php echo e(__('Share to Client')); ?>"
                                                        data-toggle="popover" title="<?php echo e(__('Share to Client')); ?>"
                                                        data-url="<?php echo e(route('projects.share.popup', [$currentWorkspace->slug, $project->id])); ?>"><i
                                                            class="ti ti-share"></i></a>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class=" px-3 top-10-scroll" style="max-height: 300px;">
                                        <?php $__currentLoopData = $project->clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        <?php if($client->avatar): ?> src="<?php echo e(asset($logo . $client->avatar)); ?>" <?php else: ?> avatar="<?php echo e($client->name); ?>" <?php endif; ?>>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0"><?php echo e($client->name); ?></h5>
                                                                    <small
                                                                        class="text-muted"><?php echo e($client->email); ?></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            <?php if(auth()->guard('web')->check()): ?>
                                                                
                                                                <?php if(\Auth::user()->type == 'admin'): ?>
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-toggle="popover" title="<?php echo e(__('Permission')); ?>"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="<?php echo e(__('Edit Permission')); ?>"
                                                                        data-url="<?php echo e(route('projects.client.permission', [$currentWorkspace->slug, $project->id, $client->id])); ?>"><i
                                                                            class="ti ti-lock"></i></a>

                                                                    <a href="#"
                                                                        class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                                        data-toggle="popover" title="<?php echo e(__('Delete')); ?>"
                                                                        data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                                        data-confirm-yes="delete-client-<?php echo e($client->id); ?>"><i
                                                                            class="ti ti-trash"></i></a>

                                                                    <form id="delete-client-<?php echo e($client->id); ?>"
                                                                        action="<?php echo e(route('projects.client.delete', [$currentWorkspace->slug, $project->id, $client->id])); ?>"
                                                                        method="POST" style="display: none;">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                    </form>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <?php if($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member'): ?>
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0"> <?php echo e(__('Files')); ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="author-box-name form-control-label mb-4"></div>

                                        <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                            <div class="dz-message" data-dz-message>
                                                <span>
                                                    <?php if(Auth::user()->getGuard() == 'client'): ?>
                                                        <?php echo e(__('No files available')); ?>

                                                    <?php else: ?>
                                                        <?php echo e(__('Drop files here to upload')); ?>

                                                    <?php endif; ?>
                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header" style="padding: 25px 35px !important;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="row">
                                            <h5 class="mb-0"><?php echo e(__('Progress')); ?><span class="text-end">
                                                    (<?php echo e(trans('messages.Last_Week_Tasks')); ?>) </span></h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                    </div>
                                </div>
                                <div id="task-chart"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"><?php echo e(__('Activity')); ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="timeline timeline-one-side top-10-scroll" data-timeline-content="axis"
                                        data-timeline-axis-style="dashed">
                                        <?php if($currentWorkspace->permission == 'Owner' || $currentWorkspace->permission == 'Member'): ?>
                                            <?php $__currentLoopData = $project->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="timeline-block px-2 pt-3">
                                                    <?php if($activity->log_type == 'Upload File'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-primary text-white">
                                                            <i class="fas fa-file"></i></span>
                                                    <?php elseif($activity->log_type == 'Create Milestone'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-cubes"></i></span>
                                                    <?php elseif($activity->log_type == 'Create Task'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-tasks"></i></span>
                                                    <?php elseif($activity->log_type == 'Create Bug'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-warning text-white">
                                                            <i class="fas fa-bug"></i></span>
                                                    <?php elseif($activity->log_type == 'Move' || $activity->log_type == 'Move Bug'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border round border-danger text-white">
                                                            <i class="fas fa-align-justify"></i></span>
                                                    <?php elseif($activity->log_type == 'Create Invoice'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-bg-dark text-white">
                                                            <i class="fas fa-file-invoice"></i></span>
                                                    <?php elseif($activity->log_type == 'Invite User'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-plus"></i></span>
                                                    <?php elseif($activity->log_type == 'Share with Client'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-info text-white">
                                                            <i class="fas fa-share"></i></span>
                                                    <?php elseif($activity->log_type == 'Create Timesheet'): ?>
                                                        <span
                                                            class="timeline-step timeline-step-sm border border-success text-white">
                                                            <i class="fas fa-clock-o"></i></span>
                                                    <?php endif; ?>

                                                    <div class="last_notification_text">
                                                        <!--   <p class="m-0"> <span><?php echo e($activity->log_type); ?> </span> </p> <br> -->
                                                        <p class="m-0">
                                                            <span><?php echo e($activity->logType($activity->log_type)); ?>

                                                            </span>
                                                        </p> <br>
                                                        <p> <?php echo $activity->getRemark(); ?> </p>
                                                        <div class="notification_time_main">
                                                            <p><?php echo e($activity->created_at->diffForHumans()); ?></p>
                                                        </div>
                                                    </div>

                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('css-page'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/custom/css/dropzone.min.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <!--
                                                                                                            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

                                                                                                             -->
    <script src="<?php echo e(asset('assets/js/plugins/apexcharts.min.js')); ?>"></script>
    <script>
        (function() {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: <?php echo json_encode($chartData['color']); ?>,
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [
                    <?php $__currentLoopData = $chartData['stages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        {
                            name: "<?php echo e(__($name)); ?>",
                            // data: 
                            data: <?php echo json_encode($chartData[$id]); ?>,
                        },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                xaxis: {
                    type: "category",
                    categories: <?php echo json_encode($chartData['label']); ?>,
                    title: {
                        text: '<?php echo e(__('Days')); ?>'
                    },
                    tooltip: {
                        enabled: false,
                    }
                },
                yaxis: {
                    show: true,
                    position: "left",
                    title: {
                        text: '<?php echo e(__('Tasks')); ?>'
                    },
                },
                grid: {
                    show: true,
                    borderColor: "#EBEBEB",
                    strokeDashArray: 0,
                    position: "back",
                    xaxis: {
                        show: true,
                        lines: {
                            show: true,
                        },
                    },
                    yaxis: {
                        show: false,
                        lines: {
                            show: false,
                        },
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5,
                    },
                    column: {
                        position: "back",
                        colors: undefined,
                        opacity: 0.5,
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0,
                    },
                },
                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },

                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#task-chart"), options);
            chart.render();
        })();
    </script>
    <script>
        $(document).ready(function() {
            if ($(".top-10-scroll").length) {
                $(".top-10-scroll").css({
                    "max-height": 300
                }).niceScroll();
            }
        });
    </script>
    <script src="<?php echo e(asset('assets/custom/js/dropzone.min.js')); ?>"></script>
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 209715200,
            parallelUploads: 1,
            //acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg,.pdf,.txt,.doc,.docx,.zip,.rar",
            url: "<?php echo e(route('projects.file.upload', [$currentWorkspace->slug, $project->id])); ?>",
            success: function(file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    show_toastr('<?php echo e(__('Success')); ?>', 'File Successfully Uploaded', 'success');
                } else {
                    myDropzone.removeFile(file);
                    // show_toastr('error', 'File type must be match with Storage setting.');
                    show_toastr('<?php echo e(__('Error')); ?>',
                        'File type and size must be match with Storage setting.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('<?php echo e(__('Error')); ?>',
                        'File type and size must be match with Storage setting.', 'error');
                } else {
                    show_toastr('<?php echo e(__('Error')); ?>',
                        'File type and size must be match with Storage setting.', 'error');
                }
            }
        });

        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("project_id", <?php echo e($project->id); ?>);
        });

        <?php if(isset($permisions) && in_array('show uploading', $permisions)): ?>
            $(".dz-hidden-input").prop("disabled", true);
            myDropzone.removeEventListeners();
        <?php endif; ?>

        function dropzoneBtn(file, response) {

            var html = document.createElement('span');
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "popover");
            download.setAttribute('download', "");
            download.setAttribute('title', "<?php echo e(__('Download')); ?>");
            // download.innerHTML = "<i class='fas fa-download mt-2'></i>";
            download.innerHTML = "<i class='ti ti-download'> </i>";
            html.appendChild(download);

            <?php if(isset($permisions) && in_array('show uploading', $permisions)): ?>
            <?php else: ?>
                var del = document.createElement('a');
                del.setAttribute('href', response.delete);
                del.setAttribute('class', "action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center");
                del.setAttribute('data-toggle', "popover");
                del.setAttribute('title', "<?php echo e(__('Delete')); ?>");
                del.innerHTML = "<i class='ti ti-trash '></i>";

                del.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm("Are you sure ?")) {
                        var btn = $(this);
                        $.ajax({
                            url: btn.attr('href'),
                            type: 'DELETE',
                            success: function(response) {
                                if (response.is_success) {
                                    btn.closest('.dz-image-preview').remove();
                                    show_toastr('<?php echo e(__('Success')); ?>', 'File Successfully Deleted',
                                        'success');
                                } else {
                                    show_toastr('<?php echo e(__('Error')); ?>', 'Something Wents Wrong.',
                                        'error');
                                }
                            },
                            error: function(response) {
                                response = response.responseJSON;
                                if (response.is_success) {
                                    show_toastr('<?php echo e(__('Error')); ?>', 'Something Wents Wrong.',
                                        'error');
                                } else {
                                    show_toastr('<?php echo e(__('Error')); ?>', 'Something Wents Wrong.',
                                        'error');
                                }
                            }
                        })
                    }
                });
                html.appendChild(del);
            <?php endif; ?>

            file.previewTemplate.appendChild(html);
        }
        <?php ($setting = App\Models\Utility::getAdminPaymentSettings()); ?>

        <?php ($files = $project->files); ?>
        <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($storage_file = asset($logo_project_files . $file->file_path)); ?>

            // Create the mock file:
            <?php if(Storage::disk($setting['storage_setting'])->exists('/project_files/' . $file->file_path)): ?>

                var mockFile = {
                    name: "<?php echo e($file->file_name); ?>",
                    size: <?php echo e(filesize('storage/project_files/' . $file->file_path)); ?>

                };
            <?php endif; ?>
            // Call the default addedfile event handler
            myDropzone.emit("addedfile", mockFile);
            // And optionally show the thumbnail of the file:
            myDropzone.emit("thumbnail", mockFile, "<?php echo e(asset($logo_project_files . $file->file_path)); ?>");
            myDropzone.emit("complete", mockFile);

            dropzoneBtn(mockFile, {
                download: "<?php echo e(route($client_keyword . 'projects.file.download', [$currentWorkspace->slug, $project->id, $file->id])); ?>",
                delete: "<?php echo e(route($client_keyword . 'projects.file.delete', [$currentWorkspace->slug, $project->id, $file->id])); ?>"
            });
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/show.blade.php ENDPATH**/ ?>