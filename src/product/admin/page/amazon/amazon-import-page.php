<?php
namespace Affilicious\Product\Admin\Page\Amazon;

use Affilicious\Common\Helper\Template_Helper;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Amazon\Category;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Helper\Shop_Template_Helper;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if(!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Amazon_Import_Page
{
    /**
     * @var Shop_Template_Repository_Interface
     */
    protected $shop_template_repository;

    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

    /**
     * @since 0.9
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(
        Shop_Template_Repository_Interface $shop_template_repository,
        Provider_Repository_Interface $provider_repository
    ) {
        $this->shop_template_repository = $shop_template_repository;
        $this->provider_repository = $provider_repository;
    }

	/**
	 * Init the admin import page.
	 *
	 * @hook aff_product_admin_import_pages
	 * @since 0.9
	 * @param array $import_pages
	 * @return array
	 */
	public function init(array $import_pages)
	{
		$import_pages[10] = [
			'title' => Amazon_Provider::NAME,
			'slug' => Amazon_Provider::SLUG,
			'render' => array($this, 'render')
		];

		return $import_pages;
	}

	/**
     * Render the admin import page.
     *
	 * @since 0.9
	 */
	public function render()
	{
        $shop_templates = [];

	    $amazon_provider = $this->provider_repository->find_one_by_slug(Amazon_Provider::slug());
	    if($amazon_provider !== null) {
            $shop_templates = $this->shop_template_repository->find_all();

            if (!empty($shop_templates)) {
                $shop_templates = array_filter($shop_templates, function(Shop_Template $shop_template) use ($amazon_provider) {
                     return $amazon_provider->get_id()->is_equal_to($shop_template->get_provider_id());
                });

                $shop_templates = array_map(function (Shop_Template $shop_template) {
                    return Shop_Template_Helper::to_array($shop_template);
                }, $shop_templates);
            }
        }

	    Template_Helper::render('admin/page/imports/amazon', [
	        'shop_templates' => $shop_templates,
            'amazon_provider_configured' => $amazon_provider !== null,
		    'categories' => Category::$germany
        ]);
	}
}
