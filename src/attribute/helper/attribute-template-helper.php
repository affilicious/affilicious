<?php
namespace Affilicious\Attribute\Helper;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Attribute_Template_Id;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Attribute_Template_Helper
{
    /**
     * Check if the Wordpress term or term ID belongs to an attribute template.
     *
     * @since 0.8.9
     * @param int|string|array|\WP_Term|Attribute_Template|Attribute_Template_Id $term_or_id
     * @return bool
     */
    public static function is_attribute_template($term_or_id)
    {
        // The argument is already an attribute template
        if ($term_or_id instanceof Attribute_Template) {
            return true;
        }

        // The argument is an attribute template ID
        if($term_or_id instanceof Attribute_Template_Id) {
            return term_exists($term_or_id->get_value(), Attribute_Template::TAXONOMY);
        }

        // The argument is an integer or string.
        if(is_int($term_or_id) || is_string($term_or_id)) {
            return term_exists(intval($term_or_id), Attribute_Template::TAXONOMY);
        }

        // The argument is an array.
        if(is_array($term_or_id) && !empty($term_or_id['id'])) {
            return term_exists(intval($term_or_id['id']), Attribute_Template::TAXONOMY);
        }

        // The argument is a term.
        if($term_or_id instanceof \WP_Term) {
            return $term_or_id->taxonomy === Attribute_Template::TAXONOMY;
        }

        return false;
    }

    /**
     * Find one attribute template by the ID or Wordpress term.
     *
     * @since 0.8
     * @param int|string|array|\WP_Term|Attribute_Template|Attribute_Template_Id $term_or_id
     * @return Attribute_Template|null
     */
    public static function get_attribute_template($term_or_id)
    {
        /** @var Attribute_Template_Repository_Interface $attribute_template_repository */
        $attribute_template_repository = \Affilicious::get('affilicious.attribute.repository.attribute_template');

        // The argument is already an attribute template
        if ($term_or_id instanceof Attribute_Template) {
            return $term_or_id;
        }

        // The argument is an attribute template ID
        if($term_or_id instanceof Attribute_Template_Id) {
            return $attribute_template_repository->find_one_by_id($term_or_id);
        }

        // The argument is an integer or string.
        if(is_int($term_or_id) || is_string($term_or_id)) {
            return $attribute_template_repository->find_one_by_id(new Attribute_Template_Id($term_or_id));
        }

        // The argument is an array of an attribute template
        if(is_array($term_or_id) && !empty($term_or_id['id'])) {
            return $attribute_template_repository->find_one_by_id(new Attribute_Template_Id($term_or_id['id']));
        }

        // The argument is an array of an attribute.
        if(is_array($term_or_id) && !empty($term_or_id['template_id'])) {
            return $attribute_template_repository->find_one_by_id(new Attribute_Template_Id($term_or_id['template_id']));
        }

        // The argument is a term.
        if($term_or_id instanceof \WP_Term) {
            return $attribute_template_repository->find_one_by_id(new Attribute_Template_Id($term_or_id->term_id));
        }

        return null;
    }

    /**
     * Convert the attribute template into an array.
     *
     * @since 0.8.9
     * @param Attribute_Template $attribute_template
     * @return array
     */
    public static function to_array(Attribute_Template $attribute_template)
    {
        $array = array(
            'id' => $attribute_template->get_id()->get_value(),
            'name' => $attribute_template->get_name()->get_value(),
            'slug' => $attribute_template->get_slug()->get_value(),
            'type' => $attribute_template->get_type()->get_value(),
            'unit' => $attribute_template->has_unit() ? $attribute_template->get_unit()->get_value() : null,
	        'custom_values' => $attribute_template->has_custom_values() ? $attribute_template->get_custom_values() : null,
        );

        $array = apply_filters('aff_attribute_template_to_array', $array, $attribute_template);

        return $array;
    }
}
