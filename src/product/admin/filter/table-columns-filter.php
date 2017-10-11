<?php
namespace Affilicious\Product\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Columns_Filter
{
    /**
     * Filter the admin table columns of the products.
     *
     * @filter manage_aff_product_posts_columns
     * @since 0.9.10
     * @param array $columns The columns to filter.
     * @return array The filtered columns.
     */
    public function filter($columns)
    {
        // Add the new columns
        $temp_columns = $columns;
        array_splice($temp_columns, 1);

        $temp_columns['thumbnail'] = __('Thumbnail', 'affilicious');

        $columns = array_merge($temp_columns, $columns);

        return $columns;
    }
}
