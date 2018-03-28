<?php
namespace Affilicious\Common\Admin\Notice;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Download_Recommendation_Notice
{
    const DISMISSIBLE_ID = 'download_recommendation';
	const PRODUCTS_API_URL = 'https://affilicious.com/edd-api/products';

	/**
     * Render the download recommendation notice.
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
        if(empty($download['info']['title']) || empty($download['info']['excerpt']) || empty($download['info']['link'])) {
            return;
        }

	    // Render the download recommendation.
	    aff_render_template('admin/notice/info-notice', [
		    'message' => sprintf(
		        __('Download recommendation: %s Visit <a href="%s" target="_blank" rel="nofollow">%s</a> now.', 'affilicious'),
                $download['info']['excerpt'],
                $download['info']['link'],
                $download['info']['title']
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

	    if(empty($downloads)) {
	    	return null;
	    }

	    $download = $downloads[rand(0, count($downloads) - 1)];
	    $download = $this->append_utm_parameters_to_link($download);

	    return $download;
    }

	/**
	 * Check if the download from the API is an add-on.
	 *
	 * @since 0.9.16
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
	 * @since 0.9.16
	 * @param array $download The download from the API call.
	 * @return bool Whether the download is basic or not
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
	 * @since 0.9.16
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
        $utm_parameters = !empty($download['utm_parameters']['download_recommendation']) ? $download['utm_parameters']['download_recommendation'] : [];
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
