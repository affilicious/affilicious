<?php
namespace Affilicious\Shop\Application\Helper;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Shop\Domain\Model\ShopTemplate;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ShopTemplateHelper
{
    /**
     * Get the shop template by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|ShopTemplate|null $postOrId
     * @return ShopTemplate
     * @throws PostNotFoundException
     */
    public static function getShopTemplate($postOrId = null)
    {
        $container = \AffiliciousPlugin::getInstance()->getContainer();
        $shopTemplateRepository = $container['affilicious.shop.repository.shop_template'];

        if ($postOrId instanceof ShopTemplate) {
            $shopTemplate = $postOrId;
        } elseif($postOrId instanceof \WP_Post) {
            $shopTemplate = $shopTemplateRepository->findById($postOrId->ID);
        } elseif (is_array($postOrId) && !empty($postOrId['shop_template_id'])) {
            $shopTemplate = $shopTemplateRepository->findById($postOrId['shop_template_id']);
        } elseif (is_int($postOrId)) {
            $shopTemplate = $shopTemplateRepository->findById($postOrId);
        } else {
            $post = get_post();
            $shopTemplate = $shopTemplateRepository->findById($post->ID);
        }

        if ($shopTemplate === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $shopTemplate;
    }
}
