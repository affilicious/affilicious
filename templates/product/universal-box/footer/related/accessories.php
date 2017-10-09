<?php
/** @var array $product The product that belongs to the universal box */
$product = aff_get_product();
?>

<?php if(aff_has_product_related_accessories()): ?>
    <aside class="aff-product-related-accessories aff-product-related">
        <h1 class="aff-product-related-title">Ähnliches Zubehör</h1>

        <div class="aff-product-related-scroll">
            <?php $relatedAccessoriesQuery = aff_get_product_related_accessories_query(); ?>
            <?php if(!empty($relatedAccessoriesQuery)): ?>
                <?php while($relatedAccessoriesQuery->have_posts()): $relatedAccessoriesQuery->the_post(); ?>
                    <?php aff_render_template('product/universal-box/footer/related/item', ['product' => $product]); ?>
                <?php endwhile; ?>

                <?php wp_reset_query(); ?>
            <?php endif; ?>
        </div>
    </aside>
<?php endif; ?>
