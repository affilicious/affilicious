<?php
namespace Affilicious\Product\Application\Helper;

use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\Shop\AffiliateLink;
use Affilicious\Product\Domain\Model\Shop\Shop;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductHelper
{
    /**
     * Get the product by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.3
     * @param int|\WP_Post|Product|null $productOrId
     * @return null|Product
     */
    public static function getProduct($productOrId = null)
    {
        $container = \AffiliciousPlugin::getInstance()->getContainer();
        $productRepository = $container['affilicious.product.infrastructure.repository.product'];
        $product = null;

        // The argument is already a product or a product variant
        if ($productOrId instanceof Product) {
            $product = $productOrId;
        }

        // The argument is an integer
        if(is_int($productOrId)) {
            $product = $productRepository->findById(new ProductId($productOrId));
        }

        // The argument is a post
        if($productOrId instanceof \WP_Post) {
            $product = $productRepository->findById(new ProductId($productOrId->ID));
        }

        // The argument is empty
        if($productOrId === null) {
            $post = get_post($productOrId);
            if ($post === null) {
                return null;
            }

            $product = $productRepository->findById(new ProductId($post->ID));
        }

        return $product;
    }
}
