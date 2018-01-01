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
        // Check if the download recommendation notice is already dismissed.
        $is_dismissed = aff_is_notice_dismissed(self::DISMISSIBLE_ID);
        if($is_dismissed) {
            return;
        }

        // Find a random download per API call and check if we have enough data to display the recommendation.
    	$download = $this->find_random_download();
        if(empty($download['title']) || empty($download['message']) || empty($download['link'])) {
            return;
        }

	    // Render the download recommendation.
	    aff_render_template('admin/notice/info-notice', [
		    'message' => sprintf(
		        __('Download recommendation: %s Visit <a href="%s" target="_blank" rel="nofollow">%s</a> now.', 'affilicious'),
                $download['message'],
                $download['link'],
                $download['title']
            ),
            'dismissible_id' => self::DISMISSIBLE_ID,
	    ]);
    }

	/**
     * Find the random download per API call.
     *
	 * @since 0.9.16
     * @return array|null Either the download or null on non existence.
	 */
    protected function find_random_download()
    {
	    $response = wp_remote_get(self::PRODUCTS_API_URL);
	    if(is_wp_error($response)) {
		    return null;
	    }

	    $body = wp_remote_retrieve_body($response);
	    $body = json_decode($body, true);

	    $downloads = isset($body['products']) ? $body['products'] : [];
	    if(empty($downloads)) {
		    return null;
	    }

	    $downloads = array_filter($downloads, function($download) {
		    return $this->is_addon($download) && ($this->is_paid($download) || $this->is_basic($download));
	    });

	    $download = $downloads[rand(0, count($downloads) - 1)];
	    $title = !empty($download['info']['title']) ? $download['info']['title'] : null;
	    $message = !empty($download['info']['excerpt']) ? $download['info']['excerpt'] : null;
	    $link = $this->build_link($download);

	    $result = [
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ];

	    return $result;
    }

    /**
     * @since 0.9.16
     * @param array $download
     * @return null|string
     */
    protected function build_link($download)
    {
        $slug = !empty($download['info']['slug']) ? $download['info']['slug'] : null;
        $link = !empty($download['info']['link']) ? $download['info']['link'] : null;
        if(empty($slug) || empty($link)) {
            return null;
        }

        $link .= '&utm_source=wordpress-installation&utm_medium=click&utm_campaign=addons&utm_content=download-recommendation&utm_term=' . $slug;

        return $link;
    }

	/**
	 * Check if the download from the API is an add-on.
	 *
	 * @since 0.9.16
	 * @param array $download The download from the API call.
	 * @return bool Whether the download is an addon or not.
	 */
	protected function is_addon($download)
	{
		if(empty($download['info']['category'])) {
			return false;
		}

		$categories = $download['info']['category'];
		foreach ($categories as $category) {
			if($category['name'] == __('Extensions', 'affilicious')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if the download from the API belongs to the basics.
	 *
	 * @since 0.9.16
	 * @param array $download The download from the API call.
	 * @return bool Whether the download is basic or not
	 */
	protected function is_basic($download)
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
	 * @since 0.9.16
	 * @param array $download The download from the API call.
	 * @return bool Whether the download is paid or not.
	 */
	protected function is_paid($download)
	{
		return !isset($download['pricing']['amount']) || floatval($download['pricing']['amount']) > 0;
	}
}
