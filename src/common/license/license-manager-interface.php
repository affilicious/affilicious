<?php
namespace Affilicious\Common\License;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface License_Manager_Interface
{
    const STORE_URL = 'https://affilicioustheme.de';

    /**
     * Activate the license for the given store.
     *
     * @since 0.7
     * @param string $item_name
     * @param string $license_key
     * @param string $store_url
     * @return bool
     */
    public function activate($item_name, $license_key, $store_url = self::STORE_URL);

    /**
     * Deactivate the license for the given store.
     *
     * @since 0.7
     * @param $item_name
     * @param $license_key
     * @param string $store_url
     * @return bool
     */
    public function deactivate($item_name, $license_key, $store_url = self::STORE_URL);
}
