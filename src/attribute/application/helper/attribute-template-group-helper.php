<?php
namespace Affilicious\Detail\Application\Helper;

use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Common\Domain\Exception\Post_Not_Found_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Group_Helper
{
    /**
     * Get the attribute template group by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|Attribute_Template_Group|null $post_or_id
     * @return Attribute_Template_Group
     * @throws Post_Not_Found_Exception
     */
    public static function get_attribute_template_group($post_or_id = null)
    {
        $container = \Affilicious_Plugin::get_instance()->get_container();
        $attribute_template_group_repository = $container['affilicious.attribute.infrastructure.repository.attribute_template_group'];

        if ($post_or_id instanceof Attribute_Template_Group) {
            $attribute_template_group = $post_or_id;
        } elseif($post_or_id instanceof \WP_Post) {
            $attribute_template_group = $attribute_template_group_repository->find_by_id($post_or_id->ID);
        } elseif (is_array($post_or_id) && !empty($post_or_id['attribute_template_group_id'])) {
            $attribute_template_group = $attribute_template_group_repository->find_by_id($post_or_id['attribute_template_group_id']);
        } elseif (is_int($post_or_id)) {
            $attribute_template_group = $attribute_template_group_repository->find_by_id($post_or_id);
        } else {
            $post = get_post();
            $attribute_template_group = $attribute_template_group_repository->find_by_id($post->ID);
        }

        if ($attribute_template_group === null) {
            throw new Post_Not_Found_Exception($post_or_id);
        }

        return $attribute_template_group;
    }
}
