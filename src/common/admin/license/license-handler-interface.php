<?php
namespace Affilicious\Common\Admin\License;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
interface License_Handler_Interface
{
    /**
     * Get the unique name of the item for display purpose.
     * An item might be an extension or theme for instance.
     *
     * @since 0.9
     * @return string The item name e.g. "Affilicious Product Comparison"
     */
    public function get_item_name();

    /**
     * Get the unique item key used for creating an option in the database.
     * An item might be an extension or theme for instance.
     *
     * @since 0.9
     * @return string The item key e.g. "affilicious_product_comparison"
     */
    public function get_item_key();

    /**
     * Activate the license of the item.
     * An item might be an extension or theme for instance.
     *
     * @since 0.9
     * @param string $license The newly activated license.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function activate_item($license);

    /**
     * Deactivate the license of the item.
     * An item might be an extension or theme for instance.
     *
     * @since 0.9
     * @param string $license The previously activated license.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function deactivate_item($license);

    /**
     * Check the license status of the item.
     * An item might be an extension or theme for instance.
     *
     * @since 0.9
     * @param string $license The current activate license.
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function check_item($license);
}
