<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_header', $product); ?>

<header class="aff-product-universal-box-header aff-product-universal-box-row">
	<?php aff_render_template('product/universal-box/header/intro', ['product' => $product]); ?>
</header>

<?php do_action('affilicious_template_after_product_universal_header', $product); ?>
