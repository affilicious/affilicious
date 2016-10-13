<?php
namespace Affilicious\Product\Application\Listener;

use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class SaveProductListener
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @since 0.6
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Store the product variants as a custom post if a product is saved
     *
     * @since 0.6
     * @param int $postId
     */
    public function listen($postId)
    {
        if(!$this->isRealSave($postId)) {
            return;
        }

        if(!isset($_POST['post_type']) || $_POST['post_type'] !== Product::POST_TYPE) {
            return;
        }

        $product = $this->productRepository->findById(new ProductId($postId));
        if($product === null) {
            return;
        }

        $variants = $product->getVariants();
        if(!empty($variants)) {
            foreach ($variants as $variant) {
                $this->productRepository->store($variant);
            }
        }

        $this->productRepository->deleteAllVariantsFromParentExcept($variants, $product->getId());
    }

    /**
     * Check if the save is a real one, not a revision or etc.
     *
     * @since 0.6
     * @param int $postId
     * @return bool
     */
    protected function isRealSave($postId)
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
        if (!current_user_can('edit_post', $postId)) {
            return false;
        }

        // Return if it's a post revision
        if (false !== wp_is_post_revision($postId)) {
            return false;
        }

        return true;
    }
}
