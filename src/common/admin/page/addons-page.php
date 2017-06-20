<?php
namespace Affilicious\Common\Admin\Page;

use Affilicious\Common\Helper\View_Helper;

if(!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Addons_Page
{
	const MENU_SLUG = 'addons';
	const PRODUCTS_URL = 'https://affilicioustheme.com/edd-api/products';

	/**
     * Init the extensions page which lists all available Affilicious Theme premium extensions.
     *
	 * @hook admin_menu
	 * @since 0.9
	 */
	public function init()
	{
		add_submenu_page(
			'edit.php?post_type=aff_product',
			__('Add-ons', 'affilicious'),
			__('Add-ons', 'affilicious'),
			'manage_options',
			self::MENU_SLUG,
			array($this, 'render')
		);
	}

	/**
     * Render the extensions page which lists all available Affilicious Theme premium extensions.
     *
	 * @since 0.9
	 */
	public function render()
	{
	    $response = wp_remote_get(self::PRODUCTS_URL);
	    if(is_wp_error($response)) {
	        return;
        }

        $body = wp_remote_retrieve_body($response);
        $body = json_decode($body, true);

        $products = isset($body['products']) ? $body['products'] : [];
        if(empty($products)) {
            return;
        }

        $products = array_filter($products, function($product) {
            return $this->is_addon($product) && ($this->is_paid($product) || $this->is_basic($product));
        });

	    View_Helper::render(AFFILICIOUS_ROOT_PATH . 'src/common/admin/view/page/addons.php', [
	        'products' => $products
        ]);
	}

    /**
     * Check if the product from the API is an add-on.
     *
     * @since 0.9
     * @param array $product
     * @return bool
     */
	private function is_addon($product)
    {
        if(empty($product['info']['category'])) {
            return false;
        }

        $categories = $product['info']['category'];
        foreach ($categories as $category) {
            if($category['name'] == __('Extensions', 'affilicious')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the product from the API belongs to the basics.
     *
     * @since 0.9
     * @param array $product
     * @return bool
     */
    private function is_basic($product)
    {
        if(empty($product['info']['tags'])) {
            return false;
        }

        $tags = $product['info']['tags'];
        foreach ($tags as $tag) {
            if($tag['slug'] == 'basics') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the product from the API is paid.
     *
     * @param array $product
     * @return bool
     */
    private function is_paid($product)
    {
        return !isset($product['pricing']['amount']) || floatval($product['pricing']['amount']) > 0;
    }
}
