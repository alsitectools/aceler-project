<?php
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $logo = \App\Models\Utility::get_file('avatars/');
?>
<?php
    $languages = \App\Models\Utility::languages();
    if (\Auth::user()->type == 'admin') {
        //añadido
        $currentWorkspace = Auth::user()->currentWorkspace;

        $setting = App\Models\Utility::getAdminPaymentSettings();

        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = $setting['site_rtl'];
    } else {
        $adminSetting = App\Models\Utility::getAdminPaymentSettings();
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
    }

    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSettings();
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }

    $currantLang = basename(App::getLocale());
    // $currantLang = Auth::user()->lang;
    if ($currantLang == '') {
        $currantLang = 'en';
    }
    // dump($currantLang);
?>


<style type="text/css">
    .top_header {
        left: auto !important;
        top: 60px !important;
    }

    .noti-body {
        height: 300px;
        overflow: auto;
    }
</style>
<header class="dash-header <?php echo e(isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : ''); ?>">

    <div class="header-wrapper">
        <div class="dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <?php if(\Auth::user()->type != 'admin'): ?>
                    <li class="dropdown dash-h-item">
                        <a class="dash-head-link dropdown-toggle arrow-none ms-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-search"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown drp-search drp-search-custom">
                            <form class="form-inline mr-auto mb-0">
                                <div class="search-element">
                                    <input class="" type="type here" placeholder="Search here. . ."
                                        aria-label="Search">

                                    <div class="search-backdrop"></div>
                                </div>
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img class="theme-avtar"
                            <?php if(\Auth::user()->avatar): ?> src="<?php echo e(asset($logo . Auth::user()->avatar)); ?>" <?php else: ?> avatar="<?php echo e(Auth::user()->name); ?>" <?php endif; ?>
                            alt="<?php echo e(Auth::user()->name); ?>">
                        <span class="hide-mob ms-2"><?php echo e(__('Hi')); ?>,<?php echo e(Auth::user()->name); ?> !</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        
                        <?php
                            $login_status = false;
                        ?>

                        <?php $__currentLoopData = Auth::user()->workspace; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workspace): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if($workspace->is_active): ?>
                                <?php
                                    $user = Auth::user();
                                    $userWorkspace = App\Models\UserWorkspace::where([
                                        ['user_id', $user->id],
                                        ['workspace_id', $workspace->id],
                                    ])->first();
                                ?>
                                <?php if(isset($userWorkspace)): ?>
                                    <?php if($userWorkspace->is_active == 1): ?>
                                        <?php
                                            $login_status = true;
                                        ?>
                                        <a href="<?php if($currentWorkspace->id == $workspace->id): ?> #<?php else: ?> <?php if(auth()->guard('web')->check()): ?><?php echo e(route('change-workspace', $workspace->id)); ?><?php elseif(auth()->guard()->check()): ?><?php echo e(route('client.change-workspace', $workspace->id)); ?><?php endif; ?> <?php endif; ?>"
                                            title="<?php echo e($workspace->name); ?>" class="dropdown-item">
                                            <?php if($currentWorkspace->id == $workspace->id): ?>
                                                <i class="ti ti-checks text-success"></i>
                                            <?php endif; ?>
                                            <span><?php echo e($workspace->name); ?></span>
                                            <?php if(isset($workspace->pivot->permission)): ?>
                                                <?php if($workspace->pivot->permission == 'Owner'): ?>
                                                    <span
                                                        class="badge bg-primary"><?php echo e(__($workspace->pivot->permission)); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-dark"><?php echo e(__('Shared')); ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="#" class="dropdown-item" title="<?php echo e(__('Locked')); ?>">
                                    <i class="ti ti-lock"></i>
                                    <span><?php echo e($workspace->name); ?></span>
                                    <?php if(isset($workspace->pivot->permission)): ?>
                                        <?php if($workspace->pivot->permission == 'Owner'): ?>
                                            <span
                                                class="badge badge-success-primary"><?php echo e(__($workspace->pivot->permission)); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-dark"><?php echo e(__('Shared')); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <?php if(Auth::user()->getGuard() == 'client'): ?>
                            <?php
                                $client = Auth::user();
                            ?>
                            <?php $__currentLoopData = $client->workspace; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workspace): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($workspace->is_active == 1): ?>
                                    <?php
                                        $clientWorkspace = App\Models\ClientWorkspace::where([
                                            ['client_id', $client->id],
                                            ['workspace_id', $workspace->id],
                                        ])->first();
                                    ?>
                                    <?php if(isset($clientWorkspace)): ?>
                                        <?php
                                            $login_status = true;
                                        ?>
                                        <?php if($clientWorkspace->is_active == 1): ?>
                                            <a href="<?php if($currentWorkspace->id == $workspace->id): ?> #<?php else: ?> <?php if(auth()->guard('web')->check()): ?><?php echo e(route('change-workspace', $workspace->id)); ?><?php elseif(auth()->guard()->check()): ?><?php echo e(route('client.change-workspace', $workspace->id)); ?><?php endif; ?> <?php endif; ?>"
                                                title="<?php echo e($workspace->name); ?>" class="dropdown-item">
                                                <?php if($currentWorkspace->id == $workspace->id): ?>
                                                    <i class="ti ti-checks text-success"></i>
                                                <?php endif; ?>
                                                <span><?php echo e($workspace->name); ?></span>
                                                <?php if(isset($workspace->pivot->permission)): ?>
                                                    <?php if($workspace->pivot->permission == 'Owner'): ?>
                                                        <span
                                                            class="badge bg-primary"><?php echo e(__($workspace->pivot->permission)); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-dark"><?php echo e(__('Shared')); ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php endif; ?>

                        <?php if(\Auth::user()->type == 'admin'): ?>
                            <?php if(auth()->guard('web')->check()): ?>
                                <?php $__currentLoopData = Auth::user()->workspace; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workspace): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($currentWorkspace->id == $workspace->id): ?>
                                        <?php if($workspace->pivot->permission == 'Owner'): ?>
                                            <a href="#!" class="dropdown-item" data-toggle="modal"
                                                data-target="#modelCreateWorkspace">
                                                <i class="ti ti-circle-plus"></i>
                                                <span><?php echo e(__('Create New Workspace')); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                            <?php if(isset($currentWorkspace) && $currentWorkspace): ?>
                                <?php if(auth()->guard('web')->check()): ?>
                                    <?php if(Auth::user()->id == $currentWorkspace->created_by): ?>
                                        <a href="#" class="dropdown-item bs-pass-para"
                                            data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                            data-text="<?php echo e(__('messages.This_action_can_not_be_undone._Do_you_want_to_continue?')); ?>"
                                            data-confirm-yes="remove-workspace-form">
                                            <i class="ti ti-circle-x"></i>
                                            <span><?php echo e(__('Remove Me From This Workspace')); ?></span>
                                        </a>
                                        <form id="remove-workspace-form"
                                            action="<?php echo e(route('delete-workspace', ['id' => $currentWorkspace->id])); ?>"
                                            method="POST" style="display: none;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    <?php else: ?>
                                        <a href="#" class="dropdown-item bs-pass-para"
                                            data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                            data-text="<?php echo e(__('messages.This_action_can_not_be_undone._Do_you_want_tocontinue?')); ?>"
                                            data-confirm-yes="remove-workspace-form">
                                            <i class="ti ti-circle-x"></i>
                                            <span><?php echo e(__('Leave Me From This Workspace')); ?></span>
                                        </a>
                                        <form id="remove-workspace-form"
                                            action="<?php echo e(route('leave-workspace', ['id' => $currentWorkspace->id])); ?>"
                                            method="POST" style="display: none;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endif; ?>
                        <a href="<?php if(auth()->guard('web')->check()): ?><?php echo e(route('users.my.account')); ?><?php elseif(auth()->guard()->check()): ?><?php echo e(route('client.users.my.account')); ?><?php endif; ?>"
                            class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span><?php echo e(__('My Profile')); ?></span>
                        </a>

                        <a href="#" class="dropdown-item "
                            onclick="event.preventDefault();document.getElementById('logout-form1').submit();">
                            <i class="ti ti-power"></i>
                            <span><?php echo e(__('Logout')); ?></span>
                        </a>
                        <form id="logout-form1"
                            action="<?php if(auth()->guard('web')->check()): ?><?php echo e(route('logout')); ?><?php elseif(auth()->guard()->check()): ?><?php echo e(route('client.logout')); ?><?php endif; ?>"
                            method="POST" style="display: none;">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </li>


            </ul>
        </div>
        <!-- Brand + Toggler (for mobile devices) -->

        <div class="ms-auto">
            <ul class="list-unstyled">
                <?php if(Auth::user()->type == 'admin'): ?>
                    <?php if (is_impersonating($guard = null)) : ?>
                        <li class="dropdown dash-h-item drp-company">
                            <a class="btn btn-danger btn-sm me-3" href="<?php echo e(route('exit.admin')); ?>"><i
                                    class="ti ti-ban"></i>
                                <?php echo e(__('Exit Admin Login')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(\Auth::user()->type == 'user'): ?>
                    <?php if($adminSetting['enable_chat'] == 'on'): ?>
                        <li class="dash-h-item">
                            <a class="dash-head-link me-0" href="<?php echo e(url('chats')); ?>">
                                <i class="ti ti-message-circle"></i>
                                <span
                                    class="bg-danger dash-h-badge message-counter custom_messanger_counter"><?php echo e($unseenCounter); ?><span
                                        class="sr-only"></span>
                                </span></a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>


                <?php if(\Auth::user()->type == 'user'): ?>
                    <li class="dropdown dash-h-item drp-notification">
                        <?php if(isset($currentWorkspace) && $currentWorkspace): ?>
                            <?php if(auth()->guard('web')->check()): ?>
                                <?php
                                    $notifications = Auth::user()->notifications($currentWorkspace->id);
                                    // $all_notifications = Auth::user()->all_notifications($currentWorkspace->id);
                                ?>
                                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    href="#" role="button" aria-haspopup="false" aria-expanded="false">

                                    <i class="ti ti-bell"></i>
                                    <span
                                        class="<?php if(count($notifications) > 0): ?> bg-danger dash-h-badge dots <?php endif; ?>"><span
                                            class="sr-only"></span></span>
                                </a>
                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end notification_menu_all">
                                    <div class="noti-header">
                                        <h5 class="m-0">Notification</h5>
                                        <a href="#"
                                            data-url="<?php echo e(route('delete_all.notifications', $currentWorkspace->slug)); ?>"
                                            class="dash-head-link clear_all_notifications">Clear All</a>
                                    </div>
                                    <div class="noti-body">
                                        <div class="limited">
                                            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $project = $notification->project;
                                                    $task = $notification->task;
                                                    $notifyingUser = $notification->user;

                                                    // Define variables for the notification data
                                                    $projectTitle = $project ? $project->title : '';
                                                    $taskTitle = $task ? $task->title : '';
                                                    $notifyingUserName = $notifyingUser->name;

                                                    // Define other variables you need for HTML
                                                    $link = ''; // Replace with the actual link
                                                    $name = ''; // Replace with the notification icon or name
                                                    $text = ''; // Replace with the notification text
                                                    $date = $notification->created_at->diffForHumans();
                                                    $data = json_decode($notification->data);
                                                ?>
                                                <?php if($notification->user && trim($notification->user->name) != ''): ?>
                                                    <?php
                                                        $name = '';
                                                        $nameParts = explode(' ', $notification->user->name);
                                                    ?>

                                                    <?php $__currentLoopData = $nameParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $name .= strtoupper($word[0]);
                                                        ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>

                                                <?php if($notification->type == 'task_assign'): ?>
                                                    <?php
                                                        if ($project) {
                                                            $link = route('projects.task.board', [
                                                                $notification->workspace_id,
                                                                $notification->project_id,
                                                            ]);
                                                            $text =
                                                                __('New task assign') .
                                                                ' <b>' .
                                                                $data->title .
                                                                '</b> ' .
                                                                __('in project') .
                                                                ' <b>' .
                                                                $project->name .
                                                                '</b>';
                                                            $icon = 'fa fa-clock-o';
                                                            if ($data->priority == 'Low') {
                                                                $icon_color = 'bg-success';
                                                            } elseif ($data->priority == 'High') {
                                                                $icon_color = 'bg-danger';
                                                            }
                                                        } else {
                                                            return '';
                                                        }
                                                    ?>
                                                <?php elseif($notification->type == 'project_assign'): ?>
                                                    <?php
                                                        $link = route('projects.show', [
                                                            $notification->workspace_id,
                                                            $notification->data->id,
                                                        ]);
                                                        $text =
                                                            __('New project assign') . ' <b>' . $data->title . '</b>';
                                                        $icon = 'fa fa-suitcase';
                                                    ?>
                                                <?php elseif($notification->type == 'bug_assign'): ?>
                                                    <?php
                                                        if ($project) {
                                                            $link = route('projects.bug.report', [
                                                                $notification->workspace_id,
                                                                $notification->project_id,
                                                            ]);
                                                            $text =
                                                                __('New bug assign') .
                                                                ' <b>' .
                                                                $data->title .
                                                                '</b> ' .
                                                                __('in project') .
                                                                ' <b>' .
                                                                $project->name .
                                                                '</b>';
                                                            $icon = 'fa fa-bug';
                                                            if ($data->priority == 'Low') {
                                                                $icon_color = 'bg-success';
                                                            } elseif ($data->priority == 'High') {
                                                                $icon_color = 'bg-danger';
                                                            }
                                                        }
                                                    ?>
                                                <?php endif; ?>
                                                <a href="<?php echo e($link); ?>"
                                                    class="list-group-item list-group-item-action">
                                                    <div class="d-flex align-items-center" data-toggle="tooltip"
                                                        data-placement="right" data-title="<?php echo e($date); ?>">
                                                        <div class="notification_icon_size">
                                                            <span
                                                                class="avatar bg-primary text-white rounded-circle px-2 py-1"><?php echo e($name); ?></span>
                                                        </div>
                                                        <div class="flex-fill ml-3">
                                                            <div class="h6 text-sm mb-0">
                                                                <?php echo e($notification->user->name); ?>

                                                                <small
                                                                    class="float-end text-muted"><?php echo e($date); ?></small>
                                                            </div>
                                                            <p class="text-sm lh-140 mb-0">
                                                                <?php echo $text; ?>

                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>

                                        <div class="all_notification">

                                        </div>

                                        
                                    </div>
                                    
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>



                
                

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span
                            class="drp-text hide-mob"><?php echo e(ucfirst(\App\Models\Utility::getlang_fullname($currantLang))); ?></span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        <?php if(\Auth::guard('client')->check()): ?>
                            

                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $languageCode => $languageFullName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('change_lang_workspace1', [$currentWorkspace->id, $languageCode])); ?>"
                                    class="dropdown-item <?php echo e($currantLang == $languageCode ? 'text-danger' : ''); ?>">
                                    <span><?php echo e($languageFullName); ?></span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(\Auth::user()->type == 'admin'): ?>
                            
                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $languageCode => $languageFullName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('change_lang_admin', $languageCode)); ?>"
                                    class="dropdown-item <?php echo e($currantLang == $languageCode ? 'text-danger' : ''); ?>">
                                    <span><?php echo e($languageFullName); ?></span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="dropdown-divider m-0"></div>
                            <a href="#" class="dropdown-item text-primary" data-ajax-popup="true"
                                data-size="md" data-title="<?php echo e(__('Create Language')); ?>" data-toggle="tooltip"
                                title="<?php echo e(__('Create Language')); ?>" data-url="<?php echo e(route('create_lang_workspace')); ?>">
                                <span class="dash-mtext"><?php echo e(__('Create Language')); ?></span></a>
                            <div class="dropdown-divider m-0"></div>
                            <a href="<?php echo e(route('lang_workspace')); ?>" class="dropdown-item text-primary"><span
                                    class="dash-mtext"><?php echo e(__('Manage Language')); ?></span></a>
                        <?php elseif(isset($currentWorkspace) && $currentWorkspace && \Auth::guard('web')->check()): ?>
                            
                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $languageCode => $languageFullName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('change_lang_workspace', [$currentWorkspace->id, $languageCode])); ?>"
                                    class="dropdown-item <?php echo e($currantLang == $languageCode ? 'text-danger' : ''); ?>">
                                    <span><?php echo e($languageFullName); ?></span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<?php if(\Auth::user()->type != 'admin' && $login_status == false): ?>
    <script>
        document.getElementById('logout-form1').submit();
    </script>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/partials/topnav.blade.php ENDPATH**/ ?>