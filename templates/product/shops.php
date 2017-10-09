<?php
/**
 * @var array $product The product that belongs to the attribute choices.
 */
?>

<?php do_action('affilicious_template_before_product_shops', $product); ?>

<?php $shops = aff_get_product_shops($product); ?>
<?php if(!empty($shops)): ?>
    <div class="aff-product-shops">
        <?php foreach ($shops as $shop): ?>
            <div class="aff-product-shops-item <?php if(!aff_is_shop_available($shop)): ?>aff-product-shops-item-not-available<?php endif; ?>">
                <div class="aff-product-shops-item-brand aff-product-shops-item-area">
		            <?php if(aff_has_shop_thumbnail($shop)): ?>
			            <?php aff_the_shop_thumbnail($shop, 'medium', ['class' => 'aff-product-shops-item-thumbnail']); ?>
		            <?php else: ?>
                        <span class="aff-product-shops-item-name"><?php aff_the_shop_name($shop); ?></span>
		            <?php endif; ?>
                </div>

                <div class="aff-product-shops-item-info aff-product-shops-item-area">
                    <?php if(aff_is_shop_available($shop) && aff_has_shop_price($shop)): ?>
                        <div class="aff-product-shops-item-price">
                            <span class="aff-product-shops-item-price-current"><?php aff_the_shop_price($shop); ?></span>

                            <?php if(aff_has_shop_old_price($shop)): ?>
                                <span class="aff-product-shops-item-price-old"><?php aff_the_shop_old_price($shop); ?></span>
                            <?php endif; ?>

                            <small class="aff-product-shops-item-price-indication aff-product-shops-item-indication">
		                        <?php aff_the_shop_price_indication($shop); ?>
                            </small>
                        </div>
                    <?php endif; ?>

                    <div class="aff-product-shops-item-actions">
                        <?php if(aff_is_shop_available($shop)): ?>
                            <a class="aff-product-shops-item-button-buy aff-product-shops-item-button"
                               href="<?php echo esc_attr(aff_get_shop_affiliate_link($shop)); ?>"
                               rel="nofollow" target="blank">
                                Jetzt kaufen
                            </a>
                        <?php else: ?>
                            <a class="aff-product-shops-item-button-not-available aff-product-shops-item-button"
                               href="<?php echo esc_attr(aff_get_shop_affiliate_link($shop)); ?>"
                               rel="nofollow" target="blank">
                                Nicht verfügbar
                            </a>
                        <?php endif; ?>

                        <small class="aff-product-shops-item-indication-updated-at aff-product-shops-item-indication">
		                    <?php aff_the_shop_updated_at_indication($shop); ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_shops', $product); ?>
