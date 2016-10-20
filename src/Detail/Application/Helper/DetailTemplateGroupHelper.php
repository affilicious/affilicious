<?php
namespace Affilicious\Detail\Application\Helper;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Detail\Domain\Model\DetailTemplateGroup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailTemplateGroupHelper
{
    /**
     * Get the detail template group by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|DetailTemplateGroup|null $postOrId
     * @return DetailTemplateGroup
     * @throws PostNotFoundException
     */
    public static function getDetailTemplateGroup($postOrId = null)
    {
        $container = \AffiliciousPlugin::getInstance()->getContainer();
        $detailTemplateGroupRepository = $container['affilicious.detail.infrastructure.repository.detail_template_group'];

        if ($postOrId instanceof DetailTemplateGroup) {
            $detailGroupTemplate = $postOrId;
        } elseif($postOrId instanceof \WP_Post) {
            $detailGroupTemplate = $detailTemplateGroupRepository->findById($postOrId->ID);
        } elseif (is_array($postOrId) && !empty($postOrId['detail_template_group_id'])) {
            $detailGroupTemplate = $detailTemplateGroupRepository->findById($postOrId['detail_template_group_id']);
        } elseif (is_int($postOrId)) {
            $detailGroupTemplate = $detailTemplateGroupRepository->findById($postOrId);
        } else {
            $post = get_post();
            $detailGroupTemplate = $detailTemplateGroupRepository->findById($post->ID);
        }

        if ($detailGroupTemplate === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $detailGroupTemplate;
    }
}
