<?php
<<<<<<< Updated upstream
// $logo=\App\Models\Utility::get_file('users-avatar/');
$logo=\App\Models\Utility::get_file('avatars/');
$logo_tasks=\App\Models\Utility::get_file('tasks/');
?>
 <div class="modal-body">
<?php if($currentWorkspace && $task): ?>

    <div class="p-2">
        <div class="form-control-label"><?php echo e(__('Description')); ?>:</div>

        <p class="text-muted mb-4">
            <?php echo e($task->description); ?>

        </p>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="form-control-label"><?php echo e(__('Create Date')); ?></div>
                <p class="mt-1"><?php echo e(\App\Models\Utility::dateFormat($task->created_at)); ?></p>
            </div>
            <div class="col-md-3">
                <div class="form-control-label"><?php echo e(__('Due Date')); ?></div>
                <p class="mt-1"><?php echo e(\App\Models\Utility::dateFormat($task->due_date)); ?></p>
            </div>
            <div class="col-md-3">
                <div class="form-control-label"><?php echo e(__('Assigned')); ?></div>
                <?php if($users = $task->users()): ?>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <img <?php if($user->avatar): ?> src="<?php echo e(asset($logo.$user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($user->name); ?>" <?php endif; ?> class="rounded-circle mt-1 w-20">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <div class="form-control-label"><?php echo e(__('Milestone')); ?></div>
                <?php ($milestone = $task->milestone()); ?>
                <p class="mt-1"><?php if($milestone): ?> <?php echo e($milestone->title); ?> <?php endif; ?></p>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs  bordar_styless mb-3" id="myTab" role="tablist">
        <li>
            <a class=" active" id="comments-tab" data-toggle="tab" href="#comments-data" role="tab" aria-controls="home" aria-selected="false"> <?php echo e(__('Comments')); ?> </a>
        </li>
        <li class="annual-billing">
            <a id="file-tab" data-toggle="tab" href="#file-data" role="tab" aria-controls="profile" aria-selected="false"> <?php echo e(__('Files')); ?> </a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active show" id="comments-data" role="tabpanel" aria-labelledby="home-tab">
            <form method="post" id="form-comment" data-action="<?php echo e(route('comment.store',[$currentWorkspace->slug,$task->project_id,$task->id,$clientID])); ?>">
            
                <textarea class="form-control form-control-light mb-2 grammer_textarea" name="comment" placeholder="<?php echo e(__('Write message')); ?>" id="example-textarea" rows="3" required></textarea>
                <div class="text-end">
                    <div class="btn-group mb-2 ml-2 d-sm-inline-block">
                        <button type="button" class="btn btn btn-primary"><?php echo e(__('Submit')); ?></button>
                    </div>
                </div>
            </form>
            <ul class="list-unstyled list-unstyled-border mt-3" id="task-comments">
                <?php $__currentLoopData = $task->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="media border-bottom mb-3">
                        <img class="mr-3 avatar-sm rounded-circle img-thumbnail" width="" style="max-width: 30px; max-height: 30px;" 
                             <?php if($comment->user_type != 'Client'): ?> <?php if($comment->user->avatar): ?> src="<?php echo e(asset($logo.$comment->user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($comment->user->name); ?>" <?php endif; ?> alt="<?php echo e($comment->user->name); ?>"
                             <?php else: ?> avatar="<?php echo e($comment->client->name); ?>" alt="<?php echo e($comment->client->name); ?>" <?php endif; ?> />
                        <div class="media-body mb-2">
                            <div class="float-left">
                                <h5 class="mt-0 mb-1 form-control-label"><?php if($comment->user_type!='Client'): ?><?php echo e($comment->user->name); ?><?php else: ?> <?php echo e($comment->client->name); ?> <?php endif; ?></h5>
                                <?php echo e($comment->comment); ?>

                            </div>
                            <?php if(auth()->guard('web')->check()): ?>
                                <div class="text-end row_line_style">
                                    <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment" data-url="<?php echo e(route('comment.destroy',[$currentWorkspace->slug,$task->project_id,$task->id,$comment->id])); ?>">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="tab-pane fade" id="file-data" role="tabpanel" aria-labelledby="profile-tab">
            <div class="form-group m-0">
                <form method="post" id="form-file" enctype="multipart/form-data" data-url="<?php echo e(route('comment.store.file',[$currentWorkspace->slug,$task->project_id,$task->id,$clientID])); ?>">
                 
                     <?php echo csrf_field(); ?>

                    <div class="choose-file mt-3">
                        <label for="file" class="">
                             <div class="logo-content">
                                    <img src="<?php echo e(asset($logo_tasks.'sample.jpg')); ?>" class="preview_img_size" id="task_file"/>
                                </div>
                            <div class=" bg-primary"> <i class="ti ti-upload px-1"></i><?php echo e(__('Choose file here')); ?></div>
                            <input type="file" class="form-control choose_file_custom" name="file" id="file" data-filename="file_create">
                            <span class="invalid-feedback" id="file-error" role="alert">
                                <strong></strong>
                            </span>
                        </label>
                       <!--  <p class="file_create"></p> -->
                    </div>
                
                    <div class="text-end">
                        <div class="">
                            <button type="submit" class="btn btn-primary"><?php echo e(__('Upload')); ?></button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="comments-file" class="mt-3">
                
                <?php $__currentLoopData = $task->taskFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                    <div class="card pb-0 mb-1 shadow-none border">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-sm">
                                        <?php if(in_array($file->extension,['.jpg','.jpeg','.png','.gif','.bmp'])): ?>
                                         <span class="avatar-title rounded">
                                             <img src="<?php echo e(asset($logo_tasks.$file->file)); ?>" class="preview_img_size" id=""/>
                                            
                                         </span>
                                        <?php else: ?>
                                         <span class="avatar-title rounded">
                                             <img src="<?php echo e(asset($logo_tasks.'sample-file.png')); ?>" class="preview_img_size" id=""/>
                                         </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col pl-0">
                                    <a href="#" class="text-muted form-control-label"><?php echo e($file->name); ?></a>
                                    <p class="mb-0"><?php echo e($file->file_size); ?></p>
                                </div>
                                <div class="col-auto">
                                    <!-- Button -->
                                    <a download href="<?php echo e($logo_tasks.$file->file); ?>" class="action-btn btn-primary  btn btn-sm d-inline-flex align-items-center">
                                        <i class="ti ti-download" data-toggle="popover" title="<?php echo e(__('Download')); ?>"></i>
                                    </a>

                                    <a class="action-btn btn-secondary  btn btn-sm d-inline-flex align-items-center" href="<?php echo e($logo_tasks.$file->file); ?>" target="_blank"  >
                                        <i class="ti ti-crosshair text-white" data-toggle="popover" title="<?php echo e(__('Preview')); ?>"></i>
                                    </a>

                                    <?php if(auth()->guard('web')->check()): ?>
                                        <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment-file" data-url="<?php echo e(route('comment.destroy.file',[$currentWorkspace->slug,$task->project_id,$task->id,$file->id])); ?>">
                                            <i class="ti ti-trash" data-toggle="popover" title="<?php echo e(__('Delete')); ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="tab-pane fade mt-3" id="sub-task-data" role="tabpanel" aria-labelledby="contact-tab">

            <div class="text-end mb-3">
                <a href="#" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#form-subtask"> <i class="ti ti-plus"></i></a>
            </div>
            <form method="post" id="form-subtask" class="collapse" data-action="<?php echo e(route('subtask.store',[$currentWorkspace->slug,$task->project_id,$task->id,$clientID])); ?>">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="col-form-label"><?php echo e(__('Name')); ?></label>
                                <input type="text" name="name" class="form-control" required placeholder="<?php echo e(__('Sub Task Name')); ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="col-form-label"><?php echo e(__('Due Date')); ?></label>
                                <input class="form-control datepicker2" type="text" id="due_date" name="due_date" autocomplete="off" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="btn-group mb-2 ml-2 d-sm-inline-block">
                            <button type="submit" class="btn btn-primary create-subtask"><?php echo e(__('Add Subtask')); ?></button>
                        </div>
                    </div>
                </div>
            </form>
            <ul class="list-group mt-3" id="subtasks">
                <?php $__currentLoopData = $task->sub_tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item py-3">
                        <div class="form-check form-switch d-inline-block">
                            <input type="checkbox" class="form-check-input" name="option" id="option<?php echo e($subTask->id); ?>" <?php if($subTask->status): ?> checked <?php endif; ?> data-url="<?php echo e(route('subtask.update',[$currentWorkspace->slug,$task->project_id,$subTask->id])); ?>">
                            <label class="custom-control-label form-control-label" for="option<?php echo e($subTask->id); ?>"><?php echo e($subTask->name); ?></label>
                        </div>
                        <div class="text-end row_line_style">
                            <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-subtask" data-url="<?php echo e(route('subtask.destroy',[$currentWorkspace->slug,$task->project_id,$subTask->id])); ?>">
                                <i class="ti ti-trash"></i>
                            </a>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
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
=======
    // $logo=\App\Models\Utility::get_file('users-avatar/');
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
?>
<div class="modal-body">
    <?php if($currentWorkspace && $task): ?>
        <div class="p-2">
            <div class="form-control-label"><?php echo e(__('Description')); ?>:</div>

            <p class="text-muted mb-4">
                <?php echo e($task->description); ?>

            </p>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-control-label"><?php echo e(__('Create Date')); ?></div>
                    <p class="mt-1"><?php echo e(\App\Models\Utility::dateFormat($task->created_at)); ?></p>
                </div>
                <div class="col-md-3">
                    <div class="form-control-label"><?php echo e(__('Due Date')); ?></div>
                    <p class="mt-1"><?php echo e(\App\Models\Utility::dateFormat($task->due_date)); ?></p>
                </div>
                <div class="col-md-3">
                    <div class="form-control-label"><?php echo e(__('Assigned')); ?></div>
                    <?php if($users = $task->users()): ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <img <?php if($user->avatar): ?> src="<?php echo e(asset($logo . $user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($user->name); ?>" <?php endif; ?>
                                class="rounded-circle mt-1 w-20">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <div class="form-control-label"><?php echo e(__('Milestone')); ?></div>
                    <?php ($milestone = $task->milestone()); ?>
                    <p class="mt-1">
                        <?php if($milestone): ?> <?php echo e($milestone->title); ?>

                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs  bordar_styless mb-3" id="myTab" role="tablist">
            <li>
                <a class=" active" id="comments-tab" data-toggle="tab" href="#comments-data" role="tab"
                    aria-controls="home" aria-selected="false"> <?php echo e(__('Comments')); ?> </a>
            </li>
            <li class="annual-billing">
                <a id="file-tab" data-toggle="tab" href="#file-data" role="tab" aria-controls="profile"
                    aria-selected="false"> <?php echo e(__('Files')); ?> </a>
            </li>
            
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade active show" id="comments-data" role="tabpanel" aria-labelledby="home-tab">
                <form method="post" id="form-comment"
                    data-action="<?php echo e(route('comment.store', [$currentWorkspace->slug, $task->project_id, $task->id, $clientID])); ?>">
                    <?php if($currentWorkspace->is_chagpt_enable()): ?>
                        <div class="row text-end pb-3">
                            <div class="col">
                                <a href="#" data-size="md" class="btn btn-primary btn-icon btn-sm"
                                    data-ajax-popup-over="true" id="grammarCheck"
                                    data-url="<?php echo e(route('grammar', ['grammar'])); ?>" data-bs-placement="top"
                                    data-title="<?php echo e(__('Grammar check with AI')); ?>">
                                    <i class="ti ti-rotate"></i> <span><?php echo e(__('Grammar check with AI')); ?></span></a>
                            </div>
                            <div class="col-auto">
                                <a href="#" data-size="lg" data-ajax-popup-over="true"
                                    class="btn btn-sm btn-primary" data-url="<?php echo e(route('generate', ['task show'])); ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="<?php echo e(__('Generate with AI')); ?>"
                                    data-title="<?php echo e(__('Generate Task Massage')); ?>">
                                    <i class="fas fa-robot px-1"></i><?php echo e(__('Generate with AI')); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <textarea class="form-control form-control-light mb-2 grammer_textarea" name="comment"
                        placeholder="<?php echo e(__('Write message')); ?>" id="example-textarea" rows="3" required></textarea>
                    <div class="text-end">
                        <div class="btn-group mb-2 ml-2 d-sm-inline-block">
                            <button type="button" class="btn btn btn-primary"><?php echo e(__('Submit')); ?></button>
                        </div>
                    </div>
                </form>
                <ul class="list-unstyled list-unstyled-border mt-3" id="task-comments">
                    <?php $__currentLoopData = $task->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="media border-bottom mb-3">
                            <img class="mr-3 avatar-sm rounded-circle img-thumbnail" width=""
                                style="max-width: 30px; max-height: 30px;"
                                <?php if($comment->user_type != 'Client'): ?> <?php if($comment->user->avatar): ?> src="<?php echo e(asset($logo . $comment->user->avatar)); ?>" <?php else: ?> avatar="<?php echo e($comment->user->name); ?>" <?php endif; ?>
                            alt="<?php echo e($comment->user->name); ?>" <?php else: ?> avatar="<?php echo e($comment->client->name); ?>"
                                alt="<?php echo e($comment->client->name); ?>" <?php endif; ?> />
                            <div class="media-body mb-2">
                                <div class="float-left">
                                    <h5 class="mt-0 mb-1 form-control-label">
                                        <?php if($comment->user_type != 'Client'): ?>
                                            <?php echo e($comment->user->name); ?>

                                        <?php else: ?>
                                            <?php echo e($comment->client->name); ?> <?php endif; ?>
                                    </h5>
                                    <?php echo e($comment->comment); ?>

                                </div>
                                <?php if(auth()->guard('web')->check()): ?>
                                    <div class="text-end row_line_style">
                                        <a href="#"
                                            class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment"
                                            data-url="<?php echo e(route('comment.destroy', [$currentWorkspace->slug, $task->project_id, $task->id, $comment->id])); ?>">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <div class="tab-pane fade" id="file-data" role="tabpanel" aria-labelledby="profile-tab">
                <div class="form-group m-0">
                    <form method="post" id="form-file" enctype="multipart/form-data"
                        data-url="<?php echo e(route('comment.store.file', [$currentWorkspace->slug, $task->project_id, $task->id, $clientID])); ?>">

                        <?php echo csrf_field(); ?>

                        <div class="choose-file mt-3">
                            <label for="file" class="">
                                <div class="logo-content">
                                    <img src="<?php echo e(asset($logo_tasks . 'sample.jpg')); ?>" class="preview_img_size"
                                        id="task_file" />
                                </div>
                                <div class=" bg-primary"> <i
                                        class="ti ti-upload px-1"></i><?php echo e(__('Choose file here')); ?></div>
                                <input type="file" class="form-control choose_file_custom" name="file"
                                    id="file" data-filename="file_create">
                                <span class="invalid-feedback" id="file-error" role="alert">
                                    <strong></strong>
                                </span>
                            </label>
                            <!--  <p class="file_create"></p> -->
                        </div>

                        <div class="text-end">
                            <div class="">
                                <button type="submit" class="btn btn-primary"><?php echo e(__('Upload')); ?></button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="comments-file" class="mt-3">
                    
                    <?php $__currentLoopData = $task->taskFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <div class="card pb-0 mb-1 shadow-none border">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-sm">
                                            <?php if(in_array($file->extension, ['.jpg', '.jpeg', '.png', '.gif', '.bmp'])): ?>
                                                <span class="avatar-title rounded">
                                                    <img src="<?php echo e(asset($logo_tasks . $file->file)); ?>"
                                                        class="preview_img_size" id="" />
                                                    
                                                </span>
                                            <?php else: ?>
                                                <span class="avatar-title rounded">
                                                    <img src="<?php echo e(asset($logo_tasks . 'sample-file.png')); ?>"
                                                        class="preview_img_size" id="" />
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col pl-0">
                                        <a href="#"
                                            class="text-muted form-control-label"><?php echo e($file->name); ?></a>
                                        <p class="mb-0"><?php echo e($file->file_size); ?></p>
                                    </div>
                                    <div class="col-auto">
                                        <!-- Button -->
                                        <a download href="<?php echo e($logo_tasks . $file->file); ?>"
                                            class="action-btn btn-primary  btn btn-sm d-inline-flex align-items-center">
                                            <i class="ti ti-download" data-toggle="popover"
                                                title="<?php echo e(__('Download')); ?>"></i>
                                        </a>

                                        <a class="action-btn btn-secondary  btn btn-sm d-inline-flex align-items-center"
                                            href="<?php echo e($logo_tasks . $file->file); ?>" target="_blank">
                                            <i class="ti ti-crosshair text-white" data-toggle="popover"
                                                title="<?php echo e(__('Preview')); ?>"></i>
                                        </a>

                                        <?php if(auth()->guard('web')->check()): ?>
                                            <a href="#"
                                                class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment-file"
                                                data-url="<?php echo e(route('comment.destroy.file', [$currentWorkspace->slug, $task->project_id, $task->id, $file->id])); ?>">
                                                <i class="ti ti-trash" data-toggle="popover"
                                                    title="<?php echo e(__('Delete')); ?>"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="tab-pane fade mt-3" id="sub-task-data" role="tabpanel" aria-labelledby="contact-tab">

                <div class="text-end mb-3">
                    <a href="#" class="btn btn-sm btn-primary" data-toggle="collapse"
                        data-target="#form-subtask"> <i class="ti ti-plus"></i></a>
                </div>
                <form method="post" id="form-subtask" class="collapse"
                    data-action="<?php echo e(route('subtask.store', [$currentWorkspace->slug, $task->project_id, $task->id, $clientID])); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label"><?php echo e(__('Name')); ?></label>
                                    <input type="text" name="name" class="form-control" required
                                        placeholder="<?php echo e(__('Sub Task Name')); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label"><?php echo e(__('Due Date')); ?></label>
                                    <input class="form-control datepicker2" type="text" id="due_date"
                                        name="due_date" autocomplete="off" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="btn-group mb-2 ml-2 d-sm-inline-block">
                                <button type="submit"
                                    class="btn btn-primary create-subtask"><?php echo e(__('Add Subtask')); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
                <ul class="list-group mt-3" id="subtasks">
                    <?php $__currentLoopData = $task->sub_tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item py-3">
                            <div class="form-check form-switch d-inline-block">
                                <input type="checkbox" class="form-check-input" name="option"
                                    id="option<?php echo e($subTask->id); ?>" <?php if($subTask->status): ?> checked <?php endif; ?>
                                    data-url="<?php echo e(route('subtask.update', [$currentWorkspace->slug, $task->project_id, $subTask->id])); ?>">
                                <label class="custom-control-label form-control-label"
                                    for="option<?php echo e($subTask->id); ?>"><?php echo e($subTask->name); ?></label>
                            </div>
                            <div class="text-end row_line_style">
                                <a href="#"
                                    class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-subtask"
                                    data-url="<?php echo e(route('subtask.destroy', [$currentWorkspace->slug, $task->project_id, $subTask->id])); ?>">
                                    <i class="ti ti-trash"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
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
                                <p class="text-muted mt-3">
                                    <?php echo e(__("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.")); ?>

                                </p>
                                <div class="mt-3">
                                    <a class="btn-return-home badge-blue" href="<?php echo e(route('home')); ?>"><i
                                            class="fas fa-reply"></i> <?php echo e(__('Return Home')); ?></a>
                                </div>
>>>>>>> Stashed changes
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<<<<<<< Updated upstream
    </div>
<?php endif; ?>
</div>
<script>
     (function () {
=======
    <?php endif; ?>
</div>
<script>
    (function() {
>>>>>>> Stashed changes
        const d_week = new Datepicker(document.querySelector('.datepicker2'), {
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
<<<<<<< Updated upstream
             format: 'yyyy-mm-dd',
=======
            format: 'yyyy-mm-dd',
>>>>>>> Stashed changes
        });
    })();
</script>



<script type="text/javascript">
<<<<<<< Updated upstream
    $('#file').change(function(){
=======
    $('#file').change(function() {
>>>>>>> Stashed changes
        let fileInput = this;

        // Check if the file is an image
        if (fileInput.files && fileInput.files[0]) {
            let fileType = fileInput.files[0].type;
            console.log(fileType);
<<<<<<< Updated upstream
            
            if (fileType.startsWith('image/')) {
                // If it's an image, load and display it
                let reader = new FileReader();
                reader.onload = (e) => { 
                    $('#task_file').attr('src', e.target.result); 
=======

            if (fileType.startsWith('image/')) {
                // If it's an image, load and display it
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#task_file').attr('src', e.target.result);
>>>>>>> Stashed changes
                }
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                // If it's not an image, show the default image
<<<<<<< Updated upstream
                $('#task_file').attr('src', '<?php echo e(asset($logo_tasks.'sample-file.png')); ?>');
            }
        }
    });
</script><?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/taskShow.blade.php ENDPATH**/ ?>
=======
                $('#task_file').attr('src', '<?php echo e(asset($logo_tasks . 'sample-file.png')); ?>');
            }
        }
    });
</script>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/taskShow.blade.php ENDPATH**/ ?>
>>>>>>> Stashed changes
