
<?php if($get == 'saved'): ?>
    <table class="messenger-list-item m-li-divider" data-contact="<?php echo e(Auth::user()->id); ?>">
        <tr data-action="0">
            
            <td>
                <div class="avatar av-m" style="background-color: #d9efff; text-align: center;">
                    <span class="far fa-bookmark" style="font-size: 22px; color: #68a5ff; margin-top: calc(50% - 10px);"></span>
                </div>
            </td>
            
            <td>
                <p data-id="<?php echo e(Auth::user()->id); ?>" data-type="user">Saved Messages <span>You</span></p>
                <span>Save messages secretly</span>
            </td>
        </tr>
    </table>
<?php endif; ?>


<?php if($get == 'users'): ?>
    <?php $user_obj = new \App\User($user->toArray()) ?>
    <table class="messenger-list-item" data-contact="<?php echo e($user->id); ?>">
        <tr data-action="0">
            
            <td style="position: relative">
                <?php if($user->active_status): ?>
                    <span class="activeStatus"></span>
                <?php endif; ?>
                <div class="avatar av-m"
                     style="background-image: url('<?php echo e(asset('/storage/'.config('chatify.user_avatar.folder').'/'.$user->avatar)); ?>');">
                </div>
            </td>
            
            <td>
                <p data-id="<?php echo e($user->id); ?>" data-type="user">
                    <?php echo e(strlen($user_obj->name) > 12 ? trim(substr($user_obj->name,0,12)).'..' : $user_obj->name); ?>

                    <span><?php echo e($lastMessage  ? $lastMessage->created_at->diffForHumans() : ''); ?></span></p>
                <span>
            
                    <?php echo $lastMessage and $lastMessage->from_id == Auth::user()->id
                        ? '<span class="lastMessageIndicator">You :</span>'
                        : ''; ?>

                    
                    <?php if($lastMessage): ?>
                        <?php if($lastMessage->attachment == null): ?>
                            <?php echo e(strlen($lastMessage->body) > 30
                                ? trim(substr($lastMessage->body, 0, 30)).'..'
                                : $lastMessage->body); ?>

                        <?php else: ?>
                            <span class="fas fa-file"></span> Attachment
                        <?php endif; ?>
                    <?php endif; ?>
        </span>
                
                <?php echo $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : ''; ?>

            </td>

        </tr>
    </table>
<?php endif; ?>


<?php if($get == 'search_item'): ?>
    <table class="messenger-list-item" data-contact="<?php echo e($user->id); ?>">
        <tr data-action="0">
            
            <td>
                <div class="avatar av-m"
                     style="background-image: url('<?php echo e(asset('/storage/'.config('chatify.user_avatar.folder').'/'.$user->avatar)); ?>');">
                </div>
            </td>
            
            <td>
                <p data-id="<?php echo e($user->id); ?>" data-type="user">
                <?php echo e(strlen($user->name) > 12 ? trim(substr($user->name,0,12)).'..' : $user->name); ?>

            </td>

        </tr>
    </table>
<?php endif; ?>


<?php if($get == 'sharedPhoto'): ?>
    <div class="shared-photo chat-image" style="background-image: url('<?php echo e($image); ?>')"></div>
<?php endif; ?>


<?php /**PATH /www/wwwroot/nileview.dokkan.design/v1/resources/views/vendor/Chatify/layouts/listItem.blade.php ENDPATH**/ ?>