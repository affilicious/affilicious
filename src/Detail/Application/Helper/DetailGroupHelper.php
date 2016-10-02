<?php
namespace Affilicious\DetailGroup\Application\Helper;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Detail\Domain\Model\DetailGroup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailGroupHelper
{
    /**
     * Get the detail group by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|DetailGroup|null $postOrId
     * @return DetailGroup
     * @throws PostNotFoundException
     */
    public static function getDetailGroup($postOrId = null)
    {
        $container = \AffiliciousPlugin::getInstance()->getContainer();
        $detailGroupRepository = $container['affilicious.detail.repository.detail_group'];

        if ($postOrId instanceof DetailGroup) {
            $detailGroup = $postOrId;
        } elseif($postOrId instanceof \WP_Post) {
            $detailGroup = $detailGroupRepository->findById($postOrId->ID);
        } elseif (is_array($postOrId) && !empty($postOrId['detail_group_id'])) {
            $detailGroup = $detailGroupRepository->findById($postOrId['detail_group_id']);
        } elseif (is_int($postOrId)) {
            $detailGroup = $detailGroupRepository->findById($postOrId);
        } else {
            $post = get_post();
            $detailGroup = $detailGroupRepository->findById($post->ID);
        }

        if ($detailGroup === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $detailGroup;
    }
}
