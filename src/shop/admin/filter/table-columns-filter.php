<?php
namespace Affilicious\Shop\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Columns_Filter
{
    /**
     * Filter the admin table columns for the shop templates.
     *
     * @filter manage_edit-aff_shop_tmpl_columns
     * @since 0.9
     * @param array $columns
     * @return array
     */
    public function filter($columns)
    {
        // Add the new columns
        $temp_columns = $columns;
        array_splice($temp_columns, 5);

        $temp_columns['thumbnail'] = __('Thumbnail', 'affilicious');
        $temp_columns['provider'] = __('Provider', 'affilicious');

        $columns = array_merge($temp_columns, $columns);

        // Remove some existing columns
        unset($columns['description'], $columns['posts']);

        return $columns;
    }
}
