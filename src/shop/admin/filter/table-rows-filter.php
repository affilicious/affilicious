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
    private $provider_repository;

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
     * @hook manage_aff_shop_tmpl_custom_column
     * @since 0.9
     * @param string $row
     * @param string $column_name
     * @param int $term_id
     * @return string
     */
    public function filter($row, $column_name, $term_id)
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
