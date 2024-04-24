<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Tracker')); ?>

<?php $__env->stopSection(); ?>    

<?php
   
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
?>

<?php $__env->startSection('links'); ?>
<?php if(\Auth::guard('client')->check()): ?>   
<li class="breadcrumb-item"><a href="<?php echo e(route('client.home')); ?>"><?php echo e(__('Home')); ?></a></li>
 <?php else: ?>
 <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?></a></li>
 <?php endif; ?>
 <?php if(\Auth::guard('client')->check()): ?>  
<li class="breadcrumb-item"><a href="<?php echo e(route('client.projects.index',$currentWorkspace->slug)); ?>"><?php echo e(__('Project')); ?></a></li>
 <?php else: ?>  
<li class="breadcrumb-item"><a href="<?php echo e(route('projects.index',$currentWorkspace->slug)); ?>"><?php echo e(__('Project')); ?></a></li>
<?php endif; ?>
<li class="breadcrumb-item"><a href="<?php echo e(route($client_keyword.'projects.show',[$currentWorkspace->slug,$project->id])); ?>"><?php echo e(__('Project Details')); ?></a></li>
<li class="breadcrumb-item"><?php echo e(__('Time Tracker')); ?></li>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('action-button'); ?>

 <a href="<?php echo e(route($client_keyword.'projects.show',[$currentWorkspace->slug,$project->id])); ?>" class="btn-submit btn btn-sm btn-primary mx-1" data-toggle="tooltip" title="<?php echo e(__('Back')); ?>">
        <i class=" ti ti-arrow-back-up"></i> 
    </a>


<?php $__env->stopSection(); ?>

   
<?php $__env->startPush('css-page'); ?>
    <link rel="stylesheet" href="<?php echo e(url('assets/custom/libs/swiper/dist/css/swiper.min.css')); ?>">
  
    
    <style>
        .product-thumbs .swiper-slide img {
            border:2px solid transparent;
            object-fit: cover;
            cursor: pointer;
        }
        .product-thumbs .swiper-slide-active img {
            border-color: #bc4f38;
        }

        .product-slider .swiper-button-next:after,
        .product-slider .swiper-button-prev:after {
            font-size: 20px;
            color: #000;
            font-weight: bold;
        }

       .modal-dialog.modal-md {
            background-color: #fff !important;
        } 
        /* .modal-backdrop {
            background:transparent !important;
        } */
        .no-image{
            min-height: 300px;
            align-items: center;
            display: flex;
            justify-content: center;
        }
      
    </style>
<?php $__env->stopPush(); ?>


<?php $__env->startSection('content'); ?>
    <div class="row">
          <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-border-style ">
                        <div class="table-responsive">
                                <table class=" table" id="selection-datatable">
                                    <thead>
                                        <tr>
                                            <th> <?php echo e(__('Description')); ?></th>
                                            <th> <?php echo e(__('Project')); ?></th>
                                            <th> <?php echo e(__('Task')); ?></th>
                                            <th> <?php echo e(__('Workspace')); ?></th>
                                            <th> <?php echo e(__('Start Time')); ?></th>
                                            <th> <?php echo e(__('End Time')); ?></th>
                                            <th><?php echo e(__('Total Time')); ?></th>
                                            <th><?php echo e(__('Action')); ?></th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                    <?php $__currentLoopData = $treckers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trecker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $total_name = App\Models\Utility::second_to_time($trecker->total_time);
                                        ?>
                                        
                                        <tr>
                                            <td><?php echo e(__($trecker->name)); ?></td>
                                             <td><?php echo e(__($trecker->project->name)); ?></td>
                                             <td><?php echo e(__($trecker->project_task)); ?></td>
                                             <td><?php echo e(__($trecker->project->workspaceData->name)); ?></td>
                                             <td><?php echo e(__(date("H:i:s",strtotime($trecker->start_time)))); ?></td>
                                             <td><?php echo e(__(date("H:i:s",strtotime($trecker->end_time)))); ?></td>
                                             <td><?php echo e(__($total_name)); ?></td>
                                             <td>
                                                 <img alt="Image placeholder" src="<?php echo e(asset('assets/images/gallery.png')); ?>" class="avatar view-images rounded-circle avatar-sm" data-toggle="tooltip" title="<?php echo e(__('View Screenshot images')); ?>" style="height: 25px;width:24px;margin-right:10px;cursor: pointer;" data-id="<?php echo e($trecker->id); ?>" id="track-images-<?php echo e($trecker->id); ?>">
 
 
                                                <a href="#" class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para" data-toggle="tooltip" title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?')); ?>" data-text="<?php echo e(__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="delete-form-<?php echo e($trecker->id); ?>">
                                                 <i class="ti ti-trash"></i>
                                                </a>
                                             </td>
                                             <?php echo Form::open(['method' => 'DELETE', 'route' => ['tracker.destroy', $trecker->id],'id'=>'delete-form-'.$trecker->id]); ?>

                                             <?php echo Form::close(); ?>

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

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg ss_modale" role="document">
            <div class="modal-content image_sider_div">
            
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script src="<?php echo e(url('assets/custom/libs/swiper/dist/js/swiper.min.js')); ?>"></script>

<script type="text/javascript">

    function init_slider(){
            if($(".product-left").length){
                    var productSlider = new Swiper('.product-slider', {
                        spaceBetween: 0,
                        centeredSlides: false,
                        loop:false,
                        direction: 'horizontal',
                        loopedSlides: 5,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev",
                        },
                        resizeObserver:true,
                    });
                var productThumbs = new Swiper('.product-thumbs', {
                    spaceBetween: 0,
                    centeredSlides: true,
                    loop: false,
                    slideToClickedSlide: true,
                    direction: 'horizontal',
                    slidesPerView: 7,
                    loopedSlides: 5,
                });
                productSlider.controller.control = productThumbs;
                productThumbs.controller.control = productSlider;
            }
        }


    $(document).on('click', '.view-images', function () {
        
            var p_url = "<?php echo e(route('tracker.image.view',$currentWorkspace->id)); ?>";
            var data = {
                'id': $(this).attr('data-id')
            };
           
            postAjax(p_url, data, function (res) {
                $('.image_sider_div').html(res);
                $('#exampleModalCenter').modal('show');   
                setTimeout(function(){
                    var total = $('.product-left').find('.product-slider').length
                    if(total > 0){
                        init_slider(); 
                    }
                
                },200);

            });
            });


            // ============================ Remove Track Image ===============================//
            $(document).on("click", '.track-image-remove', function () {
            var rid = $(this).attr('data-pid');
            $('.confirm_yes').addClass('image_remove');
            $('.confirm_yes').attr('image_id', rid);
            $('#cModal').modal('show');
            var total = $('.product-left').find('.swiper-slide').length
            });

    

            function removeImage(id){
                var p_url = "<?php echo e(route('tracker.image.remove')); ?>";
                var data = {id: id};
                deleteAjax(p_url, data, function (res) {
                    if(res.flag){
                        $('#slide-thum-'+id).remove();
                        $('#slide-'+id).remove();
                        setTimeout(function(){
                            var total = $('.product-left').find('.swiper-slide').length
                            if(total > 0){
                                init_slider();
                            }else{
                                $('.product-left').html('<div class="no-image"><h5 class="text-muted">Images Not Available .</h5></div>');
                            }
                        },200);
                    }
                    $('#cModal').modal('hide');
                    show_toastr('error', res.msg,"error");
                });
            }
            // $(document).on("click", '.remove-track', function () {
              
            // var rid = $(this).attr('data-id');
            // $('.confirm_yes').addClass('t_remove');
            // $('.confirm_yes').attr('uid', rid);
            // $('#cModal').modal('show');
        // });

      
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/projects/tracker.blade.php ENDPATH**/ ?>