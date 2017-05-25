<?php
namespace Affilicious\Detail\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Columns_Filter
{
    /**
     * Filter the admin table columns of the detail templates.
     *
     * @hook manage_edit-aff_detail_tmpl_columns
     * @since 0.9
     * @param array $columns
     * @return array
     */
    public function filter($columns)
    {
        // Add the new columns
        $temp_columns = $columns;
        array_splice($temp_columns, 5);

        $temp_columns['type'] = __('Type', 'affilicious');
        $temp_columns['unit'] = __('Unit', 'affilicious');

        $columns = array_merge($temp_columns, $columns);

        // Remove some existing columns
        unset($columns['description'], $columns['posts']);

        return $columns;
    }
}
