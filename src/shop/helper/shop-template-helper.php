<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Shop\Model\Shop_Template;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop_Template_Helper
{
    /**
     * Find one shop template by the ID or Wordpress term.
     *
     * @since 0.8
     * @param int|\WP_Term|Shop_Template $term_or_id
     * @return null|Shop_Template
     */
    public static function find_one($term_or_id)
    {
        $shop_template_repository = \Affilicious_Plugin::get('affilicious.shop.repository.shop_template');

        if(!is_int($term_or_id) && is_numeric($term_or_id)) {
            $term_or_id = intval($term_or_id);
        }

        $shop_template = null;
        if ($term_or_id instanceof Shop_Template) {
            $shop_template = $term_or_id;
        } elseif($term_or_id instanceof \WP_Term) {
            $shop_template = $shop_template_repository->find_by_id($term_or_id->term_id);
        } elseif (is_int($term_or_id)) {
            $shop_template = $shop_template_repository->find_by_id($term_or_id);
        }

        return $shop_template;
    }
}
