<div class="product-relations-item-body p-3">
    <?php if(aff_has_product_price()): ?>
        <?php get_template_part('partials/product/footer/relations/item/body/price-indication'); ?>
    <?php endif; ?>

    <?php get_template_part('partials/product/footer/relations/item/body/title'); ?>

    <?php get_template_part('partials/product/footer/relations/item/body/actions'); ?>

    <?php if(aff_has_product_shops()): ?>
        <?php get_template_part('partials/product/footer/relations/item/body/updated-at-indication'); ?>
    <?php endif; ?>
</div>
