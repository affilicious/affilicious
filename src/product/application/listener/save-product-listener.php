<?php
namespace Affilicious\Product\Application\Listener;

use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Product_Id;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Product_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Save_Product_Listener
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
     * @since 0.6
     * @param int $post_id
     */
    public function listen($post_id)
    {
        if(!$this->is_real_save($post_id)) {
            return;
        }

        if(!isset($_POST['post_type']) || $_POST['post_type'] !== Product_Interface::POST_TYPE) {
            return;
        }

        $product = $this->product_repository->find_by_id(new Product_Id($post_id));
        if($product === null) {
            return;
        }

        if(!($product instanceof Complex_Product_Interface)) {
            return;
        }

        $variants = $product->get_variants();
        if(!empty($variants)) {
            foreach ($variants as $variant) {
                $stored_variant = $this->product_repository->store($variant);

                if ($stored_variant->has_id() && !$stored_variant->get_id()->is_equal_to($variant->get_id())) {
                    $variant->set_id($stored_variant->get_id());
                }
            }
        }

        $product = $this->product_repository->store($product);
        $this->product_repository->delete_all_variants_from_parent_except($variants, $product->get_id());
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

        // AJAX? Not used here
        if (defined('DOING_AJAX') && DOING_AJAX) {
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

        return true;
    }
}
