<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_related_item', $product); ?>

<article class="aff-product-related-item">
	<?php if(has_post_thumbnail()): ?>
        <a href="<?php the_permalink() ?>">
            <?php the_post_thumbnail('post-thumbnail', [
                'class' => 'aff-product-related-item-thumbnail',
            ]); ?>
        </a>
	<?php else: ?>
        <img class="aff-product-related-item-thumbnail" alt="<?php the_title(); ?>" src="<?php echo AFFILICIOUS_ROOT_URL . 'assets/public/dist/img/no-image.png'?>">
	<?php endif; ?>
</article>

<?php do_action('affilicious_template_after_product_universal_box_related_item', $product); ?>
