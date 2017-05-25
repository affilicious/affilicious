<?php
namespace Affilicious\Common\Options;

use Affilicious\Common\Helper\View_Helper;
use Affilicious\Common\License\License_Manager;
use Affilicious\Common\License\License_Processor;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Affilicious_Options
{
    /**
     * @var License_Manager
     */
    private $license_manager;

    /**
     * @var License_Processor
     */
    private $license_processor;

    /**
     * @since 0.8.12
     * @param License_Manager $license_manager
     * @param License_Processor $license_processor
     */
    public function __construct(License_Manager $license_manager, License_Processor $license_processor)
    {
        $this->license_manager = $license_manager;
        $this->license_processor = $license_processor;
    }

    /**
	 * Render the settings into the admin area.
     *
     * @hook init
	 * @since 0.7
	 */
	public function render()
	{
		do_action('affilicious_options_affilicious_before_render');

		$scripts_tab = apply_filters('affilicious_options_affilicious_container_scripts_tab', array(
			Carbon_Field::make('header_scripts', 'affilicious_options_affilicious_container_scripts_tab_custom_header_scripts', __('Custom Header Scripts', 'affilicious'))
                ->set_help_text(__("Add your custom header scripts like CSS or JS with the proper &lt;style&gt or &lt;script&gt tags.", 'affilicious')),
			Carbon_Field::make('footer_scripts', 'affilicious_options_affilicious_container_scripts_tab_custom_footer_scripts', __('Custom Footer Scripts', 'affilicious'))
                ->set_help_text(__("Add your custom footer scripts like Google Analytics tracking code, CSS or JS with the proper &lt;style&gt or &lt;script&gt tags.", 'affilicious')),
		));

		$container = Carbon_Container::make('theme_options', 'Affilicious')
	       ->set_icon('dashicons-admin-generic')
	       ->add_tab(__('Scripts', 'affilicious'), $scripts_tab)
	       ->add_tab(__('License', 'affilicious'), $this->get_license_tab());

		apply_filters('affilicious_options_affilicious_container', $container);
        do_action('affilicious_options_affilicious_after_render');
	}

    /**
     * @since 0.8.12
     * @return array
     */
	public function get_license_tab()
    {
        $help_text = count($this->license_manager->get_license_handlers()) > 0
            ? sprintf(__('More add-ons and themes can be found on the official website of <a href="%s">Affilicious Theme</a>.', 'affilicious'), 'https://affilicioustheme.de')
            : sprintf(__('It looks like you haven\'t got any add-on or theme yet. Visit our official website of <a href="%s">Affilicious Theme</a> to see what you can start with.', 'affilicious'), 'https://affilicioustheme.de');

        $fields = array(
            Carbon_Field::make('html', 'crb_information_text')
                ->set_html(View_Helper::stringify(\Affilicious::get_root_path() . 'src/common/view/license/licenses.php', array(
                    'license_manager' => $this->license_manager,
                    'license_processor' => $this->license_processor,
                )))
                ->set_help_text($help_text)
        );

        return $fields;
    }

	/**
	 * Apply the saved settings.
     *
     * @hook init
	 * @since 0.7
	 */
	public function apply()
	{
		do_action('affilicious_options_affilicious_before_apply');

        // Nothing to do here yet

		do_action('affilicious_options_affilicious_after_apply');
	}
}
