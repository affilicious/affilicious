<?php
/**
 * @var array $product The product that belongs to the attribute choices.
 */
?>

<?php do_action('affilicious_template_before_product_review', $product); ?>

<?php if(aff_has_product_review($product)): ?>
    <div class="aff-product-review">
		<div class="aff-product-review-rating">
            <?php aff_the_product_review_rating(
                '<svg class="aff-product-review-rating-star-full aff-product-review-rating-star" width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg>',
                '<svg class="aff-product-review-rating-star-half aff-product-review-rating-star" width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z"/></svg>',
                '<svg class="aff-product-review-rating-star-empty aff-product-review-rating-star" width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg>',
                $product
            ); ?>
		</div>

        <?php if(aff_has_product_review_votes($product)): ?>
            <div class="aff-product-review-votes"><?php aff_the_product_review_votes($product); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php do_action('affilicious_template_after_product_review', $product); ?>
