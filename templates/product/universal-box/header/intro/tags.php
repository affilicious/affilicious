<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_tags', $product); ?>

<?php if(aff_has_product_tags($product)): ?>
    <div class="aff-product-tags"><?php aff_the_product_tags($product, '<span class="aff-product-tags-item">', '</span>'); ?></div>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_universal_box_tags', $product); ?>
