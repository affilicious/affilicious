<?php
namespace Affilicious\Attribute\Admin\Filter;

use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Model\Unit;
use Affilicious\Attribute\Repository\Carbon\Carbon_Attribute_Template_Repository;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Rows_Filter
{
    /**
     * Filter the admin table rows of the attribute templates.
     *
     * @filter manage_aff_attribute_tmpl_custom_column
     * @since 0.9
     * @param string $row
     * @param string $column_name
     * @param int $term_id
     * @return string
     */
    public function filter($row, $column_name, $term_id)
    {
        $value = '';

        if ($column_name === 'type') {
            $raw_type = carbon_get_term_meta($term_id, Carbon_Attribute_Template_Repository::TYPE);
            if (!empty($raw_type)) {
                $type = new Type($raw_type);
                $value = $type->get_label();
            }
        }

        if ($column_name === 'unit') {
            $raw_type = carbon_get_term_meta($term_id, Carbon_Attribute_Template_Repository::UNIT);
            if (!empty($raw_type)) {
                $type = new Unit($raw_type);
                $value = $type->get_value();
            }
        }

        return $row . $value;
    }
}
