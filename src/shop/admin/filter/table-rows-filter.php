<?php
namespace Affilicious\Shop\Admin\Filter;

use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Repository\Carbon\Carbon_Shop_Template_Repository;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Rows_Filter
{
    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

    /**
     * @since 0.9
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * Filter the admin table rows of the shop templates.
     *
     * @filter manage_aff_shop_tmpl_custom_column
     * @since 0.9
     * @param string $row The admin table column row content.
     * @param string $column_name The admin table column name.
     * @param int $term_id The term of the current row.
     * @return string The filtered row content.
     */
    public function filter($row, $column_name, $term_id)
    {
        if ($column_name == 'aff_thumbnail') {
            $row = $this->render_thumbnail_row($row, $term_id);
        }

        if ($column_name == 'aff_provider') {
			$row = $this->render_provider_row($row, $term_id);
        }

        return $row;
    }

	/**
	 * Render the thumbnail row into the shop template admin table.
	 *
	 * @since 0.9.22
	 * @param string $row The admin table column row content.
	 * @param int $term_id The term of the current row.
	 * @return string The filtered row content.
	 */
    protected function render_thumbnail_row($row, $term_id)
    {
	    $thumbnail_id = carbon_get_term_meta($term_id, Carbon_Shop_Template_Repository::THUMBNAIL_ID);
	    if(!empty($thumbnail_id)) {
		    $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'featured_preview');

		    $row .= sprintf(
			    '<img class="aff-admin-table-shop-template-thumbnail aff-admin-table-thumbnail" src="%s" />',
			    esc_url($thumbnail_url)
		    );
	    }

	    return $row;
    }

	/**
	 * Render the provider row into the shop template admin table.
	 *
	 * @since 0.9.22
	 * @param string $row The admin table column row content.
	 * @param int $term_id The term of the current row.
	 * @return string The filtered row content.
	 */
	protected function render_provider_row($row, $term_id)
	{
		$provider_id = carbon_get_term_meta($term_id, Carbon_Shop_Template_Repository::PROVIDER);
		if(!empty($provider_id) && $provider_id != 'none') {
			$provider = $this->provider_repository->find(new Provider_Id($provider_id));
			if($provider !== null) {
				$name = $provider->get_name()->get_value();

				$row .= sprintf(
					'<span class="aff-admin-table-shop-template-provider">%s</span>',
					esc_html($name)
				);
			}
		}

		return $row;
	}
}
