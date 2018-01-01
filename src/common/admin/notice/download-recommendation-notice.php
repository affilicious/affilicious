<?php
namespace Affilicious\Common\Admin\Notice;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Download_Recommendation_Notice
{
    const DISMISSIBLE_ID = 'download-recommendation';
	const PRODUCTS_API_URL = 'https://affilicioustheme.com/edd-api/products';

	/**
     * Render the notice if the license is invalid.
     *
     * @since 0.9.16
     */
    public function render()
    {
        if(aff_is_notice_dismissed(self::DISMISSIBLE_ID)) {
            return;
        }

    	$product = $this->find_random_product();

    	$title = !empty($product['info']['title']) ? $product['info']['title'] : null;
    	$message = !empty($product['info']['excerpt']) ? $product['info']['excerpt'] : null;
    	$link = !empty($product['info']['link']) ? $product['info']['link'] : null;

    	if(empty($title) || empty($message) || empty($link)) {
    		return;
	    }

	    aff_render_template('admin/notice/info-notice', [
		    'message' => sprintf(__('Download recommendation: %s Visit <a href="%s" target="_blank" rel="nofollow">%s</a> now.', 'affilicious'), $message, $link, $title),
            'dismissible_id' => self::DISMISSIBLE_ID,
	    ]);
    }

	/**
	 * @since 0.9.16
	 */
    protected function find_random_product()
    {
	    $response = wp_remote_get(self::PRODUCTS_API_URL);
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

	    $product = $products[rand(0, count($products) - 1)];

	    return $product;
    }

	/**
	 * Check if the product from the API is an add-on.
	 *
	 * @since 0.9.16
	 * @param array $product
	 * @return bool
	 */
	protected function is_addon($product)
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
	 * @since 0.9.16
	 * @param array $product
	 * @return bool
	 */
	protected function is_basic($product)
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
	 * @since 0.9.16
	 * @param array $product
	 * @return bool
	 */
	protected function is_paid($product)
	{
		return !isset($product['pricing']['amount']) || floatval($product['pricing']['amount']) > 0;
	}
}
