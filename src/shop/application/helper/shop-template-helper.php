<?php
namespace Affilicious\Shop\Application\Helper;

use Affilicious\Common\Domain\Exception\Post_Not_Found_Exception;
use Affilicious\Shop\Domain\Model\Shop_Template;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop_Template_Helper
{
    /**
     * Get the shop template by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|Shop_Template|null $post_or_id
     * @return Shop_Template
     * @throws Post_Not_Found_Exception
     */
    public static function get_shop_template($post_or_id = null)
    {
        $container = \Affilicious_Plugin::get_instance()->get_container();
        $shop_template_repository = $container['affilicious.shop.infrastructure.repository.shop_template'];

        if ($post_or_id instanceof Shop_Template) {
            $shop_template = $post_or_id;
        } elseif($post_or_id instanceof \WP_Post) {
            $shop_template = $shop_template_repository->find_by_id($post_or_id->ID);
        } elseif (is_array($post_or_id) && !empty($post_or_id['shop_template_id'])) {
            $shop_template = $shop_template_repository->find_by_id($post_or_id['shop_template_id']);
        } elseif (is_int($post_or_id)) {
            $shop_template = $shop_template_repository->find_by_id($post_or_id);
        } else {
            $post = get_post();
            $shop_template = $shop_template_repository->find_by_id($post->ID);
        }

        if ($shop_template === null) {
            throw new Post_Not_Found_Exception($post_or_id);
        }

        return $shop_template;
    }
}
