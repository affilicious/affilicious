<?php
namespace Affilicious\Common\Customizer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Customizer_Interface
{
	/**
	 * Name of the customizer.
	 *
	 * @since 0.9.10
	 * @return string
	 */
	public function get_name();

	public function get_stylesheet_handle();

    /**
     * Register the panels, sections and settings of the customizer in the back-end.
     *
     * @hook customize_register
     * @since 0.9.10
     * @param \WP_Customize_Manager $wp_customize
     */
    public function register(\WP_Customize_Manager $wp_customize);

    /**
     * Render the panels, sections and settings of the customizer into the front-end.
     *
     * @hook wp_enqueue_scripts
     * @since 0.9.10
     */
    public function render();
}
