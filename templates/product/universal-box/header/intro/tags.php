<?php
/**
 * @var array $product The product that belongs to the attribute choices.
 */
?>

<?php do_action('affilicious_template_before_product_tags', $product); ?>

<?php if(aff_has_product_tags($product)): ?>
    <div class="aff-product-tags"><?php aff_the_product_tags($product, '<span class="aff-product-tags-item">', '</span>'); ?></div>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_tags', $product); ?>
