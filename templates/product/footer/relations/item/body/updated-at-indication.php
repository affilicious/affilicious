<?php if(aff_has_product_shops()): ?>
    <?php $shop = aff_get_product_cheapest_shop(); ?>

    <small class="product-relations-item-body-updated-at-indication d-block text-center text-muted">
        <?php aff_the_shop_updated_at_indication($shop); ?>
    </small>
<?php endif; ?>
