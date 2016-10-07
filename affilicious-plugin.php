<?php
/**
 * Plugin Name: Affilicious
 * Description: Manage affiliate products in Wordpress with price comparisons, shops, details and more
 * Version: 0.5.2
 * Author: Affilicious Team
 * Author URI: http://affilicioustheme.de/
 * Plugin URI: http://affilicioustheme.de/downloads/affilicious/
 * License: GPL-2.0 or later
 * Requires at least: 4.5
 * Tested up to: 4.6
 * Text Domain: affilicious
 * Domain Path: /languages/
 *
 * Affilicious Plugin
 * Copyright (C) 2016, Affilicious - support@affilicioustheme.de
 *
 * Affilicious is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Affilicious is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Affilicious. If not, see <http://www.gnu.org/licenses/>.
 */
use Affilicious\Attribute\Application\Setup\AttributeTemplateGroupSetup;
use Affilicious\Attribute\Infrastructure\Persistence\Carbon\CarbonAttributeTemplateGroupRepository;
use Affilicious\Attribute\Infrastructure\Persistence\InMemory\InMemoryAttributeTemplateFactory;
use Affilicious\Attribute\Infrastructure\Persistence\InMemory\InMemoryAttributeTemplateGroupFactory;
use Affilicious\Common\Application\Setup\AssetSetup;
use Affilicious\Common\Application\Setup\CarbonSetup;
use Affilicious\Common\Application\Setup\FeedbackSetup;
use Affilicious\Detail\Application\Setup\DetailTemplateGroupSetup;
use Affilicious\Detail\Infrastructure\Persistence\Carbon\CarbonDetailTemplateGroupRepository;
use Affilicious\Detail\Infrastructure\Persistence\InMemory\InMemoryDetailTemplateFactory;
use Affilicious\Detail\Infrastructure\Persistence\InMemory\InMemoryDetailTemplateGroupFactory;
use Affilicious\Product\Application\MetaBox\MetaBoxManager;
use Affilicious\Product\Application\Setup\ProductSetup;
use Affilicious\Product\Application\Setup\ProductVariantSetup;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductVariantRepository;
use Affilicious\Product\Infrastructure\Persistence\InMemory\InMemoryProductFactory;
use Affilicious\Product\Infrastructure\Persistence\InMemory\InMemoryProductVariantFactory;
use Affilicious\Product\Infrastructure\Persistence\InMemory\InMemoryShopFactory;
use Affilicious\Settings\Application\Setting\AffiliciousSettings;
use Affilicious\Settings\Application\Setting\ProductSettings;
use Affilicious\Shop\Application\Setup\ShopTemplateSetup;
use Affilicious\Shop\Infrastructure\Persistence\Wordpress\WordpressShopTemplateRepository;
use Affilicious\Shop\Infrastructure\Persistence\InMemory\InMemoryShopTemplateFactory;
use Pimple\Container;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class AffiliciousPlugin
{
    const PLUGIN_NAME = 'affilicious';
    const PLUGIN_VERSION = '0.5.2';
    const PLUGIN_NAMESPACE = 'Affilicious\\';
    const PLUGIN_SOURCE_DIR = 'src/';
    const PLUGIN_LANGUAGE_DIR = 'languages/';
    const PLUGIN_STORE_URL = 'http://affilicioustheme.de';
    const PLUGIN_ITEM_NAME = 'Affilicious';
    const PLUGIN_LICENSE_KEY = 'e90a6d1a115da24a292fe0300afc402a';
    const PLUGIN_AUTHOR = 'Affilicious Team';

    /**
     * Stores the singleton instance
     *
     * @since 0.3
     * @var AffiliciousPlugin
     */
    private static $instance;

    /**
     * Register all services and parameters for the pimple dependency injection
     *
     * @see http://pimple.sensiolabs.org
     * @var Container
     */
    private $container;

    /**
     * Get the instance of the affilicious plugin
     *
     * @since 0.3
     * @return AffiliciousPlugin
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AffiliciousPlugin();
        }

        return self::$instance;
    }

    /**
     * Get the root dir of the plugin
     *
     * @since 0.3
     * @return string
     */
    public static function getRootDir()
    {
        return plugin_dir_url( __FILE__ );
    }

    /**
     * Prepare the plugin with for usage with Wordpress and namespaces
     *
     * @since 0.3
     */
    private function __construct()
    {
        if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
            require(__DIR__ . '/vendor/autoload.php');
        }

        spl_autoload_register(array($this, 'autoload'));

        $this->container = new Container();
    }

    /**
     * Get a reference to the dependency injection container
     *
     * @see https://easydigitaldownloads.com/downloads/software-licensing/
     * @since 0.3
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3
	 */
	public function run()
	{
		register_activation_hook( __FILE__, array($this, 'activate'));
		register_deactivation_hook( __FILE__, array($this, 'deactivate'));

		$this->loadIncludes();
		$this->loadFunctions();
		$this->registerServices();
		$this->registerPublicHooks();
		$this->registerAdminHooks();

		new MetaBoxManager(); // TODO: This old class will be removed later
		// We have to call the container to the run code inside
		$this->container['affilicious.common.setup.carbon'];
	}

    /**
     * Update the plugin with the help of the Software Licensing for Easy Digital Downloads
     *
     * @since 0.3
     */
    public function update()
    {
        new \EDD_SL_Plugin_Updater(self::PLUGIN_STORE_URL, __FILE__, array(
            'version' => self::PLUGIN_VERSION,
            'license' => self::PLUGIN_LICENSE_KEY,
            'item_name' => self::PLUGIN_ITEM_NAME,
            'author' => self::PLUGIN_AUTHOR,
        ));
    }

    /**
     * Make namespaces compatible with the source code of this plugin
     *
     * @since 0.3
     * @param string $class
     */
    public function autoload($class)
    {
        $prefix = 'Affilicious';
        if (stripos($class, $prefix) === false) {
            return;
        }
        $file_path = __DIR__ . '/' . self::PLUGIN_SOURCE_DIR . str_ireplace(self::PLUGIN_NAMESPACE, '', $class) . '.php';
        $file_path = str_replace('\\', DIRECTORY_SEPARATOR, $file_path);
        include_once($file_path);
    }

    /**
     * The code that runs during plugin activation.
     *
     * @since 0.3
     */
    public function activate()
    {
        // Data to send in our API request.
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> self::PLUGIN_LICENSE_KEY,
            'item_name' => urlencode(self::PLUGIN_ITEM_NAME),
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post(self::PLUGIN_STORE_URL, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params
        ));

        // Make sure the response came back okay
        $isError = is_wp_error($response);

	    return $isError;
    }

    /**
     * The code that runs during plugin deactivation.
     *
     * @since 0.3
     */
    public function deactivate()
    {
    	// Data to send in our API request.
	    $api_params = array(
		    'edd_action'=> 'deactivate_license',
		    'license' 	=> self::PLUGIN_LICENSE_KEY,
		    'item_name' => urlencode(self::PLUGIN_ITEM_NAME),
		    'url'       => home_url()
	    );

	    // Call the custom API.
	    $response = wp_remote_post(self::PLUGIN_STORE_URL, array(
	    	'timeout' => 15,
		    'sslverify' => false,
		    'body' => $api_params
	    ));

	    // Make sure the response came back okay
	    $isError = is_wp_error($response);

	    return $isError;
    }

    /**
     * Register the services for the dependency injection
     *
     * @since 0.3
     */
    public function registerServices()
    {
        $this->container['affilicious.common.setup.asset'] = function () {
            return new AssetSetup();
        };

        $this->container['affilicious.common.setup.carbon'] = function () {
            return new CarbonSetup();
        };

        $this->container['affilicious.common.setup.feedback'] = function () {
            return new FeedbackSetup();
        };

        $this->container['affilicious.product.repository.product'] = function ($c) {
            return new CarbonProductRepository(
                $c['affilicious.product.repository.product_variant'],
                $c['affilicious.detail.repository.detail_group'],
                $c['affilicious.product.factory.shop']
            );
        };

        $this->container['affilicious.product.repository.product_variant'] = function ($c) {
            return new CarbonProductVariantRepository(
                $c['affilicious.detail.repository.detail_group'],
                $c['affilicious.product.factory.shop']
            );
        };

        $this->container['affilicious.product.factory.product'] = function () {
            return new InMemoryProductFactory();
        };

        $this->container['affilicious.product.factory.product_variant'] = function () {
            return new InMemoryProductVariantFactory();
        };

        $this->container['affilicious.product.factory.shop'] = function () {
            return new InMemoryShopFactory();
        };

        $this->container['affilicious.shop.repository.shop_template'] = function ($c) {
            return new WordpressShopTemplateRepository(
                $c['affilicious.shop.factory.shop_template']
            );
        };

        $this->container['affilicious.shop.factory.shop_template'] = function () {
            return new InMemoryShopTemplateFactory();
        };

        $this->container['affilicious.detail.repository.detail_template_group'] = function ($c) {
            return new CarbonDetailTemplateGroupRepository(
                $c['affilicious.detail.factory.detail_template_group'],
                $c['affilicious.detail.factory.detail_template']
            );
        };

        $this->container['affilicious.detail.factory.detail_template_group'] = function () {
            return new InMemoryDetailTemplateGroupFactory();
        };

        $this->container['affilicious.detail.factory.detail_template'] = function () {
            return new InMemoryDetailTemplateFactory();
        };

        $this->container['affilicious.attribute.repository.attribute_template_group'] = function ($c) {
            return new CarbonAttributeTemplateGroupRepository(
                $c['affilicious.attribute.factory.attribute_template_group'],
                $c['affilicious.attribute.factory.attribute_template']
            );
        };

        $this->container['affilicious.attribute.factory.attribute_template'] = function () {
            return new InMemoryAttributeTemplateFactory();
        };

        $this->container['affilicious.attribute.factory.attribute_template_group'] = function () {
            return new InMemoryAttributeTemplateGroupFactory();
        };

        $this->container['affilicious.product.setup.product'] = function ($c) {
            return new ProductSetup(
                $c['affilicious.shop.repository.shop_template'],
                $c['affilicious.attribute.repository.attribute_template_group'],
                $c['affilicious.detail.repository.detail_template_group']
            );
        };

        $this->container['affilicious.product.setup.product_variant'] = function () {
            return new ProductVariantSetup();
        };

        $this->container['affilicious.shop.setup.shop_template'] = function () {
            return new ShopTemplateSetup();
        };

        $this->container['affilicious.detail.setup.detail_template_group'] = function () {
            return new DetailTemplateGroupSetup();
        };

        $this->container['affilicious.attribute.setup.attribute_template_group'] = function () {
            return new AttributeTemplateGroupSetup();
        };

	    $this->container['affilicious.settings.setting.affilicious'] = function () {
		    return new AffiliciousSettings();
	    };

	    $this->container['affilicious.settings.setting.product'] = function () {
		    return new ProductSettings();
	    };
    }

    /**
     * Register the plugin textdomain for internationalization.
     *
     * @since 0.5.1
     */
    public function loadTextdomain()
    {
        $dir = basename( dirname( __FILE__ ) ) . '/' . self::PLUGIN_LANGUAGE_DIR;
        load_plugin_textdomain(self::PLUGIN_NAME, false, $dir);
    }

	/**
	 * Load the includes
	 *
	 * @since 0.5.1
	 */
    public function loadIncludes()
    {
	    require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Hidden_Field.php');
	    require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Number_Field.php');

	    if (!class_exists('EDD_SL_Plugin_Updater')) {
		    include(dirname(__FILE__) . '/affilicious-plugin-updater.php');
	    }
    }

	/**
	 * Load the simple functions for an easier usage in templates
	 *
	 * @since 0.5.1
	 */
    public function loadFunctions()
    {
	    require_once(__DIR__ . '/src/functions.php');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     *
     * @since 0.3
     */
    public function registerPublicHooks()
    {
    	// Hook the text domain
	    add_action('plugins_loaded', array($this, 'loadTextdomain'));

        // Hook the public assets
        $assetSetup = $this->container['affilicious.common.setup.asset'];
        add_action('wp_enqueue_scripts', array($assetSetup, 'addPublicStyles'), 10);
        add_action('wp_enqueue_scripts', array($assetSetup, 'addPublicScripts'), 20);

        // Hook the Carbon Fields
        $carbonFieldsSetup = $this->container['affilicious.common.setup.carbon'];
        add_action('after_setup_theme', array($carbonFieldsSetup, 'crb_init_carbon_field_hidden'), 15);

        // Hook the shops
        $shopTemplateSetup = $this->container['affilicious.shop.setup.shop_template'];
        add_action('init', array($shopTemplateSetup, 'init'), 1);
        add_action('init', array($shopTemplateSetup, 'render'), 2);
        add_action('manage_shop_posts_columns', array($shopTemplateSetup, 'columnsHead'), 9, 2);
        add_action('manage_shop_posts_custom_column', array($shopTemplateSetup, 'columnsContent'), 10, 2);

        // Hook the attribute groups
        $attributeTemplateGroupSetup = $this->container['affilicious.attribute.setup.attribute_template_group'];
        add_action('init', array($attributeTemplateGroupSetup, 'init'), 3);
        add_action('init', array($attributeTemplateGroupSetup, 'render'), 4);
        add_action('manage_aff_attribute_group_posts_columns', array($attributeTemplateGroupSetup, 'columnsHead'), 9, 2);
        add_action('manage_aff_attribute_group_posts_custom_column', array($attributeTemplateGroupSetup, 'columnsContent'), 10, 2);

        // Hook the detail groups
        $detailTemplateGroupSetup = $this->container['affilicious.detail.setup.detail_template_group'];
        add_action('init', array($detailTemplateGroupSetup, 'init'), 3);
        add_action('init', array($detailTemplateGroupSetup, 'render'), 4);
        add_action('manage_detail_group_posts_columns', array($detailTemplateGroupSetup, 'columnsHead'), 9, 2);
        add_action('manage_detail_group_posts_custom_column', array($detailTemplateGroupSetup, 'columnsContent'), 10, 2);

        // Hook the products
        $productSetup = $this->container['affilicious.product.setup.product'];
        add_action('init', array($productSetup, 'init'), 5);
        add_action('init', array($productSetup, 'render'), 6);

        // Hook the product variants
        $productVariantSetup = $this->container['affilicious.product.setup.product_variant'];
        //add_action('init', array($productVariantSetup, 'init'), 5);
        //add_action('init', array($productVariantSetup, 'render'), 6);

	    // Hook the settings
        $affiliciousSettings = $this->container['affilicious.settings.setting.affilicious'];
        $productSettings = $this->container['affilicious.settings.setting.product'];
	    add_action('init', array($affiliciousSettings, 'render'), 10);
	    add_action('init', array($affiliciousSettings, 'apply'), 11);
	    add_action('init', array($productSettings, 'render'), 12);
	    add_action('init', array($productSettings, 'apply'), 13);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     *
     * @since 0.3
     */
    public function registerAdminHooks()
    {
    	// Hook the plugin updater
	    add_action('admin_init', array($this, 'update'), 0);

        // Hook the admin assets
        $assetSetup = $this->container['affilicious.common.setup.asset'];
        add_action('admin_enqueue_scripts', array($assetSetup, 'addAdminStyles'), 10);
        add_action('admin_enqueue_scripts', array($assetSetup, 'addAdminScripts'), 20);

        // Hook the feedback form
        $feedbackSetup = $this->container['affilicious.common.setup.feedback'];
        add_action('admin_menu', array($feedbackSetup, 'init'), 30);






        //add_action('admin_init', array($this, 'test'), 100);


    }

    public function test()
    {
        $productFactory = $this->container['affilicious.product.factory.product'];

        $productVariantFactory = $this->container['affilicious.product.factory.product_variant'];

        $productRepository = $this->container['affilicious.product.repository.product'];

        $productVariantRepository = $this->container['affilicious.product.repository.product_variant'];

        $product = $productRepository->findById(new \Affilicious\Product\Domain\Model\ProductId(1590));

        $product->getId();

        /*$product = $productFactory->create(
            new \Affilicious\Common\Domain\Model\Title('Parent Test')
        );


        $product = $productRepository->store($product);
        $productRepository->delete($product->getId());*/



        // Variant
        /*$productVariant = $productVariantFactory->create(
            $product,
            new \Affilicious\Common\Domain\Model\Title('Variant Test 1')
        );

        $productVariant = $productVariantRepository->store($productVariant);
        $product->addVariant($productVariant);



        $productVariantRepository->delete($productVariant->getId());*/
    }
}

$affiliciousPlugin = AffiliciousPlugin::getInstance();
$affiliciousPlugin->run();
