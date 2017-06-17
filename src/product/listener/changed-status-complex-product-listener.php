<?php
namespace Affilicious\Product\Listener;

use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Repository\Product_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Changed_Status_Complex_Product_Listener
{
    /**
     * @var Product_Repository_Interface
     */
    protected $product_repository;

    /**
     * @since 0.8.4
     * @param Product_Repository_Interface $product_repository
     */
    public function __construct(Product_Repository_Interface $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    /**
     * Change the status of the variants if the parent complex product status changes.
     *
     * @hook transition_post_status
     * @since 0.8.20
     * @param string $new_status
     * @param string $old_status
     * @param \WP_Post $post
     */
    public function listen($new_status, $old_status, \WP_Post $post)
    {
        return;
        static $changed_posts = [];

        if(!aff_is_product($post) || in_array($post->ID, $changed_posts)) {
            return;
        }

        $complex_product = $this->product_repository->find_one_by_id(new Product_Id($post->ID));
        if(!($complex_product instanceof Complex_Product) || !$complex_product->has_variants()) {
            return;
        }

        // Add the post to prevent recursion.
        $changed_posts[$post->ID] = $post->ID;

        $variants = $complex_product->get_variants();
        foreach ($variants as $variant) {
            wp_update_post(array(
                'ID' => $variant->get_id()->get_value(),
                'post_status' => $new_status
            ));
        }

        unset($changed_posts[$post->ID]);
    }
}
