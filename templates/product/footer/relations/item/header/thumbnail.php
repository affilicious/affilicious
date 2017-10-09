<?php if(has_post_thumbnail()): ?>
    <img class="product-relations-item-header-thumbnail img-fluid" src="<?php the_post_thumbnail_url(); ?>">
<?php else: ?>
    <div class="product-relations-item-header-no-thumbnail d-flex align-items-center justify-content-center">
        <?php get_template_part('partials/misc/no-thumbnail'); ?>
    </div>
<?php endif; ?>
