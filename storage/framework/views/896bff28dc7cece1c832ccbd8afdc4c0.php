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
            $setting = \App\Models\Utility::getAdminPaymentSettings();
            $languages = \App\Models\Utility::languages();
            App\models\Utility::setCaptchaConfig();
        ?>
        <?php $__env->startSection('page-title'); ?>
            <?php echo e(__('Register')); ?>

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
                    <h2 class="mb-3 f-w-600"><?php echo e(__('Register')); ?></h2>
                </div>
                <form method="POST" action="<?php echo e(route('register')); ?>">
                    <?php if(session('statuss')): ?>
                        <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                            <?php echo e(__('Email SMTP settings does not configured so please contact to your site admin.')); ?>

                        </div>
                    <?php endif; ?>
                    <?php echo csrf_field(); ?>
                    <div class="">

                        <div class="form-group mb-3">
                            <label for="fullname" class="form-label"><?php echo e(__('Full Name')); ?></label>
                            <input type="text" class="form-control  <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name"
                                id="fullname" value="<?php echo e(old('name')); ?>" required autocomplete="name" autofocus
                                placeholder="<?php echo e(__('Enter Your Name')); ?>">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="type" class="form-label"><?php echo e(__('messages.Type_user')); ?></label>
                            <input type="text" class="form-control  <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="type"
                                id="type" value="<?php echo e(old('type')); ?>" required autocomplete="type" autofocus
                                placeholder="<?php echo e(__('type user')); ?>">
                            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group mb-3">
                            
                            <label for="currant_workspace" class="form-label"><?php echo e(__('Workspace Name')); ?></label>
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="workspace">Options</label>
                                <select class="form-select" name="currant_workspace" id="currant_workspace">
                                    <option selected>Choose...</option>
                                    <option value="Alicante">Alicante</option>
                                    <option value="Aragon">Aragón</option>
                                    <option value="Asturas">Asturias</option>
                                    <option value="Baleares">Baleares</option>
                                    <option value="Catalunya">Cataluña</option>
                                    <option value="Galicia">Galicia</option>
                                    <option value="LasPalmas">Las Palmas</option>
                                    <option value="PaisVasco">Pais Vasco</option>
                                    <option value="Tenerife">Tenerife</option>
                                    <option value="Polonia">Polonia</option>
                                    <option value="Chile">Chile</option>
                                    <option value="Italia">Italia</option>
                                    <option value="Portugal">Portugal</option>
                                    <option value="EEUU">Estados Unidos de América</option>
                                    <option value="Dubai">Dubai</option>
                                    <option value="Uruguay">Uruguay</option>
                                    <option value="Marruecos">Marruecos</option>
                                    <option value="Rumania">Rumania</option>
                                    <option value="Panama">Panama</option>
                                    <option value="Peru">Perú</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Mexico">México</option>
                                    <option value="India">India</option>
                                    <option value="Filipinas">Filipinas</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="ReinoUnido">Reino Unido</option>
                                    <option value="Tunez">Tunez</option>
                                    <option value="Argelia">Argelia</option>
                                </select>
                            </div>
                            
                            <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="form-group mb-3">
                            <label for="emailaddress" class="form-label"><?php echo e(__('Email')); ?></label>
                            <input type="email" class="form-control  <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email"
                                id="emailaddress" value="<?php echo e(old('email')); ?>" required autocomplete="email"
                                placeholder="<?php echo e(__('Enter Your Email')); ?>">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
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
                                name="password" required autocomplete="new-password" id="password"
                                placeholder="<?php echo e(__('Enter Your Password')); ?>">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label"><?php echo e(__('Confirm Password')); ?></label>
                            <input type="password"
                                class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                name="password_confirmation" required autocomplete="new-password"
                                id="password_confirmation" placeholder="<?php echo e(__('Confirm Your Password')); ?>">

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
                                class="btn btn-primary btn-block mt-2"><?php echo e(__('Register')); ?></button>
                        </div>
                        <!--  <p class="my-4 text-center">or register with</p> -->
                        
                </form>
                
                <p class="mb-2 mt-2 text-center"><?php echo e(__('Already have an account?')); ?> <a
                        href="<?php echo e(route('login', $lang)); ?>" class="f-w-400 text-primary"><?php echo e(__('Login')); ?></a></p>
            </div>
        <?php $__env->stopSection(); ?>
        <?php $__env->startPush('custom-scripts'); ?>
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
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/auth/register.blade.php ENDPATH**/ ?>