<?php
namespace Affilicious\Product\Domain\Helper;

use Affilicious\Product\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\Shop;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

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
        $container = \AffiliciousPlugin::getInstance()->getContainer();
        $shopRepository = $container['affilicious.product.repository.shop'];

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
