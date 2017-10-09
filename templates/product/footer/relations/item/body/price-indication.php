<?php if(aff_has_product_price()): ?>
    <?php $shop = aff_get_product_cheapest_shop(); ?>

    <small class="product-relations-item-body-price-indication d-block text-center text-muted mb-2">
        <?php aff_the_shop_price_indication($shop); ?>
    </small>
<?php endif; ?>
