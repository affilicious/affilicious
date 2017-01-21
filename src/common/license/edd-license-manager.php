<?php
namespace Affilicious\Common\License;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @see https://easydigitaldownloads.com/downloads/software-licensing/
 */
final class EDD_License_Manager implements License_Manager_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function activate($item_name, $license_key, $store_url = self::STORE_URL)
    {
        // Data to send in our API request.
        $api_params = array(
            'edd_action'=> 'activate_license',
            'item_name' => urlencode($item_name),
            'license' 	=> $license_key,
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post($store_url, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params
        ));

        // Make sure the response came back okay
        $is_error = is_wp_error($response);

        return $is_error;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function deactivate($item_name, $license_key, $store_url = self::STORE_URL)
    {
        // Data to send in our API request.
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'item_name' => urlencode($item_name),
            'license' 	=> $license_key,
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post($store_url, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params
        ));

        // Make sure the response came back okay
        $is_error = is_wp_error($response);

        return $is_error;
    }
}
