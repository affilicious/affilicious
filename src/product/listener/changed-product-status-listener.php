<?php
namespace Affilicious\Product\Listener;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.24
 */
class Changed_Product_Status_Listener
{
    /**
     * Change the status of the variants if the parent product status is changing.
     *
     * @action save_post
     * @since 0.9.24
     * @param int $post_id
     */
    public function listen($post_id)
    {
	    static $changed_posts = [];

		// Get the post from the ID.
	    $post = get_post($post_id);
	    $new_status = $post->post_status;

        // Check if we really have a unhandled product here...
        if(!$post->post_type == Product::POST_TYPE || wp_is_post_revision($post) || in_array($post->ID, $changed_posts)) {
            return;
        }

	    // Add the post to the recursion prevention.
	    $changed_posts[$post->ID] = $post->ID;

        // If it's a simple product with variants, then set the status of the variants
	    // to "draft" to hide them in the front end.
	    if(isset($_POST['_affilicious_product_type']) && $_POST['_affilicious_product_type'] == 'simple') {
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
