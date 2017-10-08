<?php
/**
 * @var array $product The product that belongs to the universal box.
 */
?>

<?php do_action('affilicious_template_before_product_universal_header', $product); ?>

<div class="aff-product-universal-box-header aff-product-universal-box-row">
	<?php aff_render_template('product/universal-box/header/intro', ['product' => $product]); ?>
</div>

<?php do_action('affilicious_template_after_product_universal_header', $product); ?>
