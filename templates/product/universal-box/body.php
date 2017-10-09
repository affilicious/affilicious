<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_body', $product); ?>

<div class="aff-product-universal-box-body aff-product-universal-box-row">
	<?php aff_render_template('product/universal-box/body/media', ['product' => $product]); ?>

	<?php aff_render_template('product/universal-box/body/info', ['product' => $product]); ?>

    <?php if(aff_has_product_shops($product)): ?>
	    <?php aff_render_template('product/universal-box/body/buy', ['product' => $product]); ?>
    <?php endif; ?>
</div>

<?php do_action('affilicious_template_after_product_universal_box_body', $product); ?>
