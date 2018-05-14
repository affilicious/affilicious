<?php
namespace Affilicious\Common\Admin\Ajax_Handler;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.16
 */
class Dismissed_Notice_Ajax_Handler
{
    /**
     * Handle the dismissed notice per ajax.
     *
     * @hook wp_ajax_aff_dismissed_notice
     * @since 0.9.16
     */
    public function handle()
    {
        $dismissible_id = isset($_GET['dismissible-id']) ? $_GET['dismissible-id'] : null;
        if(empty($dismissible_id)) {
            wp_send_json_error(new \WP_Error(
                'aff_missing_notice_dismissible_id',
                __('The dismissible ID for the currently dismissed notice is missing.', 'affilicious')
            ));
        }

        $updated = update_option("aff_notice_{$dismissible_id}_dismissed", 'yes');
        if(!$updated) {
            wp_send_json_error(new \WP_Error(
                'aff_failed_to_update_dismissed_notice',
                sprintf(__('Failed to update the dismissed notice "%s".', 'affilicious'), $dismissible_id)
            ));
        }
    }
}
