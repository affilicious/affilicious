<?php
namespace Affilicious\Product\Setup;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.4
 */
class Assets_Setup
{
	/**
	 * @since 0.10.4
	 * @var string
	 */
	const PUBLIC_URL = AFFILICIOUS_ROOT_URL . 'assets/public/dist/';

	/**
	 * @hook wp_enqueue_scripts
	 * @since 0.10.4
	 */
	public function init()
	{
		// Styles
		wp_enqueue_style('aff-universal-box', self::PUBLIC_URL . 'css/universal-box.min.css', ['lightslider'], \Affilicious::VERSION);

		// Scripts
		wp_enqueue_script('aff-universal-box', self::PUBLIC_URL . 'js/universal-box.min.js', ['lightslider'], \Affilicious::VERSION, true);
	}
}
