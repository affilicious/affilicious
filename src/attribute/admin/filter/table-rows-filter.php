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
     * @param string $row  The admin table column row content.
     * @param string $column_name The admin table column name.
     * @param int $term_id The term of the current row.
     * @return string The filtered row content.
     */
    public function filter($row, $column_name, $term_id)
    {
	    if ($column_name == 'aff_type') {
		    $row = $this->render_type_row($row, $term_id);
	    }

	    if ($column_name == 'aff_unit') {
		    $row = $this->render_unit_row($row, $term_id);
	    }

	    return $row;
    }

	/**
	 * Render the type row into the detail template admin table.
	 *
	 * @since 0.9.22
	 * @param string $row The admin table column row content.
	 * @param int $term_id The term of the current row.
	 * @return string The filtered row content.
	 */
	protected function render_type_row($row, $term_id)
	{
		$raw_type = carbon_get_term_meta($term_id, Carbon_Attribute_Template_Repository::TYPE);
		if (!empty($raw_type)) {
			$type = new Type($raw_type);
			$label = $type->get_label();

			$row .= sprintf(
				'<span class="aff-admin-table-detail-template-type">%s</span>',
				esc_html($label)
			);
		}

		return $row;
	}

	/**
	 * Render the unit row into the detail template admin table.
	 *
	 * @since 0.9.22
	 * @param string $row The admin table column row content.
	 * @param int $term_id The term of the current row.
	 * @return string The filtered row content.
	 */
	protected function render_unit_row($row, $term_id)
	{
		$raw_type = carbon_get_term_meta($term_id, Carbon_Attribute_Template_Repository::UNIT);
		if (!empty($raw_type)) {
			$type = new Unit($raw_type);
			$value = $type->get_value();

			$row .= sprintf(
				'<span class="aff-admin-table-detail-template-unit">%s</span>',
				esc_html($value)
			);
		}

		return $row;
	}
}
