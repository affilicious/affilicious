<?php
namespace Affilicious\Common\Admin\Options;

use Affilicious\Common\Helper\View_Helper;
use Affilicious\Common\Admin\License\License_Manager;
use Affilicious\Common\Admin\License\License_Processor;
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
     * @since 0.9
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
	 * @since 0.9
	 */
	public function render()
	{
		do_action('aff_admin_options_before_render_affilicious_container');

		$container = Carbon_Container::make('theme_options', 'Affilicious')
	        ->set_icon('dashicons-admin-generic')
            ->add_tab(__('Licenses', 'affilicious'), $this->get_licenses_fields())
	        ->add_tab(__('Scripts', 'affilicious'), $this->get_scripts_fields());

        $container = apply_filters('aff_admin_options_render_affilicious_container', $container);

        do_action('aff_admin_options_after_render_affilicious_container', $container);
	}

    /**
     * Get the licenses fields.
     *
     * @since 0.9.1
     * @return Carbon_Field[]
     */
	public function get_licenses_fields()
    {
        $help_text = count($this->license_manager->get_license_handlers()) > 0
            ? sprintf(__('More add-ons and themes can be found on the official website of <a href="%s">Affilicious Theme</a>.', 'affilicious'), 'https://affilicioustheme.de')
            : sprintf(__('It looks like you haven\'t got any add-on or theme yet. Visit our official website of <a href="%s">Affilicious Theme</a> to see what you can start with.', 'affilicious'), 'https://affilicioustheme.de');

        $fields = array(
            Carbon_Field::make('html', 'affilicious_options_affilicious_container_licenses_tab_licences_field')
                ->set_html(View_Helper::stringify(\Affilicious::get_root_path() . 'src/common/admin/view/licenses/licenses.php', array(
                    'license_manager' => $this->license_manager,
                    'license_processor' => $this->license_processor,
                )))
                ->set_help_text($help_text)
        );

        return apply_filters('aff_admin_options_render_affilicious_container_licenses_fields', $fields);
    }

    /**
     * Get the scripts fields.
     *
     * @since 0.9
     * @return Carbon_Field[]
     */
    public function get_scripts_fields()
    {
        $fields = array(
            Carbon_Field::make('header_scripts', 'affilicious_options_affilicious_container_scripts_tab_custom_header_scripts', __('Custom Header Scripts', 'affilicious'))
                ->set_help_text(__("Add your custom header scripts like CSS or JS with the proper &lt;style&gt or &lt;script&gt tags.", 'affilicious')),
            Carbon_Field::make('footer_scripts', 'affilicious_options_affilicious_container_scripts_tab_custom_footer_scripts', __('Custom Footer Scripts', 'affilicious'))
                ->set_help_text(__("Add your custom footer scripts like Google Analytics tracking code, CSS or JS with the proper &lt;style&gt or &lt;script&gt tags.", 'affilicious')),
        );

        return apply_filters('aff_admin_options_render_affilicious_container_scripts_fields', $fields);
    }
}
