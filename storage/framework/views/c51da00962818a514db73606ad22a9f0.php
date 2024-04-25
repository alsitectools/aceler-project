<?php
    $languages = \App\Models\Utility::languages();
    $logo = \App\Models\Utility::get_file('logo/');
    if (Auth::user()->type == 'admin') {
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
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $settings = App\Models\Utility::getcompanySettings($currentWorkspace->id);
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
?>


<?php $__env->startSection('page-title', __('Settings')); ?>
<?php $__env->startSection('links'); ?>
    <?php if(\Auth::guard('client')->check()): ?>
        <li class="breadcrumb-item"><a href="<?php echo e(route('client.home')); ?>"><?php echo e(__('Home')); ?></a></li>
    <?php else: ?>
        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
    <?php endif; ?>
    <li class="breadcrumb-item"> <?php echo e(__('Settings')); ?></li>
<?php $__env->stopSection(); ?>
<style type="text/css">
    .row>* {
        flex-shrink: 0;
        /* width: 100%; */
        width: none !important;
        max-width: 100% !important;
        padding-right: calc(var(--bs-gutter-x) * .5);
        padding-left: calc(var(--bs-gutter-x) * .5);
        margin-top: var(--bs-gutter-y);
        /* width: auto; */
    }
</style>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            
                            <?php if(Auth::user()->type == 'admin'): ?>
                                
                                
                                
                                <a href="#tax-settings"
                                    class="list-group-item list-group-item-action border-0"><?php echo e(trans('messages.Tax_Settings')); ?>

                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#company-settings"
                                    class="list-group-item list-group-item-action border-0"><?php echo e(__('Company Settings')); ?>

                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                
                                
                                
                                

                                

                                
                                
                                <a href="#google-calender"
                                    class="list-group-item list-group-item-action border-0"><?php echo e(__('Google Calender')); ?>

                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>

                                

                                
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="workspace-settings" class="">
                        <?php echo e(Form::open(['route' => ['workspace.settings.store', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

                        <div class="row">
                            
                            <?php echo e(Form::close()); ?>

                        </div>

                        

                        

                        <div id="tax-settings" class="">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-11">
                                                    <h5 class="">
                                                        <?php echo e(trans('messages.Tax_Settings')); ?>

                                                    </h5>
                                                </div>
                                                <div class="text-end  col-auto">
                                                    <button class="btn-submit btn btn-sm btn-primary" type="button"
                                                        data-ajax-popup="true" data-title="<?php echo e(__('Add Tax')); ?>"
                                                        data-url="<?php echo e(route('tax.create', $currentWorkspace->slug)); ?>"
                                                        data-toggle="tooltip" title="<?php echo e(__('Add Tax')); ?>">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">

                                                <table id="" class="table table-bordered px-2">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('Name')); ?></th>
                                                            <th><?php echo e(__('Rate')); ?></th>
                                                            <th width="200px" class="text-right"><?php echo e(__('Action')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                <td><?php echo e($tax->name); ?></td>
                                                                <td><?php echo e($tax->rate); ?>%</td>
                                                                <td class="text-right">
                                                                    <a href="#"
                                                                        class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true"
                                                                        data-title="<?php echo e(__('Edit Tax')); ?>"
                                                                        data-url="<?php echo e(route('tax.edit', [$currentWorkspace->slug, $tax->id])); ?>"
                                                                        data-toggle="tooltip" title="<?php echo e(__('Edit Tax')); ?>">
                                                                        <i class="ti ti-pencil text-white"></i>
                                                                    </a>
                                                                    <a href="#"
                                                                        class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="<?php echo e(__('Are You Sure?')); ?>"
                                                                        data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>"
                                                                        data-confirm-yes="delete-form-<?php echo e($tax->id); ?>"data-toggle="tooltip"
                                                                        title="<?php echo e(__('Delete')); ?>">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                    <form id="delete-form-<?php echo e($tax->id); ?>"
                                                                        action="<?php echo e(route('tax.destroy', [$currentWorkspace->slug, $tax->id])); ?>"
                                                                        method="POST" style="display: none;">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="company-settings" class="tab-pane">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                <?php echo e(__('Company Settings')); ?>

                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            <form method="post"
                                                action="<?php echo e(route('workspace.settings.store', $currentWorkspace->slug)); ?>"
                                                class="payment-form">
                                                <?php echo csrf_field(); ?>
                                                <div class="row mt-3">
                                                    <div class="form-group col-md-6">
                                                        <label for="company"
                                                            class="form-label"><?php echo e(__('Name')); ?></label>
                                                        <input type="text" name="company" id="company"
                                                            class="form-control" value="<?php echo e($currentWorkspace->company); ?>"
                                                            required="required" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="address"
                                                            class="form-label"><?php echo e(trans('messages.Address')); ?></label>
                                                        <input type="text" name="address" id="address"
                                                            class="form-control" value="<?php echo e($currentWorkspace->address); ?>"
                                                            required="required" />
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="city"
                                                            class="form-label"><?php echo e(__('City')); ?></label>
                                                        <input class="form-control" name="city" type="text"
                                                            value="<?php echo e($currentWorkspace->city); ?>" id="city">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="state"
                                                            class="form-label"><?php echo e(trans('messages.State')); ?></label>
                                                        <input class="form-control" name="state" type="text"
                                                            value="<?php echo e($currentWorkspace->state); ?>" id="state">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="zipcode"
                                                            class="form-label"><?php echo e(trans('messages.Zip/Post_Code')); ?></label>
                                                        <input class="form-control" name="zipcode" type="text"
                                                            value="<?php echo e($currentWorkspace->zipcode); ?>" id="zipcode">
                                                    </div>
                                                    <div class="form-group  col-md-6">
                                                        <label for="country"
                                                            class="form-label"><?php echo e(__('Country')); ?></label>
                                                        <input class="form-control" name="country" type="text"
                                                            value="<?php echo e($currentWorkspace->country); ?>" id="country">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="telephone"
                                                            class="form-label"><?php echo e(trans('messages.Telephone')); ?></label>
                                                        <input class="form-control" name="telephone" type="text"
                                                            value="<?php echo e($currentWorkspace->telephone); ?>" id="telephone">
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit"
                                                        class="btn-submit btn btn-primary"><?php echo e(__('Save Changes')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        


                        <?php if(Auth::user()->type == 'user'): ?>
                            <div class="" id="slack-settings">
                                <?php echo e(Form::open(['route' => ['workspace.settings.Slack', $currentWorkspace->slug], 'method' => 'post', 'class' => 'd-contents'])); ?>

                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="">
                                                    <?php echo e(__('Slack Settings')); ?>

                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row company-setting">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                        <?php echo e(Form::label('Slack Webhook URL', __('Slack Webhook URL'), ['class' => 'form-label'])); ?>

                                                        <?php echo e(Form::text('slack_webhook', isset($payment_detail['slack_webhook']) ? $payment_detail['slack_webhook'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Slack Webhook URL'), 'required' => 'required'])); ?>


                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 form-group mb-3">
                                                        <?php echo e(Form::label('Module Settings', __('Module Settings'), ['class' => 'form-label'])); ?>

                                                    </div>


                                                    <div class="col-md-4">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> <?php echo e(Form::label('New Project', __('New Project'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('project_notificaation', '1', isset($payment_detail['project_notificaation']) && $payment_detail['project_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'project_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> <?php echo e(Form::label('New Task', __('New Task'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('task_notificaation', '1', isset($payment_detail['task_notificaation']) && $payment_detail['task_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'task_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6><?php echo e(Form::label('Task Stage Updated', __('Task Stage Updated'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('taskmove_notificaation', '1', isset($payment_detail['taskmove_notificaation']) && $payment_detail['taskmove_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskmove_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> <?php echo e(Form::label('New Milestone', __('New Milestone'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('milestone_notificaation', '1', isset($payment_detail['milestone_notificaation']) && $payment_detail['milestone_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestone_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> <?php echo e(Form::label('Milestone Status Updated', __('Milestone Status Updated'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('milestonest_notificaation', '1', isset($payment_detail['milestonest_notificaation']) && $payment_detail['milestonest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestonest_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> <?php echo e(Form::label('New Task Comment', __('New Task Comment'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('taskcom_notificaation', '1', isset($payment_detail['taskcom_notificaation']) && $payment_detail['taskcom_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskcom_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6><?php echo e(Form::label('New Invoice', __('New Invoice'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('invoice_notificaation', '1', isset($payment_detail['invoice_notificaation']) && $payment_detail['invoice_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                            <div class="mb-3 mb-sm-0">
                                                                <h6> <?php echo e(Form::label('Invoice Status Updated', __('Invoice Status Updated'), ['class' => 'form-label'])); ?>

                                                                </h6>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="form-check form-switch d-inline-block">
                                                                    <?php echo e(Form::checkbox('invoicest_notificaation', '1', isset($payment_detail['invoicest_notificaation']) && $payment_detail['invoicest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoicest_notificaation'])); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class=" text-end">
                                                        <?php echo e(Form::submit(__('Save Changes'), ['class' => 'btn btn-primary'])); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                        <?php endif; ?>


                        <?php if(Auth::user()->type == 'user' || Auth::user()->type == 'admin'): ?>
                            <div class="" id="google-calender">
                                <div class="card">
                                    <?php echo e(Form::open(['route' => ['google.calender.settings', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

                                    <div class="card-header">
                                        <div class="row justify-content-between">
                                            <div class="col-10">
                                                <h5 class=""><?php echo e(__('Google Calendar')); ?></h5>
                                            </div>
                                            <div class=" text-end  col-auto">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        <div class="custom-control custom-switch">

                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class=""
                                                                name="is_googlecalendar_enabled"
                                                                id="is_googlecalendar_enabled"
                                                                <?php echo e(isset($currentWorkspace->is_googlecalendar_enabled) && $currentWorkspace->is_googlecalendar_enabled == 'on' ? 'checked' : ''); ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                                <?php echo e(Form::label('Google calendar id', __('Google Calendar Id'), ['class' => 'col-form-label'])); ?>

                                                <?php echo e(Form::text('google_calender_id', !empty($currentWorkspace['google_calender_id']) ? $currentWorkspace['google_calender_id'] : '', ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id'])); ?>

                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                                <?php echo e(Form::label('Google calendar json file', __('Google Calendar json File'), ['class' => 'col-form-label'])); ?>

                                                <input type="file" class="form-control"
                                                    name="google_calender_json_file" id="google_calender_json_file">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <button class="btn-submit btn btn-primary" type="submit">
                                            <?php echo e(__('Save Changes')); ?>

                                        </button>
                                    </div>
                                    <?php echo e(Form::close()); ?>

                                </div>
                            </div>
                        <?php endif; ?>


                        
                    </div>
                    <!-- [ sample-page ] end -->
                </div>
                <!-- [ Main Content ] end -->
            </div>
        <?php $__env->stopSection(); ?>

        <?php $__env->startPush('scripts'); ?>
            <script src="<?php echo e(asset('assets/custom/js/jquery-ui.min.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/custom/js/repeater.js')); ?>"></script>
            <script src="<?php echo e(asset('assets/custom/js/colorPick.js')); ?>"></script>
            <script>
                var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                    target: '#useradd-sidenav',
                    offset: 300
                })
            </script>
            <script>
                function check_theme(color_val) {
                    $('input[value="' + color_val + '"]').prop('checked', true);
                    $('input[value="' + color_val + '"]').attr('checked', true);
                    $('a[data-value]').removeClass('active_color');
                    $('a[data-value="' + color_val + '"]').addClass('active_color');
                }
                var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                    target: '#useradd-sidenav',
                    offset: 300
                })
            </script>

            <script>
                $(document).on("click", '.send_email', function(e) {
                    e.preventDefault();
                    var title = $(this).attr('data-title');

                    var size = 'md';
                    var url = $(this).attr('data-url');
                    if (typeof url != 'undefined') {
                        $("#commonModal .modal-title").html(title);
                        $("#commonModal .modal-dialog").addClass('modal-' + size);
                        $("#commonModal").modal('show');

                        $.post(url, {
                            mail_driver: $("#mail_driver").val(),
                            mail_host: $("#mail_host").val(),
                            mail_port: $("#mail_port").val(),
                            mail_username: $("#mail_username").val(),
                            mail_password: $("#mail_password").val(),
                            mail_encryption: $("#mail_encryption").val(),
                            mail_from_address: $("#mail_from_address").val(),
                            mail_from_name: $("#mail_from_name").val(),
                        }, function(data) {
                            $('#commonModal .body').html(data);
                        });
                    }
                });
                $(document).on('submit', '#test_email', function(e) {
                    e.preventDefault();
                    $("#email_sending").show();
                    var post = $(this).serialize();
                    var url = $(this).attr('action');
                    $.ajax({
                        type: "post",
                        url: url,
                        data: post,
                        cache: false,
                        beforeSend: function() {
                            $('#test_email .btn-create').attr('disabled', 'disabled');
                        },
                        success: function(data) {
                            if (data.is_success) {
                                show_toastr('Success', data.message, 'success');
                            } else {
                                show_toastr('Error', data.message, 'error');
                            }
                            $("#email_sending").hide();
                        },
                        complete: function() {
                            $('#test_email .btn-create').removeAttr('disabled');
                        },
                    });
                })
            </script>

            <script src="<?php echo e(asset('assets/js/pages/wow.min.js')); ?>"></script>
            <script>
                // Start [ Menu hide/show on scroll ]
                let ost = 0;
                document.addEventListener("scroll", function() {
                    let cOst = document.documentElement.scrollTop;
                    if (cOst == 0) {
                        //   document.querySelector(".navbar").classList.add("top-nav-collapse");
                    } else if (cOst > ost) {
                        document.querySelector(".navbar").classList.add("top-nav-collapse");
                        document.querySelector(".navbar").classList.remove("default");
                    } else {
                        document.querySelector(".navbar").classList.add("default");
                        document
                            .querySelector(".navbar")
                            .classList.remove("top-nav-collapse");
                    }
                    ost = cOst;
                });
                // End [ Menu hide/show on scroll ]
                var wow = new WOW({
                    animateClass: "animate__animated", // animation css class (default is animated)
                });
                wow.init();
                // var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                //   target: "#navbar-example",
                // });
            </script>
            <script>
                $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
                    var template = $("select[name='invoice_template']").val();
                    var color = $("input[name='invoice_color']:checked").val();
                    $('iframe').attr('src', '<?php echo e(url($currentWorkspace->slug . '/invoices/preview')); ?>/' + template + '/' +
                        color);
                });

                $(document).ready(function() {

                    var $dragAndDrop = $("body .task-stages tbody").sortable({
                        handle: '.sort-handler'
                    });

                    var $repeater = $('.task-stages').repeater({
                        initEmpty: true,
                        defaultValues: {},
                        show: function() {
                            $(this).slideDown();
                        },
                        hide: function(deleteElement) {
                            if (confirm('<?php echo e(__('Are you sure ?')); ?>')) {
                                $(this).slideUp(deleteElement);
                            }
                        },
                        ready: function(setIndexes) {
                            $dragAndDrop.on('drop', setIndexes);
                        },
                        isFirstItemUndeletable: true
                    });


                    var value = $(".task-stages").attr('data-value');
                    if (typeof value != 'undefined' && value.length != 0) {
                        value = JSON.parse(value);
                        $repeater.setList(value);
                    }

                    var $dragAndDropBug = $("body .bug-stages tbody").sortable({
                        handle: '.sort-handler'
                    });

                    var $repeaterBug = $('.bug-stages').repeater({
                        initEmpty: true,
                        defaultValues: {},
                        show: function() {
                            $(this).slideDown();
                        },
                        hide: function(deleteElement) {
                            if (confirm('<?php echo e(__('Are you sure ?')); ?>')) {
                                $(this).slideUp(deleteElement);
                            }
                        },
                        ready: function(setIndexes) {
                            $dragAndDropBug.on('drop', setIndexes);
                        },
                        isFirstItemUndeletable: true
                    });


                    var valuebug = $(".bug-stages").attr('data-value');
                    if (typeof valuebug != 'undefined' && valuebug.length != 0) {
                        valuebug = JSON.parse(valuebug);
                        $repeaterBug.setList(valuebug);
                    }
                    $(document).on('click', '.list-group-item', function() {
                        $('.list-group-item').removeClass('active');
                        $('.list-group-item').removeClass('text-primary');
                        setTimeout(() => {
                            $(this).addClass('active').removeClass('text-primary');
                        }, 10);
                    });

                    var type = window.location.hash.substr(1);
                    $('.list-group-item').removeClass('active');
                    $('.list-group-item').removeClass('text-primary');
                    if (type != '') {
                        $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
                    } else {
                        $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
                    }
                });
            </script>


            <script>
                $('#logo').change(function() {

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#dark_logo').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);

                });

                $('#logo_white').change(function() {

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);

                });

                $('#small-favicon').change(function() {

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#favicon').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);

                });
            </script>

            <script>
                $(document).ready(function() {
                    if ($('.gdpr_fulltime').is(':checked')) {
                        $('.fulltime').show();
                    } else {
                        $('.fulltime').hide();
                    }
                    $('#gdpr_cookie').on('change', function() {
                        if ($('.gdpr_fulltime').is(':checked')) {
                            $('.fulltime').show();
                        } else {
                            $('.fulltime').hide();
                        }
                    });
                });

                var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                    target: '#useradd-sidenav',
                    offset: 300
                })

                $('.themes-color-change').on('click', function() {
                    var color_val = $(this).data('value');
                    $('.theme-color').prop('checked', false);
                    $('.themes-color-change').removeClass('active_color');
                    $(this).addClass('active_color');
                    $(`input[value=${color_val}]`).prop('checked', true);

                });

                function check_theme(color_val) {
                    $('.theme-color').prop('checked', false);
                    $('input[value="' + color_val + '"]').prop('checked', true);
                    $('#color_value').val(color_val);
                }
            </script>

            <script>
                $(document).on("click", ".email-template-checkbox", function() {
                    var chbox = $(this);
                    $.ajax({
                        url: chbox.attr('data-url'),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            status: chbox.val()
                        },
                        type: 'POST',
                        success: function(response) {
                            if (response.is_success) {
                                show_toastr('<?php echo e(__('Success')); ?>', response.success, 'success');
                                if (chbox.val() == 1) {
                                    $('#' + chbox.attr('id')).val(0);
                                } else {
                                    $('#' + chbox.attr('id')).val(1);
                                }
                            } else {
                                show_toastr('<?php echo e(__('Error')); ?>', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('<?php echo e(__('Error')); ?>', response.error, 'error');
                            } else {
                                show_toastr('<?php echo e(__('Error')); ?>', response, 'error');
                            }
                        }
                    })
                });
            </script>
            <script>
                function cust_theme_bg() {
                    var custthemebg = document.querySelector("#cust-theme-bg");

                    if (custthemebg.checked) {
                        document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                        document
                            .querySelector(".dash-header:not(.dash-mob-header)")
                            .classList.add("transprent-bg");
                    } else {
                        document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                        document
                            .querySelector(".dash-header:not(.dash-mob-header)")
                            .classList.remove("transprent-bg");
                    }

                }

                function cust_darklayout() {
                    var custdarklayout = document.querySelector("#cust-darklayout");

                    if (custdarklayout.checked) {
                        document
                            .querySelector(".m-header > .b-brand > .logo-lg")
                            .setAttribute("src", "<?php echo e(asset('assets/images/logo.svg')); ?>");
                        document
                            .querySelector("#main-style-link")
                            .setAttribute("href", "<?php echo e(asset('assets/css/style-dark.css')); ?>");
                    } else {
                        document
                            .querySelector(".m-header > .b-brand > .logo-lg")
                            .setAttribute("src", "<?php echo e(asset('assets/images/logo-dark.svg')); ?>");
                        document
                            .querySelector("#main-style-link")
                            .setAttribute("href", "<?php echo e(asset('assets/css/style.css')); ?>");
                    }

                }
            </script>
        <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/users/setting.blade.php ENDPATH**/ ?>