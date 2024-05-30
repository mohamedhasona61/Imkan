<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="<?php echo e($html_class ?? ''); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php event(new \Modules\Layout\Events\LayoutBeginHead()); ?>
    <?php
    $favicon = setting_item('site_favicon');
    ?>
    <?php if($favicon): ?>
    <?php
    $file = (new \Modules\Media\Models\MediaFile())->findById($favicon);
    ?>
    <?php if(!empty($file)): ?>
    <link rel="icon" type="<?php echo e($file['file_type']); ?>" href="<?php echo e(asset('uploads/'.$file['file_path'])); ?>" />
    <?php else: ?>:
    <link rel="icon" type="image/png" href="<?php echo e(url('images/favicon.png')); ?>" />
    <?php endif; ?>
    <?php endif; ?>

    <?php echo $__env->make('Layout::parts.seo-meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link href="<?php echo e(asset('libs/bootstrap/css/bootstrap.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/brands.min.css" integrity="sha512-bSncow0ApIhONbz+pNI52n0trz5fMWbgteHsonaPk42JbunIeM9ee+zTYAUP1eLPky5wP0XZ7MSLAPxKkwnlzw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/fontawesome.min.css" integrity="sha512-8Vtie9oRR62i7vkmVUISvuwOeipGv8Jd+Sur/ORKDD5JiLgTGeBSkI3ISOhc730VGvA5VVQPwKIKlmi+zMZ71w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/regular.min.css" integrity="sha512-sWLOh8QiEOmwfP3jd0n7AneUOa6XKif5g8GU8FqdFMIbr6rYBt1PKWBp/Wv4E6PQ6N9lFtVAndujl9HbniDTog==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/solid.min.css" integrity="sha512-6/gTF62BJ06BajySRzTm7i8N2ZZ6StspU9uVWDdoBiuuNu5rs1a8VwiJ7skCz2BcvhpipLKfFerXkuzs+npeKA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/svg-with-js.min.css" integrity="sha512-T22AGZA32A7BJVwM85+3QTgGxP7lSHb88UwE3b19YtWs0mw6x27Pw5ea/60BQkcKO4vzzsXW230pxPdw6TthGQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/v4-font-face.min.css" integrity="sha512-YAIQTShdW1GyO8PvzxYSwqmoNBXWp/vntilAZvBogk0IPJYgyeQqVFwnoFBznX6maNw4emZZWyLLmClRXKDR3A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/v4-shims.min.css" integrity="sha512-pFN+FoTX/XKTsPGLqPfu1iibiJZvRUs1yQ++Xnx4GSQnRTXBisl4PEb7a1SHliCnSqD35d6ujp9i4tmWJT0Yvg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/v5-font-face.min.css" integrity="sha512-Ipf6bU2dOLSG8/16GCm8MfSEcNMAIQloGhcn+5o5FRRkmBIvShWK2IAZQX69PPd1aiZx0cZoLWTmWRSW68tPYA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="<?php echo e(asset('libs/ionicons/css/ionicons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/icofont/icofont.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/select2/css/select2.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('dist/frontend/css/notification.css')); ?>" rel="newest stylesheet">
    <link href="<?php echo e(asset('dist/frontend/css/app.css?_ver='.config('app.asset_version'))); ?>" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo e(asset("libs/daterange/daterangepicker.css")); ?>">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css' href='https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600&display=swap' type='text/css' media='all' />
    <?php echo \App\Helpers\Assets::css(); ?>

    <?php echo \App\Helpers\Assets::js(); ?>

    <?php echo $__env->make('Layout::parts.global-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Styles -->
    <?php echo $__env->yieldPushContent('css'); ?>
    
    <link href="<?php echo e(route('core.style.customCss')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('libs/carousel-2/owl.carousel.css')); ?>" rel="stylesheet">
    <?php if(setting_item_with_lang('enable_rtl') && app()->getLocale() === 'ar'): ?>
    <link href="<?php echo e(asset('dist/frontend/css/rtl.css')); ?>" rel="stylesheet">
    <?php endif; ?>

    <?php if(!is_demo_mode()): ?>
    <?php echo setting_item('head_scripts'); ?>

    <?php echo setting_item_with_lang_raw('head_scripts'); ?>

    <?php endif; ?>

    <?php if(Route::currentRouteName() != 'website'): ?>
    <style>
        .bravo_wrap .bravo_header .content .header-left .bravo-menu ul li a {
            color: #1a2b48;
        }
        .bravo_wrap .bravo_header .content .header-right .topbar-items li a {
            color: #1a2b48;
        }
        .socials a{
            color: #1a2b48;
        }
    </style>
    <?php endif; ?>

</head>

<body class="frontend-page <?php echo e(!empty($row->header_style) ? "header-".$row->header_style : "header-normal"); ?> <?php echo e($body_class ?? ''); ?> <?php if(setting_item_with_lang('enable_rtl') && app()->getLocale() === 'ar'): ?> is-rtl <?php endif; ?> <?php if(is_api()): ?> is_api <?php endif; ?>">


    <?php if(!is_demo_mode()): ?>
    <?php echo setting_item('body_scripts'); ?>

    <?php echo setting_item_with_lang_raw('body_scripts'); ?>

    <?php endif; ?>
    <div class="bravo_wrap">
        <?php if(!is_api()): ?>
        <?php echo $__env->make('Layout::parts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('Layout::parts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>

        <?php echo $__env->make('Layout::parts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <?php if(!is_demo_mode()): ?>
    <?php echo setting_item('footer_scripts'); ?>

    <?php echo setting_item_with_lang_raw('footer_scripts'); ?>

    <?php endif; ?>

 
 

</body>

</html><?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/modules/Layout/app.blade.php ENDPATH**/ ?>