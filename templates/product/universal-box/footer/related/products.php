<?php
/** @var array $product The product that belongs to the universal box */
$product = aff_get_product();
?>

<?php if(aff_has_product_related_products()): ?>
	<aside class="aff-product-related-products aff-product-related">
        <h1 class="aff-product-related-title">Ähnliche Produkte</h1>

        <div class="aff-product-related-scroll">
            <?php $relatedProductsQuery = aff_get_product_related_products_query(); ?>
            <?php if(!empty($relatedProductsQuery)): ?>
                <?php while($relatedProductsQuery->have_posts()): $relatedProductsQuery->the_post(); ?>
                    <?php aff_render_template('product/universal-box/footer/related/item', ['product' => $product]); ?>
                <?php endwhile; ?>

                <?php wp_reset_query(); ?>
            <?php endif; ?>
        </div>
	</aside>
<?php endif; ?>
