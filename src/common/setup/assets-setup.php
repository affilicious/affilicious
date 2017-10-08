<?php
namespace Affilicious\Common\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Assets_Setup
{
    /**
     * Add the admin styles.
     *
     * @hook wp_enqueue_scripts
     * @since 0.9.9
     */
    public function add_styles()
    {
        $base_public_url = \Affilicious::get_root_url() . 'assets/public/dist/css/';

        // Register Selectize styles
        wp_enqueue_style('aff-universal-box', $base_public_url . 'universal-box.min.css', [], \Affilicious::VERSION);
    }

    /**
     * Add the admin scripts.
     *
     * @hook wp_enqueue_scripts
     * @since 0.9.9
     */
    public function add_scripts()
    {

    }
}
