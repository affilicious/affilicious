<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_attribute_choices', $product); ?>

<?php $attribute_choices = aff_get_product_attribute_choices($product) ?>
<?php if(!empty($attribute_choices)): ?>
    <!-- Don't use the deprecated "aff-product-attributes-container" -->
    <div class="aff-product-attributes aff-product-attributes-container">
        <ul class="aff-product-attributes-choices-list">
            <?php foreach ($attribute_choices as $attribute_choice): ?>
                <li class="aff-product-attributes-choices">
                    <span class="aff-product-attributes-choices-name"><?php echo esc_html($attribute_choice['name']); ?></span>
                        <ul class="aff-product-attributes-choice-list">
                        <?php foreach ($attribute_choice['attributes'] as $attribute): ?>
                            <li class="aff-product-attributes-choice <?php echo esc_attr($attribute['display']); ?>">
                                <?php if(!empty($attribute['permalink'])): ?>
                                    <a class="aff-product-attributes-choice-link" href="<?php echo esc_url($attribute['permalink']); ?>">
                                <?php endif; ?>

                                <?php echo esc_html($attribute['value']); ?>

                                <?php if(!empty($attribute['unit'])): ?>
                                    <!-- Don't use the deprecated "unit" or "aff-unit" CSS classes anymore -->
                                    <span class="aff-product-attributes-choice-unit aff-unit unit"><?php echo esc_html($attribute['unit']); ?></span>
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
<?php endif; ?>

<?php do_action('affilicious_template_after_product_universal_box_attribute_choices', $product); ?>
