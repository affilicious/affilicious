<?php
namespace Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon;

use Affilicious\ProductsPlugin\Product\Domain\Exception\InvalidPostTypeException;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonFieldGroupRepository implements FieldGroupRepositoryInterface
{
    const CARBON_FIELDS = 'affilicious_product_field_group_fields';
    const CARBON_FIELD_KEY = 'key';
    const CARBON_FIELD_TYPE = 'type';
    const CARBON_FIELD_LABEL = 'label';
    const CARBON_FIELD_DEFAULT_VALUE = 'default_value';
    const CARBON_FIELD_HELP_TEXT = 'help_text';

    /**
     * @inheritdoc
     */
    public function findById($fieldGroupId)
    {
        // The field group ID is just a simple post ID, since the field group is just a custom post type
        $post = get_post($fieldGroupId);
        if ($post === null) {
            return null;
        }

        $fieldGroup = $this->fromPost($post);
        return $fieldGroup;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => FieldGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $fieldGroups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $fieldGroup = self::fromPost($query->post);
                $fieldGroups[] = $fieldGroup;
            }

            wp_reset_postdata();
        }

        return $fieldGroups;
    }

    /**
     * Convert the post into a field group
     * @param \WP_Post $post
     * @return FieldGroup
     */
    private function fromPost(\WP_Post $post)
    {
        if($post->post_type !== FieldGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, FieldGroup::POST_TYPE);
        }

        $fieldGroup = new FieldGroup($post);

        $fields = carbon_get_post_meta($fieldGroup->getId(), self::CARBON_FIELDS, 'complex');
        if (!empty($fields)) {
            $fields = array_map(function ($field) {
                unset($field['_type']);
                return $field;
            }, $fields);

            $fieldGroup->setFields($fields);
        }

        return $fieldGroup;
    }
}
