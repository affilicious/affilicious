<?php
namespace Affilicious\Shop\Setup;

use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Repository\Carbon\Carbon_Shop_Template_Repository;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Admin_Table_Setup
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.8
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * Set up the table columns for the taxonomy.
     *
     * @hook manage_edit-aff_shop_tmpl_columns
     * @since 0.8
     * @param array $columns
     * @return array
     */
    public function setup_columns($columns)
    {
        // Add the new columns
        $temp_columns = $columns;
        array_splice($temp_columns, 5);

        $temp_columns['thumbnail'] = __('Thumbnail', 'affilicious');
        $temp_columns['provider'] = __('Provider', 'affilicious');

        $columns = array_merge( $temp_columns, $columns);

        // Remove some existing columns
        unset($columns['description'], $columns['posts']);

        return $columns;
    }

    /**
     * Set up the table rows for the taxonomy.
     *
     * @hook manage_aff_shop_tmpl_custom_column
     * @since 0.8
     * @param string $row
     * @param string $column_name
     * @param int $term_id
     * @return string
     */
    public function setup_rows($row, $column_name, $term_id)
    {
        $value = '';

        if ($column_name == 'thumbnail') {
            $thumbnail_id = carbon_get_term_meta($term_id, Carbon_Shop_Template_Repository::THUMBNAIL_ID);
            if(!empty($thumbnail_id)) {
                $value = wp_get_attachment_image($thumbnail_id, 'featured_preview');
            }
        }

        if ($column_name == 'provider') {
            $provider_id = carbon_get_term_meta($term_id, Carbon_Shop_Template_Repository::PROVIDER);
            if(!empty($provider_id) && $provider_id != 'none') {
                $provider = $this->provider_repository->find_one_by_id(new Provider_Id($provider_id));
                $value = $provider === null ?: $provider->get_name()->get_value();
            }
        }

        return $row . $value;
    }
}
