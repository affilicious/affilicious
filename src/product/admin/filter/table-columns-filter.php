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
    public function filter(array $columns)
    {
        $columns = $this->add_thumbnail_column($columns);
        $columns = $this->add_product_id_column($columns);
        $columns = $this->add_price_column($columns);
        $columns = $this->add_availability_column($columns);

        return $columns;
    }

	/**
	 * Add the thumbnail column to the product admin table.
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
	 * Add the product ID column to the product admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_product_id_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 3);

		$temp_columns['aff_product_id'] = __('Product ID', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}

	/**
	 * Add the price column to the product admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_price_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 4);

		$temp_columns['aff_price'] = __('Price', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}

	/**
	 * Add the availability column to the product admin table.
	 *
	 * @since 0.9.22
	 * @param array $columns The columns to filter.
	 * @return array The filtered columns.
	 */
	protected function add_availability_column(array $columns)
	{
		$temp_columns = $columns;
		array_splice($temp_columns, 5);

		$temp_columns['aff_availability'] = __('Availability', 'affilicious');
		$columns = array_merge($temp_columns, $columns);

		return $columns;
	}
}
