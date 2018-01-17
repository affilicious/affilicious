<?php
namespace Affilicious\Common\Admin\Page;

use Affilicious\Common\Helper\Template_Helper;

if(!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Addons_Page
{
	const MENU_SLUG = 'addons';
	const PRODUCTS_URL = 'https://affilicioustheme.com/edd-api/products';

	/**
     * Init the addons page which lists all available premium addons.
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
     * Render the addons page which lists all available premium addons.
     *
	 * @since 0.9
	 */
	public function render()
	{
	    // Find all available downloads
        $downloads = $this->find_downloads();
        if(empty($downloads)) {
            return;
        }

        // Filter the unproper downloads.
        $downloads = array_filter($downloads, function($download) {
            return $this->is_addon($download) && ($this->is_paid($download) || $this->is_basic($download));
        });

        // Append the UTM parameters to all downloads.
        $downloads = array_map(function($download) {
            return $this->append_utm_parameters_to_link($download);
        }, $downloads);

        // Render the addons page.
	    Template_Helper::render('admin/page/addons', [
	        'downloads' => $downloads
        ]);
	}

    /**
     * Find the download per API call.
     *
     * @since 0.9.16
     * @return array The found downloads.
     */
	protected function find_downloads()
    {
        $response = wp_remote_get(self::PRODUCTS_URL);
        if(is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $body = json_decode($body, true);

        $downloads = isset($body['products']) ? $body['products'] : [];
        if(empty($downloads)) {
            return [];
        }

        return $downloads;
    }

    /**
     * Check if the download from the API is an add-on.
     *
     * @since 0.9
     * @param array $download The download from the API call.
     * @return bool Whether the download is an addon or not.
     */
	protected function is_addon(array $download)
    {
        if(empty($download['info']['category'])) {
            return false;
        }

        $categories = $download['info']['category'];
        foreach ($categories as $category) {
            if($category['slug'] == 'erweiterungen') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the download from the API belongs to the basics.
     *
     * @since 0.9
     * @param array $download The download from the API call.
     * @return bool Whether the download is basic or not.
     */
    protected function is_basic(array $download)
    {
        if(empty($download['info']['tags'])) {
            return false;
        }

        $tags = $download['info']['tags'];
        foreach ($tags as $tag) {
            if($tag['slug'] == 'basics') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the download from the API is paid.
     *
     * @param array $download The download from the API call.
     * @return bool Whether the download is paid or not.
     */
    protected function is_paid(array $download)
    {
        return !isset($download['pricing']['amount']) || floatval($download['pricing']['amount']) > 0;
    }

    /**
     * Append the UTM parameters to the download link.
     *
     * @since 0.9.16
     * @param array $download The download which the UTM parameters are appended to.
     * @return array The download with appended UTM parameters.
     */
    protected function append_utm_parameters_to_link(array $download)
    {
        $link = !empty($download['info']['link']) ? $download['info']['link'] : null;
        $utm_parameters = !empty($download['utm_parameters']['addons_page']) ? $download['utm_parameters']['addons_page'] : [];
        if(empty($link) || empty($utm_parameters['utm_campaign']) || empty($utm_parameters['utm_source']) || empty($utm_parameters['utm_medium'])) {
            return $download;
        }

        $link .= "&utm_campaign={$utm_parameters['utm_campaign']}&utm_source={$utm_parameters['utm_source']}&utm_medium={$utm_parameters['utm_medium']}";
        if(!empty($utm_parameters['utm_content'])) {
            $link .= "&utm_content={$utm_parameters['utm_content']}";
        }

        if(!empty($utm_parameters['utm_term'])) {
            $link .= "&utm_term={$utm_parameters['utm_term']}";
        }

        $download['info']['link'] = $link;

        return $download;
    }
}
