<?php
namespace Affilicious\Shop\Presentation\Setup;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Shop\Domain\Model\Provider\Provider_Repository_Interface;

class Table_Column_Setup
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.7
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * Add a column header for the logo
     *
     * @since 0.6
     * @param array $defaults
     * @return array
     */
    public function columns_head($defaults)
    {
        $new = array();
        foreach ($defaults as $key => $title) {
            // Put the logo column before the date column
            if ($key == 'date') {
                $new['logo'] = __('Featured Image');
                $new['provider'] = __('Provider', 'affilicious');
            }
            $new[$key] = $title;
        }

        return $new;
    }

    /**
     * Add a column for the logo
     *
     * @since 0.6
     * @param string $column_name
     * @param int $shop_id
     */
    public function columns_content($column_name, $shop_id)
    {
        if ($column_name == 'logo') {
            $shop_logo_id = get_post_thumbnail_id($shop_id);
            if (!$shop_logo_id) {
                return;
            }

            $shop_logo = wp_get_attachment_image_src($shop_logo_id, 'featured_preview');
            if ($shop_logo) {
                echo '<img src="' . $shop_logo[0] . '" />';
            }
        }

        if ($column_name == 'provider') {
            $provider_name = carbon_get_post_meta($shop_id, 'affilicious_shop_template_provider');
            $provider = $this->provider_repository->find_by_name(new Name($provider_name));

            echo !empty($provider) ? $provider->get_title()->get_value() : __('None', 'affilicious');
        }
    }
}
