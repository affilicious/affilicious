<?php
namespace Affilicious\Product\Admin\Page\Amazon;

use Affilicious\Common\Helper\Template_Helper;
use Affilicious\Provider\Helper\Provider_Helper;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Amazon\Category;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Provider_Id;
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
	    $provider = $this->find_provider();
	    $shop_templates = $this->find_all_shop_templates($provider);
        $taxonomies = $this->find_taxonomies_with_terms();

	    Template_Helper::render('admin/page/imports/amazon', [
            'provider' => $provider,
            'shop_templates' => $shop_templates,
            'taxonomies' => $taxonomies,
            'categories' => Category::$germany,
        ]);
	}

    /**
     * Find the Amazon provider.
     *
     * @since 0.9.16
     * @return array|null
     */
	protected function find_provider()
    {
        $provider = $this->provider_repository->find_by_slug(Amazon_Provider::slug());
        if(empty($provider)) {
            return null;
        }

        $provider = Provider_Helper::to_array($provider);

        return $provider;
    }

    /**
     * Find all shop templates by the providers.
     *
     * @since 0.9.16
     * @param array|null $provider
     * @return array
     */
    protected function find_all_shop_templates($provider)
    {
        if(empty($provider['id'])) {
            return [];
        }

        $shop_templates = $this->shop_template_repository->find_all_by_provider_id(new Provider_Id($provider['id']));

        // Map all shop templates to plain arrays which can be used in the view more easily.
        $shop_templates = array_map(function (Shop_Template $shop_template) {
            return Shop_Template_Helper::to_array($shop_template);
        }, $shop_templates);

        return $shop_templates;
    }

    /**
     * Find all product taxonomies with the related terms.
     *
     * @since 0.9.16
     * @return array
     */
	protected function find_taxonomies_with_terms()
    {
        global $wp_version;

        $results = [];
        $taxonomies = aff_get_product_taxonomies('objects');

        foreach ($taxonomies as $taxonomy)
        {
            $result = [
                'name' => $taxonomy->name,
                'label' => $taxonomy->label,
                'terms' => []
            ];

            if($wp_version >= '4.5') {
                $terms = get_terms([
                    'taxonomy' => $taxonomy->name,
                    'hide_empty' => false,
                ]);
            } else {
                $terms = get_terms($taxonomy->name, [
                    'hide_empty' => false,
                ]);
            }

            foreach ($terms as $term) {
                $result['terms'][] = [
                    'term_id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                ];
            }

            $results[] = $result;
        }

        return $results;
    }
}
