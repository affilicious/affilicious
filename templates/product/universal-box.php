<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box', $product); ?>

<section class="aff-product-universal-box">
    <?php aff_render_template('product/universal-box/header', ['product' => $product]); ?>

    <?php aff_render_template('product/universal-box/body', ['product' => $product]); ?>

    <?php aff_render_template('product/universal-box/footer', ['product' => $product]); ?>
</section>

<?php do_action('affilicious_template_after_product_universal_box', $product); ?>
