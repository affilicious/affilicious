<?php
namespace Affilicious\Product\Application\Listener;

use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;
use Affilicious\Product\Domain\Model\Variant\ProductVariantRepositoryInterface;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

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
     * @var ProductVariantRepositoryInterface
     */
    protected $productVariantRepository;

    /**
     * @since 0.6
     * @param ProductRepositoryInterface $productRepository
     * @param ProductVariantRepositoryInterface $productVariantRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
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

        $product = $this->productRepository->findById(new ProductId($postId));
        if($product === null) {
            return;
        }

        $variants = $product->getVariants();
        if(!empty($variants)) {
            foreach ($variants as $variant) {
                $postId = $this->getPostIdByName($variant->getName()->getValue());
                if($postId !== null) {
                    $variant->setId(new ProductId($postId));
                }

                $this->productVariantRepository->store($variant);
            }
        }
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

    /**
     * @since 0.6
     * @param string $postName
     * @param string $output
     * @return null|int
     */
    private function getPostIdByName($postName, $output = OBJECT)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type='product_variant'",
            $postName
        );

        $postID = $wpdb->get_var($query);
        if(empty($postID) && $postID !== 0) {
            return null;
        }

        $postID = intval($postID);
        return $postID;
    }
}
