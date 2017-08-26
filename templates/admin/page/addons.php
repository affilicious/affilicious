<?php /** @var array $products */ ?>

<div class="wrap">
    <h1><?php _e('Add-ons', 'affilicious'); ?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="aff-addons-container">
                    <?php foreach ($products as $product): ?>
	                    <?php aff_render_template('admin/page/addons/item', ['product' => $product]); ?>
                    <?php endforeach; ?>

	                <?php aff_render_template('admin/page/addons/item-soon-more'); ?>
                </div>
            </div>

            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
	                <?php aff_render_template('admin/page/addons/about'); ?>

                    <?php aff_render_template('admin/page/addons/resources'); ?>
                </div>
            </div>
        </div>

        <br class="clear">
    </div>
</div>
