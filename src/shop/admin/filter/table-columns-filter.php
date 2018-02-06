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
     * @param array $columns The columns to filter.
     * @return array The filtered columns.
     */
    public function filter(array $columns)
    {
        $columns = $this->add_thumbnail_column($columns);
        $columns = $this->add_provider_column($columns);
        $columns = $this->remove_description_column($columns);
        $columns = $this->remove_posts_column($columns);

        return $columns;
    }

	/**
	 * Add the thumbnail column to the shop template admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_thumbnail_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 1);

		$temp_columns['aff_thumbnail'] = __('Thumbnail', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}

	/**
	 * Add the provider column to the shop template admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_provider_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 5);

		$temp_columns['aff_provider'] = __('Provider', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}

	/**
	 * Remove the description column to the shop template admin table.
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
	 * Remove the posts column to the shop template admin table.
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
