<?php
namespace Affilicious\Product\Application\Helper;

use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopId;

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
        $productRepository = $container['affilicious.product.repository.product'];

        if ($productOrId instanceof Product) {
            $product = $productOrId;
        } elseif($productOrId instanceof \WP_Post) {
            $product = $productRepository->findById(new ProductId($productOrId->ID));
        } elseif (is_int($productOrId)) {
            $product = $productRepository->findById(new ProductId($productOrId));
        } else {
            $post = get_post();
            if ($post === null) {
                return null;
            }

            $product = $productRepository->findById(new ProductId($post->ID));
        }

        return $product;
    }

    /**
     * Get the active shop of the product
     * If you pass in nothing as a product, the current post will be used.
     * If you pass in nothing as a shop, the first shop will be used.
     *
     * @since 0.3
     * @param int|\WP_Post|Product|null $productOrId
     * @param int|\WP_Post|Shop|null $shopOrId
     * @return null|Shop
     */
    public static function getShop($productOrId = null, $shopOrId = null)
    {
        $product = self::getProduct($productOrId);
        if($product === null) {
            return null;
        }

        if ($shopOrId === null) {
            $shops = $product->getShops();
	        $shop = !empty($shops) ? $shops[0] : null;

            return $shop;
        }

        if (is_int($shopOrId)) {
            $shopId = $shopOrId;
        } elseif (is_array($shopOrId) && !empty($shopOrId['shop_id'])) {
            $shopId = $shopOrId['shop_id'];
        } elseif ($shopOrId instanceof \WP_Post) {
            $shopId = $shopOrId->ID;
        } else {
            $shopId = $shopOrId->getId()->getValue();
        }

        $shop = $product->getShop(new ShopId($shopId));

        return $shop;
    }
}
