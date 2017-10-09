<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_intro', $product); ?>

<div class="aff-product-universal-box-related aff-product-universal-box-column-full-width aff-product-universal-box-column">
	<?php if(aff_has_product_related_products()): ?>
		<?php aff_render_template('product/universal-box/footer/related/products', ['product' => $product]); ?>
	<?php endif; ?>

	<?php if(aff_has_product_related_accessories()): ?>
		<?php aff_render_template('product/universal-box/footer/related/accessories', ['product' => $product]); ?>
	<?php endif; ?>
</div>

<?php do_action('affilicious_template_after_product_universal_box_intro', $product); ?>
