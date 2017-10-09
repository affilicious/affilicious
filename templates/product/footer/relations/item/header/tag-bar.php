<?php if(aff_has_product_price() || aff_has_product_tags()): ?>
    <div class="product-relations-item-header-tag-bar d-flex flex-nowrap justify-content-end align-items-center px-3 pt-2">
        <?php aff_the_product_tags(null, '<span class="product-preview-tag-item d-inline-block px-2 ml-2 mb-2 tag">', '</span>'); ?>

        <?php if(aff_has_product_price()): ?>
            <span class="product-preview-tag-item px-2 ml-2 mb-2 tag tag-price"><?php aff_the_product_price(); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>
