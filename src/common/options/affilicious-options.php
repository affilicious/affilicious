<?php
namespace Affilicious\Common\Options;

use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

class Affilicious_Options
{
	/**
	 * Render the settings into the admin area.
     *
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
	       ->add_tab(__('Scripts', 'affilicious'), $scripts_tab);

		apply_filters('affilicious_options_affilicious_container', $container);
        do_action('affilicious_options_affilicious_after_render');
	}

	/**
	 * Apply the saved settings.
     *
	 * @since 0.7
	 */
	public function apply()
	{
		do_action('affilicious_options_affilicious_before_apply');

        // Nothing to do here yet

		do_action('affilicious_options_affilicious_after_apply');
	}
}
