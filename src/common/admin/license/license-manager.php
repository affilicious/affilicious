<?php
namespace Affilicious\Common\Admin\License;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class License_Manager
{
    /**
     * @var License_Handler_Interface[]
     */
    private $license_handlers = array();

    /**
     * Find the item by the key and activate the related license.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @param string $license The newly activated item.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function activate_item($item_key, $license)
    {
        $license = trim($license);

        $license_handler = $this->get_license_handler($item_key);
        if($license_handler === null) {
            return License_Status::activation_error();
        }

        $status = $license_handler->activate_item($license);
        if($status->is_error()) {
            return $status;
        }

        $updated = update_option(sprintf('affilicious_license_key_%s', $item_key), $license, false);
        if(empty($updated)) {
            return License_Status::error(__('Failed to store the license.', 'affilicious'));
        }

        return $status;
    }

    /**
     * Find the item by the key and deactivate the related license.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function deactivate_item($item_key)
    {
        $license_handler = $this->get_license_handler($item_key);
        if($license_handler === null) {
            return License_Status::deactivation_error();
        }

        $license = get_option(sprintf('affilicious_license_key_%s', $item_key));
        if(empty($license)) {
            return License_Status::error(__('Failed to find the license.', 'affilicious'));
        }

        $status = $license_handler->deactivate_item($license);
        if($status->is_success()) {
            $result = delete_option(sprintf('affilicious_license_key_%s', $item_key));
            if(!$result) {
                return License_Status::error(__('Failed to delete the license.', 'affilicious'));
            }
        }

        return $status;
    }

    /**
     * Find the item by the key and check the license status.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function check_item($item_key)
    {
        $license_handler = $this->get_license_handler($item_key);
        if($license_handler === null) {
            return License_Status::error();
        }

        $license = get_option(sprintf('affilicious_license_key_%s', $item_key));
        if(empty($license)) {
            return License_Status::error(__('Failed to find the license.', 'affilicious'));
        }

        $status = $license_handler->check_item($license);

        return $status;
    }

    /**
     * Get the license item key.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @return null|string
     */
    public function get_item_license_key($item_key)
    {
        $license = get_option(sprintf('affilicious_license_key_%s', $item_key));
        if(empty($license)) {
            return null;
        }

        return $license;
    }

    /**
     * Add a new license handler.
     *
     * @since 0.9
     * @param License_Handler_Interface $license_handler
     */
    public function add_license_handler(License_Handler_Interface $license_handler)
    {
        $item_key = $license_handler->get_item_key();
        $this->license_handlers[$item_key] = $license_handler;
    }

    /**
     * Remove an existing license handler.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     */
    public function remove_license_handler($item_key)
    {
        unset($this->license_handlers[$item_key]);
    }

    /**
     * Get an existing license handler by the name.
     *
     * @since 0.9
     * @param string $key The unique item key.
     * @return null|License_Handler_Interface
     */
    public function get_license_handler($key)
    {
        return isset($this->license_handlers[$key]) ? $this->license_handlers[$key] : null;
    }

    /**
     * Get all license handlers.
     *
     * @since 0.9
     * @return License_Handler_Interface[]
     */
    public function get_license_handlers()
    {
        return array_values($this->license_handlers);
    }
}
