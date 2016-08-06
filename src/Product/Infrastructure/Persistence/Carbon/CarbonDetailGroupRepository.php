<?php
namespace Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon;

use Affilicious\ProductsPlugin\Product\Domain\Exception\InvalidPostTypeException;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonDetailGroupRepository implements DetailGroupRepositoryInterface
{
    const CARBON_DETAILS = 'affilicious_product_detail_group_fields';
    const CARBON_DETAIL_KEY = 'key';
    const CARBON_DETAIL_TYPE = 'type';
    const CARBON_DETAIL_LABEL = 'label';
    const CARBON_DETAIL_DEFAULT_VALUE = 'default_value';
    const CARBON_DETAIL_HELP_TEXT = 'help_text';

    /**
     * @inheritdoc
     */
    public function findById($detailGroupId)
    {
        // The field group ID is just a simple post ID, since the field group is just a custom post type
        $post = get_post($detailGroupId);
        if ($post === null) {
            return null;
        }

        $detailGroup = $this->fromPost($post);
        return $detailGroup;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => DetailGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $detailGroups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $detailGroup = self::fromPost($query->post);
                $detailGroups[] = $detailGroup;
            }

            wp_reset_postdata();
        }

        return $detailGroups;
    }

    /**
     * Convert the post into a field group
     * @param \WP_Post $post
     * @return DetailGroup
     */
    private function fromPost(\WP_Post $post)
    {
        if($post->post_type !== DetailGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, DetailGroup::POST_TYPE);
        }

        $detailGroup = new DetailGroup($post);

        $fields = carbon_get_post_meta($detailGroup->getId(), self::CARBON_DETAILS, 'complex');
        if (!empty($fields)) {
            $fields = array_map(function ($field) {
                unset($field['_type']);
                return $field;
            }, $fields);

            $detailGroup->setDetails($fields);
        }

        return $detailGroup;
    }
}
