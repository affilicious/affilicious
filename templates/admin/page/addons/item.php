<?php
	/** @var array $product */
?>

<div class="aff-addons-item">
	<img class="aff-addons-item-image" src="<?php echo esc_attr($download['info']['thumbnail']); ?>" />

	<div class="aff-addons-item-content">
		<h3 class="aff-addons-item-title"><?php echo esc_html($download['info']['title']); ?></h3>
		<p class="aff-addons-item-text"><?php echo esc_html($download['info']['excerpt']); ?></p>

		<a class="aff-addons-item-link" href="<?php echo esc_attr($download['info']['link']); ?>" target="_blank">
			<?php _e('Discover now', 'affilicious'); ?>
		</a>
	</div>
</div>
