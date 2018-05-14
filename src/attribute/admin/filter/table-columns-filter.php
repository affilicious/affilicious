<?php
namespace Affilicious\Attribute\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
class Table_Columns_Filter
{
    /**
     * Filter the admin table columns of the attribute templates.
     *
     * @filter manage_edit-aff_attribute_tmpl_columns
     * @since 0.9
     * @param array $columns The columns to filter.
     * @return array The filtered columns.
     */
    public function filter($columns)
    {
	    $columns = $this->add_type_column($columns);
	    $columns = $this->add_unit_column($columns);
	    $columns = $this->remove_description_column($columns);
	    $columns = $this->remove_posts_column($columns);

        return $columns;
    }

	/**
	 * Add the type column to the attribute template admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_type_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 4);

		$temp_columns['aff_type'] = __('Type', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}

	/**
	 * Add the unit column to the attribute template admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_unit_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 5);

		$temp_columns['aff_unit'] = __('Unit', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}

	/**
	 * Remove the description column to the attribute template admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function remove_description_column(array $columns)
	{
		unset($columns['description']);

		return $columns;
	}

	/**
	 * Remove the posts column to the attribute template admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function remove_posts_column(array $columns)
	{
		unset($columns['posts']);

		return $columns;
	}
}
