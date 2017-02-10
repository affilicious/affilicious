<?php
namespace Affilicious\Product\Filter;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Shop\Model\Shop_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Column_Filter
{
    /**
     * Filter the product admin table columns.
     *
     * @hook manage_aff_product_posts_columns
     * @since 0.8.5
     * @param array $columns
     * @return array
     */
    public function filter($columns)
    {
        $columns = $this->filter_taxonomies($columns);

        return $columns;
    }

    /**
     * Filter the unnecessary taxonomies like shop, attribute and detail templates.
     *
     * @since 0.8.5
     * @param array $columns
     * @return array
     */
    private function filter_taxonomies($columns)
    {
        $taxonomies = array(
            Shop_Template::TAXONOMY,
            Attribute_Template::TAXONOMY,
            Detail_Template::TAXONOMY,
        );

        foreach ($taxonomies as $taxonomy) {
            unset($columns['taxonomy-' . $taxonomy]);
        }

        return $columns;
    }
}
