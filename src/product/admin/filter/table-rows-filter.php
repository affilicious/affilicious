<?php
namespace Affilicious\Product\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Rows_Filter
{
    /**
     * Filter the admin table rows of the products.
     *
     * @filter manage_aff_product_posts_custom_column
     * @since 0.9.10
     * @param string $column The admin table column name.
     * @param string $post_id The post ID of the current row.
     */
    public function filter($column, $post_id)
    {
	    if ($column == 'thumbnail') {
            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
            $thumbnail_url = !empty($thumbnail_url) ? $thumbnail_url :  AFFILICIOUS_ROOT_URL . 'assets/public/dist/img/no-image.png';
            $thumbnail = '<img src="' . $thumbnail_url . '" />';

            echo $thumbnail;
        }
    }
}
