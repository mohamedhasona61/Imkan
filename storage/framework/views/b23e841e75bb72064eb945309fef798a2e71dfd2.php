<div >
    <div class="bravo_gallery w-100" >
        <div class="btn-group w-100">
            <span class="btn-transparent has-icon bravo-video-popup w-100" <?php if($youtube): ?> data-toggle="modal" <?php endif; ?> data-src="<?php echo e(str_ireplace("watch?v=","embed/",$youtube)); ?>" data-target="#video-<?php echo e($id); ?>">
                <video width="100%" muted autoplay loop>
                    <source src="<?php echo e(asset('dist/frontend/imkan.mp4')); ?>" type="video/mp4">
                    <source src="<?php echo e(asset('dist/frontend/imkan.mp4')); ?>" type="video/ogg">
                   </video> 
            </span>
        </div>
        <?php if($youtube): ?>
            <!--<div class="modal fade" id="video-<?php echo e($id); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">-->
            <!--    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">-->
            <!--        <div class="modal-content p-0">-->
            <!--            <div class="modal-body">-->
            <!--                <div class="embed-responsive embed-responsive-16by9">-->
            <!--                    <iframe class="embed-responsive-item bravo_embed_video" src="<?php echo e(handleVideoUrl($youtube)); ?>" allowscriptaccess="always" allow="autoplay"></iframe>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/themes/Base/Template/Views/frontend/blocks/video-player.blade.php ENDPATH**/ ?>