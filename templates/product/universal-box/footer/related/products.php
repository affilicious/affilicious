<?php
/** @var array $product The product that belongs to the universal box */
$product = !empty($product) ? $product : aff_get_product();
?>

<?php do_action('affilicious_template_before_product_universal_box_related_products', $product); ?>

<?php if(aff_has_product_related_products($product)): ?>
	<aside class="aff-product-related-products aff-product-related">
        <h1 class="aff-product-related-title"><?php _e('Related products', 'affilicious'); ?></h1>

        <div class="aff-product-related-scroll">
            <?php $relatedProductsQuery = aff_get_product_related_products_query($product); ?>
            <?php if(!empty($relatedProductsQuery)): ?>
                <?php while($relatedProductsQuery->have_posts()): $relatedProductsQuery->the_post(); ?>
                    <?php aff_render_template('product/universal-box/footer/related/item', ['product' => $product]); ?>
                <?php endwhile; ?>

                <?php wp_reset_query(); ?>
            <?php endif; ?>
        </div>
	</aside>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_universal_box_related_products', $product); ?>

