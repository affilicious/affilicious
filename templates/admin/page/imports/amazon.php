<?php
    /** @var array $provider */
    /** @var array $shop_templates */
    /** @var array $categories */
    /** @var array $taxonomies */
?>

<div id="aff-amazon-import" class="aff-import">
    <div id="aff-amazon-import-search" class="aff-import-search">
        <form id="aff-amazon-import-search-form" class="aff-import-search-form" data-provider-configured="<?php if(!empty($provider)): ?>true<?php else: ?>false<?php endif; ?>"></form>

	    <?php if(empty($provider)): ?>
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

    <div id="aff-amazon-import-config" class="aff-import-config">
        <form id="aff-amazon-import-config-shop" class="aff-import-config-shop"></form>

        <form id="aff-amazon-import-config-status" class="aff-import-config-status"></form>

        <form id="aff-amazon-import-config-taxonomy" class="aff-import-config-taxonomy"></form>

        <form id="aff-amazon-import-config-action" class="aff-import-config-action"></form>
    </div>
</div>

<?php aff_render_template('admin/page/imports/amazon/search-form-template', ['categories' => $categories]); ?>

<?php aff_render_template('admin/page/imports/amazon/search-results-item-template'); ?>

<?php aff_render_template('admin/page/imports/amazon/search-load-more-template'); ?>

<?php aff_render_template('admin/page/imports/amazon/config-shop-template', ['shop_templates' => $shop_templates]); ?>

<?php aff_render_template('admin/page/imports/amazon/config-status-template'); ?>

<?php aff_render_template('admin/page/imports/amazon/config-taxonomy-template', ['taxonomies' => $taxonomies]); ?>

<?php aff_render_template('admin/page/imports/amazon/config-action-template'); ?>
