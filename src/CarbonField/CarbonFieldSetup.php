<?php
use Affilicious\ProductsPlugin\Loader;

class CarbonFieldSetup
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * Hook into the required Wordpress actions
     */
    public function __construct()
    {
        $this->loader = new Loader();
        $this->loader->add_action('after_setup_theme', $this, 'crb_init_carbon_field_hidden', 15);
        $this->loader->run();
    }

    /**
     * Init the hidden Carbon field
     */
    public function crb_init_carbon_field_hidden() {
        if (class_exists("Carbon_Fields\\Field")) {
            include_once dirname(__FILE__) . '/Hidden_Field.php';
        }
    }
}
