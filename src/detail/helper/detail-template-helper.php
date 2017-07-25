<?php
namespace Affilicious\Detail\Helper;

use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Detail_Template_Id;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Helper
{
    /**
     * Check if the Wordpress term or term ID belongs to a detail template.
     *
     * @since 0.8.9
     * @param int|string|array|\WP_Term|Detail_Template|Detail_Template_Id $term_or_id
     * @return bool
     */
    public static function is_detail_template($term_or_id)
    {
        // The argument is already a detail template
        if ($term_or_id instanceof Detail_Template) {
            return true;
        }

        // The argument is a detail template ID
        if($term_or_id instanceof Detail_Template_Id) {
            return term_exists($term_or_id->get_value(), Detail_Template::TAXONOMY);
        }

        // The argument is an integer or string.
        if(is_int($term_or_id) || is_string($term_or_id)) {
            return term_exists(intval($term_or_id), Detail_Template::TAXONOMY);
        }

        // The argument is an array of a detail template.
        if(is_array($term_or_id) && !empty($term_or_id['id'])) {
            return term_exists(intval($term_or_id['id']), Detail_Template::TAXONOMY);
        }

        // The argument is an array of a detail.
        if(is_array($term_or_id) && !empty($term_or_id['template_id'])) {
            return term_exists(intval($term_or_id['template_id']), Detail_Template::TAXONOMY);
        }

        // The argument is a term.
        if($term_or_id instanceof \WP_Term) {
            return $term_or_id->taxonomy === Detail_Template::TAXONOMY;
        }

        return false;
    }

    /**
     * Find one detail template by the ID or Wordpress term.
     *
     * @since 0.8
     * @param int|string|array|\WP_Term|Detail_Template|Detail_Template_Id $term_or_id
     * @return Detail_Template|null
     */
    public static function get_detail_template($term_or_id)
    {
        /** @var Detail_Template_Repository_Interface $detail_template_repository */
        $detail_template_repository = \Affilicious::get('affilicious.detail.repository.detail_template');

        // The argument is already a detail template
        if ($term_or_id instanceof Detail_Template) {
            return $term_or_id;
        }

        // The argument is a detail template ID
        if($term_or_id instanceof Detail_Template_Id) {
            return $detail_template_repository->find_one_by_id($term_or_id);
        }

        // The argument is an integer or string.
        if(is_int($term_or_id) || is_string($term_or_id)) {
            return $detail_template_repository->find_one_by_id(new Detail_Template_Id($term_or_id));
        }

        // The argument is an array of a detail template
        if(is_array($term_or_id) && !empty($term_or_id['id'])) {
            return $detail_template_repository->find_one_by_id(new Detail_Template_Id($term_or_id['id']));
        }

        // The argument is an array of a detail.
        if(is_array($term_or_id) && !empty($term_or_id['template_id'])) {
            return $detail_template_repository->find_one_by_id(new Detail_Template_Id($term_or_id['template_id']));
        }

        // The argument is a term.
        if($term_or_id instanceof \WP_Term) {
            return $detail_template_repository->find_one_by_id(new Detail_Template_Id($term_or_id->term_id));
        }

        return null;
    }

    /**
     * Convert the detail template into an array.
     *
     * @since 0.8.9
     * @param Detail_Template $detail_template
     * @return array
     */
    public static function to_array(Detail_Template $detail_template)
    {
        $array = array(
            'id' => $detail_template->get_id()->get_value(),
            'name' => $detail_template->get_name()->get_value(),
            'slug' => $detail_template->get_slug()->get_value(),
            'type' => $detail_template->get_type()->get_value(),
            'unit' => $detail_template->has_unit() ? $detail_template->get_unit()->get_value() : null,
        );

        $array = apply_filters('aff_detail_to_array', $array, $detail_template);

        return $array;
    }
}
