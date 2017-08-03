<?php
namespace Affilicious\Product\Listener;

use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Repository\Product_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Saved_Complex_Product_Listener
{
    /**
     * @var Product_Repository_Interface
     */
    protected $product_repository;

    /**
     * @since 0.6
     * @param Product_Repository_Interface $product_repository
     */
    public function __construct(Product_Repository_Interface $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    /**
     * Store the product variants as a custom post if a product is saved
     *
     * @filter carbon_after_save_post_meta
     * @since 0.6
     * @param int $post_id
     */
    public function listen($post_id)
    {
        if(!$this->is_real_save($post_id)) {
            return;
        }

        $complex_product = $this->product_repository->find_one_by_id(new Product_Id($post_id));
        if(!($complex_product instanceof Complex_Product)) {
            return;
        }

        $product_variants = $complex_product->get_variants();
        foreach ($product_variants as $product_variant) {
            $this->product_repository->store($product_variant);
        }

        $this->product_repository->store($complex_product);
        $this->product_repository->delete_all_variants_except(
            $complex_product->get_id(),
            $product_variants,
            true
        );
    }

    /**
     * Check if the save is a real one, not a revision or etc.
     *
     * @since 0.6
     * @param int $post_id
     * @return bool
     */
    protected function is_real_save($post_id)
    {
        // Autosave, do nothing
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return false;
        }

        // Return if it's a post revision
        if (false !== wp_is_post_revision($post_id)) {
            return false;
        }

        if(false !== wp_is_post_autosave($post_id)) {
            return false;
        }

        if(!isset($_POST['post_type']) || $_POST['post_type'] !== Product::POST_TYPE) {
            return false;
        }

        return true;
    }
}
