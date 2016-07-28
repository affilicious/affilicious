<?php
namespace Affilicious\ProductsPlugin\Product\Field;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class FieldGroupFactory
{
    /**
     * @param \WP_Post $post
     * @return FieldGroup
     * @throws \Exception
     */
    public function create(\WP_Post $post)
    {
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
