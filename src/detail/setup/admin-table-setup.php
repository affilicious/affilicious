<?php
namespace Affilicious\Detail\Setup;

use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Model\Unit;
use Affilicious\Detail\Repository\Carbon\Carbon_Detail_Template_Repository;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Admin_Table_Setup
{
    /**
     * Set up the table columns for the taxonomy.
     *
     * @hook manage_edit-aff_detail_tmpl_columns
     * @since 0.8
     * @param array $columns
     * @return array
     */
    public function setup_columns($columns)
    {
        // Add the new columns
        $temp_columns = $columns;
        array_splice($temp_columns, 5);

        $temp_columns['type'] = __('Type', 'affilicious');
        $temp_columns['unit'] = __('Unit', 'affilicious');

        $columns = array_merge( $temp_columns, $columns);

        // Remove some existing columns
        unset($columns['description'], $columns['posts']);

        return $columns;
    }

    /**
     * Set up the table rows for the taxonomy.
     *
     * @hook manage_aff_detail_tmpl_custom_column
     * @since 0.8
     * @param string $row
     * @param string $column_name
     * @param int $term_id
     * @return string
     */
    public function setup_rows($row, $column_name, $term_id)
    {
        $value = '';

        if ($column_name === 'type') {
            $raw_type = carbon_get_term_meta($term_id, Carbon_Detail_Template_Repository::TYPE);
            if (!empty($raw_type)) {
                $type = new Type($raw_type);
                $value = $type->get_label();
            }
        }

        if ($column_name === 'unit') {
            $raw_type = carbon_get_term_meta($term_id, Carbon_Detail_Template_Repository::UNIT);
            if (!empty($raw_type)) {
                $type = new Unit($raw_type);
                $value = $type->get_value();
            }
        }

        return $row . $value;
    }
}
