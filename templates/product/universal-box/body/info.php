<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_info', $product); ?>

<div class="aff-product-universal-box-info aff-product-universal-box-column-half-width aff-product-universal-box-column">
	<?php aff_render_template('product/universal-box/body/info/attribute-choices', ['product' => $product]); ?>

	<?php aff_render_template('product/universal-box/body/info/details', ['product' => $product]); ?>
</div>

<?php do_action('affilicious_template_after_product_universal_box_info', $product); ?>
