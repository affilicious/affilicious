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
use Affilicious\ProductsPlugin\Loader;
use Affilicious\ProductsPlugin\Product\ProductSetup;
use Affilicious\ProductsPlugin\Product\Field\FieldGroupSetup;
use Affilicious\ProductsPlugin\Product\Detail\DetailGroupSetup;
use Affilicious\ProductsPlugin\Product\Shop\ShopSetup;
use Affilicious\ProductsPlugin\Assets;

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
     * Register all assets like styles and scripts
     * for the public and admin area
     * @var Assets
     */
    private $assets;

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
        require_once(self::PLUGIN_SOURCE_DIR . 'CarbonField/CarbonFieldSetup.php');
        require_once(self::PLUGIN_SOURCE_DIR . 'CarbonField/Hidden_Field.php');

        new \CarbonFieldSetup();
        $this->loader = new Loader();
        $this->assets = new Assets();
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

        new ProductSetup();
        new ShopSetup();
        new FieldGroupSetup();
        new DetailGroupSetup();
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
        $this->loader->add_action('wp_enqueue_scripts', $this->assets, 'addPublicStyles');
        $this->loader->add_action('wp_enqueue_scripts', $this->assets, 'addPublicScripts');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     */
    public function registerAdminHooks()
    {
        // Add admin assets
        $this->loader->add_action('admin_enqueue_scripts', $this->assets, 'addAdminStyles');
        $this->loader->add_action('admin_enqueue_scripts', $this->assets, 'addAdminScripts');
    }
}

$affiliciousProductsPlugin = new AffiliciousProductsPlugin();
$affiliciousProductsPlugin->run();
