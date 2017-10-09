<?php if(aff_has_product_related_products()): ?>
    <div class="product-relations product-relations-product p-4 mb-3">
        <?php get_template_part('partials/product/footer/relations/product'); ?>
    </div>
<?php endif; ?>

<?php if(aff_has_product_related_accessories()): ?>
    <div class="product-relations product-relations-accessory p-4 mb-3">
        <?php get_template_part('partials/product/footer/relations/accessory'); ?>
    </div>
<?php endif; ?>
