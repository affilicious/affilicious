<div class="wrap" style="margin-bottom: 10px;">
    <h1><?php _e('Import', 'affilicious'); ?></h1>

    <div class="nav-tab-wrapper" style="margin-bottom: 30px;">
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=aff_product&page=import')); ?>" class="nav-tab nav-tab-active">
            <?php _e('Amazon', 'affilicious'); ?>
        </a>
    </div>

    <?php include (__DIR__ . '/import/amazon.php'); ?>
</div>
