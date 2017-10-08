<?php
/**
 * @var array $product The product that belongs to the universal box.
 */
?>

<?php do_action('affilicious_template_before_product_universal_box_intro', $product); ?>

<div class="aff-product-universal-box-intro aff-product-universal-box-column-full-width aff-product-universal-box-column">
	<?php aff_render_template('product/title', ['product' => $product]); ?>

	<?php aff_render_template('product/tags', ['product' => $product]); ?>

	<?php aff_render_template('product/review', ['product' => $product]); ?>

	<?php aff_render_template('product/price', ['product' => $product]); ?>
</div>

<?php do_action('affilicious_template_after_product_universal_box_intro', $product); ?>
