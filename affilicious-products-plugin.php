<?php
/**
 * Plugin Name: Affilicious Produkte
 * Description: Erstelle und verwalte Affilicious Produkte mit den dazugehörigen Feldern und Details in Wordpress
 * Version: 0.1
 * Author: Alexander Barton
 * Author URI: https://affilicioustheme.de/author/alexander-barton
 * Plugin URI: http://affilicioustheme.de/plugins/products
 * License: MIT
 * Requires at least: 4.0
 * Tested up to: 4.6
 * Text Domain: affiliciousproducts
 * Domain Path: /languages
 */
use Affilicious\ProductsPlugin\Common\Application\Loader\Loader;
use Affilicious\ProductsPlugin\Common\Application\Setup\AssetSetup;
use Affilicious\ProductsPlugin\Common\Application\Setup\CarbonSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\ProductSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\ShopSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\FieldGroupSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\DetailGroupSetup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class AffiliciousProductsPlugin
{
    const PLUGIN_NAME = 'affilicious-products';
    const PLUGIN_VERSION = '0.1';
    const PLUGIN_NAMESPACE = 'Affilicious\\ProductsPlugin\\';
    const PLUGIN_SOURCE_DIR = 'src/';
    const PLUGIN_LANGUAGE_DIR = 'language/';

    /**
     * Register all actions and filters for the plugin.
     * @var Loader
     */
    private $loader;

    /**
     * @var CarbonSetup
     */
    private $carbonSetup;

    /**
     * @var AssetSetup
     */
    private $assetSetup;

    /**
     * @var ProductSetup
     */
    private $productSetup;

    /**
     * @var ShopSetup
     */
    private $shopSetup;

    /**
     * @var FieldGroupSetup
     */
    private $fieldGroupSetup;

    /**
     * @var DetailGroupSetup
     */
    private $detailGroupSetup;


    /**
     * Get the root dir of the plugin
     * @return string
     */
    public static function getRootDir()
    {
        return plugin_dir_url( __FILE__ );
    }

    /**
     * Prepare the plugin with for usage with Wordpress and namespaces
     */
    public function __construct()
    {
        require_once(self::PLUGIN_SOURCE_DIR . 'functions.php');
        if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
            require(__DIR__ . '/vendor/autoload.php');
        } else {
            spl_autoload_register(array($this, 'autoload'));
        }
        require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Hidden_Field.php');

        $this->loader = new Loader();
        $this->carbonSetup = new CarbonSetup();
        $this->assetSetup = new AssetSetup();
        $this->productSetup = new ProductSetup();
        $this->shopSetup = new ShopSetup();
        $this->fieldGroupSetup = new FieldGroupSetup();
        $this->detailGroupSetup = new DetailGroupSetup();
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        register_activation_hook( __FILE__, array($this, 'activate'));
        register_deactivation_hook( __FILE__, array($this, 'deactivate'));
        $this->loader->add_action('plugins_loaded', $this, 'loaded');

        $this->registerPublicHooks();
        $this->registerAdminHooks();
        $this->loader->run();
    }

    /**
     * Make namespaces compatible with the source code of this plugin
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
     */
    public function activate()
    {
        // Nothing to do here
    }

    /**
     * The code that runs during plugin deactivation.
     */
    public function deactivate()
    {
        // Nothing to do here
    }

    /**
     * The code that runs after the plugin is loaded
     */
    public function loaded()
    {
        $this->registerTextdomain();
    }

    /**
     * Register the plugin textdomain for internationalization.
     */
    public function registerTextdomain()
    {
        load_plugin_textdomain(
            self::PLUGIN_NAME,
            false,
            dirname(dirname(plugin_basename( __FILE__ ))) . '/' . self::PLUGIN_LANGUAGE_DIR
        );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     */
    public function registerPublicHooks()
    {
        // Add public assets
        $this->loader->add_action('wp_enqueue_scripts', $this->assetSetup, 'addPublicStyles');
        $this->loader->add_action('wp_enqueue_scripts', $this->assetSetup, 'addPublicScripts');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     */
    public function registerAdminHooks()
    {
        // Add admin assets
        $this->loader->add_action('admin_enqueue_scripts', $this->assetSetup, 'addAdminStyles');
        $this->loader->add_action('admin_enqueue_scripts', $this->assetSetup, 'addAdminScripts');
    }
}

$affiliciousProductsPlugin = new AffiliciousProductsPlugin();
$affiliciousProductsPlugin->run();
