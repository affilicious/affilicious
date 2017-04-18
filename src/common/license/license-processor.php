<?php
namespace Affilicious\Common\License;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class License_Processor
{
    const LICENSE_ACTIVE = 'license_active';
    const LICENSE_MISSING = 'license_missing';
    const LICENSE_ACTIVATION_SUCCESS = 'license_activation_success';
    const LICENSE_ACTIVATION_ERROR = 'license_activation_error';
    const LICENSE_DEACTIVATION_SUCCESS = 'license_deactivation_success';
    const LICENSE_DEACTIVATION_ERROR = 'license_deactivation_error';

    /**
     * @var License_Manager
     */
    private $license_manager;

    /**
     * @since 0.8.12
     * @param License_Manager $license_manager
     */
    public function __construct(License_Manager $license_manager)
    {
        $this->license_manager = $license_manager;
    }

    /**
     * Process the license handler for the request.
     *
     * @since 0.8.12
     * @param License_Handler_Interface $license_handler
     * @return License_Status The license status like valid, missing, success or error.
     */
    public function process(License_Handler_Interface $license_handler)
    {
        // Get the key for identifying the item.
        $item_key = $license_handler->get_item_key();

        $status = $this->get_previous_status($item_key);
        if($status !== null && isset($_GET['settings-updated'])) {
            $this->delete_previous_status($item_key);
            return $status;
        }

        // Check if the license is valid, invalid or missing.
        $current_license = $this->license_manager->get_item_license_key($item_key);
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $status = $current_license !== null ?
                $this->license_manager->check_item($item_key):
                License_Status::missing();

            $this->store_previous_status($item_key, $status);

            return $status;
        }

        // Get the posted license.
        $license_key = sprintf('aff-license-%s', $item_key);
        $posted_license = !empty($_POST[$license_key]) ? $_POST[$license_key] : null;

        // The license remains the same.
        if($current_license == $posted_license) {
            $status = $current_license !== null ?
                $this->license_manager->check_item($item_key) :
                License_Status::missing();

            $this->store_previous_status($item_key, $status);

            return $status;
        }

        // New license was posted.
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $current_license === null && $posted_license !== null) {
            $status = $this->license_manager->activate_item($item_key, $posted_license);
            $this->store_previous_status($item_key, $status);

            return $status;
        }

        // Old license was removed.
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $current_license !== null && $posted_license === null) {
            $status= $this->license_manager->deactivate_item($item_key);
            $this->store_previous_status($item_key, $status);

            return $status;
        }

        // New license was replaced with old one.
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $current_license !== null && $posted_license !== null && $current_license !== $posted_license) {
            $status = $this->license_manager->deactivate_item($item_key);
            if($status->is_error()) {
                $this->store_previous_status($item_key, $status);

                return $status;
            }

            $status = $this->license_manager->activate_item($item_key, $posted_license);
            if($status->is_error()) {
                $this->store_previous_status($item_key, $status);

                return $status;
            }

            $this->store_previous_status($item_key, $status);

            return $status;
        }

        // License is missing.
        $status = License_Status::missing();
        $this->store_previous_status($item_key, $status);

        return $status;
    }

    /**
     * Get the previous license status of the item.
     *
     * @since 0.8.12
     * @param string $item_key
     * @return null|License_Status
     */
    private function get_previous_status($item_key)
    {
        $type = get_option(sprintf('affilicious_license_status_type_%s', $item_key));
        if(empty($type)) {
            return null;
        }

        $message = get_option(sprintf('affilicious_license_status_message_%s', $item_key));
        if(empty($message)) {
            $message = null;
        }

        $status = new License_Status($type, $message);

        return $status;
    }

    /**
     * Store the previous license status of the item.
     *
     * @since 0.8.12
     * @param string $item_key
     * @param License_Status $status
     */
    private function store_previous_status($item_key, License_Status $status)
    {
        update_option(sprintf('affilicious_license_status_type_%s', $item_key), $status->get_type(), false);

        if($status->has_message()) {
            update_option(sprintf('affilicious_license_status_message_%s', $item_key), $status->get_message(), false);
        }
    }

    /**
     * Delete the previous license status of the item.
     *
     * @since 0.8.12
     * @param string $item_key
     */
    private function delete_previous_status($item_key)
    {
        delete_option(sprintf('affilicious_license_status_type_%s', $item_key));
        delete_option(sprintf('affilicious_license_status_message_%s', $item_key));
    }
}
