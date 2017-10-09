<h3 class="product-relations-title mx-3 mb-3"><?php _e('Related products', 'affilivice'); ?></h3>

<div class="slick-container">
    <div class="slick-relations-gallery">
        <?php $relatedProductsQuery = aff_get_product_related_products_query(); ?>
        <?php if(!empty($relatedProductsQuery)): ?>
            <?php while($relatedProductsQuery->have_posts()): $relatedProductsQuery->the_post(); ?>
                <div class="slick-slide my-0 mx-3">
                    <?php get_template_part('partials/product/footer/relations/item'); ?>
                </div>
            <?php endwhile; ?>

            <?php wp_reset_query(); ?>
        <?php endif; ?>
    </div>
</div>
