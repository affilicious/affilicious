<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_price', $product); ?>

<?php if(aff_has_product_price($product)): ?>
	<div class="aff-product-price">
		<span class="aff-product-price-current"><?php aff_the_product_price($product); ?></span>

		<?php if(aff_has_product_old_price()): ?>
			<span class="aff-product-price-old"><?php aff_the_product_old_price($product); ?></span>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_universal_box_price', $product); ?>
