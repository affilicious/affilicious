<?php
    /** @var \Affilicious\Common\License\License_Processor $license_processor */
    /** @var \Affilicious\Common\License\License_Manager $license_manager */
    $license_handlers = $license_manager->get_license_handlers();
?>

<div class="aff-license-container">
    <?php foreach ($license_handlers as $license_handler): ?>
        <?php $status = $license_processor->process($license_handler); ?>
        <div class="aff-license-item <?php if(aff_is_license_status_success($status)): ?>aff-license-item-success<?php endif; ?> <?php if(aff_is_license_status_error($status)): ?>aff-license-item-error<?php endif; ?>">
            <h3><?php echo esc_html($license_handler->get_item_name()); ?></h3>
            <div class="aff-license-item-content">
                <input class="regular-text"
                       placeholder="<?php _e('Enter license code here...', 'affilicious'); ?>"
                       name="<?php printf('aff-license-%s', $license_handler->get_item_key()); ?>"
                       value="<?php aff_the_license_key($license_handler->get_item_key()); ?>"/>

                <?php if(aff_has_license_status_message($status)): ?>
                    <p><?php aff_the_license_status_message($status); ?></p>
                <?php endif; ?>

                <button class="button-primary">
                    <?php _e('Save', 'affilicious'); ?>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
