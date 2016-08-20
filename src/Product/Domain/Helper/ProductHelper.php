<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Helper;

use Affilicious\ProductsPlugin\Product\Domain\Exception\PostNotFoundException;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\Shop;

class ProductHelper
{
    /**
     * Get the product by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.3
     * @param int|\WP_Post|Product|null $postOrId
     * @return Product
     * @throws PostNotFoundException
     */
    public static function getProduct($postOrId = null)
    {
        $container = \AffiliciousProductsPlugin::getContainer();
        $productRepository = $container['product_repository'];

        if ($postOrId instanceof Product) {
            $product = $postOrId;
        } elseif($postOrId instanceof \WP_Post) {
            $product = $productRepository->findById($postOrId->ID);
        } elseif (is_int($postOrId)) {
            $product = $productRepository->findById($postOrId);
        } else {
            $post = get_post();
            $product = $productRepository->findById($post->ID);
        }

        if ($product === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $product;
    }

    /**
     * Get the plain product details of the detail groups.
     *
     * @since 0.3
     * @param Product $product
     * @return array
     */
    public static function getDetails(Product $product)
    {
        $details = array();
        foreach ($product->getDetailGroups() as $detailGroup) {
            if (!empty($detailGroup[Product::DETAIL_GROUP_DETAILS])) {
                $details = array_merge($details, $detailGroup[Product::DETAIL_GROUP_DETAILS]);
            }
        }

        return $details;
    }

    /**
     * Get the active shop if the product
     * If you pass in nothing as a shop, the cheapest shop will be used.
     *
     * @since 0.3
     * @param Product $product
     * @param int|\WP_Post|Shop|null $shopOrId
     * @return array|null
     */
    public static function getShop(Product $product, $shopOrId = null)
    {
        if ($shopOrId === null) {
            $cheapestShop = $product->getCheapestShop();
            return $cheapestShop;
        }

        if (is_int($shopOrId)) {
            $shopId = $shopOrId;
        } elseif (is_array($shopOrId) && !empty($shopOrId['shop_id'])) {
            $shopId = $shopOrId['shop_id'];
        } elseif ($shopOrId instanceof \WP_Post) {
            $shopId = $shopOrId->ID;
        } else {
            $shopId = $shopOrId->getId();
        }

        $shop = $product->getShop($shopId);

        return $shop;
    }

    public static function getPrice(Product $product, $shopOrId = null)
    {
        $shop = ProductHelper::getShop($product, $shopOrId);
        if ($shop === null) {
            return null;
        }

        $value = $shop['price'];
        $currency = $shop['currency'];
        $price = affilicious_get_price($value, $currency);

        return $price;
    }
}
