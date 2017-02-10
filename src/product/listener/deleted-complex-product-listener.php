<?php
namespace Affilicious\Product\Listener;

use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Repository\Product_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Deleted_Complex_Product_Listener
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
     * Delete all variants of a deleted complex product.
     *
     * @hook delete_post
     * @since 0.8.4
     * @param int $post_id
     */
    public function listen($post_id)
    {
        global $post;

        $revision = get_post($post_id);
        if(empty($revision) || $revision->post_type != 'revision' || $revision->post_parent != $post->ID) {
            return;
        }

        if($post->post_type != Product::POST_TYPE || $post->post_parent > 0) {
            return;
        }

        $complex_product = $this->product_repository->find_one_by_id(new Product_Id($post->ID));
        if(!($complex_product instanceof Complex_Product) || !$complex_product->has_variants()) {
            return;
        }

        $product_ids = array();
        $product_variants = $complex_product->get_variants();
        foreach ($product_variants as $product_variant) {
            $product_ids[] = $product_variant->get_id();
        }

        if(empty($product_ids)) {
            return;
        }

        $this->product_repository->delete_all($product_ids);
    }
}
