<h3 class="product-relations-title mx-3 mb-3"><?php _e('Related accessories', 'affilivice'); ?></h3>

<div class="slick-container">
    <div class="slick-relations-gallery">
        <?php $relatedAccessoriesQuery = aff_get_product_related_accessories_query(); ?>
        <?php if(!empty($relatedAccessoriesQuery)): ?>
            <?php while($relatedAccessoriesQuery->have_posts()): $relatedAccessoriesQuery->the_post(); ?>
                <div class="slick-slide my-0 mx-3">
                    <?php get_template_part('partials/product/footer/relations/item'); ?>
                </div>
            <?php endwhile; ?>

            <?php wp_reset_query(); ?>
        <?php endif; ?>
    </div>
</div>
