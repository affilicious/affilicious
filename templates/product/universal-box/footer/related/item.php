<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<article class="aff-product-related-item">
	<?php if(has_post_thumbnail()): ?>
		<?php the_post_thumbnail('post-thumbnail', [
			'class' => 'aff-product-related-item-thumbnail',
		]); ?>
	<?php else: ?>
        <img class="aff-product-related-item-thumbnail" alt="<?php the_title(); ?>" src="<?php echo AFFILICIOUS_ROOT_URL . 'assets/public/dist/img/no-image.png'?>">
	<?php endif; ?>
</article>
