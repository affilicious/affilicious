<?php
namespace Affilicious\Product\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.10
 */
class Table_Rows_Filter
{
    /**
     * Filter the admin table rows of the products.
     *
     * @filter manage_aff_product_posts_custom_column
     * @since 0.9.10
     * @param string $column The admin table column name.
     * @param int $post_id The post ID of the current row.
     */
    public function filter($column, $post_id)
    {
	    if ($column == 'aff_thumbnail') {
            $this->render_thumbnail_row($post_id);
        }

        if($column == 'aff_product_id') {
	    	$this->render_product_id_row($post_id);
        }

	    if($column == 'aff_price') {
		    $this->render_price_row($post_id);
	    }

        if($column == 'aff_availability') {
			$this->render_availability_row($post_id);
        }
    }

	/**
	 * Render the thumbnail row into the product admin table.
	 *
	 * @since 0.9.22
	 * @param int $post_id The post ID of the current row.
	 */
    protected function render_thumbnail_row($post_id)
    {
	    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
	    $thumbnail_url = !empty($thumbnail_url) ? $thumbnail_url :  AFFILICIOUS_ROOT_URL . 'assets/public/dist/img/no-image.png';

	    printf(
		    '<img class="aff-admin-table-product-thumbnail aff-admin-table-thumbnail" src="%s" />',
		    esc_url($thumbnail_url)
	    );
    }

	/**
	 * Render the product ID row into the product admin table.
	 *
	 * @since 0.9.22
	 * @param int $post_id The post ID of the current row.
	 */
	protected function render_product_id_row($post_id)
	{
		printf(
			'<span class="aff-admin-table-product-id">%s</span>',
			esc_html($post_id)
		);
	}

	/**
	 * Render the price row into the product admin table.
	 *
	 * @since 0.9.22
	 * @param int $post_id The post ID of the current row.
	 */
    protected function render_price_row($post_id)
    {
	    $price = aff_get_product_price($post_id);
	    if(empty($price)) {
		    $price = '-';
	    }

	    printf(
	    	'<span class="aff-admin-table-product-price">%s</span>',
		    esc_html($price)
	    );
    }

	/**
	 * Render the availability row into the product admin table.
	 *
	 * @since 0.9.22
	 * @param int $post_id The post ID of the current row.
	 */
    protected function render_availability_row($post_id)
    {
	    $is_available = aff_is_product_available($post_id);

	    if($is_available) {
		    printf(
		    	'<span class="aff-admin-table-product-availability aff-admin-table-product-availability-available">%s</span>',
			    __('Available', 'affilicious')
		    );
	    } else {
		    printf(
		    	'<span class="aff-admin-table-product-availability aff-admin-table-product-availability-out-of-stock">%s</span>',
			    __('Out of stock', 'affilicious')
		    );
	    }
    }
}
