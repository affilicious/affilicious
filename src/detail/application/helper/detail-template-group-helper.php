<?php
namespace Affilicious\Detail\Application\Helper;

use Affilicious\Common\Domain\Exception\Post_Not_Found_Exception;
use Affilicious\Detail\Domain\Model\Detail_Template_Group;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Group_Helper
{
    /**
     * Get the detail template group by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|array|\WP_Post|Detail_Template_Group|null $post_or_id
     * @return Detail_Template_Group
     * @throws Post_Not_Found_Exception
     */
    public static function get_detail_template_group($post_or_id = null)
    {
        $container = \Affilicious_Plugin::get_instance()->get_container();
        $detail_template_group_repository = $container['affilicious.detail.infrastructure.repository.detail_template_group'];

        if ($post_or_id instanceof Detail_Template_Group) {
            $detail_group_template = $post_or_id;
        } elseif($post_or_id instanceof \WP_Post) {
            $detail_group_template = $detail_template_group_repository->find_by_id($post_or_id->ID);
        } elseif (is_array($post_or_id) && !empty($post_or_id['detail_template_group_id'])) {
            $detail_group_template = $detail_template_group_repository->find_by_id($post_or_id['detail_template_group_id']);
        } elseif (is_int($post_or_id)) {
            $detail_group_template = $detail_template_group_repository->find_by_id($post_or_id);
        } else {
            $post = get_post();
            $detail_group_template = $detail_template_group_repository->find_by_id($post->ID);
        }

        if ($detail_group_template === null) {
            throw new Post_Not_Found_Exception($post_or_id);
        }

        return $detail_group_template;
    }
}
