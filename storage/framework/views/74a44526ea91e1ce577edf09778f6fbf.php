<?php

    if (isset($currentWorkspace)) {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $SITE_RTL = $setting->site_rtl;
        if ($setting->theme_color) {
            $color = $setting->theme_color;
        } else {
            $color = 'theme-3';
        }
    } else {
        $setting = App\Models\Utility::getAdminPaymentSettings();
        // $SITE_RTL = env('SITE_RTL');
        $SITE_RTL = $setting['site_rtl'];
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
    }

    if (\App::getLocale() == 'ar' || \App::getLocale() == 'he') {
        $SITE_RTL = 'on';
    }

    $meta_setting = App\Models\Utility::getAdminPaymentSettings();
    $meta_images = \App\Models\Utility::get_file('uploads/logo/');
    $logo = \App\Models\Utility::get_file('logo/');
    use App\Models\Utility;
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e($SITE_RTL == 'on' ? 'rtl' : ''); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <meta name="title" content="<?php echo e($meta_setting['meta_keywords']); ?>">
    <meta name="description" content="<?php echo e($meta_setting['meta_description']); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content= "<?php echo e(env('APP_URL')); ?>">
    <meta property="og:title" content="<?php echo e($meta_setting['meta_keywords']); ?>">
    <meta property="og:description" content="<?php echo e($meta_setting['meta_description']); ?>">
    <meta property="og:image" content="<?php echo e(asset($meta_images . $meta_setting['meta_image'])); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo e(env('APP_URL')); ?>">
    <meta property="twitter:title" content="<?php echo e($meta_setting['meta_keywords']); ?>">
    <meta property="twitter:description" content="<?php echo e($meta_setting['meta_description']); ?>">
    <meta property="twitter:image" content="<?php echo e(asset($meta_images . $meta_setting['meta_image'])); ?>">

    <title>
        <?php echo e('aCeler Project'); ?> ~<?php echo $__env->yieldContent('page-title'); ?>
    </title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e($logo . 'favicon.png' . '?' . time()); ?>">

    <?php if($setting['cust_darklayout'] == 'on'): ?>
        <?php if(isset($SITE_RTL) && $SITE_RTL == 'on'): ?>
            <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-rtl.css')); ?>" id="main-style-link">
        <?php endif; ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-dark.css')); ?>">
    <?php else: ?>
        <?php if(isset($SITE_RTL) && $SITE_RTL == 'on'): ?>
            <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-rtl.css')); ?>" id="main-style-link">
        <?php else: ?>
            <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="main-style-link">
        <?php endif; ?>
    <?php endif; ?>

    <?php if(isset($SITE_RTL) && $SITE_RTL == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-auth-rtl.css')); ?>" id="main-style-link">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-auth.css')); ?>" id="main-style-link">
    <?php endif; ?>
    <?php if($setting['cust_darklayout'] == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-dark.css')); ?>" id="main-style-link">
    <?php endif; ?>
</head>


<body class="<?php echo e($color); ?>">
    <?php
    $dir = base_path() . '/resources/lang/';
    $glob = glob($dir . '*', GLOB_ONLYDIR);
    $arrLang = array_map(function ($value) use ($dir) {
        return str_replace($dir, '', $value);
    }, $glob);
    $arrLang = array_map(function ($value) use ($dir) {
        return preg_replace('/[0-9]+/', '', $value);
    }, $arrLang);
    $arrLang = array_filter($arrLang);
    $currantLang = basename(App::getLocale());
    $client_keyword = Request::route()->getName() == 'client.login' ? 'client.' : '';
    ?>

    <script src="<?php echo e(asset('assets/js/vendor-all.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/feather.min.js')); ?>"></script>

    <script>
        feather.replace();
    </script>
    <script>
        feather.replace();

        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
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
        });

        var custdarklayout = document.querySelector("#cust-darklayout");
        custdarklayout.addEventListener("click", function() {
            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "../assets/images/logo.svg");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "../assets/css/style-dark.css");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "../assets/images/logo-dark.svg");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "../assets/css/style.css");
            }
        });

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>
    <?php echo $__env->yieldPushContent('custom-scripts'); ?>
    <!-- [ auth-signup ] start -->

    <?php
        $company_logo = App\Models\Utility::get_logo();
    ?>
    
    <div class="custom-login"
        style="background-image: url('https://alsinadocs.piwigo.com/_datas/n/z/w/nzwsilkwfp/i/uploads/n/z/w/nzwsilkwfp//2021/02/25/20210225104025-91ec746b-xl.png'); background-repeat: no-repeat;">
        <div class="login-bg-img">
            <img src="<?php echo e(asset('assets/img/' . $color . '.svg')); ?>" class="login-bg-1">
            <img src="<?php echo e(asset('assets/img/user2.svg')); ?>" class="login-bg-2">
        </div>
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">
            <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">

                        

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarlogin" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                <?php echo $__env->make('landingpage::layouts.buttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <a class="" href="#"><?php echo $__env->yieldContent('language-bar'); ?></a>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>
            </main>

            <div class="row justify-content-center">
                <div class="col-md-4">
                    <?php if(session()->has('info')): ?>
                        <div class="alert alert-primary">
                            <?php echo e(session()->get('info')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session()->has('status')): ?>
                        <div class="alert alert-info">
                            <?php echo e(session()->get('status')); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="text-center">
                <div class="auth-footer">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class=" col-6 col-md-4 rounded text-center" style="background: white; color: #AA182C;">
                                <a href="#"> <span>
                                    &copy; <?php echo e(date('Y')); ?>

                                 <?php echo e('aCeler Project'); ?> ~<?php echo $__env->yieldContent('page-title'); ?>   
                                </span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <?php if($meta_setting['enable_cookie'] == 'on'): ?>
        <?php echo $__env->make('layouts.cookie_consent', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/layouts/guest.blade.php ENDPATH**/ ?>