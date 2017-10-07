<?php
/**
 * @var array $product The product that belongs to the attribute choices.
 * @var array $attribute_choices The attribute choices allow switching the product variants.
 * @var bool $escape Whether to escape the output or not.
 */
?>

<?php do_action('affilicious_template_before_product_attribute_choices', $product, $attribute_choices, $escape); ?>

<div class="aff-product-attributes-container">
	<ul class="aff-product-attributes-choices-list">
		<?php foreach ($attribute_choices as $attribute_choice): ?>
			<li class="aff-product-attributes-choices">
				<span class="aff-product-attributes-choices-name"><?php echo ($escape ? esc_html($attribute_choice['name']) : $attribute_choice['name']); ?></span>
					<ul class="aff-product-attributes-choice-list">
					<?php foreach ($attribute_choice['attributes'] as $attribute): ?>
						<li class="aff-product-attributes-choice <?php echo ($escape ? esc_attr($attribute['display']) : $attribute['display']); ?>">
							<?php if(!empty($attribute['permalink'])): ?>
								<a class="aff-product-attributes-choice-link" href="<?php echo ($escape ? esc_url($attribute['permalink']) : $attribute['permalink']); ?>">
							<?php endif; ?>

							<?php echo ($escape ? esc_html($attribute['value']) : $attribute['value']); ?>

							<?php if(!empty($attribute['unit'])): ?>
								<!-- Don't use the deprecated "unit" or "aff-unit" CSS classes anymore -->
								<span class="aff-product-attributes-choice-unit aff-unit unit"><?php echo ($escape ? esc_html($attribute['unit']) : $attribute['unit']); ?></span>
							<?php endif; ?>

							<?php if(!empty($attribute['permalink'])): ?>
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<?php do_action('affilicious_template_after_product_attribute_choices', $product, $attribute_choices, $escape); ?>
