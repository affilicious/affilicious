<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Helper;

use Affilicious\ProductsPlugin\Product\Domain\Exception\PostNotFoundException;
use Affilicious\ProductsPlugin\Product\Domain\Model\Shop;

class ShopHelper
{
    /**
     * Get the shop by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.3
     * @param int|array|\WP_Post|Shop|null $postOrId
     * @return Shop
     * @throws PostNotFoundException
     */
    public static function getShop($postOrId = null)
    {
        $container = \AffiliciousProductsPlugin::getContainer();
        $shopRepository = $container['shop_repository'];

        if ($postOrId instanceof Shop) {
            $shop = $postOrId;
        } elseif($postOrId instanceof \WP_Post) {
            $shop = $shopRepository->findById($postOrId->ID);
        } elseif (is_array($postOrId) && !empty($postOrId['shop_id'])) {
            $shop = $shopRepository->findById($postOrId['shop_id']);
        } elseif (is_int($postOrId)) {
            $shop = $shopRepository->findById($postOrId);
        } else {
            $post = get_post();
            $shop = $shopRepository->findById($post->ID);
        }

        if ($shop === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $shop;
    }
}
