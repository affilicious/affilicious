<?php
namespace Affilicious\Detail\Application\Helper;

use Affilicious\Attribute\Domain\Model\AttributeTemplateGroup;
use Affilicious\Common\Domain\Exception\PostNotFoundException;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeTemplateGroupHelper
{
    /**
     * Get the attribute template group by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|AttributeTemplateGroup|null $postOrId
     * @return AttributeTemplateGroup
     * @throws PostNotFoundException
     */
    public static function getAttributeTemplateGroup($postOrId = null)
    {
        $container = \AffiliciousPlugin::getInstance()->getContainer();
        $attributeTemplateGroupRepository = $container['affilicious.attribute.infrastructure.repository.attribute_template_group'];

        if ($postOrId instanceof AttributeTemplateGroup) {
            $attributeTemplateGroup = $postOrId;
        } elseif($postOrId instanceof \WP_Post) {
            $attributeTemplateGroup = $attributeTemplateGroupRepository->findById($postOrId->ID);
        } elseif (is_array($postOrId) && !empty($postOrId['attribute_template_group_id'])) {
            $attributeTemplateGroup = $attributeTemplateGroupRepository->findById($postOrId['attribute_template_group_id']);
        } elseif (is_int($postOrId)) {
            $attributeTemplateGroup = $attributeTemplateGroupRepository->findById($postOrId);
        } else {
            $post = get_post();
            $attributeTemplateGroup = $attributeTemplateGroupRepository->findById($post->ID);
        }

        if ($attributeTemplateGroup === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $attributeTemplateGroup;
    }
}
