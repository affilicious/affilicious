<?php
namespace Affilicious\Detail\Helper;

use Affilicious\Detail\Model\Detail_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Helper
{
    /**
     * Find one detail template by the ID or Wordpress term.
     *
     * @since 0.8
     * @param int|\WP_Term|Detail_Template $term_or_id
     * @return null|Detail_Template
     */
    public static function find_one($term_or_id)
    {
        $detail_template_repository = \Affilicious_Plugin::get('affilicious.detail.repository.detail_template');

        if(!is_int($term_or_id) && is_numeric($term_or_id)) {
            $term_or_id = intval($term_or_id);
        }

        $detail_template = null;
        if ($term_or_id instanceof Detail_Template) {
            $detail_template = $term_or_id;
        } elseif($term_or_id instanceof \WP_Term) {
            $detail_template = $detail_template_repository->find_by_id($term_or_id->term_id);
        } elseif (is_int($term_or_id)) {
            $detail_template = $detail_template_repository->find_by_id($term_or_id);
        }

        return $detail_template;
    }
}
