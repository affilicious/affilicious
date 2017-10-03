<?php
namespace Affilicious\Common\Admin\License;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class License_Manager
{
	const LICENSE_KEY_OPTION = 'affilicious_license_key_%s';
	const LICENSE_VALIDITY_OPTION = 'affilicious_license_validity_%s';

    /**
     * The license handlers are responsible for handling the activation, deactivation and license checks
     * for the specific items in a convenient way.
     * An item might be an extension or theme for instance.
     *
     * @var License_Handler_Interface[]
     */
    private $license_handlers = [];

    /**
     * Find the item by the key and activate the related license.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @param string $license_key The newly activated license key.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function activate_item($item_key, $license_key)
    {
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');
	    Assert_Helper::is_string_not_empty($license_key, __METHOD__, 'Expected the license key to be a non empty string. Got: %s', '0.9.9');

    	// Remove any whitespace before and after the license key.
	    $license_key = trim($license_key);

	    // Find the item's license handler.
        $license_handler = $this->get_license_handler($item_key);
        if($license_handler === null) {
            return License_Status::activation_error();
        }

        // Activate the item's new license key.
        $status = $license_handler->activate_item($license_key);
        if(!$status->is_success()) {
            return $status;
        }

        // Store the item's new license key.
        $this->store_item_license_key($item_key, $license_key);

        // Store the item's license validity.
	    $this->store_item_license_validity($item_key, true);

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
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

	    // Find the item's license handler.
        $license_handler = $this->get_license_handler($item_key);
        if($license_handler === null) {
            return License_Status::deactivation_error();
        }

	    // Find the item's existing license key.
	    $license_key = $this->find_item_license_key($item_key);
	    if($license_key === null) {
		    return License_Status::error(__('Failed to find the license key.', 'affilicious'));
	    }

	    // Deactivate the item's existing license key.
        $status = $license_handler->deactivate_item($license_key);
        if(!$status->is_success()) {
            return $status;
        }

        // Delete the item's existing license key.
	    $this->delete_item_license_key($item_key);


	    // Store the item's license validity.
	    $this->store_item_license_validity($item_key, false);

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
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

    	// Find the item's license handler.
        $license_handler = $this->get_license_handler($item_key);
        if($license_handler === null) {
            return License_Status::error();
        }

        // Find the item's license key.
        $license_key = $this->find_item_license_key($item_key);
        if($license_key === null) {
	        return License_Status::error(__('Failed to find the license key.', 'affilicious'));
        }

        // Check and store the item's license status.
        $status = $license_handler->check_item($license_key);

	    // Store the item's license validity.
	    $this->store_item_license_validity($item_key, $status->is_valid());

        return $status;
    }

    /**
     * Find the item's license key.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @return null|string Either the item's license key or null.
     */
    public function find_item_license_key($item_key)
    {
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

    	// Read the Wordpress option.
    	$option = sprintf(self::LICENSE_KEY_OPTION, $item_key);
        $license_key = get_option($option);
        if(empty($license_key)) {
            return null;
        }

        return $license_key;
    }

	/**
	 * Store the item's license key.
	 *
	 * @since 0.9.9
	 * @param string $item_key The unique item key.
	 * @param string $license_key The item's license key to store.
	 */
    public function store_item_license_key($item_key, $license_key)
    {
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');
	    Assert_Helper::is_string_not_empty($license_key, __METHOD__, 'Expected the license key to be a non empty string. Got: %s', '0.9.9');

	    // Remove any whitespace before and after the license key.
	    $license_key = trim($license_key);

	    // Update the Wordpress option.
    	$option = sprintf(self::LICENSE_KEY_OPTION, $item_key);
	    update_option($option, $license_key, false);
    }

	/**
	 * Delete the item's license key.
	 *
	 * @since 0.9.9
	 * @param string $item_key The unique item key.
	 */
    public function delete_item_license_key($item_key)
    {
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

    	// Delete the Wordpress option.
    	$option = sprintf(self::LICENSE_KEY_OPTION, $item_key);
	    delete_option($option);
    }

	/**
	 * Find the item's license validity.
	 *
	 * @since 0.9.9
	 * @param string $item_key The unique item key.
	 * @return bool Whether the operation was successful or not.
	 */
    public function find_item_license_validity($item_key)
    {
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

	    // Read the Wordpress option.
	    $option = sprintf(self::LICENSE_VALIDITY_OPTION, $item_key);
	    $license_validity = get_option($option);
	    if(empty($license_validity)) {
		    return false;
	    }

	    return true;
    }

	/**
	 * Store the item's license validity.
	 *
	 * @since 0.9.9
	 * @param string $item_key The unique item key.
	 * @param bool $valid Whether the license is valid or not.
	 */
	public function store_item_license_validity($item_key, $valid)
	{
		Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');
		Assert_Helper::is_boolean($valid, __METHOD__, 'Expected "valid" to be a boolean. Got: %s', '0.9.9');

		// Update the Wordpress option.
		$option = sprintf(self::LICENSE_VALIDITY_OPTION, $item_key);
		update_option($option, $valid ? '1' : '0', false);
	}

	/**
	 * Delete the item's license validity.
	 *
	 * @since 0.9.9
	 * @param string $item_key The unique item key.
	 */
	public function delete_item_license_validity($item_key)
	{
		Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

		// Delete the Wordpress option.
		$option = sprintf(self::LICENSE_VALIDITY_OPTION, $item_key);
		delete_option($option);
	}

    /**
     * Add a new license handler.
     *
     * @since 0.9
     * @param License_Handler_Interface $license_handler The license handler handles the activation, deactivation and check of the item.
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
	    Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

        unset($this->license_handlers[$item_key]);
    }

    /**
     * Get an existing license handler by the name.
     *
     * @since 0.9
     * @param string $item_key The unique item key.
     * @return null|License_Handler_Interface The license handler handles the activation, deactivation and check of the item.
     */
    public function get_license_handler($item_key)
    {
    	Assert_Helper::is_string_not_empty($item_key, __METHOD__, 'Expected the item key to be a non empty string. Got: %s', '0.9.9');

        return isset($this->license_handlers[$item_key]) ? $this->license_handlers[$item_key] : null;
    }

    /**
     * Get all license handlers.
     *
     * @since 0.9
     * @return License_Handler_Interface[] The license handlers handle the activation, deactivation and check of the items.
     */
    public function get_license_handlers()
    {
        return array_values($this->license_handlers);
    }
}
