<?php
namespace Affilicious\Product\Domain\Helper;

use Affilicious\Product\Domain\Model\DetailGroup;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\Shop;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class PostHelper
{
    /**
     * @since 0.3
     * @param mixed $postOrId
     * @return \WP_Post
     * @throws PostNotFoundException
     */
    public static function getPost($postOrId = null)
    {
        if($postOrId instanceof \WP_Post) {
            return $postOrId;
        }

        if($postOrId instanceof Product || $postOrId instanceof Shop || $postOrId instanceof DetailGroup) {
            return $postOrId->getRawPost();
        }

        if (is_int($postOrId)) {
            $post = get_post($postOrId);
        } elseif (is_string($postOrId)) {
            $post = get_post(intval($postOrId));
        } else {
            $post = get_post();
        }

        if ($post === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $post;
    }
}
