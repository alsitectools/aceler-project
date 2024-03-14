<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\GuestLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginal71c6471fa76ce19017edc287b6f4508c = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('auth-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php
            $languages = \App\Models\Utility::languages();
            $setting = \App\Models\Utility::getAdminPaymentSettings();
            App\models\Utility::setCaptchaConfig();
        ?>
        <?php $__env->startSection('page-title'); ?>
            <?php echo e(__('Login')); ?>

        <?php $__env->stopSection(); ?>


        <?php $__env->startSection('language-bar'); ?>
            <div href="#" class="lang-dropdown-only-desk">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text"> <?php echo e(ucFirst($languages[$lang])); ?>

                        </span>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('login', $code)); ?>" tabindex="0"
                                class="dropdown-item <?php echo e($code == $lang ? 'active' : ''); ?>">
                                <span><?php echo e(ucFirst($language)); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </li>
            </div>
        <?php $__env->stopSection(); ?>

        <?php $__env->startSection('content'); ?>
            <div class="card-body">
                <div class="">
                    <h2 class="mb-3 f-w-600"><?php echo e(__('Login')); ?></h2>
                </div>
                
                <?php if(session()->has('error')): ?>
                    <div>
                        <p class="text-danger"><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?>
                <form method="POST" id="form_data" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label"><?php echo e(__('Email')); ?></label>
                            <input type="email" class="form-control  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email"
                                id="emailaddress" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus
                                placeholder="<?php echo e(__('Enter Your Email')); ?>">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="error invalid-email text-danger" role="alert">
                                    <small><?php echo e($message); ?></small>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label"><?php echo e(__('Password')); ?></label>
                            <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                name="password" required autocomplete="current-password" id="password"
                                placeholder="<?php echo e(__('Enter Your Password')); ?>">
                        </div>
                        <div class="form-group mb-3 text-start">
                            <span>
                                <a href="<?php echo e(route('password.request', $lang)); ?>"
                                    tabindex="0"><?php echo e(__('Forgot Your Password?')); ?></a>
                            </span>
                        </div>

                        <?php if($setting['recaptcha_module'] == 'on'): ?>
                            <div class="form-group col-lg-12 col-md-12 mt-3">
                                
                                <?php echo NoCaptcha::display($setting['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []); ?>

                                <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="small text-danger" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <button type="submit" id="login_button"
                                class="btn btn-primary btn-block mt-2"><?php echo e(__('Login')); ?></button>
                        </div>
                        <!--  <p class="my-4 text-center">or register with</p> -->

                        
                        <p class="my-4 text-center">Don't have an account? <a href="<?php echo e(route('register', $lang)); ?>"
                                class="my-4 text-center text-primary"> Register</a></p>
                        
                </form>
                <div class="d-grid mt-3">
                    <button type="button" id="" class="btn btn-primary btn-block  "><a
                            href="<?php echo e(route('client.login', $lang)); ?>" class="" style="color:#fff">
                            <?php echo e(__('Client Login')); ?></a></button>
                </div>
            </div>
            
        <?php $__env->stopSection(); ?>
        <?php $__env->startPush('custom-scripts'); ?>
            <script src="<?php echo e(asset('assets/custom/libs/jquery/dist/jquery.min.js')); ?>"></script>
            <script>
                $(document).ready(function() {
                    $("#form_data").submit(function(e) {
                        $("#login_button").attr("disabled", true);
                        return true;
                    });
                });
            </script>
            <?php if($setting['recaptcha_module'] == 'on'): ?>
                <?php echo NoCaptcha::renderJs(); ?>

            <?php endif; ?>
        <?php $__env->stopPush(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal71c6471fa76ce19017edc287b6f4508c)): ?>
<?php $component = $__componentOriginal71c6471fa76ce19017edc287b6f4508c; ?>
<?php unset($__componentOriginal71c6471fa76ce19017edc287b6f4508c); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<style>
    .login-deafult {
        width: 139px !important;
    }
</style>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/auth/login.blade.php ENDPATH**/ ?>