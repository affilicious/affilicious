<div id="aff-amazon-import" class="aff-import">
    <div id="aff-amazon-import-search" class="aff-import-search">
        <form id="aff-amazon-import-search-form" class="aff-import-search-form" data-provider-configured="<?php if(!empty($amazon_provider_configured)): ?>true<?php else: ?>false<?php endif; ?>"></form>

	    <?php if(empty($amazon_provider_configured)): ?>
            <div class="aff-import-provider-not-configured aff-notice aff-error-notice">
                <b><?php _e('Amazon provider not configured.', 'affilicious'); ?></b>
			    <?php echo sprintf(
				    __('Add your credentials <a href="%s" target="_blank">here</a> to use the Amazon import.', 'affilicious'),
				    admin_url('admin.php?page=crbn-amazon.php')
			    ); ?>
            </div>
	    <?php endif; ?>

        <div id="aff-amazon-import-search-results" class="aff-import-search-results"></div>

        <div id="aff-amazon-import-search-load-more" class="aff-import-search-load-more"></div>
    </div>

    <form id="aff-amazon-import-config" class="aff-import-config"></form>
</div>

<?php include (__DIR__ . '/amazon/search-form-template.php'); ?>

<?php include (__DIR__ . '/amazon/search-results-item-template.php'); ?>

<?php include (__DIR__ . '/amazon/search-load-more-template.php'); ?>

<?php include (__DIR__ . '/amazon/config-template.php'); ?>
