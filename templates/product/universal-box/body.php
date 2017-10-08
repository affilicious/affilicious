<?php
/**
 * @var array $product The product that belongs to the universal box.
 */
?>

<?php do_action('affilicious_template_before_product_universal_box_body', $product); ?>

<div class="aff-product-universal-box-body aff-product-universal-box-row">
	<?php aff_render_template('product/universal-box/body/media', ['product' => $product]); ?>

	<?php aff_render_template('product/universal-box/body/info', ['product' => $product]); ?>
</div>

<?php do_action('affilicious_template_after_product_universal_box_body', $product); ?>

