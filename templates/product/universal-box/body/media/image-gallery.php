<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_image_gallery', $product); ?>

<?php $image_gallery = aff_get_product_image_gallery($product); ?>
<div class="aff-product-image-gallery">
	<?php foreach ($image_gallery as $image): ?>
		<div class="aff-product-image-gallery-item">
			<?php echo wp_get_attachment_image($image, 'aff-product-thumbnail', false, [
				'class' => 'aff-product-image-gallery-item-thumbnail'
			]); ?>
		</div>
	<?php endforeach; ?>
</div>

<?php do_action('affilicious_template_after_product_image_gallery', $product); ?>
