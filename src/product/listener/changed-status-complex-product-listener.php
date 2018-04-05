<?php
namespace Affilicious\Product\Listener;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8.4
 */
class Changed_Status_Complex_Product_Listener
{
    /**
     * Change the status of the variants if the parent complex product status changes.
     *
     * @filter transition_post_status
     * @since 0.8.20
     * @param string $new_status
     * @param string $old_status
     * @param \WP_Post $post
     */
    public function listen($new_status, $old_status, \WP_Post $post)
    {
        static $changed_posts = [];

        // Check if the post is a product and not included in the recursion prevention.
        if(!aff_is_product($post) || in_array($post->ID, $changed_posts)) {
            return;
        }

	    // Add the post to the recursion prevention.
	    $changed_posts[$post->ID] = $post->ID;

        // If it's a simple product with variants, then set the status of the variants
	    // to "draft" to hide them in the front end.
	    if(aff_is_product_simple($post)) {
	    	$new_status = 'draft';
	    }

        // Update the status of the product variants.
        $product_variants = get_posts([
        	'post_parent' => $post->ID,
	        'post_type' => Product::POST_TYPE,
	        'posts_per_page' => -1
        ]);

	    foreach ($product_variants as $product_variant) {
		    wp_update_post(array(
			    'ID' => $product_variant->ID,
			    'post_status' => $new_status
		    ));
	    }

	    // Remove the post from the recursion prevention.
	    unset($changed_posts[$post->ID]);
    }
}
