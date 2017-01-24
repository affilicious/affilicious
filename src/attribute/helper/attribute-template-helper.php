<?php
namespace Affilicious\Attribute\Helper;

use Affilicious\Attribute\Model\Attribute_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Helper
{
    /**
     * Find one attribute template by the ID or Wordpress term.
     *
     * @since 0.8
     * @param int|\WP_Term|Attribute_Template $term_or_id
     * @return null|Attribute_Template
     */
    public static function find_one($term_or_id)
    {
        $attribute_template_repository = \Affilicious_Plugin::get('affilicious.attribute.repository.attribute_template');

        if(!is_int($term_or_id) && is_numeric($term_or_id)) {
            $term_or_id = intval($term_or_id);
        }

        $attribute_template = null;
        if ($term_or_id instanceof Attribute_Template) {
            $attribute_template = $term_or_id;
        } elseif($term_or_id instanceof \WP_Term) {
            $attribute_template = $attribute_template_repository->find_by_id($term_or_id->term_id);
        } elseif (is_int($term_or_id)) {
            $attribute_template = $attribute_template_repository->find_by_id($term_or_id);
        }

        return $attribute_template;
    }
}
