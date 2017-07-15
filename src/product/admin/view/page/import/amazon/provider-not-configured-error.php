<?php if(empty($amazon_provider_configured)): ?>
    <div class="aff-amazon-import-provider-not-configured aff-error-notice">
        <b><?php _e('Amazon provider not configured.', 'affilicious'); ?></b>
        <?php echo sprintf(
            __('Add your credentials <a href="%s" target="_blank">here</a> to use the Amazon import.', 'affilicious'),
            admin_url('admin.php?page=crbn-amazon.php')
        ); ?>
    </div>
<?php endif; ?>
