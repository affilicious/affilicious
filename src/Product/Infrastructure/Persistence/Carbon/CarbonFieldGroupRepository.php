<?php
namespace Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon;

use Affilicious\ProductsPlugin\Product\Domain\Exception\InvalidPostTypeException;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroup;
use Affilicious\ProductsPlugin\Product\Domain\Model\Field;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonFieldGroupRepository implements FieldGroupRepositoryInterface
{
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

        $id = $post->ID;
        $title = $post->post_title;
        $category = carbon_get_post_meta($id, FieldGroup::CATEGORY);
        $category = !empty($category) ? $category: FieldGroup::CARBON_CATEGORY_NONE;

        $fieldGroup = new FieldGroup($id, $title, $category);
        $rawFields = carbon_get_post_meta(get_the_ID(), FieldGroup::FIELDS, 'complex');

        foreach ($rawFields as $rawField) {
            $field = new Field(
                $rawField[Field::CARBON_KEY],
                $rawField[Field::CARBON_TYPE],
                $rawField[Field::CARBON_LABEL]
            );

            $defaultValue = $rawField[Field::CARBON_DEFAULT_VALUE];
            if(!empty($defaultValue)) {
                $field->setDefaultValue($defaultValue);
            }

            $helpText = $rawField[Field::CARBON_HELP_TEXT];
            if(!empty($helpText)) {
                $field->setHelpText($helpText);
            }

            $fieldGroup->addField($field);
        }

        return $fieldGroup;
    }
}
