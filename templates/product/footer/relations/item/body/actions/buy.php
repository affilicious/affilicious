<?php $shop = aff_get_product_cheapest_shop(); ?>

<?php if(!empty($shop) &&! affvc_is_buy_button_hidden()): ?>
    <?php if(aff_is_shop_available($shop)): ?>
        <a class="product-relations-item-body-actions-buy btn btn-buy btn-block mb-2" href="<?php echo $shop['tracking']['affiliate_link']; ?>"
           rel="nofollow" target="_blank" data-shop-name="<?php echo $shop['name']; ?>">
            <?php affvc_the_buy_button_text($shop) ?>
        </a>
    <?php else: ?>
        <a class="product-relations-item-body-actions-not-available btn btn-not-available btn-block mb-2" href="<?php echo $shop['tracking']['affiliate_link']; ?>"
           rel="nofollow" target="_blank" data-shop-name="<?php echo $shop['name']; ?>">
            <?php affvc_the_not_available_button_text($shop) ?>
        </a>
    <?php endif; ?>
<?php endif; ?>
