<?php
/**
 * Plugin Name: Affilicious
 * Description: Erstelle und verwalte Affiliate Produkte in Wordpress mit Preisvergleichen, Shops, Details und mehr
 * Version: 0.4.3
 * Author: Affilicious Team
 * Author URI: http://affilicioustheme.de/author/alexander-barton
 * Plugin URI: http://affilicioustheme.de/plugins/products
 * License: MIT
 * Requires at least: 4.5
 * Tested up to: 4.6
 * Text Domain: affilicious
 * Domain Path: /languages/
 */
use Affilicious\Common\Application\Setup\AssetSetup;
use Affilicious\Common\Application\Setup\CarbonSetup;
use Affilicious\Product\Application\Setup\ProductSetup;
use Affilicious\Product\Application\Setup\ShopSetup;
use Affilicious\Product\Application\Setup\DetailGroupSetup;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Product\Infrastructure\Persistence\Wordpress\WordpressShopRepository;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonDetailGroupRepository;
use Affilicious\Product\Application\MetaBox\MetaBoxManager;
use Affilicious\Common\Application\Setup\FeedbackSetup;
use Pimple\Container;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class AffiliciousPlugin
{
    const PLUGIN_NAME = 'affilicious';
    const PLUGIN_VERSION = '0.4.3';
    const PLUGIN_NAMESPACE = 'Affilicious\\';
    const PLUGIN_SOURCE_DIR = 'src/';
    const PLUGIN_LANGUAGE_DIR = 'languages';
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
        require_once(self::PLUGIN_SOURCE_DIR . 'functions.php');
        if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
            require(__DIR__ . '/vendor/autoload.php');
        }

        spl_autoload_register(array($this, 'autoload'));

        require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Hidden_Field.php');
        require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Number_Field.php');

        if (!class_exists('EDD_SL_Plugin_Updater')) {
            include(dirname(__FILE__) . '/affilicious-plugin-updater.php');
        }

        $this->container = new Container();

        add_action('admin_init', array($this, 'update'), 0);
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
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since 0.3
     */
    public function run()
    {
        register_activation_hook( __FILE__, array($this, 'activate'));
        register_deactivation_hook( __FILE__, array($this, 'deactivate'));
        add_action('plugins_loaded', array($this, 'loaded'));

        $this->registerServices();
        $this->registerPublicHooks();
        $this->registerAdminHooks();

        new MetaBoxManager(); // This old class will be removed later
        $this->container['affilicious.common.setup.carbon'];
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
        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> self::PLUGIN_LICENSE_KEY,
            'item_name' => urlencode(self::PLUGIN_ITEM_NAME), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post(self::PLUGIN_STORE_URL, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params
        ));

        // make sure the response came back okay
        return is_wp_error($response);
    }

    /**
     * The code that runs during plugin deactivation.
     *
     * @since 0.3
     */
    public function deactivate()
    {
        // Nothing to do here
    }

    /**
     * The code that runs after the plugin is loaded
     *
     * @since 0.3
     */
    public function loaded()
    {
        $this->registerTextdomain();
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
            return new CarbonProductRepository($c['affilicious.product.repository.detail_group']);
        };

        $this->container['affilicious.product.repository.shop'] = function () {
            return new WordpressShopRepository();
        };

        $this->container['affilicious.product.repository.detail_group'] = function () {
            return new CarbonDetailGroupRepository();
        };

        $this->container['affilicious.product.setup.product'] = function ($c) {
            return new ProductSetup($c['affilicious.product.repository.detail_group'], $c['affilicious.product.repository.shop']);
        };

        $this->container['affilicious.product.setup.shop'] = function () {
            return new ShopSetup();
        };

        $this->container['affilicious.product.setup.detail_group'] = function () {
            return new DetailGroupSetup();
        };
    }

    /**
     * Register the plugin textdomain for internationalization.
     *
     * @since 0.3
     */
    public function registerTextdomain()
    {
        $dir = basename( dirname( __FILE__ ) ) . '/' . self::PLUGIN_LANGUAGE_DIR;
        load_plugin_textdomain(self::PLUGIN_NAME, false, $dir);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     *
     * @since 0.3
     */
    public function registerPublicHooks()
    {
        // Add public assets
        add_action('wp_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addPublicStyles'), 10);
        add_action('wp_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addPublicScripts'), 20);

        // Set up Carbon Fields
        add_action('after_setup_theme', array($this->container['affilicious.common.setup.carbon'], 'crb_init_carbon_field_hidden'), 15);

        // Set up shops
        add_action('init', array($this->container['affilicious.product.setup.shop'], 'init'), 1);
        add_action('init', array($this->container['affilicious.product.setup.shop'], 'render'), 2);
        add_action('manage_shop_posts_columns', array($this->container['affilicious.product.setup.shop'], 'columnsHead'), 9, 2);
        add_action('manage_shop_posts_custom_column', array($this->container['affilicious.product.setup.shop'], 'columnsContent'), 10, 2);

        // Set up detail groups
        add_action('init', array($this->container['affilicious.product.setup.detail_group'], 'init'), 3);
        add_action('init', array($this->container['affilicious.product.setup.detail_group'], 'render'), 4);

        // Set up products
        add_action('init', array($this->container['affilicious.product.setup.product'], 'init'), 5);
        add_action('init', array($this->container['affilicious.product.setup.product'], 'render'), 6);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     *
     * @since 0.3
     */
    public function registerAdminHooks()
    {
        // Add admin assets
        add_action('admin_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addAdminStyles'), 10);
        add_action('admin_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addAdminScripts'), 20);

        // Set up feedback form
        add_action('admin_menu', array($this->container['affilicious.common.setup.feedback'], 'init'), 30);
    }
}

$affiliciousPlugin = AffiliciousPlugin::getInstance();
$affiliciousPlugin->run();
