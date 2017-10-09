<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_thumbnail', $product); ?>

<?php if(has_post_thumbnail()): ?>
    <?php the_post_thumbnail('post-thumbnail', [
        'class' => 'aff-product-thumbnail',
    ]); ?>
<?php else: ?>
    <img class="aff-product-thumbnail" src="<?php echo AFFILICIOUS_ROOT_URL . 'assets/public/dist/img/no-image.pmg'; ?>" alt="No image"
<?php endif; ?>

<?php do_action('affilicious_template_after_product_thumbnail', $product); ?>

