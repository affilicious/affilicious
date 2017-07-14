<?php
namespace Affilicious\Product\Admin\Page;

use Affilicious\Common\Helper\View_Helper;
use Affilicious\Shop\Helper\Shop_Template_Helper;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if(!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Import_Page
{
    /**
     * @var Shop_Template_Repository_Interface
     */
    protected $shop_template_repository;

    /**
     * @since 0.9
     * @param Shop_Template_Repository_Interface $shop_template_repository
     */
    public function __construct(Shop_Template_Repository_Interface $shop_template_repository)
    {
        $this->shop_template_repository = $shop_template_repository;
    }

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
        $shop_templates = $this->shop_template_repository->find_all();

        if(!empty($shop_templates)) {
            $shop_templates = array_map(function (Shop_Template $shop_template) {
                return Shop_Template_Helper::to_array($shop_template);
            }, $shop_templates);
        }

	    View_Helper::render(AFFILICIOUS_ROOT_PATH . 'src/product/admin/view/page/import.php', [
	        'shop_templates' => $shop_templates
        ]);
	}
}
