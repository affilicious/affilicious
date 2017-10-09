<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_footer', $product); ?>

<footer class="aff-product-universal-box-footer aff-product-universal-box-row">
	<?php if(aff_has_product_related_products() && aff_has_product_related_accessories()): ?>
	    <?php aff_render_template('product/universal-box/footer/related', ['product' => $product]); ?>
    <?php endif; ?>
</footer>

<?php do_action('affilicious_template_after_product_universal_footer', $product); ?>
