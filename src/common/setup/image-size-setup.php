<?php
namespace Affilicious\Common\Setup;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.10
 */
class Image_Size_Setup
{
	/**
	 * Add the image sizes.
	 *
	 * @hook init
	 * @since 0.9.10
	 */
	public function init()
	{
		add_image_size('aff-product-thumbnail', 300, 300);
	}
}
