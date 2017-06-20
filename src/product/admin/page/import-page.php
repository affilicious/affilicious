<?php
namespace Affilicious\Product\Admin\Page;

use Affilicious\Common\Helper\View_Helper;

if(!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Import_Page
{
	/**
     * Init the admin import page.
     *
	 * @hook admin_menu
	 * @since 0.9
	 */
	public function init()
	{
		add_submenu_page(
			'edit.php?post_type=aff_product',
			__('Import', 'affilicious'),
			__('Import', 'affilicious'),
			'manage_options',
            'import',
			array($this, 'render')
		);
	}

	/**
     * Render the admin import page.
     *
	 * @since 0.9
	 */
	public function render()
	{
	    View_Helper::render(AFFILICIOUS_ROOT_PATH . 'src/product/admin/view/page/import.php');
	}
}
